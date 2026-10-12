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
        // เพิ่มคอลัมน์ dm_wh_score เพื่อเก็บคะแนนรอบเอวต่อส่วนสูง
        $table->integer('dm_wh_score')->default(0)->after('dm_bmi_score');
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
