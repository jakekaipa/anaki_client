<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\TransactionSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transaction_settings = array(
            array('id' => '1','admin_id' => '1','slug' => 'my_excrow','title' => 'Trade Interval Charge and Fees','fixed_charge' => '0.00','percent_charge' => '0.00','min_limit' => '0.00','max_limit' => '50000.00','monthly_limit' => '50000.00','daily_limit' => '5000.00','intervals' => '[{"min_limit":"1","max_limit":"10000","charge":"25","percent":"2"},{"min_limit":"10001","max_limit":"100000","charge":"50","percent":"1"},{"min_limit":"100001","max_limit":"1000000","charge":"100","percent":"1"},{"min_limit":"1000001","max_limit":"10000000","charge":"150","percent":"1"},{"min_limit":"10000001","max_limit":"100000000","charge":"200","percent":"1"},{"min_limit":"100000001","max_limit":"1000000000","charge":"300","percent":"1"}]','status' => '1','created_at' => NULL,'updated_at' => '2023-08-19 16:26:52')
        );

        TransactionSetting::insert(
            $transaction_settings
        );

    }
}
