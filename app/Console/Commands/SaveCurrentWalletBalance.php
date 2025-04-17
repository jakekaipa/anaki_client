<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataStatsScheduleService;

class SaveCurrentWalletBalance extends Command
{

    public function __construct(DataStatsScheduleService $dataStatsScheduleService)
    {
        parent::__construct();
        $this->dataStatsScheduleService = $dataStatsScheduleService;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save-current-wallet-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '마스터 월렛에 해당 시점에 Tether와 TRX 잔액 저장';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->dataStatsScheduleService->saveCurrentWalletBalance();
    }
}
