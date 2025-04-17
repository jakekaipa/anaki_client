<?php

namespace Database\Seeders\User;

use App\Models\Forexcrow;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForexCrowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forexcrows = array(
            array('id' => '1','user_id' => '1','currency_id' => '2','amount' => '2.0000000000000000','rate' => '1800.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:20:13','updated_at' => NULL),
            array('id' => '2','user_id' => '1','currency_id' => '3','amount' => '3.0000000000000000','rate' => '2000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:20:46','updated_at' => NULL),
            array('id' => '3','user_id' => '1','currency_id' => '4','amount' => '5.0000000000000000','rate' => '6000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '6','created_at' => '2023-08-19 17:21:33','updated_at' => '2023-08-19 17:40:06'),
            array('id' => '4','user_id' => '1','currency_id' => '5','amount' => '10.0000000000000000','rate' => '10000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:22:05','updated_at' => NULL),
            array('id' => '5','user_id' => '1','currency_id' => '6','amount' => '2.0000000000000000','rate' => '1500.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:22:42','updated_at' => NULL),
            array('id' => '6','user_id' => '1','currency_id' => '7','amount' => '0.1000000000000000','rate' => '2500000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '6','created_at' => '2023-08-19 17:26:48','updated_at' => '2023-08-19 17:39:23'),
            array('id' => '7','user_id' => '1','currency_id' => '8','amount' => '0.1000000000000000','rate' => '200000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:27:32','updated_at' => NULL),
            array('id' => '8','user_id' => '1','currency_id' => '9','amount' => '10.0000000000000000','rate' => '10000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:29:08','updated_at' => NULL),
            array('id' => '9','user_id' => '2','currency_id' => '2','amount' => '5.0000000000000000','rate' => '5000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:30:07','updated_at' => NULL),
            array('id' => '10','user_id' => '2','currency_id' => '3','amount' => '3.0000000000000000','rate' => '1800.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '6','created_at' => '2023-08-19 17:30:29','updated_at' => '2023-08-19 17:38:40'),
            array('id' => '11','user_id' => '2','currency_id' => '4','amount' => '5.0000000000000000','rate' => '6000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:31:02','updated_at' => NULL),
            array('id' => '12','user_id' => '2','currency_id' => '5','amount' => '2.0000000000000000','rate' => '2000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '6','created_at' => '2023-08-19 17:31:39','updated_at' => '2023-08-19 17:35:41'),
            array('id' => '13','user_id' => '2','currency_id' => '6','amount' => '10.0000000000000000','rate' => '7000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:32:00','updated_at' => NULL),
            array('id' => '14','user_id' => '2','currency_id' => '7','amount' => '0.1000000000000000','rate' => '2725150.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:32:45','updated_at' => NULL),
            array('id' => '15','user_id' => '2','currency_id' => '8','amount' => '1.0000000000000000','rate' => '1585444.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '6','created_at' => '2023-08-19 17:33:40','updated_at' => '2023-08-19 17:51:14'),
            array('id' => '16','user_id' => '2','currency_id' => '9','amount' => '50.0000000000000000','rate' => '55000.0000000000000000','rate_currency_id' => '1','comment' => NULL,'status' => '1','created_at' => '2023-08-19 17:34:28','updated_at' => NULL)
        );

        Forexcrow::insert($forexcrows);

    }
}
