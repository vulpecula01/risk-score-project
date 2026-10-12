<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('risk_scores', function (Blueprint $table) {
        // เพิ่มฟิลด์ hn ต่อจาก id และทำ index เพื่อให้ค้นหาได้เร็ว
        $table->string('hn')->nullable()->after('id')->index();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_scores', function (Blueprint $table) {
            //
        });
    }
};
