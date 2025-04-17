<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataStatsScheduleService;

class SaveTotalInternalTransactionCounts extends Command
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
    protected $signature = 'save-total-internal-transaction-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '전날 완료된 내부 거래 (거래금액,수수료,레퍼럴 수수료)합계 저장';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    { 
        $this->dataStatsScheduleService->saveTotalInternalTransactionCounts();
    }
}
