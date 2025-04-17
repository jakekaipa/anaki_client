<?php

namespace Database\Seeders\V3_0_0;

use Illuminate\Database\Seeder;
use App\Models\Admin\PaymentGateway;
use App\Models\Admin\PaymentGatewayCurrency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $ssl_commerze = array('slug' => 'add-money','code' => '220','type' => 'AUTOMATIC','name' => 'SSLCommerz','title' => 'SSLCommerz Payment','alias' => 'sslcommerz','account_name' => NULL,'account_number' => NULL,'image' => 'b08bb37a-6a3f-4e49-9150-752860335ee9.webp','credentials' => '[{"label":"Live Url","placeholder":"Enter Live Url","name":"live-url","value":"https:\\/\\/securepay.sslcommerz.com"},{"label":"Sandbox Url","placeholder":"Enter Sandbox Url","name":"sandbox-url","value":"https:\\/\\/sandbox.sslcommerz.com"},{"label":"Store Password","placeholder":"Enter Store Password","name":"store-password","value":""},{"label":"Store Id","placeholder":"Enter Store Id","name":"store-id","value":""}]','supported_currencies' => '["BDT","EUR","GBP","AUD","USD","CAD"]','crypto' => '0','desc' => NULL,'input_fields' => NULL,'env' => 'SANDBOX','status' => '1','last_edit_by' => '1','created_at' => '2023-11-23 04:52:33','updated_at' => '2023-11-23 04:55:01');

        $ssl_commerze_id = PaymentGateway::insertGetId($ssl_commerze);

        $razorpay = array('slug' => 'add-money','code' => '225','type' => 'AUTOMATIC','name' => 'Razorpay','title' => 'Razorpay Gateway','alias' => 'razorpay','account_name' => NULL,'account_number' => NULL,'image' => '068a884f-0f37-4c48-8457-06e77fa4b3ee.webp','credentials' => '[{"label":"Secret Key","placeholder":"Enter Secret Key","name":"secret-key","value":""},{"label":"Public Key","placeholder":"Enter Public Key","name":"public-key","value":""}]','supported_currencies' => '["INR"]','crypto' => '0','desc' => NULL,'input_fields' => NULL,'env' => 'SANDBOX','status' => '1','last_edit_by' => '1','created_at' => '2023-11-23 08:27:23','updated_at' => '2023-11-23 08:32:35');

        $razorpay_id = PaymentGateway::insertGetId($razorpay);

        $qrpay = array('slug' => 'add-money','code' => '230','type' => 'AUTOMATIC','name' => 'Qrpay','title' => 'Qrpay Gateway','alias' => 'qrpay','account_name' => NULL,'account_number' => NULL,'image' => 'cf4e938a-f837-4ed4-aada-ce0593225427.webp','credentials' => '[{"label":"Live Base Url","placeholder":"Enter Live Base Url","name":"live-base-url","value":"https:\\/\\/envato.appdevs.net\\/qrpay\\/pay\\/api\\/v1"},{"label":"Sendbox Base Url","placeholder":"Enter Sendbox Base Url","name":"sendbox-base-url","value":"https:\\/\\/envato.appdevs.net\\/qrpay\\/pay\\/sandbox\\/api\\/v1"},{"label":"Client Secret","placeholder":"Enter Client Secret","name":"client-secret","value":""},{"label":"Client Id","placeholder":"Enter Client Id","name":"client-id","value":""}]','supported_currencies' => '["USD"]','crypto' => '0','desc' => NULL,'input_fields' => NULL,'env' => 'SANDBOX','status' => '1','last_edit_by' => '1','created_at' => '2023-11-23 08:30:18','updated_at' => '2023-11-23 10:37:13');

        $qrpay_id = PaymentGateway::insertGetId($qrpay);


        $ssl_commerze_currency = array('payment_gateway_id' => $ssl_commerze_id,'name' => 'SSLCommerz BDT','alias' => 'add-money-sslcommerz-bdt-automatic','currency_code' => 'BDT','currency_symbol' => '৳','image' => NULL,'min_limit' => '10.0000000000000000','max_limit' => '10000.0000000000000000','percent_charge' => '2.0000000000000000','fixed_charge' => '0.0000000000000000','rate' => '0.1400000000000000','created_at' => '2023-11-23 04:55:01','updated_at' => '2023-11-23 05:16:23');

        PaymentGatewayCurrency::insert($ssl_commerze_currency);

        $razorpay_currency =  array('payment_gateway_id' => $razorpay_id,'name' => 'Razorpay INR','alias' => 'add-money-razorpay-inr-automatic','currency_code' => 'INR','currency_symbol' => '₹','image' => NULL,'min_limit' => '30.0000000000000000','max_limit' => '1000.0000000000000000','percent_charge' => '2.0000000000000000','fixed_charge' => '0.0000000000000000','rate' => '0.1000000000000000','created_at' => '2023-11-23 08:32:35','updated_at' => '2023-11-23 09:57:33');

        PaymentGatewayCurrency::insert($razorpay_currency);

        $qrpay_currency =  array('payment_gateway_id' => $qrpay_id,'name' => 'Qrpay USD','alias' => 'add-money-qrpay-usd-automatic','currency_code' => 'USD','currency_symbol' => '$','image' => NULL,'min_limit' => '1.0000000000000000','max_limit' => '1000.0000000000000000','percent_charge' => '2.0000000000000000','fixed_charge' => '0.0000000000000000','rate' => '0.0012000000000000','created_at' => '2023-11-23 10:37:13','updated_at' => '2023-11-23 10:37:13');

        PaymentGatewayCurrency::insert($qrpay_currency);

    }
}
