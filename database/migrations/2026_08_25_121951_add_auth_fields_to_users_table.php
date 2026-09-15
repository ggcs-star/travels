<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 100)
                    ->unique()
                    ->after('name');
            }

            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)
                    ->default('user')
                    ->index()
                    ->after('password');
            }

            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')
                    ->default(true)
                    ->index()
                    ->after('role');
            }

        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            if (Schema::hasColumn('users', 'username')) {
                $table->dropUnique(['username']);
                $table->dropColumn('username');
            }

            if (Schema::hasColumn('users', 'role')) {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropIndex(['status']);
                $table->dropColumn('status');
            }

        });
    }
};