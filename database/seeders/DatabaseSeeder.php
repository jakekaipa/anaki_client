<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Database\Seeders\Admin\RoleSeeder;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\CategoryTypeSeeder;
use Database\Seeders\Admin\SetupKycSeeder;
use Database\Seeders\Admin\SetupSeoSeeder;
use Database\Seeders\User\ForexCrowSeeder;
use Database\Seeders\Admin\ExtensionSeeder;
use Database\Seeders\Admin\SetupPageSeeder;
use Database\Seeders\Demo\SetupEmailSeeder;
use Database\Seeders\User\TransactionSeeder;
use Database\Seeders\Admin\AppSettingsSeeder;
use Database\Seeders\User\NotificaitonSeeder;
// Demo Seeder
use Database\Seeders\Admin\AdminHasRoleSeeder;
use Database\Seeders\Admin\SiteSectionsSeeder;
use Database\Seeders\Admin\BasicSettingsSeeder;
// Fresh Seeder
use Database\Seeders\Demo\PaymentGateWaySeeder;
use Database\Seeders\User\ForexOfferCrowSeeder;
use Database\Seeders\User\TransactionChargeSeeder;
use Database\Seeders\User\TransactionDeviceSeeder;
use Database\Seeders\Admin\TransactionSettingSeeder;
use Database\Seeders\Demo\User\UserSeeder as DemoUserSeeder;
use Database\Seeders\Demo\BasicSettingsSeeder as DemoBasicSettingsSeeder;
use Database\Seeders\Fresh\Admin\ExtensionSeeder as FreshExtensionSeeder;
use Database\Seeders\Fresh\Admin\SetupEmailSeeder as FreshSetupEmailSeeder;
use Database\Seeders\Fresh\Admin\PaymentGateWaySeeder as FreshPaymentGateWaySeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        // Demo Project Seeder

        // $this->call([
        //     AdminSeeder::class,
        //     RoleSeeder::class,
        //     AdminHasRoleSeeder::class,
        //     CurrencySeeder::class,
        //     DemoUserSeeder::class,
        //     DemoBasicSettingsSeeder::class,
        //     SiteSectionsSeeder::class,
        //     SetupSeoSeeder::class,
        //     AppSettingsSeeder::class,
        //     LanguageSeeder::class,
        //     SetupEmailSeeder::class,
        //     ExtensionSeeder::class,
        //     SetupPageSeeder::class,
        //     PaymentGateWaySeeder::class,
        //     TransactionSettingSeeder::class,
        //     SetupKycSeeder::class,
        //     ForexCrowSeeder::class,
        //     ForexOfferCrowSeeder::class,
        //     TransactionSeeder::class,
        //     TransactionChargeSeeder::class,
        //     TransactionDeviceSeeder::class,
        //     NotificaitonSeeder::class,
        // ]);

        // Fresh Project Seeder

        $this->call([
            AdminSeeder::class,
            // RoleSeeder::class,
            // AdminHasRoleSeeder::class,
            BasicSettingsSeeder::class,
            CurrencySeeder::class,
            LanguageSeeder::class,
            ExtensionSeeder::class,
            SiteSectionsSeeder::class,
            SetupKycSeeder::class,
            // SetupSeoSeeder::class,
            // AppSettingsSeeder::class,
            SetupEmailSeeder::class,
            // FreshExtensionSeeder::class,
            // SetupPageSeeder::class,
            // FreshPaymentGateWaySeeder::class,
            // TransactionSettingSeeder::class,
        ]);
    }
}
