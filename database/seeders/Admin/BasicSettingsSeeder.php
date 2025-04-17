<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\BasicSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasicSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'site_name'       => "Anaki",
            'site_title'      => "P2P Trading Platform",
            'base_color'      => "#007373",
            'secondary_color' => "#ea5455",
            'otp_exp_seconds' => 3600,
            'timezone'        => "Asia/Seoul",
            'site_logo_dark'  => "15ff5863-df2e-4afb-95bf-a8fcd2c76462.webp",
            'site_logo'       => "40a6dc16-fac9-4949-a084-766fd810ce0f.webp",
            'site_fav_dark'   => "955ed48c-c2aa-424b-9eb1-6fca8d0f8035.webp",
            'site_fav'        => "5239cbc0-d1cf-4aae-874f-e224f4009b90.webp",
            'user_registration'  => 1,
            'secure_password'    => 0,
            'agree_policy'       => 0,
            'force_ssl'          => 0,
            'email_verification' => 1,
            'sms_verification'   => 1,
            'email_notification' => 1,
            'push_notification'  => 1,
            'kyc_verification'   => 1,
            'web_version'        => '2.0.0',
            'mail_config'       => json_encode([
                "method" => "smtp",
                "host" => "smtp.naver.com",
                "port" => "587",
                "encryption" => "ssl",
                "username" => "planet6897@naver.com",
                "password" => "#Apple1060#",
                "from" => "planet6897@naver.com",
                "app_name" => "Anaki",
            ]),
            'broadcast_config'  => json_encode([
                "method" => "pusher",
                "app_id" => "1839678",
                "primary_key" => "3ed6bccb71d91c512427",
                "secret_key" => "60d446a7e2b0cd129a79",
                "cluster" => "ap3"
            ]),
            'push_notification_config'  => json_encode([
                "method" => "pusher",
                "instance_id" => "1a7ce924-b726-42a6-abaa-d9c3b20d475e",
                "primary_key" => "4C383D8F2308D19C8BF25EBE9B647346DCA24E51E65F4902083297052F326BAD"
            ]),
        ];

        BasicSettings::updateOrCreate(['id' => 1], $data);
    }
}
