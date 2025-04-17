<?php
namespace App\Repositories;

use App\Models\Notice;


class NoticeRepository
{
    public function __construct(
        Notice $notice
    )
    {
        $this->notice = $notice;
    }
    
    /**
     * 공지사항 리스트
     */
    public function getNoticeList(){
        return $this->notice->orderby('created_at','desc')->paginate(10);
    }

     /**
     * 공지사항 저장
     */
    public function store(Array $insertParam){
        $this->notice->insert($insertParam);
    }

    /**
     * 공지사항 상세보기
     */
    public function view($id){
        return $this->notice->where('id',$id)->first();
    }

    /**
     * 공지사항 수정
     */
    public function update(int $id, Array $updateParam)
    {
        $this->notice->where('id',$id)->update($updateParam);
    }

}