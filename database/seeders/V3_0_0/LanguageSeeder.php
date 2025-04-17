<?php

namespace Database\Seeders\V3_0_0;

use App\Models\Admin\Language;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = array(
            array('id' => '3','name' => 'Arabic','code' => 'ar','status' => '0','last_edit_by' => '1','created_at' => '2023-11-23 08:50:34','updated_at' => '2023-11-23 08:54:01','dir' => 'rtl')
        );

        Language::insert($languages);
    }
}
