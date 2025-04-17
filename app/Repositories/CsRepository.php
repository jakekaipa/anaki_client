<?php
namespace App\Repositories;

use App\Models\UserSupportTicket;
use App\Models\UserSupportTicketComments;

class CsRepository
{
    public function __construct(
        UserSupportTicket $cs,
        UserSupportTicketComments $csComments
    )
    {
        $this->cs = $cs;
        $this->csComments = $csComments;
    }
    
    /**
     * 문의 리스트
     */
    public function getCsList(int $userId = null){
        $query = $this->cs
        ->with(['getCommentsOne' => function($query) {
            $query->orderBy('deeps', 'desc');
        }]);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }   

    /**
     * 문의 상세 내역
     */
    public function getCsInfo($id){
        return $this->cs
                    ->where('id',$id)
                    ->with(['getComments' => function($query) {
                        $query->orderby('deeps', 'asc');
                    }])
                    ->first();
    }

    /**
     * 댓글 최대 순서 조회 
     */
    public function getCsCommentMaxDeeps($ticketId){
        return $this->csComments->where('user_support_ticket_id',$ticketId)->max('deeps');
    }

    /**
     * 문의 댓글 등록
     */
    public function insertComments(array $insertParam){
        $this->csComments->insert($insertParam);
    }

    /**
     * 문의 댓글 읽은사람 업데이트
     */
    public function updateComments(int $id, int $userId){
        $this->csComments->where('id',$id)->update(
            [
                'read_user_id'=> $userId
            ]);
    }

    /**
     * 문의 상태 업데이트
     */
    public function updateTicketStatus(int $ticketId,int $status){
        $this->cs->where('id',$ticketId)->update(
            [
                'status'=> $status
            ]);
    }



}