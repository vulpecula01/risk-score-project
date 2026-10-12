<!DOCTYPE html>
<html lang="th">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>รายงานผลการประเมินความเสี่ยง - {{ $score->name }}</title>
    <style>
    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: normal;
        src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
    }
    @font-face {
        font-family: 'THSarabunNew';
        font-style: normal;
        font-weight: bold;
        src: url("{{ storage_path('fonts/THSarabunNew-Bold.ttf') }}") format('truetype');
    }

    * { font-family: 'THSarabunNew', sans-serif !important; }
    body { font-size: 16pt; line-height: 1.1; color: #333; }
    p, td, div { word-break: break-all; word-wrap: break-word; }

    .header { text-align: center; margin-bottom: 20px; }
    .section-title {
        background-color: #f2f2f2;
        padding: 5px 10px;
        font-weight: bold;
        border-left: 5px solid #2c3e50;
        margin-top: 15px;
    }

    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table, th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
    th { background-color: #f9f9f9; }

    .result-box {
        border: 2px solid #2c3e50;
        padding: 10px;
        margin-top: 10px;
        text-align: center;
    }

    /* สไตล์สี */
    .text-green  { color: #27ae60; font-weight: bold; }
    .text-yellow { color: #f1c40f; font-weight: bold; }
    .text-orange { color: #e67e22; font-weight: bold; }
    .text-red    { color: #e74c3c; font-weight: bold; }
    .badge-gray  { color: #7f8c8d; }

    .footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        text-align: right;
        font-size: 12pt;
        color: #777;
    }
    </style>
</head>
<body>

    <div class="header">
        <h2>รายงานผลการประเมินความเสี่ยงสุขภาพ (Health Risk Report)</h2>
        <p>วันที่พิมพ์: {{ $printed_at }}</p>
    </div>

    <div class="section-title">ข้อมูลส่วนบุคคล</div>
    <table>
        <tr>
            <td width="30%"><strong>ชื่อ-นามสกุล:</strong></td>
            <td>{{ $score->name }}</td>
            <td width="20%"><strong>อายุ:</strong></td>
            <td>{{ $score->age }} ปี</td>
        </tr>
        <tr>
            <td><strong>เพศ:</strong></td>
            <td>{{ $score->gender == 0 ? 'ชาย' : 'หญิง' }}</td>
            <td><strong>ดัชนีมวลกาย (BMI):</strong></td>
            <td>{{ number_format($score->bmi, 2) }}</td>
        </tr>
    </table>

    <div class="section-title">1. สรุปความเสี่ยงโรคหัวใจและหลอดเลือด (Thai CVD Risk)</div>
    <div class="result-box">
        <div style="font-size: 18pt;">โอกาสเกิดโรคใน 10 ปี: <strong>{{ $score->cvd_risk_display }}</strong></div>
        <div class="{{ str_contains($score->risk_level, 'สูง') ? 'text-red' : (str_contains($score->risk_level, 'ปานกลาง') ? 'text-yellow' : 'text-green') }}">
            สถานะ: {{ $score->risk_level }}
        </div>
    </div>

    <div class="section-title">2. สรุปความเสี่ยงโรคหลอดเลือดสมอง (Stroke Risk)</div>
    <table>
        <tr>
            <th>ประเภทความเสี่ยง</th>
            <th>ระดับความเสี่ยง</th>
        </tr>
        <tr>
            <td>จำนวนปัจจัยเสี่ยงระดับสูง (High Risk) / ปัจจัยที่ควรระวัง (Caution)</td>
            <td>{{ $redCount }} สูง / {{ $yellowCount }} ระวัง</td>
        </tr>
        <tr>
            <td><strong>สรุปภาพรวม Stroke Risk</strong></td>
            <td class="{{ str_contains($score->stroke_risk_level, 'สูง') ? 'text-red' : (str_contains($score->stroke_risk_level, 'ปานกลาง') ? 'text-orange' : (str_contains($score->stroke_risk_level, 'น้อย') ? 'text-yellow' : 'text-green')) }}">
                {{ $score->stroke_risk_level }}
            </td>
        </tr>
    </table>

    <div class="section-title">3. สรุปความเสี่ยงโรคเบาหวาน (DM Risk)</div>
    <p style="margin-top: 5px;">
        คะแนนรวม: <strong>{{ $score->dm_risk_score }} คะแนน</strong> | 
        ระดับความเสี่ยง: 
        <span class="{{ str_contains($score->dm_risk_level, 'สูง') ? 'text-red' : (str_contains($score->dm_risk_level, 'ปานกลาง') ? 'text-orange' : 'text-green') }}">
            {{ $score->dm_risk_level }}
        </span>
    </p>

    <div class="section-title">4. ความเสี่ยงต่อไขมันพอกตับ (Metabolic Syndrome 5 ข้อ)</div>
    <div style="margin-top: 5px; padding: 5px; border: 1px dashed #ccc;">
        <strong>สรุปผล:</strong> {{ $score->metabolic_risk_level }}
    </div>
    

    <div class="footer">ระบบประเมินความเสี่ยงสุขภาพอัตโนมัติ</div>

</body>
</html>