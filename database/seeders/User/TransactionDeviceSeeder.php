<?php

namespace Database\Seeders\User;

use App\Models\TransactionDevice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionDeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transaction_devices = array(
            array('id' => '1','transaction_id' => '1','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '2','transaction_id' => '2','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '3','transaction_id' => '3','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '4','transaction_id' => '4','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '5','transaction_id' => '5','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '6','transaction_id' => '6','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '7','transaction_id' => '7','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '8','transaction_id' => '8','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '9','transaction_id' => '9','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '10','transaction_id' => '10','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '11','transaction_id' => '11','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '12','transaction_id' => '12','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '13','transaction_id' => '13','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '14','transaction_id' => '14','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '15','transaction_id' => '15','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '16','transaction_id' => '16','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '17','transaction_id' => '17','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '18','transaction_id' => '18','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '19','transaction_id' => '19','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '20','transaction_id' => '20','ip' => '127.0.0.1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Firefox','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL),
            array('id' => '21','transaction_id' => '21','ip' => '::1','mac' => '','city' => 'New Haven','country' => 'United States','longitude' => '-72.92','latitude' => '41.31','browser' => 'Chrome','os' => 'Windows','timezone' => 'America/New_York','created_at' => NULL,'updated_at' => NULL)
        );

        TransactionDevice::insert($transaction_devices);
    }
}
