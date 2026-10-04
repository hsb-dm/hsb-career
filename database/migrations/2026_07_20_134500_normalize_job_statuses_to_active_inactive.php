<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('jobs')
            ->where('status', 'published')
            ->update(['status' => 'active']);

        DB::table('jobs')
            ->whereIn('status', ['draft', 'closed'])
            ->update(['status' => 'inactive']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('jobs')
            ->where('status', 'active')
            ->update(['status' => 'published']);

        DB::table('jobs')
            ->where('status', 'inactive')
            ->update(['status' => 'draft']);
    }
};
