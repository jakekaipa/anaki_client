<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Log;

use Exception;
use App\Models\User;
use App\Models\BankAccounts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\User\BankAccountRequest;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Models\ReferralPartner;
use Illuminate\Support\Facades\Http;

class ProfileController extends Controller
{
    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Trader Profile";
        $user = auth()->user();
        $bankinfoData = DB::table('bankinfo')->where('userid', $user->id)->select('index', 'accountTag', 'bankName', 'accountNumber', 'accountHolder')->get();
        $payqrData = DB::table('payqrinfo')->where('userid', $user->id)->select('index', 'payTag', 'payQR')->get();
        // 레퍼럴 정보 
        $referralInfo = ReferralPartner::where([['user_id',$user->id],['group_id',0]])->first(); 
        $gatewaies = PaymentGateway::moneyOut()->manual()->get();
        return view('user.sections.profile.index', compact("page_title", "user","referralInfo","gatewaies", "bankinfoData", "payqrData"));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //Log::info("user.profile.update request:".$request);

        $validated = Validator::make($request->all(), [
            'username'     => 'required|string|max:60',
            'first_name'   => 'required|string|max:60',
            'last_name'    => 'required|string|max:60',
            'country'      => 'nullable|string',
            'city'         => 'nullable|string',
            'state'        => 'nullable|string',
            'zip_code'     => 'nullable|string',
            'address'      => 'nullable|string',
            'mobile'        => 'nullable|string|unique:users,mobile,' . auth()->user()->id,
            'image'        => "nullable|image|mimes:jpg,png,jpeg",
        ])->validate();

        // try{
        //     $validated['phone_code'] = get_country_phone_code($validated['country']);
        // }catch(Exception $e) {
        //     return $this->breakAuthentication($e->getMessage());
        // }
        // $validated['mobile_code']   = remove_speacial_char($validated['phone_code']);
        // $complete_phone             = $validated['mobile_code'] . $validated['mobile'];
        // $validated['full_mobile']    = $complete_phone;

        $validated['mobile']         = remove_speacial_char($validated['mobile']);
        $validated['firstname'] = $validated['first_name'];
        $validated['lastname'] = $validated['last_name'];

        $validated['address']       = [
            'country'  => $validated['country'] ?? null,
            'city'     => $validated['city'] ?? null,
            'state'    => $validated['state'] ?? null,
            'address'  => $validated['address'] ?? null,
            'zip_code' => $validated['zip_code'] ?? null,
        ];

        if ($request->hasFile("image")) {
            $image = upload_file($validated['image'], 'user-profile', auth()->user()->image);
            $upload_image = upload_files_from_path_dynamic([$image['dev_path']], 'user-profile');
            delete_file($image['dev_path']);
            $validated['image']     = $upload_image;
        }
        try {
            auth()->user()->update($validated);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Profile updated successfully.']]);
    }

    public function usernameUpdate(Request $request)
    {
        // Log::info("user.profile.usename.update request:" . $request);

        $validated = Validator::make($request->all(), [
            'username'     => 'required|string|max:60',
        ])->validate();

        try {
            auth()->user()->update($validated);
        } catch (Exception $e) {
            Log::info($e);
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Profile updated successfully.']]);
    }

    public function passwordUpdate(Request $request)
    {

        $basic_settings = BasicSettingsProvider::get();
        $passowrd_rule = "required|string|min:6|confirmed";

        if ($basic_settings->secure_password) {
            $passowrd_rule = ["required", Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), "confirmed"];
        }

        $request->validate([
            'current_password'      => "required|string",
            'password'              => $passowrd_rule,
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            throw ValidationException::withMessages([
                'current_password'      => 'Current password didn\'t match',
            ]);
        }

        try {
            auth()->user()->update([
                'password'  => Hash::make($request->password),
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Password updated successfully.']]);
    }

    public function accountUpdate(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'index'                 => 'required',
            'accountTag'            => "required|string",
            'bankName'              => "required|string",
            'accountNumber'         => "required|string",
            'accountName'           => "required|string"
        ])->validate();

        $user = auth()->user();
        $index = $validated['index'];

        $updateData = [
            "userid"            => $user->id,
            "index"             => $index,
            "accountTag"        => $validated['accountTag'],
            "bankName"          => $validated['bankName'],
            "accountNumber"     => $validated['accountNumber'],
            "accountHolder"     => $validated['accountName']
        ];

        DB::table('bankinfo')->updateOrInsert(["userid" => $user->id, "index" => $index], $updateData);

        return back()->with([__('성공') => [__('계좌 정보가 성공적으로 저장 되었습니다.')]]);
    }

    public function accountDelete(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'index' => 'required|integer',
        ])->validate();

        $user = auth()->user();
        $index = $validated['index'];

        $deleted = DB::table('bankinfo')
            ->where('userid', $user->id)
            ->where('index', $index)
            ->delete();

        if ($deleted) {
            return back()->with([__('성공') => [__('계좌 정보가 성공적으로 삭제되었습니다.')]]);
        } else {
            return back()->with([__('실패') => [__('계좌 정보 삭제에 실패했습니다. 다시 시도해주세요.')]]);
        }
    }

    public function qrUpdate(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'index'                 => 'required',
            'PayTag'                => "required|string",
            'payQRImage'            => "required|image|mimes:jpg,png,jpeg",
        ])->validate();

        $user = auth()->user();
        $index = $validated['index'];

        $upload_image = '';

        $requestJson = json_encode($request, JSON_PRETTY_PRINT);

        if ($request->hasFile("payQRImage")) {
            $image = upload_file($validated['payQRImage'], 'QRImage');
            $upload_image = upload_files_from_path_dynamic([$image['dev_path']], 'QRImage');
            delete_file($image['dev_path']);
            $validated['payQRImage']     = $upload_image;

            $filePath = get_files_path('QRImage');

            Log::info("profileController payQRImage:" . $upload_image . " filepath:" . $filePath);
        }

        if ($upload_image === '') {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        $updateData = [
            "userid"            => $user->id,
            "index"             => $index,
            "payTag"            => $validated['PayTag'],
            "payQR"             => $validated['payQRImage'],
        ];

        DB::table('payqrinfo')->updateOrInsert(["userid" => $user->id, "index" => $index], $updateData);

        return back()->with([__('성공') => [__('정보가 성공적으로 저장 되었습니다.')]]);
    }

    public function qrDelete(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'index' => 'required|integer',
        ])->validate();

        $user = auth()->user();
        $index = $validated['index'];

        $deleted = DB::table('payqrinfo')
            ->where('userid', $user->id)
            ->where('index', $index)
            ->delete();

        if ($deleted) {
            return back()->with([__('성공') => [__('계좌 정보가 성공적으로 삭제되었습니다.')]]);
        } else {
            return back()->with([__('실패') => [__('계좌 정보 삭제에 실패했습니다. 다시 시도해주세요.')]]);
        }
    }

    /**
     * KYC 토큰 발급 
     */
    public function getKYCToken(Request $request){
        $url = "https://kyc-api.useb.co.kr/sign-in";

        $response = $this->callAPI("POST", $url, [
            'customer_id' => intval(env('KYC_CUSTOMER_ID')),
            'username' => env('KYC_ID'),
            'password' => env('KYC_KEY'),
        ]);
        return json_decode($response, true);           
    }

    public static function callAPI($method, $url, $data = [], $header = [])
    {
        $headers['Content-Type'] = 'application/json'; 

        return Http::withHeaders($header ?: [])
            ->$method($url, $data ?: [])
            ->body();
    }

    /**
     * KYC인증시 넘겨 받는 데이터 처리
     */
    public function kycInfoUpdate(Request $request)
    {
      
        $data = $request->all();

        // 민감 정보 제외 'original_ocr_data'를 빈 배열로 치환
        if (isset($data['datas']['review_result']['id_card']['original_ocr_data'])) {
            $data['datas']['review_result']['id_card']['original_ocr_data'] = [];
        }

        // 로그에 수정된 데이터 출력
        Log::info("넘겨 받은 KYC 인증 데이터: " . json_encode($data));

        // 인증 실패시 업데이트X 로그 저장용
        if($request->input('result_type') != 1){
            return response()->json([
                'success' => false,
                'message' => __('Your KYC information verification was failed')
            ], 200);  
        }

        $validated = Validator::make($request->all(), [
            'result_type'   => 'required|numeric',
            'realname'      => 'required|string|max:60',
            'mobile'        => 'nullable|string|unique:users,mobile,' . auth()->user()->id,
            'index'         => 'required',
            'accountTag'    => "required|string",
            'bankName'      => "required|string",
            'accountNumber' => "required|string",
            'accountName'   => "required|string",
        ])->validate();
      
        try {
            $dataToUpdate = [
                'kyc_verified' => 1,
                "realname"     => $validated['realname'],
                "mobile"       => $validated['mobile'],
            ];
            auth()->user()->update($dataToUpdate);

            $dataToUpdate = [
                "accountTag"        => $validated['accountTag'],
                "bankName"          => $validated['bankName'],
                "accountNumber"     => $validated['accountNumber'],
                "accountHolder"     => $validated['accountName']
            ];

            DB::table('bankinfo')->updateOrInsert(["userid" => auth()->user()->id, "index" => 0], $dataToUpdate);

            return response()->json([
                'success' => true,
                'message' => __('Your KYC information is verified')
            ], 200);

        } catch (Exception $e) {
            Log::info($e);
            return response()->json([
                'success' => false,
                'message' => 'Something Wrong Please Ask to Anaki Customer Service Team'
            ], 400);
        }

    }
}
