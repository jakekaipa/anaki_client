<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\Extension;
use Illuminate\Database\Seeder;

class ExtensionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'id'                => 1,
                'name'              => "Tawk",
                'slug'              => "tawk-to",
                'description'       => "Go to your tawk to dashbaord. Click [setting icon] on top bar. Then click [Chat Widget] link from sidebar and follow the screenshot bellow. Copy property ID and paste it in Property ID field. Then copy widget ID and paste it in Widget ID field. Finally click on [Update] button and you are ready to go.",
                'image'             => "logo-tawk-to.png",
                'script'            => null,
                'shortcode'         => json_encode([
                    "property_id" => ["title" => "Property ID", "value" => ""],
                    "widget_id" => ["title" => "Widget ID", "value" => ""]
                ]),
                'support_image'     => "instruction-tawk-to.png",
                'status'            => 0,
                'created_at'        => '2023-12-10 07:31:51',
                'updated_at'        => '2024-01-16 20:08:58'
            ],
        ];

        foreach ($data as $extension) {
            Extension::updateOrCreate(['id' => $extension['id']], $extension);
        }
    }
}
