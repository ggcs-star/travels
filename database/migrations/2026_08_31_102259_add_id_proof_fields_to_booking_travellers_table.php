<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('booking_travellers', function (Blueprint $table) {
            $table->string('id_proof_type', 50)->after('gender');
            $table->string('id_proof_number', 100)->after('id_proof_type');
            $table->string('id_proof_document')->after('id_proof_number');
        });
    }

    public function down(): void
    {
        Schema::table('booking_travellers', function (Blueprint $table) {
            $table->dropColumn([
                'id_proof_type',
                'id_proof_number',
                'id_proof_document',
            ]);
        });
    }
};