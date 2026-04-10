<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->unsignedInteger('direct_children')->default(0)->change();
        });

        $this->syncDirectChildrenCounts();
    }

    public function down(): void
    {
        DB::table('referrals')
            ->where('direct_children', '>', 255)
            ->update(['direct_children' => 255]);

        Schema::table('referrals', function (Blueprint $table) {
            $table->unsignedTinyInteger('direct_children')->default(0)->change();
        });

        $this->syncDirectChildrenCounts();
    }

    private function syncDirectChildrenCounts(): void
    {
        DB::table('referrals')->update(['direct_children' => 0]);

        $counts = DB::table('referrals')
            ->select('parent_id', DB::raw('COUNT(*) as aggregate'))
            ->whereNotNull('parent_id')
            ->groupBy('parent_id')
            ->pluck('aggregate', 'parent_id');

        foreach ($counts as $parentId => $count) {
            DB::table('referrals')
                ->where('user_id', $parentId)
                ->update(['direct_children' => (int) $count]);
        }
    }
};
