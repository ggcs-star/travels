<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingTraveller extends Model
{
    use HasFactory;

 protected $fillable = [
    'booking_id',
    'full_name',
    'email',
    'phone',
    'date_of_birth',
    'gender',
    'id_proof_type',
    'id_proof_number',
    'id_proof_document',
];
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
