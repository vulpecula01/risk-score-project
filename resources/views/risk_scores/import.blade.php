@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">นำเข้าข้อมูลกลุ่ม (Bulk Import)</h2>
    
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-file-excel"></i> อัปโหลดไฟล์ Excel เพื่อคำนวณ Risk Score
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>ขั้นตอนการใช้งาน</h5>
                    <ol>
                        <li>ดาวน์โหลดไฟล์ตัวอย่าง (Template) ด้านขวา</li>
                        <li>กรอกข้อมูลให้ครบถ้วนตามคอลัมน์ที่กำหนด</li>
                        <li>เลือกไฟล์และกดปุ่ม "เริ่มคำนวณ"</li>
                    </ol>
                    <p class="text-danger small">* ระบบจะคำนวณคะแนน DM ปี 2566 และ Stroke Risk Scorecard ให้อัตโนมัติทุกรายชื่อ</p>
                </div>
                <div class="col-md-6 text-center border-start">
                    <a href="{{ asset('templates/risk_import_template.xlsx') }}" class="btn btn-outline-success" download>
    <i class="fas fa-download"></i> ดาวน์โหลดไฟล์ตัวอย่าง Excel
</a>
                    
                    <form action="{{ route('risk-scores.import-process') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                        @csrf
                        <input type="file" name="excel_file" class="form-control mb-3" required>
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-play"></i> เริ่มประมวลผลและบันทึก
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection