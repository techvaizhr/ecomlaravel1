<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Arr;
use App\Models\User;
use Toastr;
use Image;
use File;
use DB;
use Hash;
use App\Support\ImageOptimizer;
class UserController extends Controller
{
    public function index(Request $request)
    {
        // Filter out Vendor and Reseller users - only show Admin/Staff users with eager loaded roles
        $query = User::with('roles')
            ->whereNull('vendor_id')
            ->where(function($q) {
                $q->where('role', '!=', 'reseller')
                  ->orWhereNull('role');
            })
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['vendor', 'reseller']);
            });

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', (int)$request->status);
        }

        if ($request->filled('role_id')) {
            $query->whereHas('roles', function($rq) use ($request) {
                $rq->where('roles.id', $request->role_id);
            });
        }

        $query->orderBy('id', 'DESC');

        $perPage = $request->get('per_page', 20);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max($query->count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $data = $query->paginate($perPage)->withQueryString();
        $roles = Role::where('guard_name', 'admin')->get();
        if ($roles->isEmpty()) {
            $roles = Role::get();
        }

        $baseCount = User::whereNull('vendor_id')
            ->where(function($q) {
                $q->where('role', '!=', 'reseller')->orWhereNull('role');
            })
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['vendor', 'reseller']);
            });

        $stats = [
            'total'       => (clone $baseCount)->count(),
            'active'      => (clone $baseCount)->where('status', 1)->count(),
            'inactive'    => (clone $baseCount)->where('status', 0)->count(),
            'roles_count' => $roles->count(),
        ];

        return view('backEnd.users.index', compact('data', 'roles', 'stats'));
    }
    
    public function create()
    {
        $roles = Role::where('guard_name', 'admin')->get();
        if ($roles->isEmpty()) {
            $roles = Role::get();
        }
        return view('backEnd.users.create',compact('roles'));
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = ImageOptimizer::storeProfile($request->file('image'), 'public/uploads/users/');
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['image'] = $imageUrl;
        
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('users.index');
    }
    
    public function edit($id)
    {
        $edit_data = User::find($id);
        $roles = Role::get();
        return view('backEnd.users.edit',compact('edit_data','roles'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$request->hidden_id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);
        
        $update_data = User::find($request->hidden_id);
        if (!$update_data) {
            Toastr::error('Error','Record not found');
            return redirect()->back();
        }

        // new password
        $input = $request->except('hidden_id');
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        // new image
        if ($request->hasFile('image')) {
            $input['image'] = ImageOptimizer::storeProfile($request->file('image'), 'public/uploads/users/');
            if (!empty($update_data->image) && file_exists(base_path($update_data->image))) {
                @unlink(base_path($update_data->image));
            } elseif (!empty($update_data->image)) {
                File::delete($update_data->image);
            }
        } else {
            $input['image'] = $update_data->image;
        }
        $input['status'] = $request->status?1:0;
        $update_data->update($input);

        // role asign
        DB::table('model_has_roles')->where('model_id',$request->hidden_id)->delete();
        $update_data->assignRole($request->input('roles'));
        Toastr::success('Success','Data update successfully');
        return redirect()->route('users.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = User::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = User::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {

        $delete_data = User::find($request->hidden_id);
        if($delete_data->id!=1){
           File::delete($delete_data->image);
            $delete_data->delete();
            Toastr::success('Success','Data delete successfully'); 
        }else{
            Toastr::success('error','Data delete unsuccessfully'); 
        }
        
        return redirect()->back();
    }
}
