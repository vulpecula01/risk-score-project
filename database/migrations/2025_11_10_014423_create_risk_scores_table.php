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
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('gender')->comment('0=ชาย, 1=หญิง');
            $table->integer('age');
            $table->integer('bp_sys');
            $table->integer('bp_dias');
            $table->integer('pulse');
            $table->integer('waist');
            $table->float('height'); // ส่วนสูง
            $table->float('bmi');
            $table->integer('cholesterol');
            $table->integer('triglyceride');
            $table->integer('hdl');
            $table->integer('fbs');
            $table->integer('smoking')->comment('0=ไม่สูบ, 1=สูบ');
            $table->integer('exercise')->comment('0=ไม่ออก, 1=บางครั้ง, 2=เป็นประจำ');
            $table->integer('fam_history_dm')->comment('0=ไม่มี, 1=มี');
            $table->integer('fam_history_stroke')->comment('0=ไม่มี, 1=ไม่แน่ใจ, 2=มี');
            $table->integer('dm_from_lab')->comment('0=ไม่มี, 1=ไม่แน่ใจ, 2=มี');
            $table->integer('hypertension')->comment('0=ไม่มี, 1=มี'); // โรคความดัน
            // สามารถเพิ่มคอลัมน์สำหรับเก็บค่า Risk Score ที่คำนวณได้ในภายหลัง
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
