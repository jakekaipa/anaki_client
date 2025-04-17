<?php

namespace Database\Seeders;

use App\Models\Admin\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = array(
            array('id' => '2','admin_id' => '1','country' => 'United States','name' => 'United States dollar','code' => 'USD','symbol' => '$','type' => 'FIAT','flag' => 'd012aa5d-61ea-44f8-a67c-a22958ebb1b5.webp','rate' => '0.00130000','sender' => '1','receiver' => '1','default' => '1','status' => '1','created_at' => '2023-06-26 01:03:12','updated_at' => '2024-01-06 02:06:15'),
            array('id' => '3','admin_id' => '1','country' => 'Australia','name' => 'Australian dollar','code' => 'AUD','symbol' => '$','type' => 'FIAT','flag' => 'a7fb3014-a1a3-49ff-8e7a-10123d4d81a4.webp','rate' => '0.00190000','sender' => '0','receiver' => '0','default' => '0','status' => '1','created_at' => '2023-06-26 07:23:46','updated_at' => '2023-08-19 03:19:23'),
            array('id' => '4','admin_id' => '1','country' => 'United Kingdom','name' => 'British pound','code' => 'GBP','symbol' => '£','type' => 'FIAT','flag' => '99fa7871-2217-4fb6-818b-bcd7d47c7088.webp','rate' => '0.00098000','sender' => '0','receiver' => '0','default' => '0','status' => '1','created_at' => '2023-07-16 13:01:21','updated_at' => '2023-08-19 03:23:08'),
            array('id' => '5','admin_id' => '1','country' => 'Germany','name' => 'Euro','code' => 'EUR','symbol' => '€','type' => 'FIAT','flag' => '5b5d6626-7350-4748-91fc-54360dc29eac.webp','rate' => '0.00110000','sender' => '0','receiver' => '0','default' => '0','status' => '1','created_at' => '2023-07-16 13:02:34','updated_at' => '2023-08-19 03:27:03'),
            array('id' => '6','admin_id' => '1','country' => 'Canada','name' => 'Canadian dollar','code' => 'CAD','symbol' => '$','type' => 'FIAT','flag' => '87e69bba-20cc-4b72-87ba-19883529ea3a.webp','rate' => '0.00170000','sender' => '0','receiver' => '0','default' => '0','status' => '1','created_at' => '2023-07-16 13:03:31','updated_at' => '2023-08-19 03:23:57'),
            array('id' => '9','admin_id' => '1','country' => 'Nigeria','name' => 'Tether','code' => 'USDT','symbol' => '₮','type' => 'CRYPTO','flag' => '5a76431b-adc6-4d7e-b13a-971b6996905d.webp','rate' => '0.00126500','sender' => '0','receiver' => '0','default' => '0','status' => '1','created_at' => '2023-07-24 03:19:07','updated_at' => '2023-08-19 03:39:39')
        );

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(['id' => $currency['id']], $currency);
        }
    }
}
