<?php

namespace Database\Seeders\Demo;

use App\Models\Admin\BasicSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasicSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return voidp
     */
    public function run()
    {
        $data = [
            'site_name'       => "Anaki",
            'site_title'      => "P2P Trading Platform",
            'base_color'      => "#5fffca",
            'secondary_color' => "#ea5455",
            'otp_exp_seconds' => "3600",
            'timezone'        => "Asia/Dhaka",
            'site_logo_dark'  => "3b4270ee-2a91-4ceb-9796-1a89658d7262.webp",
            'site_logo'       => "558b36f9-3e5b-4768-88f1-c7bb52b7f3f6.webp",
            'site_fav_dark'   => "9a30b551-1ffe-4b6f-90f2-a89c7df1c592.webp",
            'site_fav'        => "c1cc7cb5-0057-4aa4-bf10-575a898cffb3.webp",
            'user_registration'  => 1,
            'email_verification' => 1,
            'kyc_verification' => 1,
            'email_notification' => 1,
            'agree_policy'       => 1,
            'web_version'        => '2.0.0',
            'mail_config'       => [
                "method" => "smtp",
                "host" => "appdevs.net",
                "port" => "465",
                "encryption" => "ssl",
                "password" => "QP2fsLk?80Ac",
                "username" => "system@appdevs.net",
                "from" => "system@appdevs.net",
                "app_name" => "Anaki",
            ],
            'broadcast_config'  => [
                "method"      => "pusher",
                "app_id"      => "1539602",
                "primary_key" => "39079c30de823f783dbe",
                "secret_key"  => "78b81e5e7e0357aee3df",
                "cluster"     => "ap2"
            ],
            'push_notification_config'  => [
                "method" => "pusher",
                "instance_id" => "809313fc-1f5c-4d0b-90bc-1c6751b83bbd",
                "primary_key" => "58C901DC107584D2F1B78E6077889F1C591E2BC39E9F5C00B4362EC9C642F03F"
            ],
        ];

        BasicSettings::firstOrCreate($data);
    }
}
