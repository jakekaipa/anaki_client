<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\BasicSettings;
use App\Notifications\Admin\SendTestMail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class SetupEmailController extends Controller
{

    /**
     * Displpay The Email Configuration Page
     *
     * @return view
     */
    public function configuration() {
        $page_title = "Email Method";
        $email_config = BasicSettings::first()->mail_config;
        return view('admin.sections.setup-email.config',compact(
            'page_title',
            'email_config',
        ));
    }


    /**
     * Display The Email Default Template Page
     *
     * @return view
     */
    public function defaultTemplate() {
        $page_title = "Default Template";
        return view('admin.sections.setup-email.default-template',compact(
            'page_title',
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'method'        => 'required|string|in:smtp,php|max:20',
            'host'          => 'required|string|max:255',
            'port'          => 'required|numeric',
            'encryption'    => 'required|string|in:ssl,tls,auto|max:15',
            'username'      => 'required|string|max:60',
            'password'      => 'required|string|max:60'
        ]);

        $validated = $validator->validate();

        $basic_settings = BasicSettings::first();
        if(!$basic_settings) {
            \Log::info('Basic settings not found in mail configuration update.');
            return back()->with(['error' => ['Basic settings not found.']]);
        }

        // Make object of email template
        $data = [
            'method'            => $validated['method'] ?? '',
            'host'              => $validated['host'] ?? '',
            'port'              => $validated['port'] ?? '',
            'encryption'        => $validated['encryption'] ?? '',
            'username'          => $validated['username'] ?? '',
            'password'          => $validated['password'] ?? '',
            'from'              => $validated['username'] ?? '',
            'app_name'          => $basic_settings['site_name'] ?? env("APP_NAME"),
        ];

        try {
            $basic_settings->update([
                'mail_config'       => $data,
            ]);
        } catch(Exception $e) {
            \Log::info('Error updating basic settings in mail configuration: ' . $e->getMessage());
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        $env_modify_keys = [
            "MAIL_MAILER"       => $data['method'],
            "MAIL_HOST"         => $data['host'],
            "MAIL_PORT"         => $data['port'],
            "MAIL_USERNAME"     => $data['username'],
            "MAIL_PASSWORD"     => $data['password'],
            "MAIL_ENCRYPTION"   => $data['encryption'],
            "MAIL_FROM_ADDRESS" => $data['from'],
            "MAIL_FROM_NAME"    => $data['app_name'],
        ];

        // 배열 값 확인 및 문자열로 변환
        foreach ($env_modify_keys as $key => $value) {
            if (is_array($value)) {
                \Log::warning("Array value detected for key {$key}. Converting to JSON string.");
                $env_modify_keys[$key] = json_encode($value);
            }
        }

        try {
            modifyEnv($env_modify_keys);
        } catch(Exception $e) {
            \Log::error('Error modifying environment variables in mail configuration: ' . $e->getMessage());
            \Log::error('Env modify keys: ' . print_r($env_modify_keys, true));
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        \Log::info('Mail configuration updated successfully.');
        return back()->with(['success' => ['Information updated successfully.']]);
    }


    public function sendTestMail(Request $request) {
        $validator = Validator::make($request->all(),[
            'email'         => 'required|string|email',
        ]);

        $validated = $validator->validate();

        try{
            Notification::route('mail',$validated['email'])->notify(new SendTestMail());
        }catch(Exception $e) {
            Log::info("SetupEmailController.sendTestMail error:".$e->getMessage());
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }
        return back()->with(['success' => ['Email sent successfully.']]);
    }
}
