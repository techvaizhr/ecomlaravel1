<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;

class AdminComplaintController extends Controller
{
    /**
     * Complaint list (Admin panel)
     * AJAX + Pagination supported
     */
    public function index(Request $request)
    {
        $query = Complaint::query();

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('phone', 'like', "%{$keyword}%")
                  ->orWhere('order_number', 'like', "%{$keyword}%")
                  ->orWhere('message', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (function_exists('apply_date_filter')) {
            apply_date_filter($query, $request, 'created_at');
        }

        $stats = [
            'total' => Complaint::count(),
            'pending' => Complaint::where('status', 'pending')->count(),
            'processing' => Complaint::where('status', 'processing')->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
        ];

        $per_page = $request->get('per_page', 15);
        $complaints = $query->latest()->paginate($per_page)->withQueryString();

        if ($request->ajax()) {
            return view('backEnd.complaints.partials.table', compact('complaints'))->render();
        }

        return view('backEnd.complaints.index', compact('complaints', 'stats'));
    }

    /**
     * Update complaint status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,resolved',
        ]);

        $complaint = Complaint::findOrFail($id);
        $complaint->status = $request->status;
        $complaint->save();

        // AJAX support থাকলেও redirect safe
        return back()->with('success', 'Complaint status updated successfully');
    }

    /**
     * Delete complaint
     */
    public function destroy($id)
    {
        $complaint = Complaint::findOrFail($id);

        // ✅ Image delete (public/complaints folder)
        if ($complaint->image) {
            $imagePath = public_path('complaints/' . $complaint->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $complaint->delete();

        return back()->with('success', 'Complaint deleted successfully');
    }
}
