<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin\TransactionSetting;
use Illuminate\Support\Facades\Validator;

class TrxSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = "Fees & Charges";
        $transaction_charges = TransactionSetting::all();
        return view('admin.sections.trx-settings.index',compact(
            'page_title',
            'transaction_charges'
        ));
    }

    /**
     * Update transaction charges
     * @param Request closer
     * @return back view
     */
    public function trxChargeUpdate(Request $request) {
        $validator = Validator::make($request->all(),[
            'slug'         => 'required|string',
            $request->slug.'_min_limit'   => 'array',
            $request->slug.'_min_limit.*' => 'required|numeric',
            $request->slug.'_max_limit'   => 'array',
            $request->slug.'_max_limit.*' => 'required|numeric',
            $request->slug.'_charge'      => 'array',
            $request->slug.'_charge.*'    => 'required|numeric',
            $request->slug.'_percent'     => 'array',
            $request->slug.'_percent.*'   => 'required|numeric',
        ]);

        if($validator->fails()){
            return back()->withErrors( $validator)->withInput();
        }

        $validated = $validator->validate();

        $transaction_setting = TransactionSetting::where('slug',$request->slug)->first();

        if(!$transaction_setting) return back()->with(['error' => ['Transaction charge not found.']]);
        $validated = replace_array_key($validated,$request->slug."_");


        $input_fields = [];
        foreach ($validated['min_limit'] ?? [] as $key => $item) {
            $input_fields[]     = [
              'min_limit' => $item,
              'max_limit' => $validated['max_limit'][$key] ?? "",
              'charge'    => $validated['charge'][$key] ?? "",
              'percent'   => $validated['percent'][$key] ?? "",
            ];
        }

        $validated['intervals'] =  $input_fields;
        $validated = Arr::except($validated,['min_limit','max_limit','charge','percent']);

        try{
            $transaction_setting->update($validated);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Charge updated successfully.']]);

    }
}
