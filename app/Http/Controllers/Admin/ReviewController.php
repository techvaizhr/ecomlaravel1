<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use App\Models\Product;
use App\Models\Review;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['product', 'customer']);

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('review', 'like', "%{$keyword}%")
                  ->orWhereHas('product', function ($sub) use ($keyword) {
                      $sub->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        if ($request->filled('ratting')) {
            $query->where('ratting', $request->ratting);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (function_exists('apply_date_filter')) {
            apply_date_filter($query, $request, 'created_at');
        }

        $stats = [
            'total'      => Review::count(),
            'active'     => Review::where('status', 'active')->count(),
            'pending'    => Review::where('status', 'pending')->count(),
            'avg_rating' => round(Review::avg('ratting') ?: 5, 1),
        ];

        $per_page = $request->get('per_page', 15);
        $show_data = $query->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->paginate($per_page)->withQueryString();

        return view('backEnd.review.index', compact('show_data', 'stats'));
    }

    public function create()
    {
        $products = Product::where(['status' => 1])->select('id', 'name')->orderBy('name', 'ASC')->get();
        $customers = Customer::where('status', 'active')->select('id', 'name', 'phone', 'email')->orderBy('name', 'ASC')->get();
        return view('backEnd.review.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id'  => 'required',
            'ratting'     => 'required|numeric|min:1|max:5',
            'review'      => 'required|string',
            'name'        => 'nullable|string|max:191',
            'email'       => 'nullable|string|max:191',
            'customer_id' => 'nullable',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'created_at'  => 'nullable|date',
            'status'      => 'nullable',
        ]);

        $name = trim($request->name ?? '');
        $email = trim($request->email ?? '');
        $customerId = $request->customer_id ? (int) $request->customer_id : null;

        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer) {
                if (empty($name)) {
                    $name = $customer->name ?? 'Customer';
                }
                if (empty($email)) {
                    $email = $customer->email ?? ($customer->phone ? $customer->phone . '@customer.local' : 'customer@review.local');
                }
            }
        }

        if (empty($name)) {
            $name = 'Verified Customer';
        }
        if (empty($email)) {
            $email = 'customer@review.local';
        }

        $reviewDate = $request->filled('created_at')
            ? Carbon::parse($request->created_at)
            : ($request->filled('review_date') ? Carbon::parse($request->review_date) : now());

        $status = ($request->status === 'active' || $request->status == 1) ? 'active' : 'pending';

        $review = new Review();
        $review->product_id  = $request->product_id;
        $review->customer_id = $customerId;
        $review->name        = $name;
        $review->email       = $email;
        $review->ratting     = $request->ratting;
        $review->review      = $request->review;
        $review->status      = $status;
        $review->created_at  = $reviewDate;
        $review->updated_at  = now();
        $review->review_date = $reviewDate;

        if ($request->hasFile('image')) {
            $review->image = $this->uploadImage($request->file('image'));
        }

        $review->save();

        if ($status === 'active') {
            $product = Product::select('id', 'ratting')->find($review->product_id);
            if ($product) {
                $product->ratting = ($product->ratting ?? 0) + 1;
                $product->save();
            }
        }

        Toastr::success('Success', 'Review added successfully with real photo & date');
        return redirect()->route('reviews.index');
    }

    public function edit($id)
    {
        $edit_data = Review::findOrFail($id);
        $products = Product::where(['status' => 1])->select('id', 'name')->orderBy('name', 'ASC')->get();
        $customers = Customer::where('status', 'active')->select('id', 'name', 'phone', 'email')->orderBy('name', 'ASC')->get();
        return view('backEnd.review.edit', compact('edit_data', 'products', 'customers'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'hidden_id'   => 'required|exists:reviews,id',
            'product_id'  => 'required',
            'name'        => 'required|string|max:191',
            'email'       => 'nullable|string|max:191',
            'ratting'     => 'required|numeric|min:1|max:5',
            'review'      => 'required|string',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'created_at'  => 'nullable|date',
        ]);

        $update_data = Review::findOrFail($request->hidden_id);

        $update_data->product_id  = $request->product_id;
        $update_data->customer_id = $request->customer_id ? (int) $request->customer_id : $update_data->customer_id;
        $update_data->name        = $request->name;
        $update_data->email       = $request->email ?: ($update_data->email ?: 'customer@review.local');
        $update_data->ratting     = $request->ratting;
        $update_data->review      = $request->review;
        $update_data->status      = ($request->status === 'active' || $request->status == 1) ? 'active' : 'pending';

        if ($request->filled('created_at')) {
            $date = Carbon::parse($request->created_at);
            $update_data->created_at  = $date;
            $update_data->review_date = $date;
        }

        // Handle image remove request
        if ($request->filled('remove_image') && $request->remove_image == 1) {
            if ($update_data->image && File::exists(base_path($update_data->image))) {
                File::delete(base_path($update_data->image));
            }
            $update_data->image = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            if ($update_data->image && File::exists(base_path($update_data->image))) {
                File::delete(base_path($update_data->image));
            }
            $update_data->image = $this->uploadImage($request->file('image'));
        }

        $update_data->save();

        Toastr::success('Success', 'Review updated successfully');
        return redirect()->route('reviews.index');
    }

    public function pending(Request $request)
    {
        $query = Review::with(['product', 'customer'])->where('status', 'pending');

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('review', 'like', "%{$keyword}%")
                  ->orWhereHas('product', function ($sub) use ($keyword) {
                      $sub->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        if ($request->filled('ratting')) {
            $query->where('ratting', $request->ratting);
        }

        if (function_exists('apply_date_filter')) {
            apply_date_filter($query, $request, 'created_at');
        }

        $stats = [
            'total'      => Review::count(),
            'active'     => Review::where('status', 'active')->count(),
            'pending'    => Review::where('status', 'pending')->count(),
            'avg_rating' => round(Review::avg('ratting') ?: 5, 1),
        ];

        $per_page = $request->get('per_page', 15);
        $data = $query->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->paginate($per_page)->withQueryString();

        return view('backEnd.review.pending', compact('data', 'stats'));
    }

    public function inactive(Request $request)
    {
        $inactive = Review::find($request->hidden_id);
        if ($inactive) {
            $inactive->status = 'pending';
            $inactive->save();
            Toastr::success('Success', 'Review marked as pending');
        } else {
            Toastr::error('Error', 'Review not found');
        }
        return redirect()->back();
    }

    public function active(Request $request)
    {
        $active = Review::find($request->hidden_id);
        if ($active) {
            $active->status = 'active';
            $active->save();

            $product = Product::select('id', 'ratting')->find($active->product_id);
            if ($product) {
                $product->ratting = ($product->ratting ?? 0) + 1;
                $product->save();
            }
            Toastr::success('Success', 'Review activated and published successfully');
        } else {
            Toastr::error('Error', 'Review not found');
        }
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        $delete_data = Review::find($request->hidden_id);
        if ($delete_data) {
            if ($delete_data->image && File::exists(base_path($delete_data->image))) {
                File::delete(base_path($delete_data->image));
            }
            $delete_data->delete();
            Toastr::success('Success', 'Review deleted successfully');
        } else {
            Toastr::error('Error', 'Review not found');
        }
        return redirect()->back();
    }

    private function uploadImage($file): string
    {
        $name = 'review_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'public/uploads/reviews';
        if (!is_dir(base_path($path))) {
            mkdir(base_path($path), 0755, true);
        }
        $file->move(base_path($path), $name);

        return 'public/uploads/reviews/' . $name;
    }
}

