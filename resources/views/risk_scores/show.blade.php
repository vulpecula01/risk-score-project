<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Risk Analysis - {{ $score->name }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600;700&display=swap');
.transition-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        border-color: #e2e8f0 !important;
    }
    .fw-extrabold { font-weight: 800; }
    @media (min-width: 992px) {
        .border-end-lg { border-right: 1px solid #f1f5f9 !important; }
    }
        body { 
            font-family: 'Sarabun', sans-serif !important; 
            background-color: #f4f7f6;
            font-size: 11pt;
        }

        .drag-handle {
            cursor: grab;
            padding: 5px 10px;
            color: rgba(255,255,255,0.6);
            transition: color 0.2s;
        }
        .drag-handle:hover { color: white; }
        .drag-handle:active { cursor: grabbing; }

        .sortable-ghost {
            opacity: 0.3;
            border: 2px dashed #007bff !important;
            transform: scale(0.95);
        }
        .sortable-chosen {
            box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
        }

        .card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        .card-header {
            border-radius: 12px 12px 0 0 !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 600;
        }

        .bg-red-risk { background-color: #dc3545 !important; color: white !important; }
        .bg-yellow-risk { background-color: #ffc107 !important; color: #212529 !important; }
        .bg-green-risk { background-color: #198754 !important; color: white !important; }

        @media print {
            .no-print { display: none; }
            .card { page-break-inside: avoid !important; border: 1px solid #ddd; }
            body { background-color: white; }
            .container { width: 100%; max-width: 100%; }
        }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-end mb-4 border-bottom pb-3">
        <div>
            <h1 class="mb-1 text-primary fw-bold"><i class="fas fa-file-medical-alt me-2"></i>รายละเอียดข้อมูล Risk Score</h1>
            <h4 class="text-muted fw-light">ผู้รับการประเมิน: {{ $score->name }}</h4>
            <h5 class="text-secondary fw-normal"><i class="fas fa-id-card me-2"></i>HN: {{ $score->hn ?? '-' }}</h5>
        </div>
        <div class="text-end no-print">
            <span class="badge bg-info-subtle text-info p-2"><i class="fas fa-info-circle me-1"></i> คุณสามารถลากหัวข้อเพื่อจัดลำดับได้</span>
        </div>
    </div>

    @php
        $getCvdColor = function($level) {
            if (str_contains($level, 'ต่ำ') || str_contains($level, 'น้อย')) return 'text-success';
            if (str_contains($level, 'ปานกลาง')) return 'text-warning';
            if (str_contains($level, 'สูง')) return 'text-danger';
            return 'text-dark';
        };

        $getStrokeColor = function($level) {
            if (str_contains($level, 'Low')) return 'text-success';
            if (str_contains($level, 'Caution')) return 'text-warning';
            if (str_contains($level, 'High')) return 'text-danger';
            return 'text-dark';
        };

        $getRiskClass = function($risk) {
            if ($risk === 'High Risk') return 'bg-red-risk';
            if ($risk === 'Caution') return 'bg-yellow-risk';
            return 'bg-green-risk';
        };
    @endphp

    <div id="sortable-cards" class="row">
       
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px; overflow: hidden;">
                <div class="card-header bg-primary text-white py-3 px-4 d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-file-medical me-2"></i>ข้อมูลส่วนบุคคล</span>
                    <i class="fas fa-grip-vertical drag-handle no-print text-white-50" style="cursor: move;"></i>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-4 border-end-md">
    <h6 class="fw-bold text-primary mb-3"><i class="fas fa-user-tag me-2"></i>ข้อมูลพื้นฐาน</h6>
    <div class="d-flex flex-column gap-2">
        <div class="d-flex justify-content-between border-bottom pb-2">
            <span class="text-muted small">เพศ / อายุ</span>
            <span class="fw-bold">{{ $score->gender == 0 ? 'ชาย' : 'หญิง' }} / {{ $score->age }} ปี</span>
        </div>
        
        {{-- คำนวณน้ำหนักย้อนกลับจาก BMI และส่วนสูง --}}
        <div class="d-flex justify-content-between border-bottom pb-2">
            <span class="text-muted small">น้ำหนัก / ส่วนสูง</span>
            <span class="fw-bold">
                @if($score->bmi > 0 && $score->height > 0)
                    {{ number_format($score->bmi * pow($score->height / 100, 2), 1) }} กก.
                @else
                    - กก.
                @endif
                / {{ $score->height }} ซม.
            </span>
        </div>

        <div class="d-flex justify-content-between border-bottom pb-2">
            <span class="text-muted small">ดัชนีมวลกาย (BMI)</span>
            <span class="fw-bold text-danger">{{ $score->bmi }}</span>
        </div>

        <div class="d-flex justify-content-between border-bottom pb-2">
            <span class="text-muted small">รอบเอว</span>
            <span class="fw-bold">
                {{ $score->waist }} ซม. 
                <span class="text-muted fw-normal">({{ number_format($score->waist / 2.54, 1) }} นิ้ว)</span>
            </span>
        </div>
    </div>
</div>
                        <div class="col-md-4 border-end-md">
    <h6 class="fw-bold text-danger mb-3"><i class="fas fa-heartbeat me-2"></i>ผลวัดร่างกาย / สัญญาณชีพ</h6>
    <div class="d-flex flex-column gap-2">
        <div class="d-flex justify-content-between border-bottom pb-2">
            <span class="text-muted small">ความดัน (Sys/Diag)</span>
            <span class="fw-bold">{{ $score->bp_sys }} / {{ $score->bp_dias }} <small>mmHg</small></span>
        </div>
        <div class="d-flex justify-content-between border-bottom pb-2">
            <span class="text-muted small">ชีพจร (Pulse)</span>
            <span class="fw-bold text-success">{{ $score->pulse ?? '-' }} <small>bpm</small></span>
        </div>
        <div class="d-flex justify-content-between border-bottom pb-2">
            <span class="text-muted small">น้ำตาลในเลือด (FBS)</span>
            <span class="fw-bold">{{ $score->fbs > 0 ? $score->fbs . ' mg/dL' : 'ไม่มีผล' }}</span>
        </div>

        {{-- เพิ่มส่วนค่าไขมันต่างๆ --}}
        <div class="d-flex justify-content-between border-bottom pb-2">
    <span class="text-muted small">ไตรกลีเซอไลด์ (TG)</span>
    <span class="fw-bold">
        {{ ($score->triglyceride ?? 0) > 0 ? $score->triglyceride . ' mg/dL' : 'ไม่มีผล' }}
    </span>
</div>
        <div class="d-flex justify-content-between border-bottom pb-2">
    <span class="text-muted small">คอเลสเตอรอล</span>
    <span class="fw-bold">
        {{ ($score->cholesterol ?? 0) > 0 ? $score->cholesterol . ' mg/dL' : 'ไม่มีผล' }}
    </span>
</div>
        <div class="d-flex justify-content-between border-bottom pb-2">
            <span class="text-muted small">ไขมันดี (HDL)</span>
            <span class="fw-bold">{{ $score->hdl > 0 ? $score->hdl . ' mg/dL' : 'ไม่มีผล' }}</span>
        </div>
    </div>
</div>
                        <div class="col-md-4">
    <h6 class="fw-bold text-warning mb-3"><i class="fas fa-history me-2"></i>ประวัติและพฤติกรรมเสี่ยง</h6>
    <div class="d-flex flex-column gap-2" style="font-size: 0.85rem;">
        
        {{-- 1. การสูบบุหรี่ --}}
        <div class="d-flex justify-content-between border-bottom pb-1">
            <span class="text-muted small">สูบบุหรี่:</span>
            <span class="fw-bold">
                @if($score->smoking == 0) ไม่สูบ
                @elseif($score->smoking == 1) สูบ
                @elseif($score->smoking == 2) กำลังเลิก
                @else - @endif
            </span>
        </div>

        {{-- 2. การออกกำลังกาย --}}
        <div class="d-flex justify-content-between border-bottom pb-1">
            <span class="text-muted small">ออกกำลังกาย:</span>
            <span class="fw-bold">
                @if($score->exercise == 0) ไม่ออก
                @elseif($score->exercise == 1) บางครั้ง
                @elseif($score->exercise == 2) เป็นประจำ
                @else - @endif
            </span>
        </div>

        {{-- 3. ประวัติความดัน --}}
        <div class="d-flex justify-content-between border-bottom pb-1">
            <span class="text-muted small">ประวัติความดัน:</span>
            <span class="fw-bold">
                {{ $score->hypertension == 1 ? 'มี' : 'ไม่มี' }}
            </span>
        </div>

        {{-- 4. ครอบครัว (เบาหวาน) --}}
        <div class="d-flex justify-content-between border-bottom pb-1">
    <span class="text-muted small">ประวัติครอบครัว (เบาหวาน):</span>
    <span class="fw-bold">
        {{-- เปลี่ยนชื่อตัวแปรให้ตรงกับ Controller --}}
        {{ (isset($score->fam_history_dm) && $score->fam_history_dm == 1) ? 'มี' : 'ไม่มี' }}
    </span>
</div>

{{-- 5. ครอบครัว (Stroke) --}}
<div class="d-flex justify-content-between border-bottom pb-1">
    <span class="text-muted small">ประวัติครอบครัว (Stroke):</span>
    <span class="fw-bold">
        {{-- เช็คค่าตามที่ระบุในฟอร์ม --}}
        @if(isset($score->fam_history_stroke))
            @if($score->fam_history_stroke == 2) มี
            @elseif($score->fam_history_stroke == 1) ไม่แน่ใจ
            @else ไม่มี @endif
        @else
            -
        @endif
    </span>
</div>

    </div>
</div>
                    </div>
                </div>
            </div>
            

        <div class="col-md-6 mb-4 sortable-item">
    <div class="card shadow-sm h-100 border-0">
        <div class="card-header py-3 text-white 
            @if(str_contains($score->risk_level, 'น้อย')) bg-success 
            @elseif(str_contains($score->risk_level, 'ปานกลาง') || str_contains($score->risk_level, 'เฝ้าระวัง')) bg-warning text-dark
            @else bg-danger @endif">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fas fa-heartbeat me-2"></i>THAI CV RISK Score (10 ปี)</span>
                <i class="fas fa-grip-vertical drag-handle no-print"></i>
            </div>
        </div>

        <div class="card-body">
            <div class="text-center mb-4">
                <span class="text-muted d-block small mb-1">ความเสี่ยงของคุณคือ</span>
                <h2 class="display-4 fw-bold mb-0 
                    @if(str_contains($score->risk_level, 'น้อย')) text-success 
                    @elseif(str_contains($score->risk_level, 'ปานกลาง') || str_contains($score->risk_level, 'เฝ้าระวัง')) text-warning 
                    @else text-danger @endif">
                    {{ number_format($score->percent_cvd_risk, 2) }}%
                </h2>
            </div>

            <div class="p-3 rounded-4 bg-light border text-center mb-4">
                <div class="small text-muted mb-1">ระดับความเสี่ยง</div>
                <strong class="{{ $getCvdColor($score->risk_level) }} fs-4 d-block mb-1">
                    {{ $score->risk_level }}
                </strong>
                <p class="small text-muted mb-0 px-2" style="line-height: 1.4;">
                    ใกล้เคียงกับระดับความเสี่ยงของคนไทยเพศเดียวกัน อายุเท่ากัน และปราศจากปัจจัยเสี่ยง
                </p>
            </div>

            <hr class="border-secondary opacity-25">

            <div class="advice-section">
                <h6 class="fw-bold text-dark mb-2"><i class="fas fa-info-circle me-1 text-primary"></i> ข้อแนะนำเบื้องต้น</h6>
                <p class="text-muted small mb-3" style="text-align: justify;">
                    เพื่อป้องกันการเกิดโรคหลอดเลือดในอนาคต ควรออกกำลังกายอย่างสม่ำเสมอ รับประทานผักผลไม้เป็นประจำ และตรวจสุขภาพประจำปี
                </p>
            </div>

            <div class="disclaimer-section p-3 rounded bg-light border-start border-3 border-secondary">
                <h6 class="fw-bold small mb-1">หมายเหตุ (Disclaimer)</h6>
                <div class="text-muted" style="font-size: 0.75rem; line-height: 1.5; text-align: justify;">
                    ผลลัพธ์ที่ได้ เป็นการประเมินความเสี่ยงต่อการเจ็บป่วยหรือเสียชีวิตจากโรคเส้นเลือดหัวใจตีบตันและโรคเส้นเลือดสมองตีบตันในระยะเวลา 10 ปีข้างหน้า

                    ผลการประเมินและคำแนะนำที่ได้รับจากโปรแกรมนี้ไม่สามารถใช้แทนการตัดสินใจของแพทย์ได้ การตรวจรักษาเพิ่มเติมหรือการให้ยารักษาขึ้นอยู่กับดุลยพินิจของแพทย์และการปรึกษากันระหว่างแพทย์และตัวท่าน

                    ผลการประเมินนี้ห้ามนำไปใช้อ้างอิงในการค้า เช่น การทำประกันชีวิต และไม่สามารถใช้กับผู้ป่วยโรคลิ้นหัวใจหรือโรคหัวใจเต้นผิดจังหวะได้

                </div>
            </div>
        </div>
    </div>
</div>


        <div class="col-md-6 mb-4 sortable-item">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white py-3">
                    <span><i class="fas fa-brain me-2"></i>STROKE RISK Score</span>
                    <i class="fas fa-grip-vertical drag-handle no-print"></i>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h5 class="{{ $getStrokeColor($score->stroke_risk_level) }} fw-bold">{{ $score->stroke_risk_level ?? 'N/A' }}</h5>
                        <div class="mt-2">
                            <span class="badge bg-danger rounded-pill">แดง {{ $redCount }}</span>
                            <span class="badge bg-warning text-dark rounded-pill ms-1">เหลือง {{ $yellowCount }}</span>
                           <span class="badge bg-success text-white rounded-pill ms-1">เขียว {{ $greenCount }}</span>
                        </div>
                    </div>
                    <table class="table table-sm table-bordered mt-2" style="font-size: 0.9rem;">
                        <thead class="bg-light text-center">
                            <tr>
                                <th>ปัจจัยเสี่ยง</th>
                                <th>ระดับ</th>
                            </tr>
                        </thead>
                        <tbody>
    @foreach ([
        'bp' => 'ความดันโลหิต', 
        'afib' => 'ภาวะหัวใจเต้นผิดจังหวะ (Atrial_Fibri)',
        'smoking' => 'ประวัติการสูบบุหรี่', 
        'cholesterol' => 'ระดับไขมันในเลือด (Cholesterol)', 
        'diabetes' => 'โรคประจำตัวเป็นเบาหวาน', 
        'exercise' => 'ประวัติการออกกำลังกาย', 
        'bmi' => 'ดัชนีมวลกาย (BMI)', 
        'fam_stroke' => 'ประวัติครอบครัวสายตรงเป็นโรคหลอดเลือดสมอง'
    ] as $key => $label)
    @php
        $riskStatus = $strokeRisks[$key] ?? 'Low Risk';
        
        // กำหนดคลาสสีตามสถานะความเสี่ยง
        $riskClass = '';
        if ($riskStatus == 'High Risk') {
            $riskClass = 'bg-danger text-white'; // สีแดง
        } 
        // เพิ่มเงื่อนไขเช็ค Moderate Risk หรือ Caution ให้เป็นสีเหลือง
        elseif ($riskStatus == 'Moderate Risk' || $riskStatus == 'Caution') { 
            $riskClass = 'bg-warning text-dark'; // สีเหลือง
        } 
        else {
            $riskClass = 'bg-success text-white'; // สีเขียว (Low Risk)
        }
    @endphp
    <tr>
        <td class="ps-2">{{ $label }}</td>
        <td class="{{ $riskClass }} text-center small fw-bold">
            {{ $riskStatus }}
        </td>
    </tr>
    @endforeach
</tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4 sortable-item">
            <div class="card shadow-sm h-100 border-start border-4 border-success">
                <div class="card-header bg-success text-white py-3">
                    <span><i class="fas fa-hand-holding-medical me-2"></i>Diabetes Mellitus (DM) Risk Score (ใน 10 ปีข้างหน้า)</span>
                    <i class="fas fa-grip-vertical drag-handle no-print"></i>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-4 text-center border-end">
                            <small class="text-muted d-block">คะแนนรวม</small>
                            <h2 class="fw-bold text-success mb-0">{{ $score->dm_risk_score }}</h2>
                        </div>
                        <div class="col-8 ps-3">
                            <h6 class="fw-bold mb-1 {{ $score->dm_risk_score >= 8 ? 'text-danger' : 'text-success' }}">
                                {{ $score->dm_risk_level }}
                            </h6>
                            
                        </div>
                    </div>

                    <table class="table table-sm table-bordered" style="font-size: 0.85rem;">
                        <thead class="table-light text-center">
                            <tr>
                                <th>ระดับความเสี่ยง</th>
                                <th>ไม่มี FBS</th>
                                <th>มี FBS</th>
                            </tr>
                        </thead>
                        <tbody>
    @php 
        $has_fbs = ($score->fbs > 0);
        $dm_score = $score->dm_risk_score;
    @endphp

    {{-- ระดับ 1: น้อย (เขียว) --}}
    @php $is_lvl1 = (!$has_fbs && $dm_score <= 6) || ($has_fbs && $dm_score <= 7); @endphp
    <tr class="{{ $is_lvl1 ? 'fw-bold' : 'text-muted' }}" style="{{ $is_lvl1 ? 'background-color: #f0fff4; border-left: 5px solid #28a745;' : '' }}">
        <td style="color: #28a745;">น้อย (0-5%)</td>
        <td class="text-center {{ $is_lvl1 && !$has_fbs ? 'text-white' : '' }}" style="{{ $is_lvl1 && !$has_fbs ? 'background-color: #28a745;' : '' }}">≤ 6</td>
        <td class="text-center {{ $is_lvl1 && $has_fbs ? 'text-white' : '' }}" style="{{ $is_lvl1 && $has_fbs ? 'background-color: #28a745;' : '' }}">≤ 7</td>
    </tr>

    {{-- ระดับ 2: น้อย-ปานกลาง (ส้ม) --}}
    @php $is_lvl2 = (!$has_fbs && $dm_score >= 7 && $dm_score <= 9) || ($has_fbs && $dm_score >= 8 && $dm_score <= 10); @endphp
    <tr class="{{ $is_lvl2 ? 'fw-bold' : 'text-muted' }}" style="{{ $is_lvl2 ? 'background-color: #fff9f0; border-left: 5px solid #fd7e14;' : '' }}">
        <td style="color: #fd7e14;">น้อย-ปานกลาง (6-10%)</td>
        <td class="text-center {{ $is_lvl2 && !$has_fbs ? 'text-white' : '' }}" style="{{ $is_lvl2 && !$has_fbs ? 'background-color: #fd7e14;' : '' }}">7-9</td>
        <td class="text-center {{ $is_lvl2 && $has_fbs ? 'text-white' : '' }}" style="{{ $is_lvl2 && $has_fbs ? 'background-color: #fd7e14;' : '' }}">8-10</td>
    </tr>

    {{-- ระดับ 3: ปานกลาง-สูง (เหลืองทอง - ปรับให้เข้มขึ้น) --}}
    @php $is_lvl3 = (!$has_fbs && $dm_score >= 10 && $dm_score <= 12) || ($has_fbs && $dm_score >= 11 && $dm_score <= 14); @endphp
    <tr class="{{ $is_lvl3 ? 'fw-bold' : 'text-muted' }}" style="{{ $is_lvl3 ? 'background-color: #fffdf0; border-left: 5px solid #d4a017;' : '' }}">
        <td style="color: #b8860b;">ปานกลาง-สูง (11-20%)</td> {{-- ใช้สี DarkGoldenRod --}}
        <td class="text-center {{ $is_lvl3 && !$has_fbs ? 'text-dark' : '' }}" style="{{ $is_lvl3 && !$has_fbs ? 'background-color: #f1c40f;' : '' }}">10-12</td>
        <td class="text-center {{ $is_lvl3 && $has_fbs ? 'text-dark' : '' }}" style="{{ $is_lvl3 && $has_fbs ? 'background-color: #f1c40f;' : '' }}">11-14</td>
    </tr>

    {{-- ระดับ 4: สูง (แดง) --}}
    @php $is_lvl4 = (!$has_fbs && ($dm_score == 13 || $dm_score == 14)) || ($has_fbs && ($dm_score == 15 || $dm_score == 16)); @endphp
    <tr class="{{ $is_lvl4 ? 'fw-bold' : 'text-muted' }}" style="{{ $is_lvl4 ? 'background-color: #fff5f5; border-left: 5px solid #dc3545;' : '' }}">
        <td style="color: #dc3545;">สูง (21-30%)</td>
        <td class="text-center {{ $is_lvl4 && !$has_fbs ? 'text-white' : '' }}" style="{{ $is_lvl4 && !$has_fbs ? 'background-color: #dc3545;' : '' }}">13-14</td>
        <td class="text-center {{ $is_lvl4 && $has_fbs ? 'text-white' : '' }}" style="{{ $is_lvl4 && $has_fbs ? 'background-color: #dc3545;' : '' }}">15-16</td>
    </tr>

    {{-- ระดับ 5: สูงมาก (ม่วง) --}}
    @php $is_lvl5 = (!$has_fbs && $dm_score >= 15) || ($has_fbs && $dm_score >= 17); @endphp
    <tr class="{{ $is_lvl5 ? 'fw-bold' : 'text-muted' }}" style="{{ $is_lvl5 ? 'background-color: #f8f5ff; border-left: 5px solid #6f42c1;' : '' }}">
        <td style="color: #6f42c1;">สูงมาก (≥ 30%)</td>
        <td class="text-center {{ $is_lvl5 && !$has_fbs ? 'text-white' : '' }}" style="{{ $is_lvl5 && !$has_fbs ? 'background-color: #6f42c1;' : '' }}">≥ 15</td>
        <td class="text-center {{ $is_lvl5 && $has_fbs ? 'text-white' : '' }}" style="{{ $is_lvl5 && $has_fbs ? 'background-color: #6f42c1;' : '' }}">≥ 17</td>
    </tr>
</tbody>
                    </table>

                    <div class="alert alert-info mt-3 py-2 px-3" style="font-size: 0.9rem;">
    <strong>คำแนะนำ:</strong> 
    @if($score->dm_risk_score < 8)
        ผู้ที่มีคะแนนความเสี่ยงน้อยกว่า 8 คะแนน และไม่มีปัจจัยเสี่ยงใดๆ ถือว่ามีความเสี่ยงต่อการเกิดโรคเบาหวานต่ำ ให้ทำแบบประเมินความเสี่ยงด้วยตนเองทุก 5 ปี จนอายุ 35 ปี
    @else
        ถ้ามีคะแนนความเสี่ยงเท่ากับหรือมากกว่า 8 คะแนน หรือมีปัจจัยเสี่ยงต่อการเกิดโรคเบาหวานข้อใดข้อหนึ่ง ให้เข้าสู่ขั้นตอนการประเมินด้วยการตรวจน้ำตาลในเลือด (FBS) ถ้าผลการตรวจเลือดปกติ ถือว่ามีความเสี่ยงต่อการเกิดโรคเบาหวานปานกลาง ให้ทำแบบประเมินตนเองทุก 3 ปี
    @endif
    <hr class="my-1">
    <small class="text-muted">* กรณีที่ไม่สามารถทำแบบประเมินได้ด้วยตนเอง ให้ประเมินโดยบุคลากรทางการแพทย์หรืออาสาสมัครสาธารณสุข</small>
</div>
                </div>
            </div>
        </div>
<div class="col-md-6 mb-4 sortable-item">
    <div class="card shadow-sm h-100 border-0" style="border-radius: 20px;">
        <div class="card-header bg-dark text-white py-3 border-0 d-flex justify-content-between align-items-center" style="border-radius: 20px 20px 0 0;">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-microscope text-warning fs-5"></i>
                <span class="fw-bold text-uppercase" style="letter-spacing: 0.5px; font-size: 0.8rem;">FATTY LIVER & METABOLIC RISK</span>
            </div>
            <i class="fas fa-grip-vertical drag-handle text-white-50 no-print" style="cursor: move;"></i>
        </div>

        <div class="card-body p-4 d-flex flex-column">
    <div class="text-center mb-4 p-3 rounded-4 bg-light">
        <small class="text-muted d-block text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">Metabolic Syndrome</small>
        <h2 class="fw-bold mb-0 {{ $score->metabolic_risk_level != 'ปกติ' ? 'text-danger' : 'text-success' }}" style="font-size: 1.6rem;">
            {{ $score->metabolic_risk_level ?? 'ปกติ' }}
        </h2>
        <small class="text-muted small">ผลการประเมินภาวะอ้วนลงพุง</small>
    </div>

    <div class="advice-section mt-auto">
        <h6 class="fw-bold text-dark mb-3" style="font-size: 0.85rem;">
            <i class="fas fa-list-ul me-2 text-primary"></i> สรุปปัจจัยเสี่ยง (Caution)
        </h6>
        <div class="row g-2 px-1">
            @php
                // เรียงลำดับใหม่ตามเกณฑ์ 1-5
                $metas = [
                    'meta_fbs_risk'         => ['no' => '1', 'label' => 'น้ำตาล', 'icon' => 'fas fa-tint'],
                    'meta_hdl_risk'         => ['no' => '2', 'label' => 'ไขมัน HDL', 'icon' => 'fas fa-shield-heart'],
                    'meta_triglyceride_risk' => ['no' => '3', 'label' => 'ไขมัน TG', 'icon' => 'fas fa-droplet'],
                    'meta_waist_risk'       => ['no' => '4', 'label' => 'รอบเอว', 'icon' => 'fas fa-ruler-combined'],
                    'meta_bp_risk'          => ['no' => '5', 'label' => 'ความดัน', 'icon' => 'fas fa-heartbeat']
                ];
            @endphp

            @foreach($metas as $m_key => $m_info)
            @php 
                // เช็คสถานะความเสี่ยง (ให้ตรงกับ Logic ใน Controller)
                $is_risk = (str_contains($score->$m_key, 'เสี่ยง') || str_contains($score->$m_key, 'เหลือง')); 
            @endphp
            <div class="col" style="min-width: 18%;">
                <div class="text-center h-100 p-2 border rounded-3 transition-hover {{ $is_risk ? 'bg-danger-subtle border-danger-subtle' : 'bg-white' }}">
                    <div class="mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle {{ $is_risk ? 'bg-danger text-white' : 'bg-light text-muted' }}" style="width: 30px; height: 30px; font-size: 0.7rem; position: relative;">
                        <i class="{{ $m_info['icon'] }}"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark" style="font-size: 0.5rem;">
                            {{ $m_info['no'] }}
                        </span>
                    </div>
                    <small class="d-block fw-bold mb-1" style="font-size: 0.65rem;">{{ $m_info['label'] }}</small>
                    <span class="badge rounded-pill fw-bold w-100 {{ $is_risk ? 'bg-danger px-1' : 'bg-light text-success border px-1' }}" style="font-size: 0.6rem;">
                        {{ $is_risk ? 'เสี่ยง' : 'ปกติ' }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

            <div class="advice-text mt-3 alert alert-warning border-0 rounded-3 p-3 small mb-0 d-flex align-items-start shadow-inner" style="background-color: #fffaf0; color: #856404; font-size: 0.8rem;">
                 <i class="fas fa-info-circle fs-6 me-3 mt-1 text-warning"></i>
                 <div>
                    <strong class="d-block mb-1">คำแนะนำเบื้องต้น:</strong>
                    ควรคุมน้ำหนัก, ออกกำลังกาย, ลดหวาน/มัน
                 </div>
            </div>
        </div>
    </div>
</div>

        <div class="card-footer bg-light border-0 py-2 text-center">
            </div> <div class="mt-4 no-print d-flex gap-2">
        <a href="{{ route('risk_scores.index') }}" class="btn btn-outline-secondary px-4 shadow-sm">
            <i class="fas fa-list me-2"></i>รายการทั้งหมด
        </a>
        <a href="{{ route('risk_scores.pdf', $score->id) }}" class="btn btn-danger px-4 shadow-sm" target="_blank"> 
            <i class="fas fa-file-pdf me-2"></i>พิมพ์เอกสาร PDF
        </a>
        <button onclick="window.print()" class="btn btn-primary px-4 shadow-sm">
            <i class="fas fa-print me-2"></i>สั่งพิมพ์หน้าปัจจุบัน
        </button>
    </div>
        </div>
    </div>
</div>
        

    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const el = document.getElementById('sortable-cards');
        new Sortable(el, {
            animation: 350,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function (evt) {
                console.log('ย้ายตำแหน่งเสร็จสมบูรณ์');
            }
        });
    });
</script>

</body>
</html>