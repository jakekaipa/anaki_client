<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\Admin\PaymentGateway;
use App\Constants\PaymentGatewayConst;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\PaymentGatewayCurrency;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $appends = ['stringStatus'];

    protected $casts = [
        'admin_id'                    => 'integer',
        'user_id'                     => 'integer',
        'user_wallet_id'              => 'integer',
        'payment_gateway_currency_id' => 'integer',
        'trx_id'                      => 'string',
        'request_amount'              => 'decimal:16',
        'available_balance'           => 'decimal:16',
        'payable'                     => 'decimal:16',
        'remark'                      => 'string',
        'status'                      => 'integer',
        'details'                     => 'object',
        'reject_reason'               => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_wallet()
    {
        return $this->belongsTo(UserWallet::class, 'user_wallet_id');
    }

    public function currency()
    {
        return $this->belongsTo(PaymentGatewayCurrency::class,'payment_gateway_currency_id');
    }

    public function forexcrow()
    {
        return $this->belongsTo(Forexcrow::class,'forexcrow_id');
    }

    public function scopeAuth($query) {
        $query->where("user_id",auth(get_auth_guard())->user()->id);
    }

    public function getStringStatusAttribute() {
        $status = $this->status;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == PaymentGatewayConst::STATUSSUCCESS) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => "Success",
            ];
        }else if($status == PaymentGatewayConst::STATUSPENDING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Pending",
            ];
        }else if($status == PaymentGatewayConst::STATUSHOLD) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Hold",
            ];
        }else if($status == PaymentGatewayConst::STATUSREJECTED) {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => "Rejected",
            ];
        }

        return (object) $data;
    }

    public function getScrowStringStatusAttribute() {
        $status = $this->forexcrow->status;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == PaymentGatewayConst::EXCROW_STATUSONGOING) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => "Ongoing",
            ];
        }else if($status == PaymentGatewayConst::EXCROW_STATUSPENDING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Pending",
            ];
        }else if($status == PaymentGatewayConst::EXCROW_STATUSCANCELLED) {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => "Cancelled",
            ];
        }
        else if($status == PaymentGatewayConst::EXCROW_STATUSPAYMENTPENDING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Payment Pending",
            ];
        }
        else if($status == PaymentGatewayConst::EXCROW_STATUSCOMPLETE) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => "Sold",
            ];
        }
        else if($status == PaymentGatewayConst::EXCROW_CANCEL_REQUEST) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Close Requested",
            ];
        }
        else if($status == PaymentGatewayConst::EXCROW_CANCEL_BY_USER) {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => "Closed",
            ];
        }

        return (object) $data;
    }


    public function getStringMarketplaceStatusAttribute() {
        $status = $this->status;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == PaymentGatewayConst::EXCROW_STATUSONGOING) {
            $data = [
                'class'     => "badge badge--success",
                'value'     => "Complete",
            ];
        }else if($status == PaymentGatewayConst::EXCROW_STATUSPENDING) {
            $data = [
                'class'     => "badge badge--warning",
                'value'     => "Pending",
            ];
        }else if($status == PaymentGatewayConst::EXCROW_STATUSCANCELLED) {
            $data = [
                'class'     => "badge badge--danger",
                'value'     => "Cancelled",
            ];
        }

        return (object) $data;
    }

    public function getUserStringStatusAttribute() {
        $status = $this->forexcrow->status;
        $data = [
            'class' => "",
            'value' => "",
        ];
        if($status == PaymentGatewayConst::EXCROW_STATUSONGOING) {
            $data = [
                'class'     => "badge-success",
                'value'     => "Ongoing",
            ];
        }else if($status == PaymentGatewayConst::EXCROW_STATUSPENDING) {
            $data = [
                'class'     => "badge--warning",
                'value'     => "Pending",
            ];
        }else if($status == PaymentGatewayConst::EXCROW_STATUSCANCELLED) {
            $data = [
                'class'     => "badge--danger",
                'value'     => "Cancelled",
            ];
        }else if($status == PaymentGatewayConst::EXCROW_STATUSPAYMENTPENDING) {
            $data = [
                'class'     => "badge--warning",
                'value'     => "Payment Pending",
            ];
        }
        else if($status == PaymentGatewayConst::EXCROW_STATUSCOMPLETE) {
            $data = [
                'class'     => "badge--success",
                'value'     => "Sold",
            ];
        }

        return (object) $data;
    }

    public function charge() {
        return $this->hasOne(TransactionCharge::class,"transaction_id","id");
    }

    public function scopeExcrowTransaction($query){
        return $query->where('type', PaymentGatewayConst::EXCROW);
    }

    public function scopeBuyingTransaction($query){
        return $query->where('type', PaymentGatewayConst::MARKETPLACE);
    }

    public function scopeAddMoney($query) {
        return $query->where("type",PaymentGatewayConst::TYPEADDMONEY);
    }

    public function scopeMoneyOut($query) {
        return $query->where("type",PaymentGatewayConst::TYPEMONEYOUT);
    }

    public function scopeSearch($query,$data) {
        $data = Str::slug($data);
        return $query->where("trx_id","like","%".$data."%")
                    ->orWhere('type', 'like', '%'.$data.'%')
                    ->orderBy('id',"DESC");

    }

    public function scopeMoneyExchange($query) {
        return $query->where("type",PaymentGatewayConst::TYPEMONEYEXCHANGE);
    }

    public function isAuthUser() {
        if($this->user_id === auth()->user()->id) return true;
        return false;
    }

    public function campaign(){
        return $this->belongsTo(Campaign::class, 'campaign_id', 'id');
    }

}
