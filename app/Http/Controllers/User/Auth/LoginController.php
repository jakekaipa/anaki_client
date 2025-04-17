<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\WalletController;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Http\Resources\User\UserResouce;
use App\Models\Admin\Currency;
use App\Models\User;
use App\Models\UserAuthorization;
use App\Models\UserWallet;
use App\Traits\User\LoggedInUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Mongo\Transfer;
use App\Models\Mongo\Wallet;
use Exception;
use Illuminate\Support\Facades\App;



class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    protected $request_data;
    protected $connectInfo;

    use AuthenticatesUsers, LoggedInUsers;

    /**
     * 메인 랜딩으로 이동
     */
    public function moveLanding(Request $request){

        // 관리자 접속 세션 있을경우 제거 
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        if (Auth::guard(get_auth_guard())->check()) {
            // 세션에서 저장된 intended URL을 가져옵니다
            $intendedUrl = session('url.intended');
            Log::info($intendedUrl);
    
            // intended URL이 있으면 해당 URL로, 없으면 기본 대시보드로 리다이렉트
            if ($intendedUrl) {
                // intended URL을 사용한 후에는 세션에서 삭제합니다
                session()->forget('url.intended');
                return redirect()->to($intendedUrl);
            }
    
            // intended URL이 없으면 기본 대시보드로 리다이렉트
            return redirect()->route('user.dashboard');
         }
    
        // 로그인하지 않은 상태라면 랜딩 페이지로 리다이렉트
        $lang = $request->getPreferredLanguage(['en', 'ko']) ?? 'en';
        Session::put('locale', $lang);
    
        return view('landing.landing');
    }

    public function showLoginForm(Request $request)
    {
        
        if (Auth::guard(get_auth_guard())->check()) {

            // 세션에서 저장된 intended URL을 가져옵니다
            $intendedUrl = session('url.intended');
            Log::info($intendedUrl);

            // intended URL이 있으면 해당 URL로, 없으면 기본 대시보드로 리다이렉트
            if ($intendedUrl) {
                // intended URL을 사용한 후에는 세션에서 삭제합니다
                session()->forget('url.intended');
                return redirect()->to($intendedUrl);
            }
            return redirect()->route('user.dashboard');
        }
        
        // $lang = $request->getPreferredLanguage(['en', 'ko']) ?? 'en';
        if($request->selectLang){
            $lang = $request->selectLang;
        } else {
            $lang = 'en';
        }
        $selectLang = $lang;
     
        Session::put('locale', $lang);
        App::setLocale($lang);
        $page_title = "User Login";
        return view('user.auth.login', compact(
            'page_title','selectLang'
        ));

    }


    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        //Log::info("LoginController.validateLogin:".json_encode($request));

        $this->request_data = $request;
        $request->validate([
            'credentials'   => 'required|string',
            'password'      => 'required|string',
        ]);
    }


    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        $request->merge(['status' => true]);
        $request->merge([$this->username() => $request->credentials]);

        $requestData1 = $request->all();
        //Log::info("LoginController.credentials1:".json_encode($request->all()));

        if (array_key_exists('connectInfo', $requestData1)) {
            $this->connectInfo = $requestData1['connectInfo'];
        }

        //Log::info("LoginController.credentials1:".$this->timeZone);
        return $request->only($this->username(), 'password', 'status');
    }


    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        $request = $this->request_data->all();
        $credentials = $request['credentials'];
        // if(filter_var($credentials,FILTER_VALIDATE_EMAIL)) {
        //     return "email";
        // }
        return "email";
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            "credentials" => [trans('auth.failed')],
        ]);
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard("web");
    }

    public static function CreateWallet($userEmail)
    {
        try {
            $result = WalletController::callAPI("POST", "http://anaki_backend:3000/generate-tron-wallet", ["userId" => $userEmail]);

            Log::info("LoginController.CreateWallet result:" . $result);

            $resultData = json_decode($result, true);
            Log::info("LoginController.CreateWallet result.userId:" . $resultData['userId']);

            if ($resultData['userId'] == $userEmail) {
                Log::info("LoginController.CreateWallet address:" . $resultData['address']);
                Log::info("LoginController.CreateWallet privateKey:" . $resultData['privateKey']);

                return $resultData;
            }
        } catch (\Exception $e) {
            Log::info("LoginController.CreateWallet error:" . $e->getMessage());
        }

        return null;
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        //Log::info("LoginController.authenticated:".$this->connectInfo);

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

        $connectInfo = json_decode($this->connectInfo, true);

        $user->two_factor_verified = false;

        $timezone = $connectInfo['timezone'] ?? 'Asia/Seoul';
        $user->lastLoginTimeZone = in_array($timezone, timezone_identifiers_list()) ? $timezone : 'Asia/Seoul';

        $user->lastLoginInfo = $this->connectInfo;
        $user->save();
        $this->refreshUserWallets($user);
        $this->createLoginLog($user);

        // 세션에서 저장된 intended URL을 가져옵니다
        $intendedUrl = session('url.intended');

        // intended URL이 있으면 해당 URL로, 없으면 기본 대시보드로 리다이렉트
        if ($intendedUrl) {
            // intended URL을 사용한 후에는 세션에서 삭제합니다
            session()->forget('url.intended');

            return redirect()->to($intendedUrl);
        }

        return redirect()->intended(route('user.dashboard'))->with(['success' => ['Login successful.']]);
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
            'password' => 'required|min:4',
        ]);

        if ($validator->fails()) {
            $message = ['error' => $validator->errors()->all()];
            return redirect()->route('user.login')->with($message);
        }

        $user = User::where('email', $request->email)->first();
         
        if (!$user) {
            $message = ['error' => ['The credentials does not match']];
            return redirect()->route('user.login')->with($message);
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
                $message = ['error' => ['Account Has been Suspended']];
                return redirect()->route('user.login')->with($message);
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

            $this->refreshUserWallets($user);
            $this->createLoginLog($user);

            Auth::login($user, true);

            return redirect()->intended(route('user.dashboard'))->with(['success' => ['Login successful.']]);
        } else {
            $message = ['error' => ['Oops password does not match.']];
            return redirect()->route('user.login')->with($message);
        }

        $page_title = "User Login";
        return view('user.auth.login', compact(
            'page_title',
        ));
    }

    public function languageChange(Request $request)
    {
        $code = $request->target;
        // $language = Language::where("code", $code);
        // if (!$language->exists()) {
        //     return back()->with(['error' => ['Language not found.']]);
        // }
        Session::put('locale', $code);

        return back()->with(['success' => ['Language switched successfully.']]);
    }
}
