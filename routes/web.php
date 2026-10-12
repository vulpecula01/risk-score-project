<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RiskScoreController;
use Illuminate\Support\Facades\Auth;

Route::post('/risk-score', [RiskScoreController::class, 'store']); 

// ต้องเพิ่มแบบ GET สำหรับหน้าแสดงผล/หน้าแรกด้วย
Route::get('/risk-score', [RiskScoreController::class, 'index'])->name('risk_scores.index');
// --- 1. ระบบ Authentication ---
Route::get('/login', function () {
    return view('auth.login'); 
})->name('login');

Route::post('/login', [RiskScoreController::class, 'login'])->name('login.submit');
Route::post('/logout', [RiskScoreController::class, 'logout'])->name('logout');


// --- 2. โซนที่ต้อง LOGIN (ย้ายขึ้นมาเพื่อจัดลำดับ Route สำคัญก่อน) ---
Route::middleware(['auth'])->group(function () {
    Route::post('/risk-score', [RiskScoreController::class, 'store']); 
    Route::get('/api/patient-info/{hn}', [PatientController::class, 'getPatientInfo'])->name('api.patient.info');
    Route::get('/risk-score/history', [App\Http\Controllers\RiskScoreController::class, 'history'])->name('risk_scores.history');
    Route::get('/api/patient-info/{hn}', function ($hn) {
    $patient = \App\Models\RiskScore::where('hn', $hn)->latest()->first();
    if ($patient) {
        return response()->json([
            'found' => true,
            'name' => $patient->name,
            'gender' => $patient->gender,
            'age' => $patient->age
        ]);
    }
    return response()->json(['found' => false]);
});

// ต้องเพิ่มแบบ GET สำหรับหน้าแสดงผล/หน้าแรกด้วย
Route::get('/risk-score', [RiskScoreController::class, 'index'])->name('risk_scores.index');
    // ** สำคัญ: ต้องวาง import-view ไว้ก่อนหน้า {id} เสมอ **
    Route::get('/risk-score/import-view', [RiskScoreController::class, 'importView'])->name('risk-scores.import-view');
    Route::post('/risk-score/import-process', [RiskScoreController::class, 'importProcess'])->name('risk-scores.import-process');

    Route::get('/risk-scores/export', [RiskScoreController::class, 'exportExcel'])->name('risk_scores.export');
    Route::get('/admin/dashboard', [RiskScoreController::class, 'dashboard'])->name('risk_scores.dashboard');
    Route::delete('/risk-score/{id}', [RiskScoreController::class, 'destroy'])->name('risk_scores.destroy');

    // ใช้ resource ต้องระวัง ลำดับ parameter
    Route::resource('risk_scores', RiskScoreController::class)->except(['show']); 
});


// --- 3. หน้าหลักและส่วนที่เข้าถึงได้ทั่วไป ---
Route::get('/', [RiskScoreController::class, 'create'])->name('home');

Route::prefix('risk-score')->group(function () {
    Route::get('/create', [RiskScoreController::class, 'create'])->name('risk_scores.create');
    Route::post('/', [RiskScoreController::class, 'store'])->name('risk_scores.store');
    
    // ย้าย {id} มาไว้ล่างสุดของกลุ่ม เพื่อไม่ให้มันไปดักคำว่า create หรือ import-view
    Route::get('/{id}', [RiskScoreController::class, 'show'])->name('risk_scores.show');
});

Route::get('/risk-score/{id}/pdf', [RiskScoreController::class, 'generatePdf'])->name('risk_scores.pdf');
Route::get('/risk-score-list', [RiskScoreController::class, 'index'])->name('risk_scores.index');

// --- 4. API ---
Route::get('/api/v1/risk', [RiskScoreController::class, 'getRiskDataList']);