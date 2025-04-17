<?php

namespace App\Http\Controllers;


use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;
use App\Models\Forexcrow;
use App\Models\FaqSection;
use App\Models\Subscriber;
use App\Models\Admin\Event;
use Illuminate\Support\Str;
use App\Models\CategoryType;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Admin\Currency;
use App\Models\Admin\Language;
use App\Models\Admin\SetupPage;
use App\Models\Admin\WebJornal;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use Illuminate\Support\Facades\Session;
use App\Models\Admin\TransactionSetting;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;

class SiteController extends Controller
{
    public function home(Request $request)
    {


        $basic_settings    = BasicSettingsProvider::get();
        $page_title        = $basic_settings->site_title ?? "Home";
        $section_slug      = Str::slug(SiteSectionConst::HOME_BANNER);
        $homeBanner        = SiteSections::getData($section_slug)->first();
        $download_slug     = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $download          = SiteSections::getData($download_slug)->first();
        $how_it_work_slug  = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $how_it_work       = SiteSections::getData($how_it_work_slug)->first();
        $overview_slug     = Str::slug(SiteSectionConst::OVERVIEW_SECTION);
        $overview          = SiteSections::getData($overview_slug)->first();
        $why_chose_us_slug = Str::slug(SiteSectionConst::WHY_CHOSE_US_SECTION);
        $why_chose_us      = SiteSections::getData($why_chose_us_slug)->first();
        $testimonial_slug  = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $testimonial       = SiteSections::getData($testimonial_slug)->first();
        $partner_slug      = Str::slug(SiteSectionConst::TOP_PARTNER);
        $partner           = SiteSections::getData($partner_slug)->first();
        $footer_slug       = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $footer            = SiteSections::getData($footer_slug)->first();
        $excrows = Forexcrow::with('saleCurrency', 'rateCurrency', 'user')
            ->whereHas('user', function ($q) {
                $q->where('status', 1);
            })
            ->orderBy('id', 'desc')
            ->where('status', 1)->take(8)->get();

        return view('frontend.index', compact(
            'page_title',
            'homeBanner',
            'download',
            'how_it_work',
            'overview',
            'why_chose_us',
            'testimonial',
            'partner',
            'footer',
            'excrows',
        ));
    }
    public function services()
    {
        $page_title = "Services";
        $service_slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $service = SiteSections::getData($service_slug)->first();
        return view('frontend.services', compact('page_title', 'service'));
    }
    public function aboutUs()
    {
        $page_title = "About Us";
        $section_slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $about = SiteSections::getData($section_slug)->first();
        return view('frontend.about-us', compact('page_title', 'about'));
    }

    public function faqs()
    {
        $page_title = "Faq";
        $faq_slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $faq = SiteSections::getData($faq_slug)->first();
        return view('frontend.faqs', compact('page_title', 'faq'));
    }

    public function webJournal()
    {
        $page_title = "Web Journal";
        $recent_journals = WebJornal::where('status', 1)->orderBy('id', 'desc')->limit(3)->get();
        $journals = WebJornal::where('status', 1)->paginate(6);
        return view('frontend.web-journal', compact(
            'page_title',
            'journals',
            'recent_journals',
        ));
    }
    public function webJournalDetails($id, $slug)
    {
        $page_title = "Web Journals Details";
        $recent_journals = WebJornal::where('status', 1)->orderBy('id', 'desc')->limit(3)->get();
        $journal = WebJornal::findOrFail($id);
        return view('frontend.web-jornal-details', compact(
            'page_title',
            'journal',
            'recent_journals',
        ));
    }
    public function contactUs()
    {
        $page_title = "Contact";
        $section_slug = Str::slug(SiteSectionConst::CONTACT_SECTION);
        $contact_us = SiteSections::getData($section_slug)->first();

        return view('frontend.contact-us', compact('page_title', 'contact_us'));
    }
    public function feeCalculator()
    {
        $page_title = "Fee Calculator";
        $section_slug = Str::slug(SiteSectionConst::FEE_CALCULATOR_SECTION);
        $fee_calculator = SiteSections::getData($section_slug)->first();

        $currencies = Currency::orderBy('default', 'ASC')->get();
        $default_currency = Currency::default();
        $amount = 0.00;

        $charges = TransactionSetting::where('slug', 'my_excrow')->first();
        $intervals = $charges->intervals;

        return view('frontend.fee-calculator', compact('page_title', 'fee_calculator', 'currencies', 'default_currency', 'intervals'));
    }
    public function pageView($slug)
    {
        $defualt = get_default_language_code() ?? 'en';
        $page = SetupPage::where('slug', $slug)->where('status', 1)->first();
        if (empty($page)) {
            abort(404);
        }
        $page_title = $page->title->language->$defualt->title;

        return view('frontend.page', compact('page_title', 'page'));
    }

    /**
     * This method for store subscriber
     * @method POST
     * @return Illuminate\Http\Request Response
     * @param Illuminate\Http\Request $request
     */
    public function subscriber(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'email' => 'email|unique:subscribers,email'
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                $error = ['errors' => $validator->errors()];
                return Response::error($error, null, 404);
            }

            $validated = $validator->safe()->all();

            try {
                Subscriber::create($validated);
            } catch (Exception $e) {
                $error = ['error' => ['Something went worng!. Please try again.']];
                return Response::error($error, null, 500);
            }

            $success = ['success' => ['Your email added to our newsletter!']];
            return Response::success($success, null, 200);
        }
    }

    /**
     * This method for store subscriber
     * @method POST
     * @return Illuminate\Http\Request Response
     * @param Illuminate\Http\Request $request
     */
    public function contactStore(Request $request)
    {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'name'    => 'required|string',
                'email'   => 'required|email',
                'message' => 'required|string',
            ]);

            if ($validator->stopOnFirstFailure()->fails()) {
                $error = ['errors' => $validator->errors()];
                return Response::error($error, null, 500);
            }

            $validated = $validator->safe()->all();

            try {
                Contact::create($validated);
            } catch (\Exception $th) {
                $error = ['error' => 'Something went worng!. Please try again.'];
                return Response::error($error, null, 500);
            }

            $success = ['success' => ['Your message submited!']];
            return Response::success($success, null, 200);
        }
    }

    public function cookieAccept()
    {
        session()->put('cookie_accepted', true);
        return response()->json('Cookie allow successfully');
    }
    public function cookieDecline()
    {
        session()->put('cookie_decline', true);
        return response()->json('Cookie decline successfully');
    }

    public function languageSwitch(Request $request)
    {
        $code = $request->target;
        Log::info('code= '.$code);
        $result =0;
        $language = Language::where("code", $code)->first();
        if (!$language) {
           $result = -1;
        }
        
        Session::put('locale', $code);
        return $result;

        // return back()->with(['success' => ['Language switched successfully.']]);
    }
}
