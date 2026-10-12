<?php

use App\Http\Controllers\RiskScoreController;

Route::prefix('v1')->group(function () {
    // 🔑 ตรวจสอบว่าใช้ชื่อ Controller ที่ถูกต้อง
    Route::get('/risk', [RiskScoreController::class, 'getRiskDataList']); 
});