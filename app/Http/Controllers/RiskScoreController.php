<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiskScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; 
use App\Exports\RiskScoresExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\RiskImport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Patient;

class RiskScoreController extends Controller
{
   public function dashboard()
    {
        // 1. ดึงข้อมูลจำนวนแยกตามระดับความเสี่ยง (ใช้ Keyword ภาษาไทยตามฐานข้อมูล)
        $totalCount = RiskScore::count();
        
        // กลุ่มความเสี่ยงน้อย
        $lowRiskCount = RiskScore::where('risk_level', 'like', '%น้อย%')->count();
        
        // กลุ่มความเสี่ยงปานกลาง
        $medRiskCount = RiskScore::where('risk_level', 'like', '%ปานกลาง%')->count();
        
        // กลุ่มความเสี่ยงสูง (กรอง "สูงมาก" ออกเพื่อให้ได้ "สูง" ปกติ)
        $highRiskCount = RiskScore::where('risk_level', 'like', '%สูง%')
                                 ->where('risk_level', 'not like', '%สูงมาก%')->count();
                                 
        // กลุ่มความเสี่ยงสูงมาก
        $veryHighRiskCount = RiskScore::where('risk_level', 'like', '%สูงมาก%')->count();

        // 2. ข้อมูลสรุปด้านอื่นๆ (Stroke, DM, Metabolic)
        $strokeHighCount = RiskScore::where('stroke_risk_level', 'like', '%สูง%')->count();
        $dmHighCount = RiskScore::where('dm_risk_level', 'like', '%สูง%')->count();
        
        // ภาวะเมแทบอลิกซินโดรม (เกณฑ์มักเป็น 3 ข้อขึ้นไป)
        $metabolicCount = RiskScore::where('metabolic_risk_count', '>=', 3)->count();

        // 3. รายการประเมินล่าสุด 10 รายการ
        $riskScores = RiskScore::latest()->take(10)->get();

        // 4. เตรียมข้อมูลสำหรับกราฟเส้น (Trend) ย้อนหลัง 6 เดือน
        $months = [];
        $chartData = [
            'veryHigh' => [],
            'high' => [],
            'med' => [],
            'low' => []
        ];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            // ชื่อเดือนภาษาไทย + พ.ศ.
            $months[] = $date->locale('th')->shortMonthName . ' ' . ($date->year + 543);
            
            $chartData['veryHigh'][] = RiskScore::whereMonth('created_at', $date->month)
                                        ->whereYear('created_at', $date->year)
                                        ->where('risk_level', 'like', '%สูงมาก%')->count();
            
            $chartData['high'][] = RiskScore::whereMonth('created_at', $date->month)
                                        ->whereYear('created_at', $date->year)
                                        ->where('risk_level', 'like', '%สูง%')
                                        ->where('risk_level', 'not like', '%สูงมาก%')->count();
                                        
            $chartData['med'][] = RiskScore::whereMonth('created_at', $date->month)
                                        ->whereYear('created_at', $date->year)
                                        ->where('risk_level', 'like', '%ปานกลาง%')->count();
                                        
            $chartData['low'][] = RiskScore::whereMonth('created_at', $date->month)
                                        ->whereYear('created_at', $date->year)
                                        ->where('risk_level', 'like', '%น้อย%')->count();
        }

        // 5. คำนวณสถิติร้อยละ
        $lowRiskPercent = $totalCount > 0 ? round(($lowRiskCount / $totalCount) * 100, 1) : 0;
        $medRiskPercent = $totalCount > 0 ? round(($medRiskCount / $totalCount) * 100, 1) : 0;
        $highRiskPercent = $totalCount > 0 ? round(($highRiskCount / $totalCount) * 100, 1) : 0;
        $veryHighRiskPercent = $totalCount > 0 ? round(($veryHighRiskCount / $totalCount) * 100, 1) : 0;

        return view('risk_scores.dashboard', compact(
            'totalCount', 
            'lowRiskCount', 
            'medRiskCount', 
            'highRiskCount', 
            'veryHighRiskCount', 
            'strokeHighCount', 
            'dmHighCount', 
            'metabolicCount',
            'riskScores', 
            'months', 
            'chartData',
            'lowRiskPercent',
            'medRiskPercent',
            'highRiskPercent',
            'veryHighRiskPercent'
        ));
    }

    public function exportExcel()
    {
        $fileName = 'Risk_Scores_Report_' . date('Y-m-d_H-i') . '.xlsx';
        return Excel::download(new RiskScoresExport, $fileName);
    }

    public function login(Request $request)
    {
        // 1. Validation: รับค่า 'login' ให้ตรงกับ name="login" ใน HTML
        $request->validate([
            'login'    => ['required'], 
            'password' => ['required'],
        ], [
            'login.required'    => 'กรุณากรอกอีเมลหรือชื่อผู้ใช้',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
        ]);

        // 2. ดึงค่าจาก input ที่ชื่อว่า 'login'
        $loginValue = $request->input('login');
        $password   = $request->input('password');

        // 3. ตรวจสอบว่าสิ่งที่กรอกมาเป็น Email หรือ Username
        // ถ้ามี @ จะมองว่าเป็น email ถ้าไม่มีจะมองว่าเป็น name
        $fieldType = filter_var($loginValue, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // 4. พยายาม Login ด้วยเงื่อนไขที่เช็คได้
       if (Auth::attempt([$fieldType => $loginValue, 'password' => $password])) {
    $request->session()->regenerate();

    // แก้บรรทัดนี้: ให้ไปหน้า Dashboard หรือหน้า Create แทน
    return redirect()->route('risk_scores.index')->with('success', 'เข้าสู่ระบบสำเร็จ');
}

        // 5. Login ไม่สำเร็จ: ส่งกลับหน้าเดิมพร้อม Error และค่าที่เคยกรอก (ยกเว้น password)
        return back()->withErrors([
            'email' => 'ชื่อผู้ใช้/อีเมล หรือรหัสผ่านไม่ถูกต้อง',
        ])->withInput($request->only('login'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private const SUR_ROOT = 0.964588;

    private const THAI_CVD_COEFFS_TC = [
        'AGE' => 0.08183,
        'SEX' => 0.39499, 
        'SBP' => 0.02084,
        'DM' => 0.69974, 
        'CHOL' => 0.00212,
        'SMOKING' => 0.41916,
        'EXP_BASELINE' => 7.04423,
    ];

    private const THAI_CVD_COEFFS_WHR = [
        'AGE' => 0.079,
        'SEX' => 0.128, 
        'SBP' => 0.019350987,
        'DM' => 0.58454, 
        'WHR' => 3.512566,
        'SMOKING' => 0.459,
        'EXP_BASELINE' => 7.712325,
    ];

    private const THAI_CVD_COEFFS_WC = [
        'AGE' => 0.08372,
        'SEX' => 0.05988, 
        'SBP' => 0.02034,
        'DM' => 0.59953, 
        'WC' => 0.01283,
        'SMOKING' => 0.459,
        'EXP_BASELINE' => 7.31047,
    ];

   public function index(Request $request)
{
    $search = $request->input('search');
    $query = RiskScore::orderBy('id', 'desc');
    
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('hn', 'like', '%' . $search . '%'); // 🌟 ค้นหาจาก HN เพิ่มเติม
        });
    }
    
    $riskScores = $query->get();
    return view('risk_scores.index', compact('riskScores', 'search'));
}

    public function create()
    {
        return view('risk_scores.create'); 
    }

    public function store(Request $request)
    {
        $validatedData = $this->calculateAndRespond($request, false);
        $newScore = RiskScore::create($validatedData);

        return redirect()->route('risk_scores.show', $newScore->id) 
                         ->with('success', 'บันทึกข้อมูลและคำนวณ Risk Score สำเร็จแล้ว!');
    }

    public function show(int $id)
    {
        $score = RiskScore::findOrFail($id); 
        $stroke_data = $this->calculateCategoricalStrokeRisk($score->toArray()); 

        return view('risk_scores.show', [
            'score' => $score,
            'strokeRisks' => $stroke_data['risks'], 
            'redCount' => $stroke_data['red'],
            'yellowCount' => $stroke_data['yellow'],
            'greenCount' => $stroke_data['green'],
        ]);
    }

    public function apiCalculate(Request $request): JsonResponse
    {
        try {
            $validatedData = $this->calculateAndRespond($request, true);
            return response()->json([
                'status' => 'success',
                'data' => $validatedData
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'ข้อมูลไม่ถูกต้อง กรุณาตรวจสอบ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการคำนวณ: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateAndRespond(Request $request, bool $isApi = false): array
    {
        $validationRules = [
            'hn' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:0,1',
            'age' => 'required|integer|min:1',
            'bp_sys' => 'required|integer|min:50',
            'bp_dias' => 'required|integer|min:30',
            'pulse' => 'required|integer|min:40',
            'waist' => 'required|numeric|min:10', 
            'height' => 'required|numeric|min:10',
            'bmi' => 'required|numeric|min:5', 
            'cholesterol' => 'nullable|integer', 
            'triglyceride' => 'nullable|integer',
            'hdl' => 'nullable|integer',
            'fbs' => 'nullable|integer',
            'smoking' => 'required|in:0,1,2',
            'exercise' => 'required|in:0,1,2',
            'fam_history_dm' => 'required|in:0,1',
            'fam_history_stroke' => 'required|in:0,1,2',
            'dm_from_lab' => 'required|in:0,1,2',
            'hypertension' => 'required|in:0,1',
        ];

        $validatedData = $request->validate($validationRules);
        $validatedData['hn'] = $request->input('hn');
        $age = (float)$validatedData['age'];
        $sbp = (float)$validatedData['bp_sys'];
        $waist_cm = (int)$validatedData['waist'];
        $height_cm = (float)$validatedData['height'];
        $smoking_status = (float)$validatedData['smoking'];
        $cholesterol = (float)($validatedData['cholesterol'] ?? 0); 
        $dm_status = ($validatedData['dm_from_lab'] == 2) ? 1.0 : 0.0;
        $sex_score = ($validatedData['gender'] == 0) ? 1.0 : 0.0;

        $whr = ($height_cm > 0) ? ($waist_cm / $height_cm) : 0; 
        
        $sum_beta_x = 0;
        $coeffs = [];
        $formula_used = '';

        if ($cholesterol > 0) {
            $coeffs = self::THAI_CVD_COEFFS_TC;
            $formula_used = 'TC';
            $val_cal = $cholesterol * $coeffs['CHOL'];
            $sum_beta_x = ($age * $coeffs['AGE']) + ($sex_score * $coeffs['SEX']) + ($sbp * $coeffs['SBP']) + ($dm_status * $coeffs['DM']) + $val_cal + ($smoking_status * $coeffs['SMOKING']);
            $validatedData['cholesterol_cal'] = round($val_cal, 8);
            $validatedData['whr_cal'] = null;
        } elseif ($whr > 0) {
            $coeffs = self::THAI_CVD_COEFFS_WHR;
            $formula_used = 'WHR';
            $val_cal = $whr * $coeffs['WHR'];
            $sum_beta_x = ($age * $coeffs['AGE']) + ($sex_score * $coeffs['SEX']) + ($sbp * $coeffs['SBP']) + ($dm_status * $coeffs['DM']) + $val_cal + ($smoking_status * $coeffs['SMOKING']);
            $validatedData['whr_cal'] = round($val_cal, 8);
            $validatedData['cholesterol_cal'] = null;
        } else {
            $coeffs = self::THAI_CVD_COEFFS_WC;
            $formula_used = 'WC';
            $val_cal = $waist_cm * $coeffs['WC'];
            $sum_beta_x = ($age * $coeffs['AGE']) + ($sex_score * $coeffs['SEX']) + ($sbp * $coeffs['SBP']) + ($dm_status * $coeffs['DM']) + $val_cal + ($smoking_status * $coeffs['SMOKING']);
            $validatedData['whr_cal'] = null;
            $validatedData['cholesterol_cal'] = null;
        }

        $diff_baseline = (float)($sum_beta_x - $coeffs['EXP_BASELINE']);
        $exponent = exp($diff_baseline);
        $cvd_risk_decimal = 1 - pow(self::SUR_ROOT, $exponent);
        $cvd_risk_percent = $cvd_risk_decimal * 100;
        
        if ($cvd_risk_decimal > 0.3) {
            $risk_display = "มากกว่า 30 %";
            $risk_level = 'ระดับความเสี่ยง >=30 % จัดอยู่ในกลุ่มเสี่ยงสูงมาก';
        } else {
            $risk_display = round($cvd_risk_percent, 2) . " %";
            if ($cvd_risk_percent < 10.0) {
                $risk_level = 'จัดอยู่ในกลุ่มเสี่ยงน้อย';
            } elseif ($cvd_risk_percent < 20.0) {
                $risk_level = 'จัดอยู่ในกลุ่มเสี่ยงปานกลาง';
            } else { 
                $risk_level = 'จัดอยู่ในกลุ่มเสี่ยงสูง';
            }
        }

        $validatedData['percent_cvd_risk'] = round($cvd_risk_percent, 4);
        $validatedData['risk_level'] = $risk_level;
        $validatedData['cvd_risk_display'] = $risk_display; 
        $validatedData['full_score_sum'] = round($sum_beta_x, 8);

        // --- จุดที่แก้ไข: อัปเดตการรับค่าให้ตรงกับฟังก์ชันคำนวณใหม่ ---
        $stroke_data = $this->calculateCategoricalStrokeRisk($validatedData);
        $validatedData['stroke_score_sum'] = $stroke_data['total_score']; 
        $validatedData['percent_stroke_risk'] = $stroke_data['risk_percent'];
        $validatedData['stroke_risk_level'] = $stroke_data['risk_level'];
        $validatedData['stroke_risks_factors'] = json_encode($stroke_data['risks']);
        $validatedData['stroke_risks_red'] = $stroke_data['red'];
        $validatedData['stroke_risks_yellow'] = $stroke_data['yellow'];
        // ป้องกัน Error หาก Database ต้องการ Green หรือค่าอื่นๆ
        $validatedData['stroke_risks_green'] = $stroke_data['green'] ?? 0; 

        $dm_data = $this->calculateDMRiskScore($validatedData);
        $validatedData['dm_risk_score'] = $dm_data['dm_risk_score'];
        $validatedData['dm_risk_level'] = $dm_data['dm_risk_level'];
        $validatedData['dm_age_score'] = $dm_data['dm_age_score'];
        $validatedData['dm_bp_score'] = $dm_data['dm_bp_score'];
        $validatedData['dm_fam_dm_score'] = $dm_data['dm_fam_dm_score'];
        $validatedData['dm_bmi_score'] = $dm_data['dm_bmi_score'];
        $validatedData['dm_fbs_score'] = $dm_data['dm_fbs_score'];
        $validatedData['dm_wh_score'] = $dm_data['dm_wh_score'];

        $meta_data = $this->calculateMetabolicRisk($validatedData);
        $validatedData['metabolic_risk_count'] = $meta_data['count'];
        $validatedData['metabolic_risk_level'] = $meta_data['summary'];
        $validatedData['meta_bp_risk'] = $meta_data['risks']['bp'];
        $validatedData['meta_waist_risk'] = $meta_data['risks']['waist'];
        $validatedData['meta_triglyceride_risk'] = $meta_data['risks']['triglyceride'];
        $validatedData['meta_hdl_risk'] = $meta_data['risks']['hdl'];
        $validatedData['meta_fbs_risk'] = $meta_data['risks']['fbs'];

        $validatedData['cholesterol'] = $cholesterol;
        $validatedData['triglyceride'] = (float)($validatedData['triglyceride'] ?? 0);
        $validatedData['hdl'] = (float)($validatedData['hdl'] ?? 0);
        $validatedData['fbs'] = (float)($validatedData['fbs'] ?? 0);
        $validatedData['whr'] = round($whr, 8); 

        return $validatedData;
    }

    private function calculateDMRiskScore(array $data): array
{
    // 1. เตรียมข้อมูลพื้นฐาน
    $age = (int)$data['age'];
    $bmi = (float)($data['bmi'] ?? 0);
    $waist = (float)($data['waist'] ?? 0); 
    $height = (float)($data['height'] ?? 0);
    $fbs = (int)($data['fbs'] ?? 0);

    // 2. ตรวจสอบหน่วยรอบเอว (นิ้ว -> ซม.)
    $waist_cm = ($waist < 60) ? ($waist * 2.54) : $waist;

    // 3. คำนวณรอบเอวต่อส่วนสูง (WHtR)
    $wh_ratio = ($height > 0) ? ($waist_cm / $height) : 0;
    $wh_s = ($wh_ratio > 0.6) ? 5 : (($wh_ratio > 0.5) ? 3 : 0);

    // 4. คะแนนอายุ
    $age_s = ($age >= 60) ? 4 : (($age >= 50) ? 3 : (($age >= 45) ? 2 : (($age >= 35) ? 1 : 0)));

    // 5. คะแนน BMI
    $bmi_s = ($bmi >= 27.5) ? 3 : (($bmi >= 23) ? 1 : 0);

    // 6. คะแนนความดันโลหิต
    $bp_s = ($data['bp_sys'] >= 140 || $data['bp_dias'] >= 90) ? 4 : (($data['bp_sys'] >= 120 || $data['bp_dias'] >= 80) ? 2 : 0);

    // 7. คะแนนประวัติครอบครัว (สายตรง)
    $fam_s = ($data['fam_history_dm'] == 1) ? 2 : 0;

    // 8. คะแนนน้ำตาลในเลือด (FPG)
    // เช็คว่ามี FBS หรือไม่ จากเงื่อนไขที่ส่งมาจากฟอร์มหรือค่าที่ไม่เป็น 0/ว่าง
    $has_fpg = isset($data['fbs_present']) ? ($data['fbs_present'] === 'yes') : ($fbs > 0);
    $fbs_s = ($has_fpg && $fbs >= 100) ? 5 : 0;

    // รวมคะแนนทั้งหมด
    $total = $age_s + $bmi_s + $wh_s + $bp_s + $fam_s + $fbs_s;

    // 9. แปลผลระดับความเสี่ยง 5 ระดับ ตามเกณฑ์ Thai DRS 2566
    if ($has_fpg) {
        // --- กรณี "มี" FPG ---
        if ($total >= 17) {
            $level = 'สูงมาก (>=30%)';
        } elseif ($total >= 15) {
            $level = 'สูง (21-30%)';
        } elseif ($total >= 11) {
            $level = 'ปานกลาง-สูง (11-20%)';
        } elseif ($total >= 8) {
            $level = 'น้อย-ปานกลาง (6-10%)';
        } else {
            $level = 'น้อย (0-5%)';
        }
    } else {
        // --- กรณี "ไม่มี" FPG ---
        if ($total >= 15) {
            $level = 'สูงมาก (>=30%)';
        } elseif ($total >= 13) {
            $level = 'สูง (21-30%)';
        } elseif ($total >= 10) {
            $level = 'ปานกลาง-สูง (11-20%)';
        } elseif ($total >= 7) {
            $level = 'น้อย-ปานกลาง (6-10%)';
        } else {
            $level = 'น้อย (0-5%)';
        }
    }

    return [
        'dm_risk_score' => $total,
        'dm_risk_level' => 'กลุ่มเสี่ยง ' . $level,
        'dm_age_score' => $age_s,
        'dm_bmi_score' => $bmi_s,
        'dm_wh_score' => $wh_s,
        'dm_bp_score' => $bp_s,
        'dm_fam_dm_score' => $fam_s,
        'dm_fbs_score' => $fbs_s,
    ];
}

    private function calculateMetabolicRisk(array $data): array
{
    $count = 0; 
    $results = []; 
    $hit_items = []; 
    
    $fbs = (int)($data['fbs'] ?? 0);
    $hdl = (int)($data['hdl'] ?? 0);
    $triglyceride = (int)($data['triglyceride'] ?? 0);
    $waist = (float)($data['waist'] ?? 0);
    $is_dm = (int)($data['family_diabetes'] ?? 0); // สมมติว่า field นี้คือโรคประจำตัวเบาหวาน
    $is_ht = (int)($data['hypertension'] ?? 0);    // ประวัติความดัน/กำลังรักษา

    // 1. Glucose (FBS ≥ 100 หรือ เป็นเบาหวาน)
    if (($fbs >= 100 && $fbs > 0) || $is_dm == 1) { 
        $results['fbs'] = 'ข้อ 1 (เหลือง)'; $hit_items[] = 1; $count++; 
    } else { 
        $results['fbs'] = 'ไม่เข้าเกณฑ์'; 
    }

    // 2. HDL (< 40 ชาย / < 50 หญิง)
    $hdl_risk = false;
    if ($hdl > 0) {
        if ($data['gender'] == 0 && $hdl < 40) $hdl_risk = true;
        elseif ($data['gender'] == 1 && $hdl < 50) $hdl_risk = true;
    }
    if ($hdl_risk) { 
        $results['hdl'] = 'ข้อ 2 (เหลือง)'; $hit_items[] = 2; $count++; 
    } else { 
        $results['hdl'] = 'ไม่เข้าเกณฑ์'; 
    }

    // 3. Triglycerides (≥ 150)
    if ($triglyceride >= 150 && $triglyceride > 0) { 
        $results['triglyceride'] = 'ข้อ 3 (เหลือง)'; $hit_items[] = 3; $count++; 
    } else { 
        $results['triglyceride'] = 'ไม่เข้าเกณฑ์'; 
    }

    // 4. Waist (≥ 94 ชาย / ≥ 80 หญิง)
    $waist_threshold = ($data['gender'] == 0) ? 94 : 80; 
    if ($waist >= $waist_threshold && $waist > 0) { 
        $results['waist'] = 'ข้อ 4 (เหลือง)'; $hit_items[] = 4; $count++; 
    } else { 
        $results['waist'] = 'ไม่เข้าเกณฑ์'; 
    }

    // 5. Hypertension (Sys ≥ 130 หรือ Dias ≥ 85 หรือ อยู่ระหว่างรักษา)
    if ($data['bp_sys'] >= 130 || $data['bp_dias'] >= 85 || $is_ht == 1) { 
        $results['bp'] = 'ข้อ 5 (เหลือง)'; $hit_items[] = 5; $count++; 
    } else { 
        $results['bp'] = 'ไม่เข้าเกณฑ์'; 
    }

    // สรุปผลตามเกณฑ์ >= 3 ข้อ
    $is_metabolic_risk = ($count >= 3);
    $summary_text = ($count > 0) 
        ? "คุณมีความเสี่ยงโรคไขมันพอกตับ จำนวน {$count} ข้อ ได้แก่ข้อที่ " . implode(', ', $hit_items)
        : 'คุณไม่มีความเสี่ยงของโรคไขมันพอกตับ';

    return [
        'count' => $count, 
        'is_risk' => $is_metabolic_risk, // เพิ่ม flag สำหรับเช็คความเสี่ยงรุนแรง (>=3 ข้อ)
        'summary' => $summary_text, 
        'risks' => $results
    ];
}

    private function calculateCategoricalStrokeRisk(array $data): array
{
    // 1. ดึงข้อมูลจาก $data (ตรวจสอบคีย์ให้ตรงกับฐานข้อมูล/Request)
    $age = (int)$data['age'];
    $sys = (int)$data['bp_sys'];
    $dia = (int)$data['bp_dias'];
    $cholesterol = (int)($data['cholesterol'] ?? 0);
    $bmi = (float)($data['bmi'] ?? 0);
    $smoking = (int)$data['smoking'];
    $exercise = (int)$data['exercise'];
    $fam_stroke = (int)$data['fam_history_stroke'];
    $dm_status = (int)$data['dm_from_lab'];
    $pulse = (int)($data['pulse'] ?? 0);
    $risks = [];
    $red = 0; 
    $yellow = 0;
    $green = 0;

    // --- คำนวณรายข้อตามเกณฑ์ Stroke Risk Scorecard ---

    // 1. Blood Pressure (ความดันโลหิต)
    if ($sys >= 140 || $dia >= 90) { $risks['bp'] = 'High Risk'; $red++; }
    elseif ($sys >= 120 || $dia >= 80) { $risks['bp'] = 'Caution'; $yellow++; }
    else { $risks['bp'] = 'Low Risk'; $green++; }

    // 2. Atrial Fibrillation (ภาวะหัวใจเต้นผิดจังหวะ) - หากไม่มีข้อมูลให้เป็น Low Risk
    if ($pulse >= 200) {
    // เปลี่ยนจาก $results['afib'] เป็น $risks['afib']
    $risks['afib'] = 'High Risk'; 
    $red++;
} else {
    // เปลี่ยนจาก $results['afib'] เป็น $risks['afib']
    $risks['afib'] = 'Low Risk'; 
    $green++;
}

    // 3. Smoking (การสูบบุหรี่)
    if ($smoking == 1) { 
    // กรณีสูบ (สีแดง)
    $risks['smoking'] = 'High Risk'; 
    $red++; 
} elseif ($smoking == 2) { 
    // กรณีกำลังเลิก (สีเหลือง)
    $risks['smoking'] = 'Caution'; // หรือใช้คำว่า 'กำลังเลิก' ตามความเหมาะสม
    $yellow++; // อย่าลืมประกาศตัวแปร $yellow = 0; ไว้ที่หัวฟังก์ชันด้วยนะครับ
} else { 
    // กรณีไม่สูบ (สีเขียว)
    $risks['smoking'] = 'Low Risk'; 
    $green++; 
}

    // 4. Cholesterol (ระดับไขมันในเลือด)
    if ($cholesterol >= 240) { 
        $risks['cholesterol'] = 'High Risk'; $red++; 
    } elseif ($cholesterol >= 200) { 
        $risks['cholesterol'] = 'Caution'; $yellow++; 
    } else { 
        // รวมถึงกรณี $cholesterol == 0 ด้วย
        $risks['cholesterol'] = 'Low Risk'; $green++; 
    }


    // 5. Diabetes (โรคเบาหวาน)
    if ($dm_status == 2) { $risks['diabetes'] = 'High Risk'; $red++; }
    elseif ($dm_status == 1) { $risks['diabetes'] = 'Caution'; $yellow++; }
    else { $risks['diabetes'] = 'Low Risk'; $green++; }

    // 6. Exercise (การออกกำลังกาย)
    if ($exercise == 0) { $risks['exercise'] = 'High Risk'; $red++; } // Couch potato
    elseif ($exercise == 1) { $risks['exercise'] = 'Caution'; $yellow++; } // Some exercise
    else { $risks['exercise'] = 'Low Risk'; $green++; } // Regular

    // 7. Diet/BMI (น้ำหนักตัว) - แก้ไขคีย์จาก diet เป็น bmi เพื่อลด Error
    if ($bmi >= 30.0) { $risks['bmi'] = 'High Risk'; $red++; }
    elseif ($bmi >= 25.0) { $risks['bmi'] = 'Caution'; $yellow++; }
    else { $risks['bmi'] = 'Low Risk'; $green++; }

    // 8. Stroke in Family (ประวัติครอบครัว)
    if ($fam_stroke == 1) { $risks['fam_stroke'] = 'High Risk'; $red++; }
    elseif ($fam_stroke == 2) { $risks['fam_stroke'] = 'Caution'; $yellow++; }
    else { $risks['fam_stroke'] = 'Low Risk'; $green++; }

    // --- สรุประดับความเสี่ยง (Risk Scorecard Results) ---

    if ($red >= 3) {
    // กรณีแดง >= 3 ข้อ เป็นเสี่ยงสูง
    $risk_level = 'High Risk - เสี่ยงสูง';
    $risk_percent = 15;
} elseif ($yellow >= 4 && $yellow <= 6) {
    // กรณีเหลือง 4-6 ข้อ เป็นระดับเฝ้าระวัง
    $risk_level = 'Caution - เฝ้าระวัง';
    $risk_percent = 5;
} elseif ($green >= 6 && $green <= 8) {
    // กรณีเขียว 6-8 ข้อ เป็นเสี่ยงน้อย
    $risk_level = 'Low Risk - เสี่ยงต่ำ';
    $risk_percent = 2;
} else {
    // กรณีอื่นๆ เช่น แดง 1, เหลือง 3, เขียว 4 (ตามที่คุณยกตัวอย่าง)
    $risk_level = 'ไม่สามารถสรุปผลได้ (Inconclusive)';
    $risk_percent = 0; 
}

    return [
        'total_score' => $green, // คะแนนตามจำนวนข้อที่เป็น Low Risk (สีเขียว)
        'risk_percent' => $risk_percent,
        'risk_level' => $risk_level,
        'risks' => $risks, // ส่งค่ากลับไปครบทั้ง 8 ข้อ
        'red' => $red,
        'yellow' => $yellow,
        'green' => $green
    ];
}

    public function destroy(int $id)
    {
        $score = RiskScore::findOrFail($id); 
        $score->delete();
        return redirect()->route('risk_scores.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }

    public function generatePdf($id)
{
    $score = RiskScore::findOrFail($id);
    
    // แปลง JSON ความเสี่ยง Stroke กลับมาเป็น Array เพื่อแสดงผลใน Report
    $stroke_risks = json_decode($score->stroke_risks_factors, true) ?? [];

    $pdf = Pdf::loadView('reports.risk_report_pdf', [
        'score' => $score,
        'stroke_risks' => $stroke_risks
    ])->setPaper('a4', 'portrait');

    return $pdf->stream('Health_Report_'.$score->hn.'.pdf');
}

// ประมวลผลไฟล์ Excel


private function saveRiskData($data)
{
    // --- 1. คำนวณคะแนนเบาหวาน (Diabetes Risk Score - Thai DRS 2566) ---
    $dm_score = 0;

    // อายุ
    if ($data['age'] >= 60) $dm_score += 4;
    elseif ($data['age'] >= 50) $dm_score += 3;
    elseif ($data['age'] >= 45) $dm_score += 2;
    elseif ($data['age'] >= 35) $dm_score += 1;

    // BMI
    if ($data['bmi'] >= 27.5) $dm_score += 3;
    elseif ($data['bmi'] >= 23) $dm_score += 1;

    // รอบเอวต่อส่วนสูง (Waist to Height Ratio)
    $whtr = $data['waist'] / $data['height'];
    if ($whtr > 0.6) $dm_score += 5;
    elseif ($whtr > 0.5) $dm_score += 3;

    // ความดันโลหิต
    if ($data['bp_sys'] >= 140 || $data['bp_dias'] >= 90) $dm_score += 4;
    elseif ($data['bp_sys'] >= 120 || $data['bp_dias'] >= 80) $dm_score += 2;

    // ประวัติครอบครัว
    if ($data['fam_history_dm'] == 1) $dm_score += 2;

    // คะแนนจาก FBS (ถ้ามี)
    $has_fbs = ($data['fbs'] !== null && $data['fbs'] > 0);
    if ($has_fbs && $data['fbs'] >= 100 && $data['fbs'] <= 125) {
        $dm_score += 5;
    }

    // --- 2. แปลผล 5 ระดับ ตามเกณฑ์ที่ส่งมา (Thai DRS 2566) ---
    if ($has_fbs) {
        if ($dm_score >= 17) $dm_level = "สูงมาก (>=30%)";
        elseif ($dm_score >= 15) $dm_level = "สูง (21-30%)";
        elseif ($dm_score >= 11) $dm_level = "ปานกลาง-สูง (11-20%)";
        elseif ($dm_score >= 8) $dm_level = "น้อย-ปานกลาง (6-10%)";
        else $dm_level = "น้อย (0-5%)";
    } else {
        if ($dm_score >= 15) $dm_level = "สูงมาก (>=30%)";
        elseif ($dm_score >= 13) $dm_level = "สูง (21-30%)";
        elseif ($dm_score >= 10) $dm_level = "ปานกลาง-สูง (11-20%)";
        elseif ($dm_score >= 7) $dm_level = "น้อย-ปานกลาง (6-10%)";
        else $dm_level = "น้อย (0-5%)";
    }

    // --- 3. บันทึกลง Database (ตรวจสอบชื่อ Model และ Column ให้ตรงกับของคุณ) ---
    // ตัวอย่างเช่นใช้ Model ชื่อ RiskScore
    // RiskScoreController.php
return \App\Models\RiskScore::create([
    'name'               => $data['name'],
    'age'                => $data['age'],
    'gender'             => $data['gender'],
    'weight'             => $data['weight'] ?? 0,
    'height'             => $data['height'] ?? 0,
    'waist'              => $data['waist'] ?? 0,
    'bmi'                => $data['bmi'] ?? 0,
    'bp_sys'             => $data['bp_sys'] ?? 0,
    'bp_dias'            => $data['bp_dias'] ?? 0,
    'fbs'                => $data['fbs'],
    'cholesterol'        => $data['total_chol'] ?? 0,
    'hdl'                => $data['hdl'] ?? 0,
    'triglyceride'       => $data['triglyceride'] ?? 0,
    'ldl'                => $data['ldl'] ?? 0,
    'ast'                => $data['ast'] ?? 0,
    'alt'                => $data['alt'] ?? 0,
    'pulse'              => $data['pulse'] ?? 0,
    'smoking'            => $data['smoking'] ?? 0,
    'exercise'           => $data['exercise'] ?? 0,
    'fam_history_dm'     => $data['fam_history_dm'] ?? 0,
    
    // 🌟 เพิ่มฟิลด์เหล่านี้เพื่อแก้ Error ตัวล่าสุดและตัวถัดๆ ไปที่อาจเกิดขึ้น
    'fam_history_stroke' => $data['fam_history_stroke'] ?? 0,
    'hypertension'       => $data['hypertension'] ?? 0,
    'diabetes'           => $data['diabetes'] ?? 0,
    'cvd'                => $data['cvd'] ?? 0,
    'atrial_fibrillation' => $data['atrial_fibrillation'] ?? 0,
    
    'dm_risk_level'      => $dm_level,
]);
}
public function importProcess(Request $request) 
{
    $request->validate([
        'excel_file' => 'required|mimes:xlsx,xls'
    ]);

    try {
        $file = $request->file('excel_file');
        // ใช้ toArray เพื่อดึงข้อมูลออกมาวน Loop
        $data_excel = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
        
        $rows = $data_excel[0];
        array_shift($rows); // เอาแถว Header ออก

        foreach ($rows as $row) {
            if (empty($row[0])) continue; // ข้ามแถวว่าง

            // 1. จัดชุดข้อมูลให้ตรงกับ Format ของฟังก์ชันคำนวณหลัก
            $importData = [
                'hn'                 => $row[0],
                'name'               => $row[1],
                'age'                => (int)($row[2] ?? 0),
                'gender'             => (int)($row[3] ?? 0),
                'weight'             => (float)($row[4] ?? 0),
                'height'             => (float)($row[5] ?? 0),
                'waist'              => (float)($row[6] ?? 0),
                'bp_sys'             => (int)($row[7] ?? 0),
                'bp_dias'            => (int)($row[8] ?? 0),
                'pulse'              => (int)($row[9] ?? 0),
                'fbs'                => (int)($row[10] ?? 0),
                'cholesterol'        => (int)($row[11] ?? 0),
                'triglyceride'       => (int)($row[12] ?? 0),
                'hdl'                => (int)($row[13] ?? 0),
                'smoking'            => (string)($row[14] ?? '0'),
                'exercise'           => (string)($row[15] ?? '0'),
                'fam_history_dm'     => (string)($row[16] ?? '0'),
                'fam_history_stroke' => (string)($row[17] ?? '0'),
                'hypertension'       => (string)($row[18] ?? '0'),
                'dm_from_lab'        => ($row[10] > 0) ? '2' : '0', // ถ้ามีค่า FBS ให้เซตเป็นมีประวัติ/ผลแล็บ
            ];

            // 2. คำนวณ BMI อัตโนมัติ (จำเป็นสำหรับการแปลผล)
            if ($importData['weight'] > 0 && $importData['height'] > 0) {
                $h_m = $importData['height'] / 100;
                $importData['bmi'] = round($importData['weight'] / ($h_m * $h_m), 2);
            } else {
                $importData['bmi'] = 0;
            }

            // 3. 🌟 หัวใจสำคัญ: ส่งข้อมูลไปเข้าฟังก์ชันคำนวณหลัก (calculateAndRespond)
            // เพื่อให้ได้ Risk Level, สี, และคะแนนที่ถูกต้องเหมือนการคีย์มือ
            $mockRequest = new Request($importData);
            $calculatedResult = $this->calculateAndRespond($mockRequest, true);

            // 4. บันทึกลงฐานข้อมูลโดยใช้ผลลัพธ์ที่คำนวณได้
            RiskScore::create($calculatedResult);
        }

        return redirect()->back()->with('success', 'นำเข้าข้อมูลและคำนวณความเสี่ยงทุกด้านเรียบร้อยแล้ว');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
    }
}
public function history(Request $request)
{
    $hn = $request->query('hn');
    // ดึงข้อมูลทั้งหมดของ HN นี้ เรียงจากวันที่ล่าสุดขึ้นก่อน
    $histories = RiskScore::where('hn', $hn)
                ->orderBy('created_at', 'desc')
                ->get();

    return view('risk_scores.history', compact('histories', 'hn'));
}
public function getPatientInfo($hn)
{
    // ค้นหาข้อมูลจากตารางผู้ป่วย (ตัวอย่างตารางชื่อ Patient)
    $patient = Patient::where('hn', $hn)->first();

    if ($patient) {
        return response()->json([
            'found' => true,
            'name' => $patient->name,
            'gender' => $patient->gender,
            'age' => $patient->age, // ถ้ามีวันเกิดอาจจะคำนวณอายุจากตรงนี้ได้
            // ข้อมูลอื่นๆ เช่น รอบเอวล่าสุด ส่วนสูง
        ]);
    }

    return response()->json(['found' => false]);
}
public function importView()
{
    // 💡 ใส่ชื่อโฟลเดอร์ตามด้วยจุด (.) แล้วตามด้วยชื่อไฟล์
    return view('risk_scores.import'); 
}
}