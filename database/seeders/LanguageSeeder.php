<?php

namespace Database\Seeders;

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
            array('id' => '1','name' => 'English','code' => 'en','status' => '1','last_edit_by' => '1','created_at' => NULL,'updated_at' => '2024-01-16 20:55:18','dir' => 'ltr'),
            array('id' => '2','name' => '한국어','code' => 'ko','status' => '0','last_edit_by' => '1','created_at' => NULL,'updated_at' => '2024-01-16 20:55:17','dir' => 'ltr')
        );

        foreach ($languages as $language) {
            Language::updateOrCreate(['id' => $language['id']], $language);
        }
    }
}
