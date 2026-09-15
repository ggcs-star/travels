<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactController extends ApiController
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => [
                'required',
                'string',
                'in:tour-enquiry,booking,destination,custom-trip,other',
            ],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        $inquiry = ContactInquiry::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'subject' => $validated['subject'],
            'message' => trim($validated['message']),
            'status' => 'new',
        ]);

        return $this->success(
            ['id' => $inquiry->id],
            'Thank you for contacting us. Your enquiry has been submitted successfully.',
            201
        );
    }
}
