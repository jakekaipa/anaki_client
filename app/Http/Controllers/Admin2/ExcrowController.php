<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Forexcrow;
use App\Models\UserWallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use App\Providers\Admin\CurrencyProvider;
use Illuminate\Support\Facades\Validator;

class ExcrowController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "All Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', PaymentGatewayConst::EXCROW)->orderBy('id', 'desc')->paginate(20);

        return view('admin.sections.forexcrow.index', compact(
            'page_title',
            'transactions'
        ));
    }


    /**
     * Pending Add Money Logs View.
     * @return view $pending-add-money-logs
     */
    public function pending()
    {
        $page_title = "Pending Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', PaymentGatewayConst::EXCROW)->orderBy('id', 'desc')->where('status', 2)->paginate(20);
        return view('admin.sections.forexcrow.index', compact(
            'page_title',
            'transactions'
        ));
    }


    /**
     * Pending Add Money Logs View.
     * @return view $pending-add-money-logs
     */
    public function paymentPending()
    {
        $page_title = "Payment Pending Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile','forexcrow'
        )->where('type', PaymentGatewayConst::EXCROW)->orderBy('id', 'desc')
        ->whereHas('forexcrow', function($q){
            $q->where('status', 5);
        })
        ->where('status', 1)
        ->paginate(20);

        return view('admin.sections.forexcrow.index', compact(
            'page_title',
            'transactions'
        ));
    }


    /**
     * Complete forexcrow log Logs View.
     * @return view $pending-add-money-logs
     */
    public function complete()
    {
        $page_title = "Complete Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile','forexcrow'
        )->where('type', PaymentGatewayConst::EXCROW)
        ->whereHas('forexcrow', function($q){
            $q->where('status', 6);
        })->orderBy('id', 'desc')
        ->where('status', 1)
        ->paginate(20);

        return view('admin.sections.forexcrow.index', compact(
            'page_title',
            'transactions'
        ));
    }


    /**
     * Complete Add Money Logs View.
     * @return view $complete-forexcrow-logs
     */
    public function ongoingLogs()
    {
        $page_title = "Ongoing Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile','forexcrow'
        )->where('type', PaymentGatewayConst::EXCROW)
        ->whereHas('forexcrow', function($q){
            $q->where('status', 1);
        })->orderBy('id', 'desc')
        ->where('status', 1)
        ->paginate(20);
        return view('admin.sections.forexcrow.index', compact(
            'page_title',
            'transactions'
        ));
    }

    /**
     * Canceled Add Money Logs View.
     * @return view $canceled-forexcrow-logs
     */
    public function canceled()
    {
        $page_title = "Canceled Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', PaymentGatewayConst::EXCROW)->orderBy('id', 'desc')->where('status', 4)->paginate(20);
        return view('admin.sections.forexcrow.index', compact(
            'page_title',
            'transactions'
        ));
    }

    /**
     * Canceled Add Money Logs View.
     * @return view $canceled-forexcrow-logs
     */
    public function closedRequest()
    {
        $page_title = "Closed Request Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', PaymentGatewayConst::EXCROW)->orderBy('id', 'desc')->where('status', 7)->paginate(20);
        return view('admin.sections.forexcrow.index', compact(
            'page_title',
            'transactions'
        ));
    }

    /**
     * Closed Add Money Logs View.
     * @return view $canceled-forexcrow-logs
     */
    public function closed()
    {
        $page_title = "Closed Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', PaymentGatewayConst::EXCROW)->orderBy('id', 'desc')->where('status', 8)->paginate(20);
        return view('admin.sections.forexcrow.index', compact(
            'page_title',
            'transactions'
        ));
    }

    /**
     * This method for show details of add money
     * @return view $details-forexcrow-logs
     */
    public function logDetails($id){
        $data = Transaction::where('id',$id)->with(
            'user:id,firstname,email,username,full_mobile',
            'currency:id,name,alias,payment_gateway_id,currency_code,rate',
        )->where('type', PaymentGatewayConst::EXCROW)->first();
        $page_title = "Trade details for".'  '.$data->trx_id;
        return view('admin.sections.forexcrow.details', compact(
            'page_title',
            'data'
        ));
    }

    /**
     * This method for approved add money
     * @method PUT
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request Response
     */
    public function approved(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = Transaction::with('forexcrow')->where('id',$request->id)->where('status',2)->where('type', PaymentGatewayConst::EXCROW)->first();
        $forexcrow = Forexcrow::findOrFail($data->forexcrow->id);

        try{
            // update forexcrow
            $forexcrow->status = 1;
            $forexcrow->save();
            //update transaction
            $data->status = 1;
            $data->save();

            return redirect()->back()->with(['success' => ['Forexcrow request approved successfully.']]);
        }catch(Exception $e){
            return back()->with(['error' => [$e->getMessage()]]);
        }
    }

    /**
     * This method for reject add money
     * @method PUT
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request Response
     */
    public function rejected(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required|integer',
            'reject_reason' => 'required|string',
        ]);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $data = Transaction::with('forexcrow')->where('id',$request->id)->where('status',2)->where('type', PaymentGatewayConst::EXCROW)->first();
        $forexcrow = Forexcrow::findOrFail($data->forexcrow->id);

        $reject['status'] = 4;
        $reject['reject_reason'] = $request->reject_reason;
        try{
            $data->fill($reject)->save();
             // update forexcrow
             $forexcrow->status = 4;
             $forexcrow->save();
            return redirect()->back()->with(['success' => ['Forexcrow request canceled successfully.']]);
        }catch(Exception $e){
            return back()->with(['error' => [$e->getMessage()]]);
        }
    }

    /**
     * This method for reject add money
     * @method PUT
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request Response
     */
    public function closeRequestRejected(Request $request){
        $validator = Validator::make($request->all(),[
            'id' => 'required|integer',
            'reject_reason' => 'required|string',
        ]);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = Transaction::with('forexcrow')->where('id',$request->id)->where('status',7)->where('type', PaymentGatewayConst::EXCROW)->first();
        $forexcrow = Forexcrow::findOrFail($data->forexcrow->id);

        $reject['status'] = 1;
        $reject['reject_reason'] = $request->reject_reason;

        try{
            $data->fill($reject)->save();
             // update forexcrow
             $forexcrow->status = 1;
             $forexcrow->save();
            return redirect()->back()->with(['success' => ['Forexcrow closed request canceled successfully.']]);
        }catch(Exception $e){
            return back()->with(['error' => [$e->getMessage()]]);
        }
    }


     /**
     * This method for approved add money
     * @method PUT
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request Response
     */
    public function closedRequestApprove(Request $request){

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = Transaction::with('forexcrow')->where('id',$request->id)->where('status',7)->where('type', PaymentGatewayConst::EXCROW)->first();
        $forexcrow = Forexcrow::with('saleCurrency')->findOrFail($data->forexcrow->id);
        $currency = CurrencyProvider::default();
        $wallet = UserWallet::where('user_id', $forexcrow->user_id)->where('currency_id', $currency->id)->first();

        try{
            $amount = $data->request_amount / $forexcrow->saleCurrency->rate;
            $wallet->balance = $wallet->balance + $amount;
            $wallet->save();
            // update forexcrow
            $forexcrow->status = 8;
            $forexcrow->save();
            //update transaction
            $data->status = 8;
            $data->save();

            return redirect()->back()->with(['success' => ['Forexcrow closed request approved successfully.']]);
        }catch(Exception $e){
            return back()->with(['error' => [$e->getMessage()]]);
        }
    }
}
