<?php

namespace Database\Seeders\User;

use App\Models\TransactionCharge;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transaction_charges = array(
            array('id' => '1','transaction_id' => '1','percent_charge' => '30.7692307692310000','fixed_charge' => '25.0000000000000000','total_charge' => '55.7692307692310000','created_at' => '2023-08-19 17:20:13','updated_at' => NULL),
            array('id' => '2','transaction_id' => '2','percent_charge' => '31.5789473684210000','fixed_charge' => '25.0000000000000000','total_charge' => '56.5789473684210000','created_at' => '2023-08-19 17:20:46','updated_at' => NULL),
            array('id' => '3','transaction_id' => '3','percent_charge' => '102.0408163265300000','fixed_charge' => '25.0000000000000000','total_charge' => '127.0408163265300000','created_at' => '2023-08-19 17:21:33','updated_at' => NULL),
            array('id' => '4','transaction_id' => '4','percent_charge' => '181.8181818181800000','fixed_charge' => '25.0000000000000000','total_charge' => '206.8181818181800000','created_at' => '2023-08-19 17:22:05','updated_at' => NULL),
            array('id' => '5','transaction_id' => '5','percent_charge' => '23.5294117647060000','fixed_charge' => '25.0000000000000000','total_charge' => '48.5294117647060000','created_at' => '2023-08-19 17:22:42','updated_at' => NULL),
            array('id' => '6','transaction_id' => '6','percent_charge' => '25000.0000000000000000','fixed_charge' => '150.0000000000000000','total_charge' => '25150.0000000000000000','created_at' => '2023-08-19 17:26:48','updated_at' => NULL),
            array('id' => '7','transaction_id' => '7','percent_charge' => '1470.5882352941000000','fixed_charge' => '100.0000000000000000','total_charge' => '1570.5882352941000000','created_at' => '2023-08-19 17:27:32','updated_at' => NULL),
            array('id' => '8','transaction_id' => '8','percent_charge' => '158.1027667984200000','fixed_charge' => '25.0000000000000000','total_charge' => '183.1027667984200000','created_at' => '2023-08-19 17:29:08','updated_at' => NULL),
            array('id' => '9','transaction_id' => '9','percent_charge' => '76.9230769230770000','fixed_charge' => '25.0000000000000000','total_charge' => '101.9230769230800000','created_at' => '2023-08-19 17:30:07','updated_at' => NULL),
            array('id' => '10','transaction_id' => '10','percent_charge' => '31.5789473684210000','fixed_charge' => '25.0000000000000000','total_charge' => '56.5789473684210000','created_at' => '2023-08-19 17:30:29','updated_at' => NULL),
            array('id' => '11','transaction_id' => '11','percent_charge' => '102.0408163265300000','fixed_charge' => '25.0000000000000000','total_charge' => '127.0408163265300000','created_at' => '2023-08-19 17:31:02','updated_at' => NULL),
            array('id' => '12','transaction_id' => '12','percent_charge' => '36.3636363636360000','fixed_charge' => '25.0000000000000000','total_charge' => '61.3636363636360000','created_at' => '2023-08-19 17:31:39','updated_at' => NULL),
            array('id' => '13','transaction_id' => '13','percent_charge' => '117.6470588235300000','fixed_charge' => '25.0000000000000000','total_charge' => '142.6470588235300000','created_at' => '2023-08-19 17:32:00','updated_at' => NULL),
            array('id' => '14','transaction_id' => '14','percent_charge' => '25000.0000000000000000','fixed_charge' => '150.0000000000000000','total_charge' => '25150.0000000000000000','created_at' => '2023-08-19 17:32:45','updated_at' => NULL),
            array('id' => '15','transaction_id' => '15','percent_charge' => '14705.8823529410000000','fixed_charge' => '150.0000000000000000','total_charge' => '14855.8823529410000000','created_at' => '2023-08-19 17:33:40','updated_at' => NULL),
            array('id' => '16','transaction_id' => '16','percent_charge' => '395.2569169960500000','fixed_charge' => '50.0000000000000000','total_charge' => '445.2569169960500000','created_at' => '2023-08-19 17:34:28','updated_at' => NULL),
            array('id' => '17','transaction_id' => '17','percent_charge' => '40.0000000000000000','fixed_charge' => '25.0000000000000000','total_charge' => '65.0000000000000000','created_at' => '2023-08-19 17:35:41','updated_at' => NULL),
            array('id' => '18','transaction_id' => '18','percent_charge' => '36.0000000000000000','fixed_charge' => '25.0000000000000000','total_charge' => '61.0000000000000000','created_at' => '2023-08-19 17:38:39','updated_at' => NULL),
            array('id' => '19','transaction_id' => '19','percent_charge' => '25000.0000000000000000','fixed_charge' => '150.0000000000000000','total_charge' => '25150.0000000000000000','created_at' => '2023-08-19 17:39:23','updated_at' => NULL),
            array('id' => '20','transaction_id' => '20','percent_charge' => '120.0000000000000000','fixed_charge' => '25.0000000000000000','total_charge' => '145.0000000000000000','created_at' => '2023-08-19 17:40:06','updated_at' => NULL),
            array('id' => '21','transaction_id' => '21','percent_charge' => '15854.4400000000000000','fixed_charge' => '150.0000000000000000','total_charge' => '16004.4400000000000000','created_at' => '2023-08-19 17:51:13','updated_at' => NULL)
        );

        TransactionCharge::insert($transaction_charges);
    }
}
