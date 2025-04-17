<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Forexcrow;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use Illuminate\Support\Facades\Validator;

class MarketplaceController extends Controller
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
        )->where('type', PaymentGatewayConst::MARKETPLACE)->orderBy('id', 'desc')->paginate(20);

        return view('admin.sections.marketplace.index', compact(
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
        )->where('type', PaymentGatewayConst::MARKETPLACE)->where('status', 2)->orderBy('id', 'desc')->paginate(20);
        return view('admin.sections.marketplace.index', compact(
            'page_title',
            'transactions'
        ));
    }


    /**
     * Complete Add Money Logs View.
     * @return view $complete-marketplace-logs
     */
    public function complete()
    {
        $page_title = "Complete Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', PaymentGatewayConst::MARKETPLACE)->where('status', 1)->orderBy('id', 'desc')->paginate(20);
        return view('admin.sections.marketplace.index', compact(
            'page_title',
            'transactions'
        ));
    }

    /**
     * Canceled Add Money Logs View.
     * @return view $canceled-marketplace-logs
     */
    public function canceled()
    {
        $page_title = "Canceled Logs";
        $transactions = Transaction::with(
            'user:id,firstname,email,username,mobile',
        )->where('type', PaymentGatewayConst::MARKETPLACE)->where('status', 4)->orderBy('id', 'desc')->paginate(20);
        return view('admin.sections.marketplace.index', compact(
            'page_title',
            'transactions'
        ));
    }

    /**
     * This method for show details of add money
     * @return view $details-marketplace-logs
     */
    public function logDetails($id){
        $data = Transaction::with('forexcrow')->where('id',$id)->with(
            'user:id,firstname,email,username,full_mobile',
            'currency:id,name,alias,payment_gateway_id,currency_code,rate',
        )->where('type', PaymentGatewayConst::MARKETPLACE)->first();

        $page_title = "marketplace details for".'  '.$data->trx_id;
        return view('admin.sections.marketplace.details', compact(
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

        $data = Transaction::with('forexcrow')->where('id',$request->id)->where('status',2)->where('type', PaymentGatewayConst::MARKETPLACE)->first();

        $forexcrow = Forexcrow::find($data->forexcrow->id);

        try{
            //update transaction
            $data->status = 1;
            $data->save();
            // Update forexcrow
            $forexcrow->update(['status' => 6]);

            return redirect()->back()->with(['success' => ['Marketplace request approved successfully.']]);
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
        $data = Transaction::with('forexcrow')->where('id',$request->id)->where('status',2)->where('type', PaymentGatewayConst::MARKETPLACE)->first();
        $forexcrow = Forexcrow::findOrFail($data->forexcrow->id);

        $reject['status'] = 4;
        $reject['reject_reason'] = $request->reject_reason;
        try{
            $data->fill($reject)->save();
            $forexcrow->update(['status' => 1]);
            return redirect()->back()->with(['success' => ['Forexcrow request canceled successfully.']]);
        }catch(Exception $e){
            return back()->with(['error' => [$e->getMessage()]]);
        }
    }
}
