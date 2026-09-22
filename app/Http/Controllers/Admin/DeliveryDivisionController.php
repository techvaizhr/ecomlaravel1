<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryDivision;
use Illuminate\Http\Request;
use Toastr;

class DeliveryDivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:shipping-list|shipping-create|shipping-edit|shipping-delete', ['only' => ['index']]);
        $this->middleware('permission:shipping-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:shipping-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:shipping-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $query = DeliveryDivision::query();

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
            $perPage = max(DeliveryDivision::count(), 1);
        } else {
            $perPage = max((int)$perPage, 10);
        }

        $show_data = $query->paginate($perPage)->withQueryString();

        $stats = [
            'total'    => DeliveryDivision::count(),
            'active'   => DeliveryDivision::where('status', 1)->count(),
            'inactive' => DeliveryDivision::where('status', 0)->count(),
        ];

        return view('backEnd.delivery.division_index', compact('show_data', 'stats'));
    }

    public function create()
    {
        return view('backEnd.delivery.division_create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:190',
        ]);

        DeliveryDivision::create([
            'name'       => $request->name,
            'sort_order' => (int) $request->input('sort_order', 0),
            'status'     => $request->boolean('status') ? 1 : 0,
        ]);

        Toastr::success('বিভাগ যোগ করা হয়েছে', 'সফল');

        return redirect()->route('admin.delivery.divisions.index');
    }

    public function edit($id)
    {
        $edit_data = DeliveryDivision::findOrFail($id);

        return view('backEnd.delivery.division_edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:190',
            'id'   => 'required|exists:divisions,id',
        ]);

        $row = DeliveryDivision::findOrFail($request->id);
        $row->update([
            'name'       => $request->name,
            'sort_order' => (int) $request->input('sort_order', 0),
            'status'     => $request->boolean('status') ? 1 : 0,
        ]);

        Toastr::success('আপডেট হয়েছে', 'সফল');

        return redirect()->route('admin.delivery.divisions.index');
    }

    public function destroy(Request $request)
    {
        $row = DeliveryDivision::findOrFail($request->hidden_id);
        $row->delete();
        Toastr::success('মুছে ফেলা হয়েছে', 'সফল');

        return redirect()->back();
    }
}
