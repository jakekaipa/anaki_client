<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
    */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        if($user->email_verified == false){
            return mailVerificationTemplate($user);
        }elseif($user->status == false){
            Auth::logout();
            return redirect()->route('user.login')->with(['error' => ['Your account is inactive.']]);
        }else{
            return $next($request);
        }

        return $next($request);
    }
}
