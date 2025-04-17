<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Str;
use App\Models\CategoryType;
use Illuminate\Http\Request;
use App\Models\Admin\Language;
use App\Models\Admin\WebJornal;
use App\Constants\LanguageConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\Validator;

class WebJornalController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::whereNot('code', LanguageConst::NOT_REMOVABLE)->get();
    }

    /**
     * Mehtod for show event page
     * @method GET
     * @return Illuminate\Http\Request Response
     */
    public function index()
    {
        $page_title = "Web Journal";
        $languages = $this->languages;
        $data = WebJornal::orderBy('id', 'desc')->paginate();
        return view('admin.sections.web-jornal.index', compact(
            'page_title',
            'languages',
            'data',
        ));
    }
    /**
     * Mehtod for store campaign item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'image'         => 'nullable|image|mimes:png,jpg,jpeg,svg,webp',
        ]);

        $details_field = [
            'details'     => "required|string"
        ];
        $title_filed = [
            'title'     => "required|string",
        ];
        $tags = [
            'tags'     => "required|array",
        ];

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal', 'web-jornal-add');
        }
        $validated = $validator->validate();

        // Multiple language data set
        $language_wise_desc = $this->contentValidate($request, $details_field);
        $language_wise_title = $this->contentValidate($request, $title_filed);
        $language_wise_tags = $this->contentValidate($request, $tags);

        $desc_data['language']  = $language_wise_desc;
        $title_data['language'] = $language_wise_title;
        $tag_data['language']   = $language_wise_tags;


        $validated['details']     = $desc_data;
        $validated['title']       = $title_data;
        $validated['tags']        = $tag_data;
        $validated['slug']        = Str::slug($title_data['language']['en']['title']);
        $validated['created_at']  = now();
        $validated['admin_id']    = Auth::user()->id;

        // Check Image File is Available or not
        if ($request->hasFile('image')) {
            $image = get_files_from_fileholder($request, 'image');
            $upload = upload_files_from_path_dynamic($image, 'web-jornal');
            $validated['image'] = $upload;
        }

        try {
            WebJornal::create($validated);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Web journal added successfully.']]);
    }

    /**
     * Mehtod for show event eidt page
     * @method GET
     * @return Illuminate\Http\Request Response
     */
    public function edit($id)
    {
        $page_title = "Web Journal Edit";
        $languages = $this->languages;
        $data = WebJornal::findOrFail($id);
        return view('admin.sections.web-jornal.edit', compact(
            'page_title',
            'languages',
            'data',
        ));
    }

    /**
     * Mehtod for update event
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function update(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'target'   => 'required',
            'image'    => 'nullable|image|mimes:png,jpg,jpeg,svg,webp',
        ]);

        $details_field = [
            'details'     => "required|string"
        ];
        $title_filed = [
            'title'     => "required|string",
        ];
        $tags = [
            'tags'     => "required|array",
        ];

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $validated = $validator->validate();

        $event = WebJornal::findOrFail($validated['target']);

        // Multiple language data set
        $language_wise_desc = $this->contentValidate($request, $details_field);
        $language_wise_title = $this->contentValidate($request, $title_filed);
        $language_wise_tags = $this->contentValidate($request, $tags);

        $desc_data['language']  = $language_wise_desc;
        $title_data['language'] = $language_wise_title;
        $tag_data['language']   = $language_wise_tags;


        $validated['details']    = $desc_data;
        $validated['title']      = $title_data;
        $validated['tags']       = $tag_data;
        $validated['slug']       = Str::slug($title_data['language']['en']['title']);
        $validated['created_at'] = now();
        $validated['admin_id']   = Auth::user()->id;

        // Check Image File is Available or not
        if ($request->hasFile('image')) {
            $image = get_files_from_fileholder($request, 'image');
            $upload = upload_files_from_path_dynamic($image, 'web-jornal', $event->image);
            $validated['image'] = $upload;
        }

        try {
            $event->update($validated);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return redirect()->route('admin.setup.sections.web-jornal.index')->with(['success' => ['Web journal updated successfully.']]);
    }

    /**
     * Mehtod for status update event
     * @method PUT
     * @param \Illuminate\Http\Request  $request
     */
    public function statusUpdate(Request $request) {
        $validator = Validator::make($request->all(),[
            'status'                    => 'required|boolean',
            'data_target'               => 'required|string',
        ]);
        if ($validator->stopOnFirstFailure()->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error,null,400);
        }
        $validated = $validator->safe()->all();
        $id = $validated['data_target'];

        $event = WebJornal::findOrFail($id);

        if(!$event) {
            $error = ['error' => ['Web jornal record not found in our system.']];
            return Response::error($error,null,404);
        }

        try{
            $event->update([
                'status' => ($validated['status'] == true) ? false : true,
            ]);
        }catch(Exception $e) {
            $error = ['error' => ['Something went worng!. Please try again.']];
            return Response::error($error,null,500);
        }

        $success = ['success' => ['Web jornal status updated successfully!']];
        return Response::success($success,null,200);
    }

    /**
     * Mehtod for delete event
     * @method PUT
     * @param \Illuminate\Http\Request  $request
     */
    public function delete(Request $request) {
        $request->validate([
            'target'    => 'required|string',
        ]);

        $event = WebJornal::findOrFail($request->target);

        try{
            $image_link = get_files_path('web-jornal') . '/' . $event->image;
            delete_file($image_link);
            $event->delete();
        }catch(Exception $e) {
            return back()->with(['error' => ['Something went wrong. Please try again.']]);
        }

        return back()->with(['success' => ['Event deleted successfully.']]);
    }

    /**
     * Method for get languages form record with little modification for using only this class
     * @return array $languages
     */
    public function languages()
    {
        $languages = Language::whereNot('code', LanguageConst::NOT_REMOVABLE)->select("code", "name")->get()->toArray();
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
    public function contentValidate($request, $basic_field_name, $modal = null)
    {
        $languages = $this->languages();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach ($request->all() as $input_name => $input_value) {
            foreach ($languages as $language) {
                $input_name_check = explode("_", $input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_", $input_name_check);
                if ($input_lang_code == $language['code']) {
                    if (array_key_exists($input_name_check, $basic_field_name)) {
                        $langCode = $language['code'];
                        if ($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        } else {
                            $validation_rules[$input_name] = str_replace("required", "nullable", $basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if ($modal == null) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        } else {
            $validator = Validator::make($request->all(), $validation_rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal", $modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }
}
