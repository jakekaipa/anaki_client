<?php
namespace App\Http\Middleware\Admin;
use App\Constants\GlobalConst;
use App\Constants\LanguageConst;
use App\Models\Admin\Language;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            $default_language = Language::where('status',GlobalConst::ACTIVE)->first();
            $default_language_code = $default_language->code ?? LanguageConst::NOT_REMOVABLE;
            if(session()->has('locale')) {
                $locale = session()->get("locale");
            }else {
                $locale = $default_language_code;
            }
            App::setLocale($locale);
        }catch(Exception $e) {

        }

        return $next($request);
    }
}
