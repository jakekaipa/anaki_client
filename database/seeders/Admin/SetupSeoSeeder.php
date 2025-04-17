<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\SetupSeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetupSeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'slug'         => "Anaki-p2p-trading-platform-full-solution",
            'title'        => 'Anaki - P2P Trading Platform Full Solution',
            'desc'         => "Anaki is the ultimate business software solution for P2P trading. Seamlessly integrating Android and iOS platforms, complemented by an intuitive web interface and efficient admin panels, Anaki equips entrepreneurs with a comprehensive toolkit.",
            'tags'         => ['Anaki',"Trade"],
            'last_edit_by' => 1,
        ];

        SetupSeo::firstOrCreate($data);
    }
}
