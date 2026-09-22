<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        if ($request->filled('keyword')) {
            $query->where('email', 'like', '%' . trim($request->keyword) . '%');
        }

        if (function_exists('apply_date_filter')) {
            apply_date_filter($query, $request, 'created_at');
        }

        $today = date('Y-m-d');
        $thisMonth = date('Y-m');
        $stats = [
            'total' => NewsletterSubscriber::count(),
            'today' => NewsletterSubscriber::whereDate('created_at', $today)->count(),
            'this_month' => NewsletterSubscriber::where('created_at', 'like', "{$thisMonth}%")->count(),
        ];

        $per_page = $request->get('per_page', 15);
        $subscribers = $query->latest()->paginate($per_page)->withQueryString();

        if ($request->ajax()) {
            return view('backEnd.newsletterSubscriber.partials.table', compact('subscribers'))->render();
        }

        return view('backEnd.newsletterSubscriber.index', compact('subscribers', 'stats'));
    }

    public function destroy(Request $request, $id)
    {
        $subscriber = NewsletterSubscriber::findOrFail($id);
        $subscriber->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Subscriber deleted successfully',
            ]);
        }

        Toastr::success('Subscriber deleted successfully');
        return redirect()->back();
    }
}
