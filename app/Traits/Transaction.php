<?php

namespace App\Traits;

use Exception;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Carbon;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Constants\NotificationConst;
use Illuminate\Support\Facades\Auth;

trait Transaction
{
    public function createTransactionChildRecords(int $transaction_id, $output)
    {
        $this->createTransactionChargeRecord($transaction_id, $output);
        $this->createTransactionDeviceRecord($transaction_id);
        $this->notification($output);
    }

    public function createTransactionChargeRecord(int $transaction_id, $output)
    {
        DB::beginTransaction();
        try {
            DB::table('transaction_charges')->insert([
                'transaction_id'    => $transaction_id,
                'percent_charge'    => $output['amount']->percent_charge,
                'fixed_charge'      => $output['amount']->fixed_charge,
                'total_charge'      => $output['amount']->total_charge,
                'created_at'        => now(),
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }

    public function createTransactionDeviceRecord(int $transaction_id)
    {
        $client_ip = request()->ip() ?? false;
        $location = geoip()->getLocation($client_ip);
        $agent = new Agent();
        $mac = "";

        DB::beginTransaction();
        try {
            DB::table("transaction_devices")->insert([
                'transaction_id' => $transaction_id,
                'ip'            => $client_ip,
                'mac'           => $mac,
                'city'          => $location['city'] ?? "",
                'country'       => $location['country'] ?? "",
                'longitude'     => $location['lon'] ?? "",
                'latitude'      => $location['lat'] ?? "",
                'timezone'      => $location['timezone'] ?? "",
                'browser'       => $agent->browser() ?? "",
                'os'            => $agent->platform() ?? "",
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
    }


    public function notification($output)
    {
        $notification_content = [
            'title'   => __('Add Money'),
            'message' => __(':amount :currency has been added to your :wallet_currency wallet', [
                'amount'          => $output['amount']->requested_amount,
                'currency'        => $output['wallet']->currency->code,
                'wallet_currency' => $output['wallet']->currency->code
            ]),
            'time'    => Carbon::now()->diffForHumans(),
            'image'   => files_asset_path('profile-default'),
        ];
        UserNotification::create([
            'type'      => NotificationConst::BALANCE_ADDED,
            'user_id'  =>  Auth::guard(get_auth_guard())->user()->id,
            'message'   => $notification_content,
        ]);
    }
}
