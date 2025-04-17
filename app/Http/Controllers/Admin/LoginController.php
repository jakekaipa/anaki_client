<?php

namespace App\Http\Controllers\Admin;

use App\Constants\NotificationConst;
use App\Events\Admin\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Admin\AdminLoginLogs;
use App\Models\Admin\AdminNotification;
use App\Models\Admin\Admin;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * 
     * 관리자 로그인 폼
     * @return view
     */
    public function showLoginForm()
    {
        Log::info("Admin LoginController.showLoginForm");
        return view('admin.loginForm');
    }

    /**
     * 관리자 페이지 로그인
     */
    public function login(Request $request){
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $adminInfo = Admin::where('email', $request->email)->first();

        if (!$adminInfo || !Hash::check($request->password, $adminInfo->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        Log::info("Admin Login Success");
        Auth::guard('admin')->login($adminInfo);  // 'admin' guard 사용

        $request->session()->regenerate();

        return redirect()->intended('/admin/dashboard');  
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
        $this->createLoginLog($user);
        $this->updateInfo($user);
        return redirect()->intended(route('admin.dashboard'));
    }


    protected function createLoginLog($admin)
    {

        $client_ip = request()->ip() ?? false;
        $location = geoip()->getLocation($client_ip);

        $agent = new Agent();

        // $mac = exec('getmac');
        // $mac = explode(" ",$mac);
        // $mac = array_shift($mac);
        $mac = "";

        $data = [
            'admin_id'      => $admin->id,
            'ip'            => $client_ip,
            'mac'           => $mac,
            'city'          => $location['city'] ?? "",
            'country'       => $location['country'] ?? "",
            'longitude'     => $location['lon'] ?? "",
            'latitude'      => $location['lat'] ?? "",
            'timezone'      => $location['timezone'] ?? "",
            'browser'       => $agent->browser() ?? "",
            'os'            => $agent->platform() ?? "",
        ];

        try {
            AdminLoginLogs::create($data);
            $notification_message = [
                'title'   => $admin->fullname . "(" . $admin->username . ")" . " logged in.",
                'time'      => Carbon::now()->diffForHumans(),
                'image'     => get_image($admin->image, 'admin-profile'),
            ];
            AdminNotification::create([
                'type'      => NotificationConst::SIDE_NAV,
                'admin_id'  => $admin->id,
                'message'   => $notification_message,
            ]);
            event(new NotificationEvent($notification_message));
        } catch (Exception $e) {
            // return false;
        }
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
            'credential' => [trans('auth.failed')],
        ]);
    }


    protected function updateInfo($admin)
    {
        try {
            $admin->update([
                'last_logged_in'    => now(),
                'login_status'      => true,
            ]);
        } catch (Exception $e) {
            // handle error
        }
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
        return $request->only($this->username(), 'password', 'status');
    }
}
