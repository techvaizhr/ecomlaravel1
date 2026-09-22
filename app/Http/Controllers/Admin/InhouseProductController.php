<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Toastr;

class InhouseProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
    }

    /**
     * Display all inhouse products (products without vendor_id)
     */
    public function index(Request $request)
    {
        // Show only inhouse products (vendor_id is null)
        $query = Product::whereNull('vendor_id')
            ->orderBy('id','DESC')
            ->with('image','category');

        if ($request->keyword) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('pro_barcode', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('id', $keyword);
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->stock_status) {
            if ($request->stock_status === 'out_of_stock') {
                $query->where('stock', '<=', 0);
            } elseif ($request->stock_status === 'low_stock') {
                $query->where('stock', '>', 0)->where('stock', '<=', 5);
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock', '>', 5);
            }
        }

        // Global Smart Date Filter helper
        if (function_exists('apply_date_filter')) {
            apply_date_filter($query, $request, 'created_at');
        }

        $per_page = $request->get('per_page', 25);
        $data = $query->paginate($per_page)->withQueryString();
        $categories = Category::where('parent_id', 0)->where('status', 1)->select('id', 'name')->get();
        
        return view('backEnd.inhouse_product.index', compact('data', 'categories'));
    }

    /**
     * Show single product details
     */
    public function show($id)
    {
        $product = Product::whereNull('vendor_id')
            ->with('image','images','category','subcategory','childcategory','brand','colors','sizes')
            ->findOrFail($id);
            
        return view('backEnd.inhouse_product.show', compact('product'));
    }
}
