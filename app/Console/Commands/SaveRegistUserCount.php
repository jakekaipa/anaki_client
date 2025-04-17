<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DataStatsScheduleService;

class SaveRegistUserCount extends Command
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
    protected $signature = 'save-regist-user-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '전날 회원 가입자수 저장';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->dataStatsScheduleService->saveRegistUserCount();
    }
}
