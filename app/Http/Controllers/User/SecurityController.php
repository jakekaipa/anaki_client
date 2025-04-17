<?php

namespace App\Http\Controllers\User;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SecurityController extends Controller
{
    public function google2FA()
    {
        $page_title = __("Two Factor Authenticator");
        $qr_code = generate_google_2fa_auth_qr();
        return view('user.sections.security.google-2fa', compact('page_title', 'qr_code'));
    }

    public function google2FAStatusUpdate(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'target'        => "required|numeric",
        ])->validate();

        $user = auth()->user();
        try {
            $user->update([
                'two_factor_status'         => $user->two_factor_status ? 0 : 1,
                'two_factor_verified'       => true,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }
        return back()->with(['success' => ['Security setting updated successfully.']]);
    }

    public function tetherTransfer2FAUpdate(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'two_factor_tether_transfer' => "required|boolean",
        ])->validate();

        $user = auth()->user();
        try {
            $user->update([
                'two_factor_tether_transfer' => $user->two_factor_tether_transfer ? 0 : 1,
            ]);
        } catch (Exception $e) {
            \Log::info($e);
            return response()->json(['success' => false, 'message' => 'Something went wrong. Please try again.'], 200);
        }
        return response()->json(['success' => true, 'message' => 'Security setting updated successfully.'], 200);
    }
}
