<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\Admin\AppSettings;
use App\Models\Admin\AppOnboardScreens;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'version'             => '1.0.1',
            'splash_screen_image' => '8645bd5a-a6ce-4f3b-b3d4-585ef316e209.webp',
            'url_title'           => 'App Urls',
            'android_url'         => 'https://play.google.com/store',
            'iso_url'             => 'https://www.apple.com/app-store',
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];


        AppSettings::firstOrCreate($data);

        $onboard =[
            [
              'id'           => 1,
              'title'        => "Effortless Your Path to Seamless Trading",
              'sub_title'    => "Simply sign up, verify your identity, and explore a world of trading opportunities within minutes. Experience trading made easy with it.",
              'image'        => '8f3b88f9-fe9a-43fa-8e1e-8f34cfa95f77.webp',
              'status'       => 1,
              'last_edit_by' => 1,
              'created_at'   => date('Y-m-d H:i:s'),
              'updated_at'   => date('Y-m-d H:i:s'),
            ]
          ];
        AppOnboardScreens::insert($onboard);
    }
}
