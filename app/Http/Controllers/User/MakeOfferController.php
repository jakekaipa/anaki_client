<?php

namespace App\Http\Controllers\User;

use Exception;
use DateTime;
use DateInterval;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\WalletController;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use App\Models\TradeOffer;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class MakeOfferController extends Controller
{

    /**
     * Make Offer page show
     *
     * @method GET
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Make Offer";
        //Log::info("user.make-offer.index");

        $user = auth()->user();

        $usdtBalance1 = 0;
        if ($user->usdtBalance != null) {
            $usdtBalance1 = $user->usdtBalance;
        }

        $usdtPending = 0;
        if ($user->usdtPending != null) {
            $usdtPending = $user->usdtPending;
        }

        $usdtBalance = "" . $usdtBalance1;

        try {
            $prices = WalletController::getPrices();
        } catch (\Exception $e) {
            Log::info("WalletController.getPrices error:" . $e->getMessage());
        }


        $bankinfoData = DB::table('bankinfo')->where('userid', $user->id)->select('index', 'accountTag', 'bankName', 'accountNumber', 'accountHolder')->get();
        $payqrData = DB::table('payqrinfo')->where('userid', $user->id)->select('index', 'payTag', 'payQR')->get();

        return view('user.make-offer.index', compact("page_title", "usdtBalance", "prices", "usdtPending", "bankinfoData", "payqrData"));
    }


    /**
     * This method for submit make-offer
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request
     */
    public function submit(Request $request)
    {
       
        try {
            //유효성 검사
            $validator = Validator::make($request->all(), [
                'action'            => 'required',
                'priceType'         => 'required',
                'tradeVolMin'       => 'required:number',
                'tradeVolMax'       => 'required:number',
                'offerTimeLimit'    => 'required',
                'payQRUrl'          => "nullable|string",
                'secretTrade'       => 'nullable',
                'password'          => 'nullable',
                'offerLabel'        => 'required|string|min:2|max:1024',
                'offerCondition'    => 'required|string|min:2|max:1024',
                'payQRImage'        => 'nullable|file|mimes:jpg,jpeg,png|max:10240',
                'fixedPrice'        => 'required:number',
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'error' => $validator->errors()->all()]);
            }

            $requestData = $request->all();
            $validated = $validator->validated();
            $user = auth()->user();

            // 중복 호출 방지
            $lockKey = "makeOffer_".$user->id.$validated['action'].$validated['priceType'];
            if (!Cache::add($lockKey, true, 10)) {
                return response()->json(['success' => false,'message' => '이미 처리 중입니다. 잠시 후 다시 시도해주세요.',], 400);
            }

            if (array_key_exists('secretTrade', $validated) == false || $validated['secretTrade'] == false) {
                $validated['secretTrade'] = false;
                $validated['password'] = null;
            }

            if ($validated['action'] == "sell") {
               
                $usdtBalance = 0;
                $usdtPending = 0;
                if ($user->usdtBalance != null) {
                    $usdtBalance = $user->usdtBalance;
                }
                if ($user->usdtPending != null) {
                    $usdtPending = $user->usdtPending;
                }

                $prices = WalletController::getPrices();

                if ($validated['tradeVolMax'] > ($usdtBalance - $usdtPending) * $prices['KRW']) {
                    // return response()->json(['success' => false, 'message' => $validated['tradeVolMax']."/".$usdtBalance."/".$usdtPending.'/'.$prices['KRW'].'//'.($usdtBalance - $usdtPending) * $prices['KRW']]);
                    return response()->json(['success' => false, 'message' => 'You don\'t have enough USDT balance.']);
                }


                if (array_key_exists('bankTrasfer', $requestData)) {
                    $validator1 = Validator::make($request->all(), [
                        'accountTag'        => 'required',
                        'bankName'          => 'required',
                        'accountNumber'     => 'required',
                        'accountName'       => 'required'
                    ]);

                    if ($validator1->fails()) {
                        return response()->json(['success' => false, 'message' => 'Bank information is required.']);
                    }

                    // Save or update bank information
                    $user = auth()->user();
                    $bankInfo = DB::table('bankinfo')
                        ->where('userid', $user->id)
                        ->where('accountTag', $request->accountTag)
                        ->first();

                    if ($bankInfo) {
                        // Update existing record
                        DB::table('bankinfo')
                            ->where('userid', $user->id)
                            ->where('accountTag', $request->accountTag)
                            ->update([
                                'bankName' => $request->bankName,
                                'accountNumber' => $request->accountNumber,
                                'accountHolder' => $request->accountName
                            ]);
                    } else {
                        $maxIndex = DB::table('bankinfo')
                            ->where('userid', $user->id)
                            ->max('index');

                        $newIndex = is_null($maxIndex) ? 0 : $maxIndex + 1;

                        // Insert new record
                        DB::table('bankinfo')->insert([
                            'userid' => $user->id,
                            'accountTag' => $request->accountTag,
                            'bankName' => $request->bankName,
                            'accountNumber' => $request->accountNumber,
                            'accountHolder' => $request->accountName,
                            'index' => $newIndex
                        ]);
                    }
                }

                if ($request->hasFile("payQRImage")) {
                    $validator2 = Validator::make($request->all(), [
                        'payTag'        => 'required',
                    ]);

                    if ($validator2->fails()) {
                        return response()->json(['success' => false, 'message' => 'Pay tag is required.']);
                    }

                    $image = upload_file($validated['payQRImage'], 'QRImage');
                    $upload_image = upload_files_from_path_dynamic([$image['dev_path']], 'QRImage');
                    delete_file($image['dev_path']);
                    $validated['payQRUrl'] = $upload_image;

                    $maxIndex = DB::table('payqrinfo')
                        ->where('userid', $user->id)
                        ->max('index');

                    $newIndex = is_null($maxIndex) ? 0 : $maxIndex + 1;

                    $updateData = [
                        "userid"            => $user->id,
                        "index"             => $newIndex,
                        "payTag"            => $request->payTag,
                        "payQR"             => $upload_image,
                    ];

                    DB::table('payqrinfo')->updateOrInsert(["userid" => $user->id, "index" => $newIndex], $updateData);
                }

                if ($validated['priceType'] == 'MarketPrice') {
                    $validator3 = Validator::make($request->all(), [
                        // 'offerMargin'       => 'required|gt:4'
                        'offerMargin'       => 'required'
                    ]);

                    if ($validator3->fails()) {
                        return response()->json(['success' => false, 'message' => 'Offer margin should be over 5%.']);
                    }
                } else {
                    $validator4 = Validator::make($request->all(), [
                        'fixedPrice'        => 'required|gt:0'
                    ]);

                    if ($validator4->fails()) {
                        return response()->json(['success' => false, 'message' => 'Fixed price is incorrect.']);
                    }
                }
            }

            // if ($request->hasFile("payQRImage")) {
            //     Log::info('originalName ' . $request->file('payQRImage')->getClientOriginalName());
            //     // Log::info('payQRImage ' . $validated['payQRImage']);
            //     // Log::info('payQRUrl ' . $validated['payQRUrl']);
            // }

            $tableName = 'trade_offers';

            $insertData = [
                'order_type'        => $validated['action'],
                'user_id'           => Auth::id(),
                'priceType'         => $validated['priceType'] == "MarketPrice" ? 0 : 1,
                'tradeVolMin'       => $validated['tradeVolMin'],
                'tradeVolMax'       => $validated['tradeVolMax'],
                'offerTimeLimit'    => $validated['offerTimeLimit'],
                'offerMargin'       => 0,
                'fixedPrice'        => $validated['priceType'] == "MarketPrice" ? 0 : $validated['fixedPrice'],
                'password'          => $validated['password'],
            ];

            if (array_key_exists('bankName', $requestData)) {
                $insertData['bankName'] = $requestData['bankName'];
            }
            if (array_key_exists('accountNumber', $requestData)) {
                $insertData['accountNumber'] = $requestData['accountNumber'];
            }
            if (array_key_exists('accountName', $requestData)) {
                $insertData['accountName'] = $requestData['accountName'];
            }
            if (array_key_exists('payQRUrl', $validated)) {
                $insertData['payQR'] = $validated['payQRUrl'];
            }
            if (array_key_exists('offerMargin', $requestData)) {
                $insertData['offerMargin'] = $requestData['offerMargin'];
            }
            if (array_key_exists('fixedPrice', $requestData)) {
                $insertData['fixedPrice'] = $requestData['fixedPrice'];
            }
            if (array_key_exists('offerTag', $requestData)) {
                $insertData['offerTag'] = $requestData['offerTag'];
            }
            if (array_key_exists('offerLabel', $requestData)) {
                $insertData['offerLabel'] = $requestData['offerLabel'];
            }
            if (array_key_exists('offerCondition', $requestData)) {
                $insertData['offerCondition'] = $requestData['offerCondition'];
            }
            if (array_key_exists('checkboxMobileAuth', $requestData)) {
                $insertData['needMobileAuth'] = true;
            } else {
                $insertData['needMobileAuth'] = false;
            }
            if (array_key_exists('checkboxKYCAuth', $requestData)) {
                $insertData['needKYCAuth'] = true;
            } else {
                $insertData['needKYCAuth'] = false;
            }
            if (array_key_exists('checkboxAccountAuth', $requestData)) {
                $insertData['needAccountAuth'] = true;
            } else {
                $insertData['needAccountAuth'] = false;
            }

            if ($validated['priceType'] != 'MarketPrice') {
                $insertData['offerMargin'] = 0;
            }

            if ($insertData['offerMargin'] == null) {
                $insertData['offerMargin'] = 0;
            }
            if ($insertData['bankName'] == null) {
                $insertData['bankName'] = "";
            }
            if ($insertData['accountNumber'] == null) {
                $insertData['accountNumber'] = "";
            }
            if ($insertData['accountName'] == null) {
                $insertData['accountName'] = "";
            }

            $order_id = DB::table($tableName)->insertGetId($insertData);
            $action = $validated['action'];

            // 가격 정보 가져오기
            $prices = WalletController::getPrices();

            // JSON 응답 데이터 준비 
            $responseData = [
                'success'=> true,
                'message' => 'Your offer registered successfully!',
                'data' => [
                     'item'     => $insertData,
                     'prices'   => $prices,
                     'action'   => $action,
                     'order_id' => urlSafeEncrypt($order_id)
                ]    
            ];

            Log::info('거래만들기 응답 ');
            Log::info(json_encode($responseData));

            return response()->json($responseData);
        } catch (Exception $e) {
            Log::info("user.make-offer.submit exception:" . $e);
            return response()->json(['success' => false, 'message' => 'Something went wrong. Please try again.']);
        }
    }

    /**
     * 거래 제안 내역 수정폼 
     */
    public function moveUpdateForm($id){
       
        $user = auth()->user();
      
        $usdtBalance1 = 0;
        if ($user->usdtBalance != null) {
            $usdtBalance1 = $user->usdtBalance;
        }

        $usdtPending = 0;
        if ($user->usdtPending != null) {
            $usdtPending = $user->usdtPending;
        }
        
        $page_title ='updateMakeOffForm';
        // 현재 USDT 사용 가능량 
        $availableUsdt = $user->usdtBalance - $user->usdtPending;
        // 시장가
        $prices = WalletController::getPrices();
        $tradeOfferId = $id;
        // 거래 정보
        $tradeInfo = TradeOffer::with('user')->findOrFail(urlSafeDecrypt($id));
        return view('user.make-offer.updateForm',compact("page_title","prices","tradeInfo","tradeOfferId","availableUsdt"));
    } ///updateForm

    /**
     * 거래 내용 업데이트 
     */
    public function updateMakeOffer(Request $request){
        
        $validator = Validator::make($request->all(), [
            'tradeVolMin'       => 'required:numeric',
            'tradeVolMax'       => 'required:numeric',
            'offerTimeLimit'    => 'required',
            'secretTrade'       => 'nullable',
            'password'          => 'nullable',
            'offerLabel'        => 'required|string|min:2|max:100',
            'offerCondition'    => 'required|string|min:2|max:500',
            'fixedPrice'        => 'required:numeric',
            'priceType'         => 'required|int',
            'orderType'         => 'required|string',
            'offerMargin'       => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()->all()]);
        }

        $user = auth()->user();
        
        $validatedData = $validator->validated();

        // 현재 유저 USDT 사용 가능량 
        $availableUsdt = $user->usdtBalance - $user->usdtPending;
        // 현재시세
        $prices = WalletController::getPrices();

        // 판매시에 가지고 있는것보다 판매량이 클때 오류 
        if($validatedData['orderType'] === 'sell'){
            if($validatedData['priceType'] == 1){ // 고정가격시에
                if ( $validatedData['tradeVolMax'] > ($availableUsdt * $validatedData['fixedPrice'] ) ) {
                    return -1;
                }
            } else { // 시장가격시에
                if ( $validatedData['tradeVolMax'] > ( ($availableUsdt * ($prices['KRW'] * (100 + $validatedData['offerMargin']))) / 100 ) ) {
                    return -1;
                }
            }
        }
        
        try{
            TradeOffer::where('id',urlSafeDecrypt($request->tradeOfferId))->update([
                'priceType'         => $request->priceType,
                'tradeVolMax'       => $request->tradeVolMax,
                'tradeVolMin'       => $request->tradeVolMin,
                'fixedPrice'        => $request->fixedPrice,
                'offerMargin'       => ($request->priceType == 1)? 0 : $request->offerMargin,
                'offerTimeLimit'    => $request->offerTimeLimit,
                'password'          => ($request->secretTrade === 'on')? ($request->password)??null : null,
                'needKYCAuth'       => ($request->checkboxKYCAuth === 'on')? 1 : 0,
                'offerLabel'        => $request->offerLabel,
                'offerCondition'    => $request->offerCondition,
            ]);
            $result = 1;
            
        } catch(\Exception $error) {
            Log::info('error:'.$error);
            $result = 0;
        }
        
        return $result;
    }
}
