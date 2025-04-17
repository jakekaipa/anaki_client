<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Notifications\User\SendMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    /**
     * Mehtod for show subscriber page
     * @method GET
     * @return Illuminate\Http\Request Response
     */
    public function index() {
        $page_title = "All Subscriber";
        $data = Subscriber::orderBy('id')->paginate();

        return view('admin.sections.subscribers.index',compact(
            'page_title',
            'data',
        ));
    }

    /**
     * Mehtod for mail send for all subscriber
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request Response
     */
    public function emailSend(Request $request){

        $validator = Validator::make($request->all(),[
            'subject' => 'required|string|max:250',
            'message' => 'required|string|max:2000'
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput()->with('modal','subscriber-email-send');
        }

        $validated = $validator->validate();

        $emails = Subscriber::all();

        if($emails->isEmpty()){
            return back()->with(['error' => ['No subscribers found.']]);
        }

        try {
            Notification::send($emails, new SendMail((object) $validated));
        } catch (\Throwable $th) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Email sent successfully.']]);
    }

    /**
     * Mehtod for delete subscriber item
     * @method DELETE
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Request Response
     */
    public function delete(Request $request){
        $request->validate([
            'target'    => 'required|string',
        ]);

        $subscriber = Subscriber::findOrFail($request->target);

        try {
            $subscriber->delete();
        } catch (\Throwable $th) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Subscriber deleted successfully.']]);
    }


}
