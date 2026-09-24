<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryDistrict;
use App\Models\DeliveryDivision;
use Illuminate\Http\Request;
use Toastr;

class DeliveryDistrictController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:shipping-list|shipping-create|shipping-edit|shipping-delete', ['only' => ['index']]);
        $this->middleware('permission:shipping-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:shipping-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:shipping-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request, $division)
    {
        $division  = DeliveryDivision::findOrFail($division);

        if ($request->has('restore') || DeliveryDistrict::where('division_id', $division->id)->where('name', 'LIKE', '%?%')->exists() || $division->name == '??????' || str_contains($division->name, '?')) {
            \App\Support\DeliveryLocation::restoreBanglaNames();
            $division->refresh();
        }

        $query = DeliveryDistrict::query()->where('division_id', $division->id);

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
            'total'    => DeliveryDistrict::where('division_id', $division->id)->count(),
            'active'   => DeliveryDistrict::where('division_id', $division->id)->where('status', 1)->count(),
            'inactive' => DeliveryDistrict::where('division_id', $division->id)->where('status', 0)->count(),
        ];

        return view('backEnd.delivery.district_index', compact('division', 'show_data', 'stats'));
    }

    public function create($division)
    {
        $division = DeliveryDivision::findOrFail($division);

        return view('backEnd.delivery.district_create', compact('division'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'division_id'      => 'required|exists:divisions,id',
            'name'             => 'required|string|max:190',
            'delivery_charge'  => 'required|integer|min:0',
        ]);

        DeliveryDistrict::create([
            'division_id'      => $request->division_id,
            'name'             => $request->name,
            'delivery_charge'  => (int) $request->delivery_charge,
            'sort_order'       => (int) $request->input('sort_order', 0),
            'status'           => $request->boolean('status') ? 1 : 0,
        ]);

        Toastr::success('জেলা যোগ করা হয়েছে', 'সফল');

        return redirect()->route('admin.delivery.districts.index', $request->division_id);
    }

    public function edit($id)
    {
        $edit_data = DeliveryDistrict::with('division')->findOrFail($id);

        return view('backEnd.delivery.district_edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id'               => 'required|exists:districts,id',
            'division_id'      => 'required|exists:divisions,id',
            'name'             => 'required|string|max:190',
            'delivery_charge'  => 'required|integer|min:0',
        ]);

        $row = DeliveryDistrict::findOrFail($request->id);
        $row->update([
            'division_id'      => $request->division_id,
            'name'             => $request->name,
            'delivery_charge'  => (int) $request->delivery_charge,
            'sort_order'       => (int) $request->input('sort_order', 0),
            'status'           => $request->boolean('status') ? 1 : 0,
        ]);

        Toastr::success('আপডেট হয়েছে', 'সফল');

        return redirect()->route('admin.delivery.districts.index', $request->division_id);
    }

    public function destroy(Request $request)
    {
        $row = DeliveryDistrict::findOrFail($request->hidden_id);
        $divisionId = $row->division_id;
        $row->delete();
        Toastr::success('মুছে ফেলা হয়েছে', 'সফল');

        return redirect()->route('admin.delivery.districts.index', $divisionId);
    }
}
