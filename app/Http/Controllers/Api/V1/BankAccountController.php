<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use App\Models\BankAccounts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\Api\Helpers as ApiResponse;

class BankAccountController extends Controller
{
    public function index(){

        $bank_accounts = BankAccounts::with('paymetGateway')->where('user_id', Auth::guard(get_auth_guard())->user()->id)->get();
        $gatewaies = PaymentGateway::moneyOut()->manual()->get();

        $message = ['success', ['Data fetch successfully']];

        $data = [
            'bank_accounts' => $bank_accounts,
            'gatewaies'     => $gatewaies,
        ];

        return ApiResponse::success($message, $data);

    }

    public function storeOrUpdate(Request $request){

        $validator = Validator::make($request->all(), [
            'bank'           => 'required:integer',
            'account_name'   => 'required:string',
            'account_number' => 'required:string',
            'is_default'     => 'nullable',
            'target'         => 'nullable',
        ]);

        if($validator->fails()){
            $message = ['error', $validator->errors()->all()];
            return ApiResponse::validation($message);
        }

        $validated = $validator->validated();


        try {

            if(isset($validated['is_default'])){
                BankAccounts::where('user_id', Auth::guard(get_auth_guard())->id())->where('is_default', 1)->update(['is_default' => 2]);
            }

            $bank_account = BankAccounts::updateOrCreate(['id' => $validated['target']],[
                'user_id'            => Auth::id(),
                'payment_gateway_id' => $validated['bank'],
                'account_name'       => $validated['account_name'],
                'account_number'     => $validated['account_number'],
                'is_default'         => isset($validated['is_default']) ? 1 : 2,
            ]);

            if(isset($validated['target'])){
                $message =  ['success', ['Bank account udpated successfully!']];
            }else{
                $message =  ['success', ['Bank account created successfully!']];
            }

            $data = [
                'bank_account' => $bank_account
            ];

        } catch (\Exception $th) {
            $message =  ['error',['Someting went wrong, Please try again!']];
            return ApiResponse::error($message, []);
        }

        return ApiResponse::success($message, $data);
    }

    public function bankAccountDelete(Request $request){

        $validator = Validator::make($request->all(), [
            'target' => 'required'
        ]);

        if($validator->fails()){
            $message = ['error', $validator->errors()->all()];
            return ApiResponse::validation($message);
        }

        try{
            $bank_accounts = BankAccounts::find($request->target);

            if(!$bank_accounts){
                $message = ['error', ['Bank account not found!.']];

                return ApiResponse::error($message, []);
            }

            $bank_accounts->delete();

            $message = ['success', ['Bank accounts deleted successfully.']];

        }catch(Exception $e) {
            $message = ['error', ['Something went wrong! Please try again.']];
            return ApiResponse::error($message, []);

        }

        return ApiResponse::onlySuccess($message);
    }
}
