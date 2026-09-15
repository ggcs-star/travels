<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Store contact inquiry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'subject' => [
                'required',
                'string',
                'in:tour-enquiry,booking,destination,custom-trip,other',
            ],

            'message' => [
                'required',
                'string',
                'max:3000',
            ],
        ]);

        ContactInquiry::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => trim($validated['message']),
            'status' => 'new',
        ]);

        return redirect()
            ->back()
            ->with('success', 'Thank you for contacting us. Your enquiry has been submitted successfully.');
    }
}