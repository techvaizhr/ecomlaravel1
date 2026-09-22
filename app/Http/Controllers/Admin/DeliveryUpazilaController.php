<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryDistrict;
use App\Models\DeliveryUpazila;
use Illuminate\Http\Request;
use Toastr;

class DeliveryUpazilaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:shipping-list|shipping-create|shipping-edit|shipping-delete', ['only' => ['index']]);
        $this->middleware('permission:shipping-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:shipping-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:shipping-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request, $district)
    {
        $district  = DeliveryDistrict::with('division')->findOrFail($district);
        $query = DeliveryUpazila::query()->where('district_id', $district->id);

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        if ($request->filled('status') && $request->status !== '') {
            $query->where('status', (int)$request->status);
        }

        $query->orderBy('sort_order')->orderBy('name');

        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all' || $perPage == -1) {
            $perPage = max($query->count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $show_data = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total'    => DeliveryUpazila::where('district_id', $district->id)->count(),
            'active'   => DeliveryUpazila::where('district_id', $district->id)->where('status', 1)->count(),
            'inactive' => DeliveryUpazila::where('district_id', $district->id)->where('status', 0)->count(),
        ];

        return view('backEnd.delivery.upazila_index', compact('district', 'show_data', 'stats'));
    }

    public function create($district)
    {
        $district = DeliveryDistrict::with('division')->findOrFail($district);

        return view('backEnd.delivery.upazila_create', compact('district'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'district_id' => 'required|exists:districts,id',
            'name'        => 'required|string|max:190',
        ]);

        DeliveryUpazila::create([
            'district_id' => $request->district_id,
            'name'        => $request->name,
            'sort_order'  => (int) $request->input('sort_order', 0),
            'status'      => $request->boolean('status') ? 1 : 0,
        ]);

        Toastr::success('উপজেলা যোগ করা হয়েছে', 'সফল');

        return redirect()->route('admin.delivery.upazilas.index', $request->district_id);
    }

    public function edit($id)
    {
        $edit_data = DeliveryUpazila::with('district.division')->findOrFail($id);

        return view('backEnd.delivery.upazila_edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id'          => 'required|exists:upazilas,id',
            'district_id' => 'required|exists:districts,id',
            'name'        => 'required|string|max:190',
        ]);

        $row = DeliveryUpazila::findOrFail($request->id);
        $row->update([
            'district_id' => $request->district_id,
            'name'        => $request->name,
            'sort_order'  => (int) $request->input('sort_order', 0),
            'status'      => $request->boolean('status') ? 1 : 0,
        ]);

        Toastr::success('আপডেট হয়েছে', 'সফল');

        return redirect()->route('admin.delivery.upazilas.index', $request->district_id);
    }

    public function destroy(Request $request)
    {
        $row        = DeliveryUpazila::findOrFail($request->hidden_id);
        $districtId = $row->district_id;
        $row->delete();
        Toastr::success('মুছে ফেলা হয়েছে', 'সফল');

        return redirect()->route('admin.delivery.upazilas.index', $districtId);
    }
}
