<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('risk_scores', function (Blueprint $table) {
        $table->float('dm_risk_score')->nullable()->comment('คะแนนรวมความเสี่ยงเบาหวาน');
        $table->string('dm_risk_level')->nullable()->comment('ระดับความเสี่ยงเบาหวาน 10 ปี');
        
        // คอลัมน์สำหรับคะแนนย่อย
        $table->integer('dm_age_score')->nullable();
        $table->integer('dm_bp_score')->nullable(); // คะแนน BP + Hypertension Logic
        $table->integer('dm_fam_dm_score')->nullable();
        $table->integer('dm_bmi_score')->nullable();
        $table->integer('dm_fbs_score')->nullable();
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
