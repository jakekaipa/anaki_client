<?php

namespace App\Traits\PaymentGateway;

use Exception;
use Jenssegers\Agent\Agent;
use App\Models\TemporaryData;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait ManualCrowPayment{


    /**
     * This method for insert forexcrow
     * @return $id;
     */
    public function forexCrowInsert($data){
        try{
            $id = DB::table("forexcrows")->insertGetId([
                'user_id'          => Auth::id(),
                'currency_id'      => $data->currency,
                'amount'           => $data->amount,
                'rate'             => $data->rate,
                'rate_currency_id' => $data->rate_currency,
                'status'           => 1,
                'created_at'       => now(),
            ]);
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $id;
    }


    /**
     * This method for insert record in transaction table
     * @return $id;
     */
    public function insertRecordManual($data,$forexcrow_id = null, $trx_id_prefix = 'EX', $status = 1, $get_values = null) {
        try{
            $trx_id = generateTrxString("transactions","trx_id",$trx_id_prefix,8);
            $id = DB::table("transactions")->insertGetId([
                'user_id'                     => Auth::user()->id,
                'forexcrow_id'                => $forexcrow_id,
                'payment_gateway_currency_id' => $data->payment_gateway_currency_id ?? NULL,
                'user_wallet_id'              => $data->wallet->id,
                'type'                        => $data->transaction_type,
                'trx_id'                      => $trx_id,
                'request_amount'              => $data->amount,
                'payable'                     => $data->subtotal,
                'available_balance'           => $data->wallet->balance - $data->total_amount,
                'remark'                      => ucwords(remove_speacial_char($data->transaction_type)),
                'details'                     => json_encode($get_values),
                'status'                      => $status,
                'created_at'                  => now(),
            ]);
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $id;
    }

    /**
     * This method for insert record in transaction charge table
     * @return $id;
     */
    public function insertChargesManual($data,$id) {
        try{
            DB::table('transaction_charges')->insert([
                'transaction_id'    => $id,
                'percent_charge'    => $data->percent_charge,
                'fixed_charge'      => $data->fixed_charge,
                'total_charge'      => $data->total_charge,
                'created_at'        => now(),
            ]);
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * This method for insert record in transaction device table
     * @return $id;
     */
    public function insertDeviceManual($id) {
        $client_ip = request()->ip() ?? false;
        $location  = geoip()->getLocation($client_ip);
        $agent     = new Agent();
        $mac       = "";
        try{
            DB::table("transaction_devices")->insert([
                'transaction_id'=> $id,
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
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function removeTempDataManual($identifier) {
        if(isset($identifier)){
             TemporaryData::where("identifier",$identifier)->delete();
        }
    }


    public function userWalletUpdate($amount, $wallet){

        $wallet = UserWallet::find($wallet->id);

        $wallet->update([
            'balance' => $wallet->balance - $amount,
        ]);
    }
}
