<?php

namespace App\Http\Controllers\Admin;

use App\Constants\LanguageConst;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\Admin\Language;
use App\Models\Admin\SiteSections;
use App\Models\FaqSection;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;

class SetupSectionsController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::whereNot('code',LanguageConst::NOT_REMOVABLE)->get();
    }

    /**
     * Register Sections with their slug
     * @param string $slug
     * @param string $type
     * @return string
     */
    public function section($slug,$type) {
        $sections = [
            'login-section'    => [
                'view'   => "loginView",
                'update' => "loginUpdate",
            ],
            'register-section'    => [
                'view'   => "registerView",
                'update' => "registerUpdate",
            ],
            'home_banner'    => [
                'view'   => "bannerView",
                'update' => "bannerUpdate",
            ],
            'fee-calculator'    => [
                'view'   => "feeCalculatorView",
                'update' => "feeCalculatorUpdate",
            ],
            'about_section'  => [
                'view'       => "aboutView",
                'update'     => "aboutUpdate",
                'itemStore'  => "aboutItemStore",
                'itemUpdate' => "aboutItemUpdate",
                'itemDelete' => "aboutItemDelete",
            ],
            'download-app'    => [
                'view'   => "downloadAppView",
                'update' => "downloadAppUpdate",
            ],
            'partner_section'  => [
                'view'       => "partnerView",
                'update'     => "partnerUpdate",
                'itemStore'  => "partnerItemStore",
                'itemUpdate' => "partnerItemUpdate",
                'itemDelete' => "partnerItemDelete",
            ],
            'how_it_work'  => [
                'view'       => "howItWorkView",
                'update'     => "howItWorkUpdate",
                'itemStore'  => "howItWorkItemStore",
                'itemUpdate' => "howItWorkItemUpdate",
                'itemDelete' => "howItWorkItemDelete",
            ],
            'service-section'  => [
                'view'       => "serviceSectionView",
                'update'     => "serviceSectionUpdate",
                'itemStore'  => "serviceSectionItemStore",
                'itemUpdate' => "serviceSectionItemUpdate",
                'itemDelete' => "serviceSectionItemDelete",
            ],
            'overview_section'  => [
                'view'       => "overviewView",
                'update'     => "overviewUpdate",
                'itemStore'  => "overviewItemStore",
                'itemUpdate' => "overviewItemUpdate",
                'itemDelete' => "overviewItemDelete",
            ],
            'why_chose_us_section'  => [
                'view'       => "whyChoseUsView",
                'update'     => "whyChoseUsUpdate",
                'itemStore'  => "whyChoseUsItemStore",
                'itemUpdate' => "whyChoseUsItemUpdate",
                'itemDelete' => "whyChoseUsItemDelete",
            ],
            'testimonial_section'  => [
                'view'       => "testimonialView",
                'update'     => "testimonialUpdate",
                'itemStore'  => "testimonialItemStore",
                'itemUpdate' => "testimonialItemUpdate",
                'itemDelete' => "testimonialItemDelete",
            ],
             'contact'    => [
                'view'   => "contactView",
                'update' => "contactUpdate",
            ],
            'footer-section'  => [
                'view'       => "footerView",
                'update'     => "footerUpdate",
                'itemStore'  => "footerItemStore",
                'itemUpdate' => "footerItemUpdate",
                'itemDelete' => "footerItemDelete",
            ],
            'faq-section'    => [
                'view'       => "faqView",
                'update'     => "faqUpdate",
                'itemStore'  => "faqItemStore",
                'itemUpdate' => "faqItemUpdate",
                'itemDelete' => "faqItemDelete",
            ],

        ];

        if(!array_key_exists($slug,$sections)) abort(404);
        if(!isset($sections[$slug][$type])) abort(404);
        $next_step = $sections[$slug][$type];
        return $next_step;
    }

    /**
     * Method for getting specific step based on incomming request
     * @param string $slug
     * @return method
     */
    public function sectionView($slug) {
        $section = $this->section($slug,'view');
        return $this->$section($slug);
    }

    /**
     * Method for distribute store method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemStore(Request $request, $slug) {
        $section = $this->section($slug,'itemStore');
        return $this->$section($request,$slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemUpdate(Request $request, $slug) {
        $section = $this->section($slug,'itemUpdate');
        return $this->$section($request,$slug);
    }

    /**
     * Method for distribute delete method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemDelete(Request $request,$slug) {
        $section = $this->section($slug,'itemDelete');
        return $this->$section($request,$slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionUpdate(Request $request,$slug) {
        $section = $this->section($slug,'update');
        return $this->$section($request,$slug);
    }
    //========================LOGIN SECTION  Section Start============================
    public function loginView($slug) {
        $page_title = __("Login Section");
        $section_slug = Str::slug(SiteSectionConst::LOGIN_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.login-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function loginUpdate(Request $request,$slug) {
        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::LOGIN_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    //========================LOGIN SECTION  Section End============================
    //========================REGISTER SECTION  Section Start============================
    public function registerView($slug) {
        $page_title = __("Register Section");
        $section_slug = Str::slug(SiteSectionConst::REGISTER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.register-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function registerUpdate(Request $request,$slug) {
        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::REGISTER_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        $validated['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $validated;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    //========================REGISTER SECTION  Section End============================

    /**
     * Mehtod for show banner section page
     * @param string $slug
     * @return view
     */
    public function bannerView($slug) {
        $page_title = __("Home Banner Section");
        $section_slug = Str::slug(SiteSectionConst::HOME_BANNER);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.home-banner',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Mehtod for update banner section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function bannerUpdate(Request $request,$slug) {

        $validator = Validator::make($request->all(),[
            'video_link'      => 'required|string|max:255',
            'button_link'      => 'required|string|max:255',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','category-add');
        }

        $validated = $validator->validate();

        $basic_field_name = ['heading' => "required|string|max:100",'sub_heading' => "required|string|max:255",'button_name' => "required|string|max:50", 'title' => "required|string|max:100", 'box_title' => "required|string|max:100", 'box_value' => "required|string|max:100"];

        $slug = Str::slug(SiteSectionConst::HOME_BANNER);
        $section = SiteSections::where("key",$slug)->first();


        $data['images']['banner_image'] = $section->value->images->banner_image ?? "";
        $data['images']['image'] = $section->value->images->image ?? "";
        if($request->hasFile("banner_image")) {
            $data['images']['banner_image']      = $this->imageValidate($request,"banner_image",$section->value->images->banner_image ?? null);
        }
        if($request->hasFile("image")) {
            $data['images']['image']      = $this->imageValidate($request,"image",$section->value->images->image ?? null);
        }



        $data['language']     = $this->contentValidate($request,$basic_field_name);
        $data['video_link']   = $validated['video_link'];
        $data['button_link']   = $validated['button_link'];
        $update_data['value'] = $data;
        $update_data['key']   = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }

    //========================Fee Calculator Section End============================
    /**
     * Mehtod for show banner section page
     * @param string $slug
     * @return view
     */
    public function feeCalculatorView($slug) {
        $page_title = __("Fee Calculator Section");
        $section_slug = Str::slug(SiteSectionConst::FEE_CALCULATOR_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.fee-calculator-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    /**
     * Mehtod for update banner section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function feeCalculatorUpdate(Request $request,$slug) {

        $basic_field_name = ['heading' => "required|string|max:100"];

        $slug = Str::slug(SiteSectionConst::FEE_CALCULATOR_SECTION);
        $section = SiteSections::where("key",$slug)->first();


        $data['images']['banner_image'] = $section->value->images->banner_image ?? "";
        if($request->hasFile("banner_image")) {
            $data['images']['banner_image']      = $this->imageValidate($request,"banner_image",$section->value->images->banner_image ?? null);
        }



        $data['language']     = $this->contentValidate($request,$basic_field_name);
        $update_data['value'] = $data;
        $update_data['key']   = $slug;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    //========================Fee Calculator Section End============================
    /**
     * Mehtod for show solutions section page
     * @param string $slug
     * @return view
     */
    public function aboutView($slug) {
        $page_title = __("About Section");
        $section_slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.about-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Mehtod for update solutions section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUpdate(Request $request,$slug) {
        $basic_field_name = [
            'title'        => "required|string|max:100",
            'heading'      => "required|string|max:100",
            'experience'   => "required|integer",
            'feedback'     => "required|integer",
            'contributors' => "required|integer",
            'sub_heading'  => "required|string|max:450",
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['images']['image_one'] = $section->value->images->image_one ?? "";

        if($request->hasFile("image_one")) {
            $data['images']['image_one']      = $this->imageValidate($request,"image_one",$section->value->images->image_one ?? null);
        }

        $data['images']['image_two'] = $section->value->images->image_two ?? "";
        if($request->hasFile("image_two")) {
            $data['images']['image_two']      = $this->imageValidate($request,"image_two",$section->value->images->image_two ?? null);
        }

        $data['images']['image_three'] = $section->value->images->image_three ?? "";
        if($request->hasFile("image_three")) {
            $data['images']['image_three']      = $this->imageValidate($request,"image_three",$section->value->images->image_three ?? null);
        }

        $data['images']['image_four'] = $section->value->images->image_four ?? "";
        if($request->hasFile("image_four")) {
            $data['images']['image_four']      = $this->imageValidate($request,"image_four",$section->value->images->image_four ?? null);
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }

    /**
     * Mehtod for store solution item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutItemStore(Request $request,$slug) {
        $basic_field_name = [
            'title'     => "required|string|max:255"
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"about-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['About item added successfully.']]);
    }

    /**
     * Mehtod for update about item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutItemUpdate(Request $request,$slug) {
        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255"
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);


        $language_wise_data = $this->contentValidate($request,$basic_field_name,"about-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Information updated successfully.']]);
    }

    /**
     * Mehtod for delete about item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::ABOUT_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);
        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return  $e->getMessage();
        }

        return back()->with(['success' => ['About item deleted successfully.']]);
    }
    //=======================About  Section End===================================
    //=======================Download App Section Start============================
    public function downloadAppView($slug) {
        $page_title = __("Download App Section");
        $section_slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.download-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function downloadAppUpdate(Request $request,$slug) {


        $validator = Validator::make($request->all(),[
            'button_link'      => 'required|string|max:255',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','category-add');
        }

        $validated = $validator->validate();

        $basic_field_name = [
            'title'           => "required|string|max:100",
            'heading'         => "required|string|max:100",
            'sub_heading'     => "required|string|max:255",
            'button_name'     => "required|string|max:100",
            'heading_two'     => "required|string|max:100",
            'sub_heading_two' => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::DOWNLOAD_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $data['button_link']  = $validated['button_link'];

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    //=======================Download App Section End=========================
    //=======================Top Partnet Section Start=============================
    public function partnerView($slug) {
        $page_title = __("Top Partner Section");
        $section_slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.partner-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Mehtod for update solutions section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function partnerUpdate(Request $request,$slug) {
        $basic_field_name = ['heading' => "required|string|max:100", 'title' => 'required|string|max:100'];
        $slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $section_data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;
        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Partner section updated successfully.']]);
    }

    /**
     * Mehtod for store solution item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function partnerItemStore(Request $request,$slug) {
        $request->validate([
            'image' => "required|image|mimes:png,jpg,webp,jpeg,svg"
        ]);


        $slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['image'] = "";
        if($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request,"image",$section->value->items->image ?? null);
        }
        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item added successfully.']]);
    }

    /**
     * Mehtod for update solution item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function partnerItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'    => "required|string",
            'image' => "required|image|mimes:png,jpg,webp,jpeg,svg"
        ]);

        $slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);
        $request->merge(['old_image' => $section_values['items'][$request->target]['image'] ?? null]);
        if($request->hasFile("image")) {
            $section_values['items'][$request->target]['image']    = $this->imageValidate($request,"image",$section_values['items'][$request->target]['image'] ?? null);
        }

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Information updated successfully.']]);
    }

    /**
     * Mehtod for delete solution item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function partnerItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::TOP_PARTNER);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        try{
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            delete_file($image_link);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item deleted successfully.']]);
    }
    //=======================Top Partner Section End===============================
    //=======================testimonial Section End===============================

    public function testimonialView($slug) {
        $page_title = __("Testimonial Section");
        $section_slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.testimonial-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function testimonialUpdate(Request $request,$slug) {

        $validator = Validator::make($request->all(), [
            'rating'       => "required|integer|max:5",
            'total_review' => "required|integer",
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        $basic_field_name = [
            'title'        => "required|string|max:100",
            'heading'      => "required|string|max:100",
            'sub_heading'  => "required|string|max:255",
            'review_title' => "required|string|max:100",
        ];

        $slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        $data['images']['image'] = $section->value->images->image ?? "";
        if($request->hasFile("image")) {
            $data['images']['image']      = $this->imageValidate($request,"image",$section->value->images->image ?? null);
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $data['rating']       = $validated['rating'] > 5 ? 5 : $validated['rating'];
        $data['total_review'] = $validated['total_review'];

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    public function testimonialItemStore(Request $request,$slug) {

        $validator = Validator::make($request->all(), [
            'review_rating'       => "required|integer|max:5",
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput()->with('modal', 'testimonial-add');
        }

        $validated = $validator->validated();

        $basic_field_name = [
            'name'        => "required|string|max:100",
            'designation' => "required|string|max:100",
            'details'     => "required|string|max:450",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"testimonial-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['review_rating'] = $validated['review_rating'];
        $section_data['items'][$unique_id]['image'] = "";

        if($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request,"image",$section->value->items->image ?? null);
        }

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item added successfully.']]);
    }
    public function testimonialItemUpdate(Request $request,$slug) {


        $validator = Validator::make($request->all(), [
            'review_rating_edit' => "required|integer|max:5",
            'target'             => "required|string",
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput()->with('modal', 'testimonial-edit');
        }

        $validated = $validator->validated();

        $basic_field_name = [
            'name_edit'        => "required|string|max:100",
            'designation_edit' => "required|string|max:100",
            'details_edit'     => "required|string|max:450",
        ];

        $slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        $request->merge(['old_image' => $section_values['items'][$request->target]['image'] ?? null]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"testimonial-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);



        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['review_rating'] = $validated['review_rating_edit'];

        if($request->hasFile("image")) {
            $section_values['items'][$request->target]['image']    = $this->imageValidate($request,"image",$section_values['items'][$request->target]['image'] ?? null);
        }

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Information updated successfully.']]);
    }

    public function testimonialItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::TESTIMONIAL_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        try{
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            delete_file($image_link);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item deleted successfully.']]);
    }
    //=======================testimonial Section End===============================

    //=======================How it work Section Start===============================
    public function howItWorkView($slug) {
        $page_title = __("How It Work Section");
        $section_slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.how-it-work',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function howItWorkUpdate(Request $request,$slug) {
        $basic_field_name = [
            'title' => "required|string|max:100",
            'heading' => "required|string|max:100"
        ];

        $slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    public function howItWorkItemStore(Request $request,$slug) {

        $validator = Validator::make($request->all(), [
            'icon' => 'required|string'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','how-it-work-add');
        }

        $validated = $validator->validate();

        $basic_field_name = [
            'name'     => "required|string|max:100",
            'details'   => "required|string|max:255",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"how-it-work-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item added successfully.']]);
    }
    public function howItWorkItemUpdate(Request $request,$slug) {

        $validator = Validator::make($request->all(), [
            'icon_edit' => 'required|string'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','how-it-work-edit');
        }

        $validated = $validator->validate();


        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'name_edit'     => "required|string|max:100",
            'details_edit'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);

        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"how-it-work-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);


        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon'] = $validated['icon_edit'];

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Information updated successfully.']]);
    }
    public function howItWorkItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::HOW_IT_WORK_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item deleted successfully.']]);
    }
    //=======================How it work Section End===============================

    //=======================Overview Section Start===============================
    public function overviewView($slug) {
        $page_title = __("Overview Section");
        $section_slug = Str::slug(SiteSectionConst::OVERVIEW_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        // dd($data);

        return view('admin.sections.setup-sections.overview',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function overviewUpdate(Request $request,$slug) {

        $validator = Validator::make($request->all(), [
            'button_link' => 'required|string'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validate();

        $basic_field_name = [
            'title'              => "required|string|max:100",
            'heading'            => "required|string|max:100",
            'sub_heading_top'    => "required|string|max:255",
            'sub_heading_bottom' => "required|string|max:255",
            'button_name'        => "required|string|max:100",
        ];

        $slug = Str::slug(SiteSectionConst::OVERVIEW_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        $data['images']['banner_image'] = $section->value->images->banner_image ?? "";
        if($request->hasFile("banner_image")) {
            $data['images']['banner_image']      = $this->imageValidate($request,"banner_image",$section->value->images->banner_image ?? null);
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $data['button_link'] = $validated['button_link'];

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    public function overviewItemStore(Request $request,$slug) {

        $basic_field_name = [
            'name'     => "required|string|max:100",
            'value'   => "required|string|max:100",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"overview-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::OVERVIEW_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item added successfully.']]);
    }
    public function overviewItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'name_edit'     => "required|string|max:100",
            'value_edit'   => "required|string|max:100",
        ];

        $slug = Str::slug(SiteSectionConst::OVERVIEW_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);

        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"overview-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);


        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Information updated successfully.']]);
    }
    public function overviewItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::OVERVIEW_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item deleted successfully.']]);
    }
    //=======================Overview Section End===============================

    //======================= Why Chose Us Section Start===============================
    public function whyChoseUsView($slug) {
        $page_title = __("Why Chose Us Section");
        $section_slug = Str::slug(SiteSectionConst::WHY_CHOSE_US_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.why-chose-us-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function whyChoseUsUpdate(Request $request,$slug) {

        $basic_field_name = [
            'title'              => "required|string|max:100",
            'heading'            => "required|string|max:100",
            'sub_heading'    => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::WHY_CHOSE_US_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    public function whyChoseUsItemStore(Request $request,$slug) {

        $validator = Validator::make($request->all(), [
            'icon'    => "required|string|max:100",
        ]);

        if($validator->fails()){
            return back()->withErrors($validator)->withInput()->with('modal', 'why-chose-us-add');
        }

        $validated = $validator->validated();

        $basic_field_name = [
            'name'    => "required|string|max:100",
            'details' => "required|string|max:255",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"why-chose-us-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::WHY_CHOSE_US_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item added successfully.']]);
    }
    public function whyChoseUsItemUpdate(Request $request,$slug) {

        $validator = Validator::make($request->all(), [
            'icon_edit' => 'required|string'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','why-chose-us-edit');
        }

        $validated = $validator->validate();


        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'name_edit'     => "required|string|max:100",
            'details_edit'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::WHY_CHOSE_US_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);

        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"why-chose-us-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);


        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon'] = $validated['icon_edit'];

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Information updated successfully.']]);
    }
    public function whyChoseUsItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::WHY_CHOSE_US_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item deleted successfully.']]);
    }
    //======================= Why Chose Us Section End===============================

    //=======================Service Section Start===============================
    public function serviceSectionView($slug) {
        $page_title = __("Service Section");
        $section_slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.service-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function serviceSectionUpdate(Request $request,$slug) {
        $basic_field_name = [
            'title' => "required|string|max:100",
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:255"
        ];

        $slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }

        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    public function serviceSectionItemStore(Request $request,$slug) {

        $validator = Validator::make($request->all(), [
            'icon' => 'required|string'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','service-section-add');
        }

        $validated = $validator->validate();

        $basic_field_name = [
            'name'     => "required|string|max:100",
            'details'   => "required|string|max:255",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"service-section-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;
        $section_data['items'][$unique_id]['icon'] = $validated['icon'];

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item added successfully.']]);
    }
    public function serviceSectionItemUpdate(Request $request,$slug) {

        $validator = Validator::make($request->all(), [
            'icon_edit' => 'required|string'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','service-section-edit');
        }

        $validated = $validator->validate();


        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'name_edit'     => "required|string|max:100",
            'details_edit'   => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);

        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"service-section-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);


        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon'] = $validated['icon_edit'];

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Information updated successfully.']]);
    }
    public function serviceSectionItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::SERVICE_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item deleted successfully.']]);
    }
    //=======================Service Section End===============================

    //======================= work Section Start===============================

    public function contactView($slug) {
        $page_title = __("Contact Section");
        $section_slug = Str::slug(SiteSectionConst::CONTACT_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.contact-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function contactUpdate(Request $request,$slug) {

        $basic_field_name = [
            'title'        => "required|string|max:100",
            'heading'      => "required|string|max:100",
            'location'     => "required|string|max:255",
            'phone'        => "required|string",
            'office_hours' => "required|string|max:255",
            'email'        => "required|string|max:100",
        ];

        $slug = Str::slug(SiteSectionConst::CONTACT_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        $data['language']  = $this->contentValidate($request,$basic_field_name);
        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    //=======================Download App Section End==============================

      //=======================footer Section End===============================

    public function  footerView($slug) {
        $page_title = __("Footer Section");
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $data = SiteSections::getData($section_slug)->first();

        $languages = $this->languages;

        return view('admin.sections.setup-sections.footer-section',compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    public function  footerUpdate(Request $request,$slug) {
        $basic_field_name = [
            'footer_text' => "required|string|max:100",
            'newsltter_details' => "required|string"
        ];

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }
    public function  footerItemStore(Request $request,$slug) {
        $basic_field_name = [
            'name'     => "required|string|max:100",
            'social_icon'   => "required|string|max:255",
            'link'   => "required|string|url|max:255",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"icon-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item added successfully.']]);
    }
    public function  footerItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'name_edit'     => "required|string|max:100",
            'social_icon_edit'   => "required|string|max:255",
            'link_edit'   => "required|string|url|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"icon-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Information updated successfully.']]);
    }

    public function footerItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item deleted successfully.']]);
    }

    //=======================footer Section End===============================

    //=======================Faq  Section Start=======================
    public function faqView($slug){
        $page_title = __("Setup FAQ");
        $allFaq = FaqSection::orderByDesc('id')->get();

        $section_slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $data = SiteSections::getData($section_slug)->first();

        $languages = $this->languages;

        return view('admin.sections.faq.index',compact(
            'page_title',
            'allFaq',
            'slug',
            'languages',
            'data',
        ));
    }
    public function  faqUpdate(Request $request,$slug) {
        $basic_field_name = [
            'title'        => "required|string|max:100",
            'heading'      => "required|string|max:100",
            'sub_heading'  => "required|string|max:255",
        ];
        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::where("key",$slug)->first();
        if($section != null) {
            $data = json_decode(json_encode($section->value),true);
        }else {
            $data = [];
        }
        $data['language']  = $this->contentValidate($request,$basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully.']]);
    }

    public function faqItemStore(Request $request,$slug) {

        $basic_field_name = [
            'question' => "required|string|max:100",
            'answer'   => "required|string|max:400",
        ];

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"faq-add");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::where("key",$slug)->first();

        if($section != null) {
            $section_data = json_decode(json_encode($section->value),true);
        }else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try{
            SiteSections::updateOrCreate(['key' => $slug],$update_data);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item added successfully.']]);
    }
    public function faqItemUpdate(Request $request,$slug) {

        $request->validate([
            'target'    => "required|string",
        ]);

        $basic_field_name = [
            'question_edit'     => "required|string|max:100",
            'answer_edit'   => "required|string|max:450",
        ];

        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);

        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);

        $language_wise_data = $this->contentValidate($request,$basic_field_name,"faq-edit");
        if($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function($language) {
            return replace_array_key($language,"_edit");
        },$language_wise_data);


        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try{
            $section->update([
                'value' => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Information updated successfully.']]);
    }
    public function faqItemDelete(Request $request,$slug) {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::getData($slug)->first();
        if(!$section) return back()->with(['error' => ['Section not found.']]);
        $section_values = json_decode(json_encode($section->value),true);
        if(!isset($section_values['items'])) return back()->with(['error' => ['Section item not found.']]);
        if(!array_key_exists($request->target,$section_values['items'])) return back()->with(['error' => ['Section item is invalid.']]);
        try{
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Section item deleted successfully.']]);
    }


//=======================Faq Section End=======================


    /**
     * Method for get languages form record with little modification for using only this class
     * @return array $languages
     */
    public function languages() {
        $languages = Language::whereNot('code',LanguageConst::NOT_REMOVABLE)->select("code","name")->get()->toArray();
        $languages[] = [
            'name'      => LanguageConst::NOT_REMOVABLE_CODE,
            'code'      => LanguageConst::NOT_REMOVABLE,
        ];
        return $languages;
    }

    /**
     * Method for validate request data and re-decorate language wise data
     * @param object $request
     * @param array $basic_field_name
     * @return array $language_wise_data
     */
    public function contentValidate($request,$basic_field_name,$modal = null) {
        $languages = $this->languages();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach($request->all() as $input_name => $input_value) {
            foreach($languages as $language) {
                $input_name_check = explode("_",$input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_",$input_name_check);
                if($input_lang_code == $language['code']) {
                    if(array_key_exists($input_name_check,$basic_field_name)) {
                        $langCode = $language['code'];
                        if($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        }else {
                            $validation_rules[$input_name] = str_replace("required","nullable",$basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if($modal == null) {
            $validated = Validator::make($request->all(),$validation_rules)->validate();
        }else {
            $validator = Validator::make($request->all(),$validation_rules);
            if($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal",$modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }

    /**
     * Method for validate request image if have
     * @param object $request
     * @param string $input_name
     * @param string $old_image
     * @return boolean|string $upload
     */
    public function imageValidate($request,$input_name,$old_image) {
        if($request->hasFile($input_name)) {
            $image_validated = Validator::make($request->only($input_name),[
                $input_name         => "image|mimes:png,jpg,webp,jpeg,svg",
            ])->validate();
            $image = get_files_from_fileholder($request,$input_name);
            $upload = upload_files_from_path_dynamic($image,'site-section',$old_image);
            return $upload;
        }

        return false;
    }

}
