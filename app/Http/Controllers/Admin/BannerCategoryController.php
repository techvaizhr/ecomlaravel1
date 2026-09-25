<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerCategory;
use Illuminate\Support\Facades\Cache;
use Toastr;
class BannerCategoryController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:banner-category-list|banner-category-create|banner-category-edit|banner-category-delete', ['only' => ['index','store']]);
         $this->middleware('permission:banner-category-create', ['only' => ['create','store']]);
         $this->middleware('permission:banner-category-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:banner-category-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = BannerCategory::orderBy('id','DESC')->get();
        return view('backEnd.banner.category.index',compact('data'));
    }
    public function create()
    {
        $categories = BannerCategory::orderBy('id','DESC')->select('id','name')->get();
        return view('backEnd.banner.category.create',compact('categories'));
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
            'name' => 'required',
            'status' => 'required',
        ]);
        $input = $request->all();
        BannerCategory::create($input);
        $this->clearHomepageCache();
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('banner_category.index');
    }
    
    public function edit($id)
    {
        $edit_data = BannerCategory::find($id);
        return view('backEnd.banner.category.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = BannerCategory::findOrFail($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);

        $this->clearHomepageCache();
        Toastr::success('Success','Data update successfully');
        return redirect()->route('banner_category.index');
    }
 
    public function inactive(Request $request)
    {
        $id = $request->hidden_id ?? $request->id;
        $inactive = BannerCategory::find($id);
        if ($inactive) {
            $inactive->status = 0;
            $inactive->save();
            $this->clearHomepageCache();
            Toastr::success('Success','Data inactive successfully');
        }
        return redirect()->back();
    }

    public function active(Request $request)
    {
        $id = $request->hidden_id ?? $request->id;
        $active = BannerCategory::find($id);
        if ($active) {
            $active->status = 1;
            $active->save();
            $this->clearHomepageCache();
            Toastr::success('Success','Data active successfully');
        }
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $id = $request->hidden_id ?? $request->id;
        $delete_data = BannerCategory::find($id);
        if ($delete_data) {
            $delete_data->delete();
            $this->clearHomepageCache();
            Toastr::success('Success','Data delete successfully');
        }
        return redirect()->back();
    }
}
