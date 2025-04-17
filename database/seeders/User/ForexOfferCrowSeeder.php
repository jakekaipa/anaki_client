<?php

namespace Database\Seeders\User;

use App\Models\ForexcrowOffer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ForexOfferCrowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forexcrow_offers = array(
            array('id' => '1','type' => 'OFFER','forexcrow_id' => '14','for_user_id' => '2','creator_id' => '1','receiver_id' => '2','amount' => '0.1000000000000000','rate' => '2500000.0000000000000000','rate_currency_id' => '1','sale_currency_id' => '7','status' => '2','created_at' => '2023-08-19 17:51:45','updated_at' => '2023-08-19 17:51:45'),
            array('id' => '2','type' => 'OFFER','forexcrow_id' => '5','for_user_id' => '1','creator_id' => '2','receiver_id' => '1','amount' => '2.0000000000000000','rate' => '1400.0000000000000000','rate_currency_id' => '1','sale_currency_id' => '6','status' => '2','created_at' => '2023-08-19 17:53:46','updated_at' => '2023-08-19 17:53:46'),
            array('id' => '3','type' => 'OFFER','forexcrow_id' => '2','for_user_id' => '1','creator_id' => '2','receiver_id' => '1','amount' => '3.0000000000000000','rate' => '1800.0000000000000000','rate_currency_id' => '1','sale_currency_id' => '3','status' => '2','created_at' => '2023-08-19 17:54:06','updated_at' => '2023-08-19 17:54:06'),
            array('id' => '4','type' => 'OFFER','forexcrow_id' => '16','for_user_id' => '2','creator_id' => '1','receiver_id' => '2','amount' => '50.0000000000000000','rate' => '50000.0000000000000000','rate_currency_id' => '1','sale_currency_id' => '9','status' => '2','created_at' => '2023-08-19 17:54:33','updated_at' => '2023-08-19 17:54:33'),
            array('id' => '5','type' => 'OFFER','forexcrow_id' => '13','for_user_id' => '2','creator_id' => '1','receiver_id' => '2','amount' => '10.0000000000000000','rate' => '6000.0000000000000000','rate_currency_id' => '1','sale_currency_id' => '6','status' => '2','created_at' => '2023-08-19 17:54:40','updated_at' => '2023-08-19 17:54:40'),
            array('id' => '6','type' => 'OFFER','forexcrow_id' => '11','for_user_id' => '2','creator_id' => '1','receiver_id' => '2','amount' => '5.0000000000000000','rate' => '5000.0000000000000000','rate_currency_id' => '1','sale_currency_id' => '4','status' => '2','created_at' => '2023-08-19 17:54:46','updated_at' => '2023-08-19 17:54:46'),
            array('id' => '7','type' => 'OFFER','forexcrow_id' => '9','for_user_id' => '2','creator_id' => '1','receiver_id' => '2','amount' => '5.0000000000000000','rate' => '4000.0000000000000000','rate_currency_id' => '1','sale_currency_id' => '2','status' => '2','created_at' => '2023-08-19 17:54:52','updated_at' => '2023-08-19 17:54:52')
        );

        ForexcrowOffer::insert($forexcrow_offers);
    }
}
