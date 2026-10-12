<?php

// database/migrations/XXXX_XX_XX_XXXXXX_add_cvd_risk_columns_to_risk_scores_table.php

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
            // คอลัมน์สำหรับเก็บ Component Cal (ค่าคำนวณย่อย)
            $table->float('age_cal')->nullable();
            $table->float('gender_cal')->nullable();
            $table->float('sbp_cal')->nullable();
            $table->float('cholesterol_cal')->nullable();
            $table->float('smoking_cal')->nullable();
            $table->float('dm_cal')->nullable();
            
            // คอลัมน์สำหรับเก็บผลลัพธ์รวม
            $table->float('full_score_sum')->nullable()->comment('ผลรวมของค่า Cal ทั้งหมด');
            $table->float('percent_cvd_risk')->nullable()->comment('ความเสี่ยง CVD เป็นเปอร์เซ็นต์');
            $table->string('risk_level')->nullable()->comment('ระดับความเสี่ยง (ต่ำ/ปานกลาง/สูง)');

            // หากคุณเคยมีคอลัมน์ 'cvd_risk_score' หรือ 'total_sum' มาก่อน ควรลบหรือเปลี่ยนชื่อใน Migration เก่า
            // แต่เนื่องจากเราสร้างใหม่ ให้ใช้ชื่อเหล่านี้
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_scores', function (Blueprint $table) {
            // คำสั่งสำหรับลบคอลัมน์ที่เราเพิ่ม
            $table->dropColumn([
                'age_cal', 
                'gender_cal', 
                'sbp_cal', 
                'cholesterol_cal', 
                'smoking_cal', 
                'dm_cal',
                'full_score_sum',
                'percent_cvd_risk',
                'risk_level'
            ]);
        });
    }
};