<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        .advice-col { width: 50%; vertical-align: top; padding: 10px; font-size: 9.5pt; line-height: 1.4; }
    
    /* หัวข้อย่อยแบบ ก), ข) หรือ 1., 2. */
    .sub-content { margin-left: 20px; margin-bottom: 10px; }
    
    /* รายการที่มีเครื่องหมายถูก (✓) นำหน้าแบบเป๊ะๆ */
    .check-list { list-style: none; padding: 0; margin: 5px 0; }
    .check-list li { position: relative; padding-left: 20px; margin-bottom: 3px; }
    .check-list li:before { content: "✓"; position: absolute; left: 0; font-weight: bold; }

    /* รายการที่มีจุดดำ (•) ย่อยด้านใน */
    .bullet-list { list-style: none; padding: 0; margin: 5px 0 5px 15px; }
    .bullet-list li { position: relative; padding-left: 15px; }
    .bullet-list li:before { content: "•"; position: absolute; left: 0; }

    /* ตารางไขมันพอกตับ */
    .liver-table { width: 100%; border-collapse: collapse; margin-top: 10px; border: 1px solid #000; }
    .liver-table th, .liver-table td { border: 1px solid #000; text-align: center; padding: 5px; font-size: 9pt; }
    .liver-table th { background-color: #f2f2f2; }
    
    .symbol-check { font-family: DejaVu Sans, sans-serif; } /* ป้องกันตัวอักษรเพี้ยนใน PDF */
    .advice-col { 
        width: 50%; 
        vertical-align: top; 
        padding: 5px 10px; 
        border-right: 1px solid #003366; 
        font-size: 10.5pt; /* ปรับขนาดให้เล็กลงเล็กน้อยเพื่อให้ข้อความเต็มๆ อยู่ครบ */
        line-height: 1.2; 
    }
.liver-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 9.5pt; }
    .liver-table th, .liver-table td { border: 1px solid #000; text-align: center; padding: 3px; }
    .liver-table th { background-color: #e6f2ff; }
    
    /* สไตล์เครื่องหมายถูกและดาว */
    .check-mark { font-family: DejaVu Sans, sans-serif; color: #000; font-size: 11pt; }
    .star-icon { color: #000; font-size: 10pt; }

    /* ปรับแต่ง List ให้ไม่มี bullet เดิมและใช้สไตล์ตามภาพ */
    .custom-list { list-style-type: none; padding-left: 15px; }
    .custom-list li { position: relative; margin-bottom: 3px; }
    .custom-list li:before { content: "✓"; position: absolute; left: -15px; font-weight: bold; }
    
    .nested-alphabet { list-style-type: lower-latin; padding-left: 25px; }
    .info-icon { display: inline-block; width: 15px; height: 15px; vertical-align: middle; }
    /* สไตล์สำหรับหัวข้อกรณีต่างๆ */
    .risk-case-title {
        font-weight: bold;
        color: #003366;
        margin-top: 8px;
        display: block;
        border-left: 4px solid #003366;
        padding-left: 5px;
        background-color: #f9f9f9;
    }

    /* จัดสไตล์รายการข้อ 1, 2, 3 ให้สวยงาม */
    ul.advice-list {
        margin: 5px 0 10px 18px; /* เว้นระยะจากหัวข้อ */
        padding: 0;
        list-style-type: none; /* ปิด bullet เดิม */
    }

    ul.advice-list li {
        position: relative;
        margin-bottom: 4px; /* ระยะห่างระหว่างข้อ */
        text-align: justify; /* ให้ข้อความตัดคำสวยๆ */
    }

    /* ใส่เลขข้อหรือสัญลักษณ์ด้านหน้า */
    ul.advice-list li:before {
        content: "•"; /* ใช้สัญลักษณ์ Bullet หรือจะเปลี่ยนเป็นตัวเลขก็ได้ */
        color: #003366;
        font-weight: bold;
        position: absolute;
        left: -15px;
    }
        .advice-list { list-style-type: none; padding: 0; margin: 0; }
        .advice-list li { margin-bottom: 4px; text-align: justify; }
        .advice-title { font-weight: bold; color: #003366; text-decoration: underline; display: block; margin-bottom: 3px; }
        .risk-group { margin-bottom: 8px; border-bottom: 1px dashed #eee; padding-bottom: 5px; }
        .risk-group:last-child { border-bottom: none; }
        

    /* จัดการรายการข้อใหญ่ */
    .advice-item {
        margin-bottom: 10px;
        position: relative;
    }
    
    /* ส่วนของข้อความที่มีจุดย่อยด้านใน */
    .sub-bullet-container {
        margin-left: 20px; /* เยื้องให้ตรงกับแนวข้อความ */
        margin-top: 3px;
    }
    
    .sub-bullet {
        display: block; /* ให้ขึ้นบรรทัดใหม่เป็นข้อๆ */
        position: relative;
        padding-left: 15px; /* เว้นที่ให้จุดดำ */
        margin-bottom: 2px;
    }
    
    .sub-bullet:before {
        content: "•"; /* ใส่จุดดำนำหน้า */
        position: absolute;
        left: 0;
        font-weight: bold;
    }
        @font-face {
        font-family: 'THSarabunNew';
        /* ใช้คำสั่ง public_path เพื่อให้ dompdf หาไฟล์ในระบบ Linux เจอ */
        src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    @font-face {
        font-family: 'THSarabunNew';
        src: url("{{ public_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
        font-weight: bold;
        font-style: normal;
    }
        
        @page { margin: 15px; } /* ลดขอบกระดาษทุกด้านให้เหลือน้อยที่สุด */

        body { 
            font-family: 'THSarabunNew'; 
            font-size: 13pt; /* ปรับขนาดฟอนต์ให้สมดุล */
            line-height: 1.0; 
            margin: 0; padding: 0; 
            color: #333;
        }
        
        .w-100 { width: 100%; }
        .main-table { width: 100%; border-collapse: collapse; margin-bottom: 0px; }
        .main-table td { vertical-align: top; padding: 1px 0; }
        
        .label { color: #003366; white-space: nowrap; font-size: 12pt; }
        .value { font-weight: bold; padding-left: 2px; color: #000; font-size: 12pt; }
        
        /* Header ชิดขอบบน */
        .header-container { border-bottom: 1.5px solid #003366; margin-bottom: 5px; padding-bottom: 2px; }
        .logo { width: 130px; height: auto; margin-top: -5px; } /* เลื่อนโลโก้ขึ้น */
        
        /* Risk Box แบบติดกัน */
        .risk-section { 
            border: 1px solid #2e3192; 
            border-radius: 4px; 
            margin-top: 3px; 
            padding: 4px 8px;
            background-color: #fcfcfc;
        }
        .risk-title { color: #2e3192; font-weight: bold; font-size: 13.5pt; display: inline-block; }
        
        /* Advice Section บีบพื้นที่ */
        .advice-container { margin-top: 5px; border: 1.2px solid #003366; }
        .advice-header { 
            background-color: #f0f7ff; text-align: center; font-weight: bold; 
            padding: 1px; border-bottom: 1px solid #003366; color: #003366; font-size: 13pt;
        }
        .advice-col { 
            width: 50%; vertical-align: top; padding: 3px 6px; 
            border-right: 1px solid #003366; font-size: 11pt; line-height: 1.0;
        }
        .color-box { display: inline-block; width: 10px; height: 10px; margin-left: 2px; border: 0.5px solid #000; vertical-align: middle; }
        
        .footer-note { font-size: 8.5pt; margin-top: 3px; color: #003366; text-align: center; }
    </style>
</head>
<body>
@php
    // ฟังก์ชันเลือกสีสำหรับ Thai CVD Risk และ Stroke
    $getColor = function($level) {
        $level = trim($level);
        if (str_contains($level, 'ต่ำ') || str_contains($level, 'ปกติ') || str_contains($level, 'น้อย')) return '#28a745'; // เขียว
        if (str_contains($level, 'ปานกลาง')) return '#ffc107'; // เหลือง/ทอง
        if (str_contains($level, 'สูงมาก') || str_contains($level, 'รุนแรง')) return '#663399'; // ม่วง
        if (str_contains($level, 'สูง')) return '#fd7e14'; // ส้ม
        if (str_contains($level, 'อันตราย') || str_contains($level, 'แดง')) return '#dc3545'; // แดง
        return '#000'; // ดำปกติ
    };

    // ฟังก์ชันเลือกสีสำหรับ ไขมันพอกตับ (ตามจำนวนข้อ)
    $getLiverColor = function($count) {
        if ($count == 0) return '#28a745'; // เขียว
        if ($count <= 2) return '#ffc107'; // เหลือง
        if ($count <= 4) return '#fd7e14'; // ส้ม
        return '#dc3545'; // แดง (5 ข้อ)
    };
@endphp
    <div class="header-container">
        <table class="main-table">
            <tr>
                <td width="20%"><span class="label">No:</span> <span class="value">{{ $score->id }}</span></td>
                <td width="25%"><span class="label">HN:</span> <span class="value">{{ $score->hn }}</span></td>
                <td width="25%"><span class="label">ประจำปี:</span> <span class="value">{{ date('Y') + 543 }}</span></td>
                <td width="30%" align="right" rowspan="2">
                    <img src="{{ public_path('images/brh.png') }}" class="logo">
                </td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">ชื่อ-สกุล:</span> <span class="value">{{ $score->name }}</span></td>
                <td width="30%"><span class="label">บริษัท:</span> <span class="value">{{ $score->company ?? 'บริษัท โรงพยาบาลกรุงเทพระยอง จำกัด' }}</span></td>
            </tr>
        </table>
    </div>

    <table class="main-table" style="margin-top: -5px;">
    <tr>
        <td width="15%"><span class="label">เพศ:</span> <span class="value">{{ $score->gender == 0 ? 'ชาย' : 'หญิง' }}</span></td>
        <td width="15%"><span class="label">อายุ (ปี):</span> <span class="value">{{ $score->age }}</span></td>
        <td width="20%"><span class="label">รอบเอว (ซม.):</span> <span class="value">{{ $score->waist }}</span></td>
        <td width="20%"><span class="label">ส่วนสูง (ซม.):</span> <span class="value">{{ $score->height }}</span></td>
        <td width="30%"><span class="label">ดัชนีมวลกาย (kg/m²):</span> <span class="value">{{ number_format($score->bmi, 2) }}</span></td>
    </tr>

    <tr>
        <td width="25%"><span class="label">ความดันโลหิต (mmHg):</span> <span class="value">{{ $score->bp_sys }} / {{ $score->bp_dias }}</span></td>
        <td width="25%"><span class="label">น้ำตาลในเลือด (mg/dL):</span> <span class="value">{{ $score->fbs ?? 0 }}</span></td>
        <td width="30%"><span class="label">ไขมันคอเลสเตอรอล (mg/dL):</span> <span class="value">{{ $score->cholesterol ?? 0 }}</span></td>
        <td width="25%"><span class="label">ไขมันดี HDL (mg/dL):</span> <span class="value">{{ $score->hdl ?? 0 }}</span></td>
    </tr>

    <tr>
        <td width="30%"><span class="label">ไขมันไตรกลีเซอไรด์ (mg/dL):</span> <span class="value">{{ $score->triglyceride ?? 0 }}</span></td>
        <td width="25%"><span class="label">ประวัติการสูบบุหรี่:</span> <span class="value">{{ $score->smoking == 1 ? 'สูบ' : 'ไม่สูบ' }}</span></td>
        <td colspan="2"><span class="label">ประวัติการออกกำลังกาย:</span> <span class="value">{{ $score->exercise == 2 ? 'ออกกำลังกายเป็นประจำ' : ($score->exercise == 1 ? 'บางครั้ง' : 'ไม่ออกกำลังกาย') }}</span></td>
    </tr>

    <tr>
        <td colspan="2"><span class="label">ประวัติครอบครัวสายตรงเป็น โรคหลอดเลือดสมอง:</span> <span class="value">{{ $score->fam_history_stroke == 2 ? 'มี' : 'ไม่มี' }}</span></td>
        <td colspan="2"><span class="label">ประวัติครอบครัวสายตรงเป็น โรคเบาหวาน:</span> <span class="value">{{ $score->fam_history_dm == 1 ? 'มี' : 'ไม่มี' }}</span></td>
    </tr>

    <tr>
        <td colspan="2"><span class="label">โรคประจำตัวเป็นเบาหวาน:</span> <span class="value">{{ $score->dm_from_lab == 2 ? 'มี' : 'ไม่มี' }}</span></td>
        <td colspan="2"><span class="label">โรคประจำตัวเป็นความดันโลหิตสูง:</span> <span class="value">{{ $score->hypertension == 1 ? 'มี' : 'ไม่มี' }}</span></td>
    </tr>
</table>

    <div class="risk-section">
        <table class="w-100">
            <tr>
                <td width="7%"><img src="{{ public_path('images/heart.jpg') }}" style="width: 25px;"></td>
                <td width="58%"><span class="risk-title">Thai CVD risk (10 ปี)</span></td>
                <td width="35%" align="right"><span class="value" style="color: {{ $getColor($score->risk_level) }}; font-size: 13pt;">
    {{ $score->percent_cvd_risk }}% ({{ $score->risk_level }})
</span></td>
            </tr>
        </table>
    </div>

    <div class="risk-section" style="border-color: #663399;">
        <table class="w-100">
            <tr>
                <td width="7%"><img src="{{ public_path('images/brainp.png') }}" style="width: 25px;"></td>
                <td width="58%"><span class="risk-title" style="color: #663399;">Stroke Risk (โรคหลอดเลือดสมอง)</span></td>
                <td width="35%" align="right"><span class="value" style="color: {{ $getColor($score->stroke_risk_level) }}; font-size: 13pt;">
    {{ $score->stroke_risk_level }}
</span></td>
            </tr>
        </table>
    </div>

    <div class="advice-container" style="margin-top: 5px; page-break-inside: avoid;">
    <div class="advice-header" style="font-size: 13pt; padding: 2px;">คำแนะนำระดับความเสี่ยงแต่ละด้าน</div>
    
    <div style="border: 1.2px solid #002e5d; background-color: white; overflow: hidden;">
        <table width="100%" cellpadding="0" cellspacing="0" style="table-layout: fixed; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; border-right: 1.2px solid #002e5d; padding: 2px;">
                    <img src="{{ public_path('images/cvr.jpg') }}" 
                         style="width: 100%; max-height: 680px; display: block; object-fit: contain;">
                </td>
                
                <td style="width: 50%; vertical-align: top; padding: 2px;">
                    <img src="{{ public_path('images/stroke.jpg') }}" 
                         style="width: 100%; max-height: 680px; display: block; object-fit: contain;">
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 5px; line-height: 0;">
    <img src="{{ public_path('images/page1.jpg') }}" style="width: 100%; height: auto; display: block;">
</div>
</div>

    <div style="page-break-before: always;"></div>

<div class="header-container">
        <table class="main-table">
            <tr>
                <td width="20%"><span class="label">No:</span> <span class="value">{{ $score->id }}</span></td>
                <td width="25%"><span class="label">HN:</span> <span class="value">{{ $score->hn }}</span></td>
                <td width="25%"><span class="label">ประจำปี:</span> <span class="value">{{ date('Y') + 543 }}</span></td>
                <td width="30%" align="right" rowspan="2">
                    <img src="{{ public_path('images/brh.png') }}" class="logo">
                </td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">ชื่อ-สกุล:</span> <span class="value">{{ $score->name }}</span></td>
                <td width="30%"><span class="label">บริษัท:</span> <span class="value">{{ $score->company ?? 'บริษัท โรงพยาบาลกรุงเทพระยอง จำกัด' }}</span></td>
            </tr>
        </table>
    </div>

    <table class="main-table" style="margin-top: -5px;">
    <tr>
        <td width="15%"><span class="label">เพศ:</span> <span class="value">{{ $score->gender == 0 ? 'ชาย' : 'หญิง' }}</span></td>
        <td width="15%"><span class="label">อายุ (ปี):</span> <span class="value">{{ $score->age }}</span></td>
        <td width="20%"><span class="label">รอบเอว (ซม.):</span> <span class="value">{{ $score->waist }}</span></td>
        <td width="20%"><span class="label">ส่วนสูง (ซม.):</span> <span class="value">{{ $score->height }}</span></td>
        <td width="30%"><span class="label">ดัชนีมวลกาย (kg/m²):</span> <span class="value">{{ number_format($score->bmi, 2) }}</span></td>
    </tr>

    <tr>
        <td width="25%"><span class="label">ความดันโลหิต (mmHg):</span> <span class="value">{{ $score->bp_sys }} / {{ $score->bp_dias }}</span></td>
        <td width="25%"><span class="label">น้ำตาลในเลือด (mg/dL):</span> <span class="value">{{ $score->fbs ?? 0 }}</span></td>
        <td width="30%"><span class="label">ไขมันคอเลสเตอรอล (mg/dL):</span> <span class="value">{{ $score->cholesterol ?? 0 }}</span></td>
        <td width="25%"><span class="label">ไขมันดี HDL (mg/dL):</span> <span class="value">{{ $score->hdl ?? 0 }}</span></td>
    </tr>

    <tr>
        <td width="30%"><span class="label">ไขมันไตรกลีเซอไรด์ (mg/dL):</span> <span class="value">{{ $score->triglyceride ?? 0 }}</span></td>
        <td width="25%"><span class="label">ประวัติการสูบบุหรี่:</span> <span class="value">{{ $score->smoking == 1 ? 'สูบ' : 'ไม่สูบ' }}</span></td>
        <td colspan="2"><span class="label">ประวัติการออกกำลังกาย:</span> <span class="value">{{ $score->exercise == 2 ? 'ออกกำลังกายเป็นประจำ' : ($score->exercise == 1 ? 'บางครั้ง' : 'ไม่ออกกำลังกาย') }}</span></td>
    </tr>

    <tr>
        <td colspan="2"><span class="label">ประวัติครอบครัวสายตรงเป็น โรคหลอดเลือดสมอง:</span> <span class="value">{{ $score->fam_history_stroke == 2 ? 'มี' : 'ไม่มี' }}</span></td>
        <td colspan="2"><span class="label">ประวัติครอบครัวสายตรงเป็น โรคเบาหวาน:</span> <span class="value">{{ $score->fam_history_dm == 1 ? 'มี' : 'ไม่มี' }}</span></td>
    </tr>

    <tr>
        <td colspan="2"><span class="label">โรคประจำตัวเป็นเบาหวาน:</span> <span class="value">{{ $score->dm_from_lab == 2 ? 'มี' : 'ไม่มี' }}</span></td>
        <td colspan="2"><span class="label">โรคประจำตัวเป็นความดันโลหิตสูง:</span> <span class="value">{{ $score->hypertension == 1 ? 'มี' : 'ไม่มี' }}</span></td>
    </tr>
</table>
    </div>

    <div class="risk-section">
        <table class="w-100">
            <tr>
                <td width="7%"><img src="{{ public_path('images/dmp.jpg') }}" style="width: 28px;"></td>
                <td width="58%"><span class="risk-title" style="color: #f7941d;">ความเสี่ยงต่อการเกิดโรคเบาหวาน (Diabetes Risk)</span></td>
                <td width="35%" align="right">
                    <span class="value" style="color: {{ $getColor($score->dm_risk_level) }}; font-size: 13pt;">
    {{ $score->dm_risk_level ?? 'ไม่ระบุ' }}
</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="risk-section">
        <table class="w-100">
            <tr>
                <td width="7%"><img src="{{ public_path('images/liverp.jpg') }}" style="width: 28px;"></td>
                <td width="58%"><span class="risk-title" style="color: #ed1c24;">ความเสี่ยงโรคไขมันพอกตับ (Fatty Liver Risk)</span></td>
                <td width="35%" align="right">
    <span class="value" style="color: #ed1c24; font-size: 13pt;">
        @php
            $actual_count = 0;
            // ตรวจสอบปัจจัยเสี่ยงโดยใช้ชื่อคอลัมน์ให้ตรงกับ Logic ด้านบน (str_contains 'เสี่ยง' หรือ 'เหลือง')
            if (str_contains($score->meta_fbs_risk ?? '', 'เสี่ยง') || str_contains($score->meta_fbs_risk ?? '', 'เหลือง')) $actual_count++;
            if (str_contains($score->meta_hdl_risk ?? '', 'เสี่ยง') || str_contains($score->meta_hdl_risk ?? '', 'เหลือง')) $actual_count++;
            if (str_contains($score->meta_triglyceride_risk ?? '', 'เสี่ยง') || str_contains($score->meta_triglyceride_risk ?? '', 'เหลือง')) $actual_count++;
            if (str_contains($score->meta_waist_risk ?? '', 'เสี่ยง') || str_contains($score->meta_waist_risk ?? '', 'เหลือง')) $actual_count++;
            if (str_contains($score->meta_bp_risk ?? '', 'เสี่ยง') || str_contains($score->meta_bp_risk ?? '', 'เหลือง')) $actual_count++;
        @endphp
        พบปัจจัยเสี่ยง {{ $actual_count }} จาก 5 ข้อ
    </span>
</td>
            </tr>
        </table>
    </div>

    <div class="advice-container" style="margin-top: 5px; page-break-inside: avoid;">
    <div class="advice-header" style="font-size: 13pt; padding: 2px;">คำแนะนำสำหรับความเสี่ยงโรคเบาหวานและโรคไขมันพอกตับ แต่ละระดับความเสี่ยง</div>
    
    <div style="border: 1.2px solid #002e5d; background-color: white; overflow: hidden;">
        <table width="100%" cellpadding="0" cellspacing="0" style="table-layout: fixed; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; border-right: 1.2px solid #002e5d; padding: 2px;">
                    <img src="{{ public_path('images/dm.jpg') }}" 
                         style="width: 100%; max-height: 680px; display: block; object-fit: contain;">
                </td>
                
                <td style="width: 50%; vertical-align: top; padding: 2px;">
                    <img src="{{ public_path('images/liver.jpg') }}" 
                         style="width: 100%; max-height: 680px; display: block; object-fit: contain;">
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 5px; line-height: 0;">
    <img src="{{ public_path('images/page2.jpg') }}" style="width: 100%; height: auto; display: block;">
</div>
</div>

</body>
</html>