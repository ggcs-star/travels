<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    /**
     * Display all inquiries.
     */
    public function index(Request $request)
    {
        $query = ContactInquiry::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $inquiries = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'all' => ContactInquiry::count(),

            'new' => ContactInquiry::where('status', 'new')->count(),

            'read' => ContactInquiry::where('status', 'read')->count(),

            'replied' => ContactInquiry::where('status', 'replied')->count(),
        ];

        return view('admin.inquiries.index', compact(
            'inquiries',
            'counts'
        ));
    }

    /**
     * Show single inquiry.
     */
    public function show(ContactInquiry $inquiry)
    {
        if ($inquiry->status === 'new') {
            $inquiry->update([
                'status' => 'read',
                'read_at' => now(),
            ]);
        }

        return view('admin.inquiries.show', compact('inquiry'));
    }

    /**
     * Update inquiry status.
     */
    public function updateStatus(
        Request $request,
        ContactInquiry $inquiry
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:new,read,replied',
            ],
        ]);

        $data = [
            'status' => $validated['status'],
        ];

        if ($validated['status'] === 'read' && ! $inquiry->read_at) {
            $data['read_at'] = now();
        }

        if ($validated['status'] === 'replied') {
            if (! $inquiry->read_at) {
                $data['read_at'] = now();
            }

            $data['replied_at'] = now();
        }

        $inquiry->update($data);

        return redirect()
            ->back()
            ->with('success', 'Inquiry status updated successfully.');
    }

    /**
     * Delete inquiry.
     */
    public function destroy(ContactInquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()
            ->route('admin.inquiries.index')
            ->with('success', 'Inquiry deleted successfully.');
    }
}