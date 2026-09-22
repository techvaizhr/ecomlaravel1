<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Toastr;
use Image;
use File;
use DB;
use App\Support\ImageOptimizer;

class GeneralSettingController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:setting-list|setting-create|setting-edit|setting-delete', ['only' => ['index','store']]);
        $this->middleware('permission:setting-create', ['only' => ['create','store']]);
        $this->middleware('permission:setting-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:setting-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        // ✅ Single record pattern: redirect directly to edit/create without listing/loop
        $setting = GeneralSetting::orderBy('id', 'desc')->first();

        if ($setting) {
            return redirect()->route('settings.edit', $setting->id);
        }

        return redirect()->route('settings.create');
    }
    public function create()
    {
        return view('backEnd.settings.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',

			'fraud_api_key' => 'required',
			'copyright_color' => 'required',
			'primary_color' => 'required',
			'secodery_color' => 'required',
			'footer_color' => 'required',
			'facebook_page_username' => 'required',
			
            'white_logo' => 'required',
			'og_baner' => 'required',
            'favicon' => 'required',
            'status' => 'required',
        ]);

        // Process logos & banners with secure ImageOptimizer (WebP & size constraints)
        $imageUrl = ImageOptimizer::storeLogo($request->file('white_logo'), 'public/uploads/settings/');
        $image2Url = ImageOptimizer::storeLogo($request->file('dark_logo'), 'public/uploads/settings/');
        $image4Url = ImageOptimizer::storeBanner($request->file('og_baner'), 'public/uploads/settings/');
        $image3Url = ImageOptimizer::store($request->file('favicon'), 'public/uploads/settings/', null, 50, 128, 128);

        $input = $request->all();
        $input['white_logo'] = $imageUrl;
        $input['dark_logo'] = $image2Url;
        $input['favicon'] = $image3Url;
        $input['og_baner'] = $image4Url;
        
        $input['vendor_enabled'] = $request->has('vendor_enabled') ? 1 : 0;
        $input['reseller_enabled'] = $request->has('reseller_enabled') ? 1 : 0;
        $input['checkout_otp_enabled'] = $request->has('checkout_otp_enabled') ? 1 : 0;
        $input['news_ticker_enabled'] = $request->has('news_ticker_enabled') ? 1 : 0;
        $input['homepage_brands_enabled'] = $request->has('homepage_brands_enabled') ? 1 : 0;
        $input['homepage_vendors_enabled'] = $request->has('homepage_vendors_enabled') ? 1 : 0;
        $input['homepage_blogs_enabled'] = $request->has('homepage_blogs_enabled') ? 1 : 0;

        GeneralSetting::create($input);

        // APP_NAME sync
        if (!empty($input['name'])) {
            $this->updateEnvAppName($input['name']);
        }

        Toastr::success('Success','Data insert successfully');
        return redirect()->route('settings.index');
    }
    
    public function edit($id)
    {
        $edit_data = GeneralSetting::find($id);
        return view('backEnd.settings.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        $update_data = GeneralSetting::find($request->id);
        $input = $request->all();
        // new white logo (<= 100 KB WebP)
        if ($request->hasFile('white_logo')) {
            $input['white_logo'] = ImageOptimizer::storeLogo($request->file('white_logo'), 'public/uploads/settings/');
            if ($update_data->white_logo && file_exists(public_path($update_data->white_logo))) {
                @unlink(public_path($update_data->white_logo));
            }
        } else {
            $input['white_logo'] = $update_data->white_logo;
        }

        // new dark logo (<= 100 KB WebP)
        if ($request->hasFile('dark_logo')) {
            $input['dark_logo'] = ImageOptimizer::storeLogo($request->file('dark_logo'), 'public/uploads/settings/');
            if ($update_data->dark_logo && file_exists(public_path($update_data->dark_logo))) {
                @unlink(public_path($update_data->dark_logo));
            }
        } else {
            $input['dark_logo'] = $update_data->dark_logo;
        }

        // new OG banner (<= 300 KB WebP)
        if ($request->hasFile('og_baner')) {
            $input['og_baner'] = ImageOptimizer::storeBanner($request->file('og_baner'), 'public/uploads/settings/');
            if ($update_data->og_baner && file_exists(public_path($update_data->og_baner))) {
                @unlink(public_path($update_data->og_baner));
            }
        } else {
            $input['og_baner'] = $update_data->og_baner;
        }

        // new favicon image (<= 50 KB WebP)
        if ($request->hasFile('favicon')) {
            $input['favicon'] = ImageOptimizer::store($request->file('favicon'), 'public/uploads/settings/', null, 50, 128, 128);
            if ($update_data->favicon && file_exists(public_path($update_data->favicon))) {
                @unlink(public_path($update_data->favicon));
            }
        } else {
            $input['favicon'] = $update_data->favicon;
        }
        $input['status'] = 1;
        
        // Handle vendor_enabled and reseller_enabled (checkbox returns '1' if checked, null if unchecked)
        $input['vendor_enabled'] = $request->has('vendor_enabled') ? 1 : 0;
        $input['reseller_enabled'] = $request->has('reseller_enabled') ? 1 : 0;
        $input['checkout_otp_enabled'] = $request->has('checkout_otp_enabled') ? 1 : 0;
        $input['news_ticker_enabled'] = $request->has('news_ticker_enabled') ? 1 : 0;
        $input['homepage_brands_enabled'] = $request->has('homepage_brands_enabled') ? 1 : 0;
        $input['homepage_vendors_enabled'] = $request->has('homepage_vendors_enabled') ? 1 : 0;
        $input['homepage_blogs_enabled'] = $request->has('homepage_blogs_enabled') ? 1 : 0;

        $update_data->update($input);

        // APP_NAME sync: site title পরিবর্তন হলে .env আপডেট করো
        if (!empty($input['name'])) {
            $this->updateEnvAppName($input['name']);
        }

        Cache::forget('general_setting');
        Cache::forget('seo_settings');
        Cache::forget('frontend_homepage_v1');
        Cache::forget('frontend_homepage_v2');
        Cache::forget('frontend_homepage_v3');
        Cache::forget('side_categories');
        Cache::forget('menu_categories');
        Cache::forget('menu_categories_v4');
        Cache::forget('brands_list');
        Cache::forget('pages_top');
        Cache::forget('pages_right');
        Cache::forget('common_menu');

        Toastr::success('Settings updated successfully!', 'Success');
        return redirect()->route('settings.edit', $update_data->id);
    }
 
    public function inactive(Request $request)
    {
        $inactive = GeneralSetting::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = GeneralSetting::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = GeneralSetting::find($request->hidden_id);
        File::delete($delete_data->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }

    private function updateEnvAppName(string $name): void
    {
        $envPath = base_path('.env');
        if (!is_file($envPath) || !is_writable($envPath)) {
            return;
        }

        $content    = file_get_contents($envPath);
        $escapedName = str_contains($name, ' ') ? '"' . addslashes($name) . '"' : $name;

        if (preg_match('/^APP_NAME=.*/m', $content)) {
            $content = preg_replace('/^APP_NAME=.*/m', 'APP_NAME=' . $escapedName, $content);
        } else {
            $content .= "\nAPP_NAME=" . $escapedName;
        }

        file_put_contents($envPath, $content);

        // OPcache & config cache clear
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        try {
            \Artisan::call('config:clear');
        } catch (\Throwable $e) {
            // silent
        }
    }
}
