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

class DataStatsController extends Controller
{
    public function __construct(DataStatsRepository $dataStats)
    {
        $this->firstDayOfMonth = Carbon::now()->firstOfMonth()->format('Y-m-d');
        $this->lastDayOfMonth = Carbon::now()->format('Y-m-d');
        $this->dataStats = $dataStats;  
    }

    /**
     * 날짜 만드는 함수
     */
    protected function getWhereParams(Request $request)
    {
        if ($request->start && $request->end) {
            return [
                'start' => $request->start,
                'end' => $request->end
            ];
        } else {
            return [
                'start' => $this->firstDayOfMonth,
                'end' => $this->lastDayOfMonth
            ];
        }
    }

    /**
     * 가입자수 통계 조회
     */
    public function getRegistUserCount(Request $request)
    {
        $column = ['register_amount','created_at'];
        $registUserCount =  $this->dataStats->getGeneralStatData(
            where: $this->getWhereParams($request),
            column: $column
            );
            
          // print_r($userCountData);

        $userCountData=[];
        foreach ($registUserCount as $data) {
            $date = date('y-m-d', strtotime($data['created_at']));
            $userCountData[] =[
                'date' => $date,
                'value' => $data['register_amount']
            ];
        }
    
        $date =  $this->getWhereParams($request);
        $now = Carbon::now()->toDateString();
        return view('admin.dataStatsRegistUser',compact('userCountData','date','now'));
    }

    /**
     * 거래 금액 통계 조회
     */
    public function getTradeAmount(Request $request){
       $column = ['trade_amount','trade_fee','referral_fee','created_at'];

       $tradeAmout =  $this->dataStats->getTradeStatData(
                                where: $this->getWhereParams($request),
                                column: $column
                        );

        $tradeAmoutData = [
            'trade_amount' => [],
            'trade_fee' => [],
            'referral_fee' => [],
        ];
        
        foreach ($tradeAmout as $data) {
            $date = date('y-m-d', strtotime($data['created_at']));
            foreach (['trade_amount', 'trade_fee', 'referral_fee'] as $field) {
                $tradeAmoutData[$field][] = [
                    'date' => $date,
                    'value' => $data[$field],
                ];
            }
        }

        //print_r($tradeAmoutData);
        //exit;
        $date =  $this->getWhereParams($request);
        $now = Carbon::now()->toDateString();
       return view('admin.dataStatsTradeAmout',compact('tradeAmoutData','date','now'));
    }
     
    /**
     * 거래 건수 통계 조회 
     */
    public function getTradeCount(Request $request){
        $column = ['trade_count','completed_trade_count','dipute_trade_count','dipute_solved_trade_count','cancel_trade_count','created_at'];
        $tradeCount =  $this->dataStats->getTradeStatData(
            where: $this->getWhereParams($request),
            column: $column
        );

        $tradeCountData = [
            'trade_amount' => [],
            'trade_fee' => [],
            'referral_fee' => [],
        ];
        
        foreach ($tradeCount as $data) {
            $date = date('y-m-d', strtotime($data['created_at']));
            foreach (['trade_count', 'completed_trade_count', 'dipute_trade_count','dipute_solved_trade_count','cancel_trade_count'] as $field) {
                $tradeCountData[$field][] = [
                    'date' => $date,
                    'value' => $data[$field],
                ];
            }
        }

       
        $date =  $this->getWhereParams($request);
        $now = Carbon::now()->toDateString();

        return view('admin.dataStatsTradeCount',compact('tradeCountData','date','now'));
    }

}
