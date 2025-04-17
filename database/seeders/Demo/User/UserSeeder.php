<?php

namespace Database\Seeders\Demo\User;

use App\Models\User;
use App\Models\UserWallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
            array('firstname' => 'App','lastname' => 'Devs','username' => 'appdevs','email' => 'user@appdevs.net','mobile_code' => NULL,'mobile' => '1444444444','full_mobile' => NULL,'password' => '$2y$10$9aZ0muf2GJX5keenkQ18XOZHD2V1PDOjOnG5mjFmiZMsSJUH.GKzy','refferal_user_id' => NULL,'image' => NULL,'status' => '1','address' => '{"country":null,"city":"Dhaka","state":"Mirpur","address":"Mirpur, DOHS","zip_code":"1245"}','email_verified' => '1','sms_verified' => '1','kyc_verified' => '1','ver_code' => NULL,'ver_code_send_at' => NULL,'two_factor_verified' => '0','two_factor_status' => '0','two_factor_secret' => NULL,'device_id' => NULL,'email_verified_at' => NULL,'remember_token' => NULL,'deleted_at' => NULL,'created_at' => '2023-12-07 07:13:15','updated_at' => '2023-12-07 07:16:02'),
            array('firstname' => 'Test','lastname' => 'User','username' => 'testuser','email' => 'user2@appdevs.net','mobile_code' => NULL,'mobile' => NULL,'full_mobile' => NULL,'password' => '$2y$10$aGXwisU89afAGobCaxZOXea/5SO4aSk4RaBos/z3I3uMhdnBWPk9G','refferal_user_id' => NULL,'image' => NULL,'status' => '1','address' => NULL,'email_verified' => '1','sms_verified' => '1','kyc_verified' => '1','ver_code' => NULL,'ver_code_send_at' => NULL,'two_factor_verified' => '0','two_factor_status' => '0','two_factor_secret' => NULL,'device_id' => NULL,'email_verified_at' => NULL,'remember_token' => NULL,'deleted_at' => NULL,'created_at' => '2023-12-07 07:20:56','updated_at' => '2023-12-07 07:20:56')
        );

        User::insert($users);

        $user_wallets = array(
            array('id' => '1','user_id' => '1','currency_id' => '1','balance' => '100000000000','status' => '1','created_at' => '2023-08-19 17:10:44','updated_at' => NULL),
            array('id' => '2','user_id' => '2','currency_id' => '1','balance' => '100000000000','status' => '1','created_at' => '2023-08-19 17:11:14','updated_at' => NULL)
        );

        UserWallet::insert($user_wallets);
    }
}
