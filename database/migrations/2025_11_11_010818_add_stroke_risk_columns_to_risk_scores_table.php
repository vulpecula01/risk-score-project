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
        $table->float('stroke_score_sum')->nullable();
        $table->float('percent_stroke_risk')->nullable();
        $table->string('stroke_risk_level')->nullable();
    });
}
public function down(): void
{
    Schema::table('risk_scores', function (Blueprint $table) {
        $table->dropColumn(['stroke_score_sum', 'percent_stroke_risk', 'stroke_risk_level']);
    });
}
};
