<?php

namespace App\Http\Middleware;

use Closure;

class CheckApiLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 'en',       //  English         =>  English
        // 'hi',       //  Hindi           =>  हिंदी
        // 'ta',       //  Tamil           =>  தமிழ்
        // 'mr',       //  Marathi         =>  मराठी
        // 'bn',       //  Bengali         =>  বাংলা
        // 'gu',       //  Gujarati        =>  ગુજરાતી
        // 'kn',       //  Kannada         =>  ಕನ್ನಡ
        // 'ml',       //  Malayalam       =>  മലയാളം
        // 'bho',      //  Bhojpuri        =>  भोजपुरी
        // 'pa',       //  Punjabi         =>  पंजाबी
        // 'te',       //  Telugu          =>  తెలుగు

        $language_allowed  = ['en', 'hi', 'ta', 'mr', 'bn', 'gu', 'kn', 'ml', 'bho', 'pa', 'te'];
        $language = request()->header()['x-language'][0] ?? "en";
        $language = in_array($language, $language_allowed) ? $language : 'en';
        app()->setLocale($language);

        return $next($request);
    }
}
