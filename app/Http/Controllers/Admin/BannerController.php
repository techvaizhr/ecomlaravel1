<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use App\Models\BannerCategory;
use App\Models\Banner;
use Illuminate\Support\Facades\Cache;
use Toastr;
use File;

class BannerController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:banner-list|banner-create|banner-edit|banner-delete', ['only' => ['index','store']]);
         $this->middleware('permission:banner-create', ['only' => ['create','store']]);
         $this->middleware('permission:banner-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:banner-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = Banner::with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keyword')) {
            $query->where(function($q) use ($request) {
                $q->where('link', 'like', '%' . trim($request->keyword) . '%')
                  ->orWhereHas('category', function($sub) use ($request) {
                      $sub->where('name', 'like', '%' . trim($request->keyword) . '%');
                  });
            });
        }

        if (function_exists('apply_date_filter')) {
            apply_date_filter($query, $request, 'created_at');
        }

        $stats = [
            'total' => Banner::count(),
            'active' => Banner::where('status', 1)->count(),
            'inactive' => Banner::where('status', 0)->count(),
            'categories' => BannerCategory::count(),
        ];

        $categories = BannerCategory::orderBy('name', 'ASC')->select('id', 'name')->get();
        $per_page = $request->get('per_page', 15);
        $data = $query->orderBy('id', 'DESC')->paginate($per_page)->withQueryString();

        return view('backEnd.banner.index', compact('data', 'stats', 'categories'));
    }
    public function create()
    {
        $categories = BannerCategory::orderBy('id','DESC')->select('id','name')->get();
        return view('backEnd.banner.create',compact('categories'));
    }

    protected function clearHomepageCache(): void
    {
        Cache::forget('frontend_homepage_v1');
        Cache::forget('frontend_homepage_v2');
        Cache::forget('frontend_homepage_v3');
        Cache::forget('frontend_homepage_v4');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'link' => 'required',
            'status' => 'nullable',
        ]);

        $fileUrl = ImageOptimizer::storeBanner($request->file('image'), 'public/uploads/banner/');

        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $input['image'] = $fileUrl;
        Banner::create($input);

        $this->clearHomepageCache();

        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('banners.index');
    }
    
    public function edit($id)
    {
        $edit_data = Banner::find($id);
        $categories = BannerCategory::select('id', 'name')->get();
        return view('backEnd.banner.edit', compact('edit_data', 'categories'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'link' => 'required',
        ]);
        $update_data = Banner::findOrFail($request->id);
        $input = $request->all();
        if ($request->hasFile('image')) {
            $input['image'] = ImageOptimizer::storeBanner($request->file('image'), 'public/uploads/banner/');
            if ($update_data->image && File::exists(public_path($update_data->image))) {
                File::delete(public_path($update_data->image));
            }
        } else {
            $input['image'] = $update_data->image;
        }

        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);

        $this->clearHomepageCache();

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('banners.index');
    }
 
    public function inactive(Request $request)
    {
        $id = $request->hidden_id ?? $request->id;
        $inactive = Banner::find($id);
        if ($inactive) {
            $inactive->status = 0;
            $inactive->save();
            $this->clearHomepageCache();
            Toastr::success('Success', 'Banner deactivated successfully');
        } else {
            Toastr::error('Error', 'Banner not found');
        }
        return redirect()->back();
    }

    public function active(Request $request)
    {
        $id = $request->hidden_id ?? $request->id;
        $active = Banner::find($id);
        if ($active) {
            $active->status = 1;
            $active->save();
            $this->clearHomepageCache();
            Toastr::success('Success', 'Banner activated successfully');
        } else {
            Toastr::error('Error', 'Banner not found');
        }
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $id = $request->hidden_id ?? $request->id;
        $delete_data = Banner::find($id);
        if ($delete_data) {
            if ($delete_data->image && File::exists(public_path($delete_data->image))) {
                File::delete(public_path($delete_data->image));
            }
            $delete_data->delete();
            $this->clearHomepageCache();
            Toastr::success('Success', 'Banner deleted successfully');
        } else {
            Toastr::error('Error', 'Banner not found');
        }
        return redirect()->back();
    }
}
