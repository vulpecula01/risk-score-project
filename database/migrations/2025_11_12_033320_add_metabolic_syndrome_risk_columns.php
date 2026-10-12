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
        $table->integer('metabolic_risk_count')->nullable()->comment('จำนวนเกณฑ์ Metabolic Syndrome ที่เข้าข่าย (5 ข้อ)');
        $table->string('metabolic_risk_level')->nullable()->comment('ผลสรุปความเสี่ยงไขมันพอกตับ');
        
        // คอลัมน์สำหรับเก็บผลการประเมินรายข้อ (ข้อ 1 - 5)
        $table->string('meta_bp_risk')->nullable(); 
        $table->string('meta_waist_risk')->nullable();
        $table->string('meta_triglyceride_risk')->nullable();
        $table->string('meta_hdl_risk')->nullable();
        $table->string('meta_fbs_risk')->nullable();
    });
}
public function down(): void
{
    Schema::table('risk_scores', function (Blueprint $table) {
        $table->dropColumn(['metabolic_risk_count', 'metabolic_risk_level', 
                            'meta_bp_risk', 'meta_waist_risk', 'meta_triglyceride_risk',
                            'meta_hdl_risk', 'meta_fbs_risk']);
    });
}
};
