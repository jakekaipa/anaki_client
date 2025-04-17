<?php

namespace Database\Seeders\Demo;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetupEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $env_modify_keys = [
            "MAIL_MAILER"       => "smtp",
            "MAIL_HOST"         => "smtp.naver.com",
            "MAIL_PORT"         => "587",
            "MAIL_USERNAME"     => "planet6897@naver.com",
            "MAIL_PASSWORD"     => "abcdefg",
            "MAIL_ENCRYPTION"   => "ssl",
            "MAIL_FROM_ADDRESS" => "planet6897@naver.com",
            "MAIL_FROM_NAME"    => "Anaki",
        ];

        modifyEnv($env_modify_keys);
    }
}
