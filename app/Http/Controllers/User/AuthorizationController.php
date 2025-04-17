<?php

namespace App\Http\Controllers\User;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\UserKycData;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Admin\SetupKyc;
use Illuminate\Support\Carbon;
use App\Models\UserAuthorization;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Validation\ValidationException;
use App\Notifications\User\Auth\SendAuthorizationCode;

class AuthorizationController extends Controller
{
    use ControlDynamicInputFields;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMailFrom($token)
    {
        $user_authorize = UserAuthorization::where("token", $token)->first();
        $resend_time = 0;
        if (Carbon::now() <= $user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            $resend_time = Carbon::now()->diffInSeconds($user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE));
        }

        $page_title = setPageTitle("Mail Authorization");

        $user_email = $user_authorize->user->email ?? '';

        return view('user.auth.authorize.verify-mail', compact("page_title", "token", "resend_time", "user_email"));
    }

    /**
     * Verify authorizaation code.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mailVerify(Request $request, $token)
    {
        $request->merge(['token' => $token]);
        $request->validate([
            'token'     => "required|string|exists:user_authorizations,token",
            'code*'      => "required|integer",
        ]);

        $code = implode($request->code);


        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = UserAuthorization::where("token", $request->token)->where("code", $code)->first();

        if (!$auth_column) {
            return redirect()->back()->with(['error' => ['Invalid OTP code.']]);
        }

        if ($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $this->authLogout($request);
            return redirect()->route('user.login')->with(['error' => ['Session expired. Please try again.']]);
        }
        try {
            $auth_column->user->update([
                'email_verified'    => true,
            ]);
            $auth_column->delete();
        } catch (Exception $e) {
            $this->authLogout($request);
            return redirect()->route('user.login')->with(['error' => [__('Something went wrong. Please try again.')]]);
        }

        // 세션에서 저장된 intended URL을 가져옵니다
        $intendedUrl = session('url.intended');

        // intended URL이 있으면 해당 URL로, 없으면 기본 대시보드로 리다이렉트
        if ($intendedUrl) {
            // intended URL을 사용한 후에는 세션에서 삭제합니다
            session()->forget('url.intended');

            return redirect()->to($intendedUrl);
        }

        return redirect()->intended(route("user.dashboard"))->with(['success' => ['Email verified successfully.']]);
    }

    public function authLogout(Request $request)
    {
        auth()->guard("web")->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function mailResendToken($token)
    {
        $user_authorize = UserAuthorization::where("token", $token)->first();
        $resend_code = generate_random_code();
        try {
            if ($user_authorize !== null) {
                $user_authorize->update([
                    'code'          => $resend_code,
                    'created_at'    => now(),
                ]);
                $data = $user_authorize->toArray();
                $user_authorize->user->notify(new SendAuthorizationCode((object) $data));
            }
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'code'      => 'Something went wrong. Please try again.',
            ]);
        }
        return redirect()->route('user.authorize.mail', $token)->with(['success' => ['Mail OTP resent successfully.']]);
    }

    public function showKycFrom()
    {   
        $basic_settings   = BasicSettings::first();
        if ($basic_settings['kyc_verification'] == false) return back()->with(['success' => ['Identity verification not required.']]);

        $user = auth()->user();
        $page_title = __("KYC Verification");
        $user_kyc = SetupKyc::userKyc()->first();

        if (!$user_kyc) return back()->with(['success' => ['Identity verification not required.']]);
        $kyc_data = $user_kyc->fields;
        $kyc_fields = [];
        if ($kyc_data) {
            $kyc_fields = array_reverse($kyc_data);
        }

        $userId = $user->id;

        return view('user.sections.kyc.verify-kyc', compact("page_title", "kyc_fields", "user_kyc", "userId"));
    }

    public function kycSubmit(Request $request)
    {
        \Log::info('kycSubmit info:' .$request->all());

        $user = auth()->user();
        if ($user->kyc_verified == GlobalConst::VERIFIED) return back()->with(['success' => ['You are already a KYC verified user.']]);

        DB::beginTransaction();
        try {
            $user->update([
                'kyc_verified'  => GlobalConst::SUCCESS,
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $user->update([
                'kyc_verified'  => GlobalConst::DEFAULT,
            ]);
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return redirect()->route('user.authorize.kyc')->with(['success' => ['KYC information submitted successfully.']]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showSMSFrom($token)
    {
        $user_authorize = UserAuthorization::where("token", $token)->first();
        $resend_time = 0;
        if (Carbon::now() <= $user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            $resend_time = Carbon::now()->diffInSeconds($user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE));
        }
        $page_title = setPageTitle("SMS Authorization");
        return view('user.auth.authorize.verify-sms', compact("page_title", "token", "resend_time"));
    }

    public function smsResendToken($token)
    {
        $user_authorize = UserAuthorization::where("token", $token)->first();
        $resend_code = generate_random_code();
        try {
            $user_authorize->update([
                'code'          => $resend_code,
                'created_at'    => now(),
            ]);

            $data = $user_authorize->toArray();
            $message = 'Your ' . env('APP_NAME') . ' verification code is' . $data['code'];

            $basic_settings = BasicSettingsProvider::get();
            sendTwilioMessage($message, Auth::user()->full_mobile, $basic_settings);
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'code'      => 'Something went wrong. Please try again.',
            ]);
        }
        return redirect()->route('user.authorize.sms', $token)->with(['success' => ['SMS OTP resent successfully.']]);
    }

    /**
     * Verify authorizaation code.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function smsVerify(Request $request, $token)
    {
        $request->merge(['token' => $token]);
        $request->validate([
            'token'     => "required|string|exists:user_authorizations,token",
            'code*'      => "required|integer",
        ]);

        $code = implode($request->code);

        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = UserAuthorization::where("token", $request->token)->where("code", $code)->first();
        if ($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $this->authLogout($request);
            return redirect()->route('user.login')->with(['error' => ['Session expired. Please try again.']]);
        }
        try {
            $auth_column->user->update([
                'sms_verified'    => true,
            ]);
            $auth_column->delete();
        } catch (Exception $e) {
            $this->authLogout($request);
            return redirect()->route('user.login')->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return redirect()->intended(route("user.dashboard"))->with(['success' => ['Account verified successfully.']]);
    }

    /**
     * Googel 2FA Form
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function showGoogle2FAForm()
    {
        $page_title =  "Authorize Google Two Factor";
        return view('user.auth.authorize.verify-g2fa', compact('page_title'));
    }

    public function google2FASubmit(Request $request)
    {
        $request->validate([
            'code*'    => "required|numeric",
        ]);
        $code = implode($request->code);;
        $user = auth()->user();
        if (!$user->two_factor_secret) {
            return back()->with(['warning' => ['Your secret key not stored properly. Please contact with system administrator']]);
        }
        if (google_2fa_verify($user->two_factor_secret, $code)) {
            $user->update([
                'two_factor_verified'   => true,
            ]);

            // 세션에서 저장된 intended URL을 가져옵니다
            $intendedUrl = session('url.intended');

            // intended URL이 있으면 해당 URL로, 없으면 기본 대시보드로 리다이렉트
            if ($intendedUrl) {
                // intended URL을 사용한 후에는 세션에서 삭제합니다
                session()->forget('url.intended');

                return redirect()->to($intendedUrl);
            }

            return redirect()->intended(route('user.dashboard'));
        }
        return back()->with(['warning' => ['Failed to login. Please try again']]);
    }
}
