<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Models\UserAuthorization;
use App\Traits\User\LoggedInUsers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\User\RegisteredUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Http\Resources\User\UserResouce;
use App\Providers\Admin\BasicSettingsProvider;
use App\Notifications\User\Auth\SendAuthorizationCode;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use  App\Http\Controllers\User\Auth\LoginController;

class AuthController extends Controller
{
    use LoggedInUsers, RegisteredUsers;
    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    /**
     * Mehtod for user login
     * @method POST
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Request  Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|max:40',
            'password' => 'required|min:6',
        ]);
        
        if ($validator->fails()) {
            $error = ['error' => $validator->errors()->all()];
            return ApiResponse::onlyValidation($error);
        }

        $user = User::where('username', trim(strtolower($request->email)))->orWhere('email', $request->email)->first();

        if (!$user) {
            $error = ['error' => ['The credentials does not match']];
            return ApiResponse::onlyValidation($error);
        }

        if($user->status == 0){
            $message = ['error' => ['This Account Has Been Blocked']];
            return redirect()->route('user.login')->with($message);
        }

        $user->two_factor_verified = false;
        $user->save();

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;

        $user_data = [
            'token'         => $token,
            'image_path'    => get_files_public_path('user-profile'),
            'default_image' => get_files_public_path('default'),
            "base_ur"       => url('/'),
            'user'          => new UserResouce($user)
        ];

        if (Hash::check($request->password, $user->password)) {
            if ($user->status == 0) {
                $error = ['error' => ['Account Has been Suspended']];
                return ApiResponse::onlyValidation($error);
            } elseif ($user->email_verified == 0) {
                $user_authorize = UserAuthorization::where("user_id", $user->id)->first();
                $resend_code = generate_random_code();
                $user_authorize->update([
                    'code'          => $resend_code,
                    'created_at'    => now(),
                ]);
                $data = $user_authorize->toArray();
                $user->notify(new SendAuthorizationCode((object) $data));
                $message = ['success' => ['Please check email and verify your account']];
                return ApiResponse::success($message, $user_data);
            } else if ($user->sms_verified == 0) {
                $user_authorize = UserAuthorization::where("user_id", $user->id)->first();
                $resend_code = generate_random_code();
                $user_authorize->update([
                    'code'          => $resend_code,
                    'created_at'    => now(),
                ]);

                $basic_settings = BasicSettingsProvider::get();

                $message = 'Your ' . env('APP_NAME') . ' verification code is' . $resend_code;

                if ($basic_settings->sms_config->name == 'twilio') {
                    sendTwilioMessage($message, $user->full_mobile, $basic_settings);
                }

                $message = ['success' => ['Please check your mobile and verify your account']];
                return ApiResponse::success($message, $user_data);
            }

            // $this->refreshUserWallets($user);
            $this->createLoginLog($user);

            $message = ['success' => ['Login successful.']];
            return ApiResponse::success($message, $user_data);
        } else {
            $error = ['error' => ['The credentials does not match']];
            return ApiResponse::onlyError($error);
        }
    }

    /**
     * Mehtod for user register
     * @method POST
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Request  Response
     */

    public function register(Request $request)
    {
        $basic_settings = $this->basic_settings;
        $passowrd_rule = "required|string|min:6";

        if ($basic_settings->secure_password) {
            $passowrd_rule = ["required", Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()];
        }

        $agree_policy = $this->basic_settings->agree_policy == 1 ? 'required|in:on' : 'nullable';

        $validator = Validator::make($request->all(), [
            'first_name'   => 'required|string|max:50',
            'last_name'    => 'required|string|max:50',
            'email'        => 'required|email|max:160|unique:users',
            'password'     => $passowrd_rule,
            'policy'       => $agree_policy,
        ]);

        if ($validator->fails()) {
            $error =  ['error' => $validator->errors()->all()];
            return ApiResponse::onlyValidation($error);
        }

        $validated = $validator->validated();
        $basic_settings             = $this->basic_settings;
        //User Create

        $validated = Arr::except($validated, ['agree']);

        $validated['firstname']      = $validated['first_name'];
        $validated['lastname']       = $validated['last_name'];
        $validated['email_verified'] = ($basic_settings->email_verification == true) ? 0 : 1;
        $validated['kyc_verified']   = ($basic_settings->kyc_verification == true) ? 0 : 1;
        $validated['sms_verified']   = ($basic_settings->sms_verification == true) ? 0 : 1;
        $validated['status']         = 1;
        $validated['password']       = Hash::make($validated['password']);
        $validated['username']       = generateUsername($validated['first_name'], $validated['last_name']);

        $user = User::create($validated);

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        // $this->createUserWallets($user);

        if ($basic_settings->email_verification == true) {
            $data = [
                'user_id'       => $user->id,
                'code'          => generate_random_code(),
                'token'         => generate_unique_string("user_authorizations", "token", 200),
                'created_at'    => now(),
            ];
            DB::beginTransaction();
            try {
                UserAuthorization::where("user_id", $user->id)->delete();
                DB::table("user_authorizations")->insert($data);
                $user->notify(new SendAuthorizationCode((object) $data));
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                $error = ['error' => ['Something went wrong! Please try again']];
                return ApiResponse::error($error);
            }
        }

        if ($basic_settings->email_verification == 1) {
            $message =  ['success' => ['Please check email and verify your account']];
        } else {
            $message =  ['success' => ['Registration successful']];
        }

        $data = [
            'token' => $token,
            'image_path' => get_files_public_path('user-profile'),
            'default_image' => get_files_public_path('default'),
            "base_ur"       => url('/'),
            'user' => new UserResouce($user)
        ];

        return ApiResponse::success($message, $data);
    }

    // 구글 로그인 버튼
    public function redirectToProvider($provider,  Request $request)
    {
        $intendedUrl = $request->input('intended');
        if ($intendedUrl && filter_var(urldecode($intendedUrl), FILTER_VALIDATE_URL)) {
            session(['oauth_intended_url' => $intendedUrl]);
        }
        
    
        return Socialite::driver($provider)
            ->redirectUrl(config("services.{$provider}.redirect"))
            ->redirect();
    }

    // 구글 로그인 콜백
    public function handleProviderCallback($provider, Request $request)
    {   
        try {
            $socialUser = Socialite::driver($provider)->user();
            // \Log::Info(json_encode($socialUser->getRaw()));
        } catch (\Exception $e) {
            $error = ['error' => ['OAuth 로그인 중 오류가 발생했습니다.']];
            return ApiResponse::onlyError($error);
        }

        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // 이름 분리
            $fullName = $socialUser->getName() ?? '';
            $nameParts = explode(' ', $fullName);
            $firstName = $nameParts[0] ?? '';
            $lastName = end($nameParts) ?? '';
            if (count($nameParts) > 1) {
                array_shift($nameParts);
                array_pop($nameParts);
                $middleName = implode(' ', $nameParts);
                $firstName .= ' ' . $middleName;
            }
            
            // 사용자 이름 생성
            $username = $this->generateUniqueUsername($socialUser->getEmail());

            // 새 사용자 생성
            $user = User::create([
                'firstname' => $firstName,
                'lastname' => $lastName,
                'email' => $socialUser->getEmail(),
                'email_verified' => 1,
                'sms_verified' => 0,
                'status' => 1,
                'password' => Hash::make(Str::random(16)),
                'username' => $username,
                'image'    => $socialUser->avatar ?? "",
            ]);

            // $this->createUserWallets($user);
        } else { // 계정이 있다면
            
            if($user->status == 0){
                $message = ['error' => ['This Account Has Been Blocked']];
                return redirect()->route('user.login')->with($message);
            }
            if (!$user->image) {
                $user->image = $socialUser->avatar ?? "";
                $user->save();
            }
        }

        if ($user->walletAddress == null || $user->walletPrivateKey == null) {
            $walletResult = LoginController::CreateWallet($user->email);
            if ($walletResult != null) {
                $user->walletAddress = $walletResult['address'];
                $user->walletPrivateKey = $walletResult['privateKey'];
                $user->usdtBalance = 0;
                $user->usdtPending = 0;
                $user->lastUSDTBalance = 0;
            }
        }

        $user->two_factor_verified = false;
        $user->save();

        $token = $user->createToken('Social Login Grant Client')->accessToken;

        $user_data = [
            'token'         => $token,
            'image_path'    => get_files_public_path('user-profile'),
            'default_image' => get_files_public_path('default'),
            "base_ur"       => url('/'),
            'user'          => new UserResouce($user)
        ];

        if ($user->status == 0) {
            $error = ['error' => ['Account Has been Suspended']];
            return ApiResponse::onlyValidation($error);
        } elseif ($user->sms_verified == 0 && $this->basic_settings->sms_verification) {
            $user_authorize = UserAuthorization::updateOrCreate(
                ["user_id" => $user->id],
                [
                    'code'          => generate_random_code(),
                    'created_at'    => now(),
                ]
            );

            $basic_settings = BasicSettingsProvider::get();

            $message = 'Your ' . env('APP_NAME') . ' verification code is ' . $user_authorize->code;

            if (
                isset($basic_settings) &&
                isset($basic_settings->sms_config) &&
                isset($basic_settings->sms_config->name) &&
                $basic_settings->sms_config->name == 'twilio'
            ) {
                sendTwilioMessage($message, $user->full_mobile, $basic_settings);
                $message = ['success' => ['Please check your mobile and verify your account']];
                return ApiResponse::success($message, $user_data);
            }
        }

        // $this->refreshUserWallets($user);
        $this->createLoginLog($user);

        Auth::login($user, true);

        $intendedUrl = session('oauth_intended_url');

        // intended URL이 있으면 해당 URL로, 없으면 기본 대시보드로 리다이렉트
        if ($intendedUrl) {
            // intended URL을 사용한 후에는 세션에서 삭제합니다
            session()->forget('oauth_intended_url');

            return redirect()->to($intendedUrl);
        }

        return redirect()->intended(route('user.dashboard'))->with(['success' => ['Login successful.']]);

        // $message = ['success' => ['Login Operation successful.ful']];
        // return ApiResponse::success($message, $user_data);
    }

    private function generateUniqueUsername($email)
    {
        // 이메일에서 @ 앞 부분 추출
        $baseUsername = explode('@', $email)[0];
        
        $username = $baseUsername;
        $i = 1;
    
        // 중복 확인 및 고유한 사용자 이름 생성
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $i;
            $i++;
        }
    
        return $username;
    }
}
  