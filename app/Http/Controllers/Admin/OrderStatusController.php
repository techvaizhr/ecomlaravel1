<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\Cache;
use Image;
use File;
use Toastr;

class OrderStatusController extends Controller
{
    public function index(Request $request)
    {
        $data = OrderStatus::withCount('orders')->orderBy('id','ASC')->get();
        return view('backEnd.orderstatus.index',compact('data'));
    }

    public function create()
    {
        return view('backEnd.orderstatus.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);
        $input = $request->all();
        $input['slug'] = strtolower(preg_replace('/\s+/u', '-', trim($request->name)));

        if (OrderStatus::where('slug', $input['slug'])->exists()) {
            $input['slug'] = $input['slug'] . '-' . time();
        }

        OrderStatus::create($input);
        $this->clearOrderStatusCache();

        Toastr::success('Success','Data insert successfully');
        return redirect()->route('orderstatus.index');
    }
    
    public function edit($id)
    {
        $edit_data = OrderStatus::find($id);
        return view('backEnd.orderstatus.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = OrderStatus::find($request->id);
        $input = $request->all();
        $input['status'] = $request->status ? 1 : 0;
        $input['slug'] = strtolower(preg_replace('/\s+/u', '-', trim($request->name)));

        if (OrderStatus::where('slug', $input['slug'])->where('id', '!=', $request->id)->exists()) {
            $input['slug'] = $input['slug'] . '-' . time();
        }

        $update_data->update($input);
        $this->clearOrderStatusCache();

        Toastr::success('Success','Data update successfully');
        return redirect()->route('orderstatus.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = OrderStatus::find($request->hidden_id);
        if ($inactive) {
            $inactive->status = 0;
            $inactive->save();
            $this->clearOrderStatusCache();
        }
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }

    public function active(Request $request)
    {
        $active = OrderStatus::find($request->hidden_id);
        if ($active) {
            $active->status = 1;
            $active->save();
            $this->clearOrderStatusCache();
        }
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $delete_data = OrderStatus::find($request->hidden_id);
        if ($delete_data) {
            $delete_data->delete();
            $this->clearOrderStatusCache();
        }
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }

    protected function clearOrderStatusCache()
    {
        Cache::forget('order_status_list');
        Cache::forget('order_statuses_list');
        Cache::forget('all_orders_count');
        Cache::forget('new_order_count');
        Cache::forget('pending_orders_list');
        Cache::forget('incomplete_orders_count');
    }
}
