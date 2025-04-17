<?php
namespace App\Repositories;

use App\Models\User;
use App\Models\Admin\GeneralStatData;
use App\Models\Admin\TradeStatData;


class DataStatsRepository
{
    public function __construct(
        User $user,
        GeneralStatData $generalStatData,
        TradeStatData   $tradeStatData
    )
    {
        $this->user = $user;
        $this->generalStatData = $generalStatData;
        $this->tradeStatData   = $tradeStatData;
    }
    
    /**
     * 통계 일반 데이터 조회(usdt,trx,register)
     */
    public function getGeneralStatData(array $where=[],array $column){
        $query = $this->generalStatData->newQuery();
        
        if (isset($where['start']) && isset($where['end'])) {
            $query->whereBetween('created_at', [$where['start'], $where['end']]);
        }

        return $query->select($column)->get();
    }
    
    /**
     * 통계 일반 데이터 저장
     */
    public function insertGeneralData(array $insertParam){
        $this->generalStatData->insert($insertParam);
    }

    /**
     * 통계 일반 데이터 업데이트
     */
    public function updateGeneralData(array $updateParam,string $createdAt){
        $this->generalStatData->where('created_at',$createdAt)->update($updateParam);
    }

    /**
     * 통계 거래 데이터 조회
     */
    public function getTradeStatData(array $where=[],array $column){
        $query = $this->tradeStatData->newQuery();
        
        if (isset($where['start']) && isset($where['end'])) {
            $query->whereBetween('created_at', [$where['start'], $where['end']]);
        }

        return $query->select($column)->get();

    }

    /**
     * 통계 거래 데이터 저장
     */
    public function insertTradeStatData(array $insertParam){
        $this->tradeStatData->insert($insertParam); 
    }

    /**
     * 통계 거래 데이터 업데이트
     */
    public function updateTradeStatData(array $updateParam,string $createdAt ){
        $this->tradeStatData->where('created_at',$createdAt)->update($updateParam);
    }
}