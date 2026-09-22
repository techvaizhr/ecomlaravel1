<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;

class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            'permission:contact-message-list|contact-message-edit|contact-message-delete',
            ['only' => ['index']]
        );

        $this->middleware(
            'permission:contact-message-edit',
            ['only' => ['status']]
        );

        $this->middleware(
            'permission:contact-message-delete',
            ['only' => ['destroy']]
        );
    }

    public function index(Request $request)
    {
        $query = ContactMessage::query();

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function($q) use ($keyword) {
                $q->where('full_name', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%")
                  ->orWhere('mobile', 'like', "%{$keyword}%")
                  ->orWhere('subject', 'like', "%{$keyword}%")
                  ->orWhere('details', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if (function_exists('apply_date_filter')) {
            apply_date_filter($query, $request, 'created_at');
        }

        $stats = [
            'total' => ContactMessage::count(),
            'unread' => ContactMessage::where('status', 0)->count(),
            'read' => ContactMessage::where('status', 1)->count(),
        ];

        $per_page = $request->get('per_page', 15);
        $messages = $query->latest()->paginate($per_page)->withQueryString();

        // AJAX pagination support
        if ($request->ajax()) {
            return view('backEnd.contactMessage.partials.table', compact('messages'))->render();
        }

        return view('backEnd.contactMessage.index', compact('messages', 'stats'));
    }

    /**
     * 🔹 AJAX Status Toggle
     */
    public function status(Request $request, $id)
    {
        $message = ContactMessage::findOrFail($id);

        $message->status = $message->status == 0 ? 1 : 0;
        $message->save();

        // AJAX response
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status'  => $message->status,
                'text'    => $message->status == 1 ? 'Seen' : 'Pending',
            ]);
        }

        Toastr::success('Status updated successfully');
        return redirect()->back();
    }

    /**
     * 🔹 AJAX Delete Message
     */
    public function destroy(Request $request, $id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully',
            ]);
        }

        Toastr::success('Message deleted successfully');
        return redirect()->back();
    }
}
