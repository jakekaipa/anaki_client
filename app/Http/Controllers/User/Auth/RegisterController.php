<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin\Currency;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Models\UserWallet;
use App\Traits\User\RegisteredUsers;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use App\Mail\UserEmailVerification;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailVerification;
use App\Http\Controllers\User\WalletController;
use App\Models\ReferralPartner;
use App\Models\RegistUserLogs;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, RegisteredUsers;

    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm($referralCode=null)
    {
        // abort(404);

        // if($agree_policy = $this->basic_settings->user_registration == 0){
        //     abort(404);
        // }
        
        $client_ip = request()->ip() ?? false;
        $user_country = geoip()->getLocation($client_ip)['country'] ?? "";

        $page_title = "User Registration";
        return view('user.auth.register', compact(
            'page_title',
            'user_country',
            'referralCode',
        ));
    }

    /**
     * Handle a registration request for the application.
     * 회원 가입
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $userAgent = $request->header('User-Agent');
            $registLogParam['ip']           = $request->ip();
            $registLogParam['data']         = json_encode($request->all(),true);
            $registLogParam['browser']      = get_browser_name($userAgent);
            $registLogParam['os']           = get_os_name($userAgent);
            $registLogParam['created_at']   = now();
            RegistUserLogs::insert($registLogParam);
             
            // 인증 미 회원 막기
            if(!EmailVerification::where([['email',$request->input('email')],['use_flag','Y']])->first()){
                return back()->with(['error' => ['Check your email verification']]);
            }
           
            $validator  = $this->validator($request->all());
            
            if ($validator->fails()) {
                \Log::info('Validation failed', $validator->errors()->all());
                return back()->withErrors($validator)->withInput();
            }
           
            $validated = $validator->validated();

            if ($validated['password'] !== $validated['password1']) {
                return back()->with(['error' => ['The password and confirmation password do not match.']]);
            }

            // $basic_settings              = $this->basic_settings;

            $validated                   = Arr::except($validated, ['agree']);

            $validated['email_verified'] = 1;
            $validated['sms_verified']   = 0;
            $validated['kyc_verified']   = 0;

            $validated['password']       = Hash::make($validated['password']);
            $validated['password1']       = Hash::make($validated['password1']);
            $validated['username']       = generateUsername($validated['first_name'], $validated['last_name']);
            $validated['firstname']      = $validated['first_name'];
            $validated['lastname']       = $validated['last_name'];
            
            
            event(new Registered($user = $this->create($validated)));
           
             // 레퍼럴 가입시에
             if(isset($request->referralCode)){
                $referralInfo = explode('_',$request->referralCode);
                $agentInfo = ReferralPartner::where('referral_code',$referralInfo[0])->first();
                $insertReferralPartnerParam['user_id']  = $user->id;
                $insertReferralPartnerParam['group_id'] = $agentInfo->id;
                $insertReferralPartnerParam['fee_rate'] = $referralInfo[1] / 10;
                Log::info('레퍼럴 파트너 가입');
                Log::info($insertReferralPartnerParam);
                ReferralPartner::create($insertReferralPartnerParam);
                
            }

            $this->guard()->login($user);

            // 지갑 생성
            try {
                $result = WalletController::callAPI("POST", "http://anaki_backend:3000/generate-tron-wallet", ["userId" => $request->email]);
    
                Log::info("RegistController.CreateWallet result:" . $result);
    
                $resultData = json_decode($result, true);
                Log::info("RegistController.CreateWallet result.userId:" . $resultData['userId']);
    
                if ($resultData['userId'] == $request->email) {
                    // 지갑 주소 업데이트
                    $userInfo = User::where('email',$request->email)->first();
                    $userInfo->walletAddress = $resultData['address'];
                    $userInfo->walletPrivateKey = $resultData['privateKey'];
                    $userInfo->save();
                }
            } catch (\Exception $e) {
                Log::info("RegistController.CreateWallet error:" . $e->getMessage());
            }
            // 리다이렉션
            return $this->registered($request, $user);
        } catch (Exception $e) {
            Log::info($e);
        }
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data)
    {
        // $basic_settings = $this->basic_settings;
        $passowrd_rule = "required|string|min:6";

        // if ($basic_settings->secure_password) {
        //     $passowrd_rule = ["required", Password::min(6)];
        // }

        // return Validator::make($data, [
        //     'first_name'   => 'required|string|max:60',
        //     'last_name'    => 'required|string|max:60',
        //     'email'        => 'required|string|email|max:150|unique:users,email',
        //     'password'     => $passowrd_rule,
        //     'password1'    => $passowrd_rule
        // ]);
       return $validator = Validator::make($data, [
            'first_name'   => 'required|string|max:10',
            'last_name'    => 'required|string|max:10',
            'email'        => 'required|string|email|max:20|unique:users,email',
            'password'     => $passowrd_rule,
            'password1'    => $passowrd_rule,
            "code"         => 'required|string|max:6'
        ], [
            // 커스텀 메시지 배열
            'first_name.required'  => "{{ __('please_enter_first_name') }}",
            'last_name.required'   =>  "{{ __('please_enter_last_name') }}",
            'email.required'       => "{{ __('please_enter_email') }}",
            'email.unique'        => "{{ __('이미 가입된 이메일 입니다') }}",
            'password.required'    => "{{ __('password_requirements') }}"
        ]);
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // dd($data);
        return User::create($data);
    }


    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // $this->createUserWallets($user);
        return redirect()->intended(route('user.dashboard'));
    }

    /**
     * 인증 이메일 전송
     */
    public function sendEmailVerificationCode(Request $request){
        $email = $request->email;
        // 이메일 중복 체크 
        $isExists = User::where('email',$email)->first(); 
        if($isExists)
        {
            return -1;
        }

        $code = $this->makeCode();
        $param = [
                'code'=>$code,
                'use_flag'=>'N'
        ];
        // 인증 코드 DB 저장
        EmailVerification::insert($param);
        // 메일반송 
        Mail::to($email)->send(new UserEmailVerification($code));
        return 1;
    }

    /**
     * 랜덤 코드 발행
     */
    public function makeCode(){
         $min = 10000; // 최소 5자리 수
         $max = 99999; // 최대 5자리 수
         return rand($min, $max);
    }

    /**
     * 이메일 코드 인증
     */
    public function codeVerification(Request $request){
        $code = $request->code;
        $email = $request->email;

        $isExistsCode =  EmailVerification::where('code',$code)->where('use_flag','N')->first();
        if($isExistsCode){ // 코드 존재시에 
            $isExistsCode->use_flag  =  'Y';
            $isExistsCode->updated_at = now();
            $isExistsCode->email = $email;
            $isExistsCode->save();
            $result['message'] = 1;
        } else{ // 코드 미존재 
            $result['message'] =-1;
        }
        return $result;
    }
}
