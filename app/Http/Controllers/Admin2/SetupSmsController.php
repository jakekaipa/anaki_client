<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Http\Request;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;

class SetupSmsController extends Controller
{
    /**
     * Displpay The SMS Configuration Page
     *
     * @return view
     */
    public function configuration() {
        $page_title = "Email Method";
        $sms_config = BasicSettings::first()->sms_config;
        return view('admin.sections.setup-sms.config',compact(
            'page_title',
            'sms_config',
        ));
    }

    public function update(Request $request){

        $message = [
            'name.required'               => 'The name field is required',
            'name.string'                 => 'The name field have to be string',
            'name.max'                    => 'The name field is too long',
            'twilio_account_sid.required' => 'The account sid field is required',
            'twilio_account_sid.string'   => 'The account sid field have to be string',
            'twilio_account_sid.max'      => 'The account sid field is too long',
            'twilio_auth_token.required'  => 'The auth token field is required',
            'twilio_auth_token.string'    => 'The auth token field have to be string',
            'twilio_auth_token.max'       => 'The auth token field is too long',
            'twilio_from_number.required' => 'The form number field is required',
            'twilio_from_number.string'   => 'The form number field have to be string',
            'twilio_from_number.max'      => 'The form number field is too long',
        ];

        $rules = [
            'name'               => 'required|string|max:100',
            'twilio_account_sid' => 'required|string|max:100',
            'twilio_auth_token'  => 'required|string|max:100',
            'twilio_from_number' => 'required|string|max:100',
        ];

        $validator = Validator::make($request->all(), $rules ,$message);


        if($validator->stopOnFirstFailure()->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $basic_settings = BasicSettings::first();
        if(!$basic_settings) {
            return back()->with(['error' => ['Basic settings not found.']]);
        }

        $data = [
            'name'               => $validated['name'] ?? false,
            'twilio_account_sid' => $validated['twilio_account_sid'] ?? false,
            'twilio_auth_token'  => $validated['twilio_auth_token'] ?? false,
            'twilio_from_number' => $validated['twilio_from_number'] ?? false,
        ];

        try {
            $basic_settings->update([
                'sms_config' => $data,
            ]);
        } catch (\Exception $th) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['SMS configuration updated successfully.']]);

    }

    public function sendTestSMS(Request $request) {
        $validator = Validator::make($request->all(),[
            'phone'         => 'required|string',
        ]);

        $validated = $validator->validate();

        try{
            $basic_settings = BasicSettingsProvider::get();
            sendTwilioMessage('Test SMS From Forexcrow, Just avoid this message!', $validated['phone'], $basic_settings);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['SMS sent successfully.']]);
    }
}
