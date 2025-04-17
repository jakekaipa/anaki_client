<?php

namespace App\Http\Controllers\Admin;

use App\Constants\NotificationConst;
use App\Events\Admin\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Admin\AdminLoginLogs;
use App\Models\Admin\AdminNotification;
use App\Models\Admin\Admin;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use App\Repositories\DataStatsRepository;

class DashboardController extends Controller
{
    public function __construct(DataStatsRepository $dataStats)
    {
        $this->firstDayOfMonth = Carbon::now()->firstOfMonth();
        $this->lastDayOfMonth = Carbon::now()->lastOfMonth();
        $this->dataStats = $dataStats;  
    }

    protected function getWhereParams(Request $request)
    {
        if ($request->start && $request->end) {
            return [
                'start' => $request->start . "00:00:00",
                'end' => $request->end . "23:59:59"
            ];
        } else {
            return [
                'start' => $this->firstDayOfMonth . "00:00:00",
                'end' => $this->lastDayOfMonth . "23:59:59"
            ];
        }
    }

    /**
     * 대쉬보드
     */
    public function index(Request $request)
    {
        $column = ['usdt_amount','trx_amount','created_at'];
        $dataList =  $this->dataStats->getGeneralStatData(
            where: $this->getWhereParams($request),
            column: $column
        );

        foreach ($dataList as $data) {
            $date = date('y-m-d', strtotime($data['created_at']));
            $usdtData[] = [
                'date' => $date,
                'value' => $data['usdt_amount']
            ];
            $trxData[] = [
                'date' => $date,
                'value' => $data['trx_amount']
            ];
        }
        return view('admin.dashboard', compact('usdtData', 'trxData'));
    }

}
