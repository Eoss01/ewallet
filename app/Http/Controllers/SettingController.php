<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingController extends Controller
{
    public function run()
    {
        //
    }

    public function translate()
    {
        $language_en_json = base_path('lang/en.json');
        $language_cn_json = base_path('lang/cn.json');
        $language_bm_json = base_path('lang/bm.json');

        $existing_translations_en = json_decode(File::get($language_en_json), true);
        $existing_translations_cn = json_decode(File::get($language_cn_json), true);
        $existing_translations_bm = json_decode(File::get($language_bm_json), true);

        $new_translations = [];

        $pattern = '/__\((\'[^\']*\')\)/';

        $view_files = File::allFiles(base_path('resources/views'));

        foreach ($view_files as $view_file)
        {
            $contents = file_get_contents($view_file->getPathname());

            preg_match_all($pattern, $contents, $matches);

            foreach ($matches[1] as $match)
            {
                $translation_key = trim($match, "'");

                if (!isset($existing_translations_en[$translation_key]))
                {
                    $new_translations[$translation_key] = $translation_key;
                }

                if (!isset($existing_translations_cn[$translation_key]))
                {
                    $new_translations[$translation_key] = $translation_key;
                }

                if (!isset($existing_translations_bm[$translation_key]))
                {
                    $new_translations[$translation_key] = $translation_key;
                }
            }
        }

        $controller_files = File::allFiles(app_path('Http/Controllers'));

        foreach ($controller_files as $controller_file)
        {
            $contents = file_get_contents($controller_file->getPathname());

            preg_match_all($pattern, $contents, $matches);

            foreach ($matches[1] as $match)
            {
                $translation_key = trim($match, "'");

                if (!isset($existing_translations_en[$translation_key]))
                {
                    $new_translations[$translation_key] = $translation_key;
                }

                if (!isset($existing_translations_cn[$translation_key]))
                {
                    $new_translations[$translation_key] = $translation_key;
                }

                if (!isset($existing_translations_bm[$translation_key]))
                {
                    $new_translations[$translation_key] = $translation_key;
                }
            }
        }

        File::put(public_path('translations.json'), json_encode($new_translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function changeLanguage($locale)
    {
        App::setLocale($locale);

        Session::put("locale", $locale);

        return redirect()->back();
    }

    public function index()
    {
        $setting = Setting::latest()->first();

        return view('settings.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::firstWhere('cid', $request->setting_cid);

        if (!$setting)
        {
            return Redirect::back()->with('error', __('Setting not found!'))->withInput();
        }

        $request->validate([
            'rebate_percent' => 'required|numeric|min:1|regex:/^\d{1,13}(\.\d{1,2})?$/',
        ]);

        if ($request->version != $setting->version)
        {
            return redirect()->back()->with('error', __('Record has been updated by another user, please try again!'))->withInput();
        }

        $setting->update([
            'rebate_percent' => $request->rebate_percent,
        ]);

        return Redirect::route('settings.index')->with('success', __('Setting is updated.'));
    }
}
