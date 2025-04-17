<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataStatsScheduleService;

class SaveCompletedInternalTransactions extends Command
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
    protected $signature = 'save-completed-internal-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = ' 전날 총 거래 건수 (총 건수 , 거래완료,분쟁건수,분쟁해결,취소 건수)합계 저장';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->dataStatsScheduleService->saveCompletedInternalTransactions();
    }
}
