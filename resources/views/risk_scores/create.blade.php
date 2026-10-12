<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบฟอร์มประเมินความเสี่ยงสุขภาพ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body { background-color: #f0f2f5; }
        .card-header-custom {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            font-size: 1.15rem;
            padding: 15px;
        }
        .form-check-inline { margin-right: 1.5rem; }
        .form-label { font-weight: 600; color: #34495e; margin-bottom: 0.2rem; }
        .accordion-button { font-weight: bold; font-size: 1.1rem; }
        .accordion-item { border-color: #bdc3c7; }
        .top-right-link {
            position: absolute;
            top: 25px;
            right: 25px;
        }
        .lab-input-group .input-group-text {
            background-color: #ecf0f1;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        
        <div class="d-flex justify-content-center align-items-center mb-4 position-relative">
            <h1 style="color:#2c3e50;"><i class="fas fa-notes-medical me-2"></i> แบบฟอร์มประเมินความเสี่ยงสุขภาพ</h1>
            
            <a href="{{ route('risk_scores.index') }}" class="btn btn-outline-secondary btn-sm top-right-link shadow-sm d-none d-md-block">
                <i class="fas fa-list me-1"></i> ดูรายการทั้งหมด
            </a>
        </div>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        @if ($errors->any())
            <div class="alert alert-danger shadow-sm">
                <i class="fas fa-exclamation-triangle me-2"></i> กรุณาตรวจสอบข้อมูลที่กรอกให้ถูกต้อง
            </div>
        @endif

        <form method="POST" action="{{ route('risk_scores.store') }}">
            @csrf

            <div class="accordion shadow-lg" id="riskFormAccordion">
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button bg-primary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <i class="fas fa-id-card me-2"></i> 1. ข้อมูลส่วนบุคคล (Personal & Biometrics)
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#riskFormAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-4 mb-3">
    <label for="hn" class="form-label">เลข HN / CN</label>
    <div class="input-group">
        <input type="text" class="form-control" id="hn" name="hn" value="{{ old('hn') }}" placeholder="ระบุเลข HN">
        <span class="input-group-text" id="hn-status"><i class="fas fa-search"></i></span>
    </div>
    <small id="hn-message" class="text-muted"></small>
</div>
                                <div class="col-md-4 mb-3">
                                    <label for="name" class="form-label">ชื่อ-นามสกุล</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="กรอกชื่อ-นามสกุล" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label class="form-label d-block">เพศ</label>
                                    <div class="d-flex align-items-center pt-1">
                                        <div class="form-check form-check-inline me-4">
                                            <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="gender_0" value="0" {{ old('gender') == '0' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="gender_0">ชาย</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="gender_1" value="1" {{ old('gender') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="gender_1">หญิง</label>
                                        </div>
                                    </div>
                                    @error('gender')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="age" class="form-label">อายุ (ปี)</label>
                                    <input type="number" class="form-control @error('age') is-invalid @enderror" id="age" name="age" value="{{ old('age') }}" required min="1" placeholder="เช่น 42">
                                    @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="weight" class="form-label">น้ำหนัก (กก.)</label>
                                    <input type="number" step="0.1" class="form-control @error('weight') is-invalid @enderror" id="weight" name="weight" value="{{ old('weight') }}" required min="1" placeholder="เช่น 65.5">
                                    @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="height" class="form-label">ส่วนสูง (ซม.)</label>
                                    <input type="number" step="0.1" class="form-control @error('height') is-invalid @enderror" id="height" name="height" value="{{ old('height') }}" required min="10" placeholder="เช่น 154.5">
                                    @error('height')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="bmi" class="form-label">BMI</label>
                                    <input type="text" class="form-control @error('bmi') is-invalid @enderror" id="bmi" name="bmi" value="{{ old('bmi') }}" readonly placeholder="คำนวณอัตโนมัติ">
                                    @error('bmi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                {{-- 🌟 START: เพิ่มช่องกรอกรอบเอว (นิ้ว) และช่องแปลงเป็น (ซม.) --}}
                                <div class="col-md-4 mb-3">
                                    <label for="waist_inch" class="form-label">รอบเอว (นิ้ว)</label>
                                    <input type="number" step="0.1" class="form-control" id="waist_inch" name="waist_inch" value="{{ old('waist_inch') }}" placeholder="กรอกเป็นนิ้ว">
                                    <small class="text-muted">*ระบบจะแปลงเป็น ซม. ให้ทันที</small>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="waist" class="form-label">รอบเอว (ซม.)</label>
                                    <input type="number" step="0.01" class="form-control @error('waist') is-invalid @enderror" id="waist" name="waist" value="{{ old('waist') }}" required min="10" placeholder="คำนวณจากนิ้ว หรือกรอกเอง">
                                    @error('waist')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                {{-- 🌟 END: รอบเอว --}}

                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed bg-info text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <i class="fas fa-vial me-2"></i> 2. ข้อมูลทางคลินิก (Vitals & Lab Results)
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#riskFormAccordion">
                        <div class="accordion-body">
                            <h5 class="mb-3 text-secondary">ข้อมูล Vital Signs</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="bp_sys" class="form-label">BP (Sys) (mmHg)</label>
                                    <input type="number" class="form-control @error('bp_sys') is-invalid @enderror" id="bp_sys" name="bp_sys" value="{{ old('bp_sys') }}" required min="50">
                                    @error('bp_sys')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="bp_dias" class="form-label">BP (Dias) (mmHg)</label>
                                    <input type="number" class="form-control @error('bp_dias') is-invalid @enderror" id="bp_dias" name="bp_dias" value="{{ old('bp_dias') }}" required min="30">
                                    @error('bp_dias')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="pulse" class="form-label">Pulse (ครั้ง/นาที)</label>
                                    <input type="number" class="form-control @error('pulse') is-invalid @enderror" id="pulse" name="pulse" value="{{ old('pulse') }}" required min="40">
                                    @error('pulse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            
                            <hr>
                            <h5 class="mb-3 text-secondary">ผลเลือด (หากไม่มีให้เลือก "ไม่มี")</h5>

<div class="row">
    @php
        $labs = [
            ['id' => 'fbs', 'label' => 'FBS (mg/dL)', 'min' => 40, 'default' => '90'],
            ['id' => 'cholesterol', 'label' => 'Total Cholesterol (mg/dL)', 'min' => 50, 'default' => '190'],
            ['id' => 'triglyceride', 'label' => 'Triglyceride (mg/dL)', 'min' => 10, 'default' => '140'],
            ['id' => 'hdl', 'label' => 'HDL (mg/dL)', 'min' => 10, 'default' => '50']
        ];
    @endphp

    @foreach($labs as $lab)
    <div class="col-md-6 mb-4 lab-input-group">
        <label class="form-label">{{ $lab['label'] }}</label>
        <div class="input-group">
            <div class="input-group-text p-0 border-end-0">
                <div class="form-check form-check-inline ms-3">
                    <input class="form-check-input lab-present" type="radio" 
                           name="{{ $lab['id'] }}_present" id="{{ $lab['id'] }}_yes" value="yes" 
                           {{ old($lab['id'].'_present') == 'yes' ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $lab['id'] }}_yes">มี</label>
                </div>
                <div class="form-check form-check-inline me-3">
                    <input class="form-check-input lab-present" type="radio" 
                           name="{{ $lab['id'] }}_present" id="{{ $lab['id'] }}_no" value="no" 
                           {{ old($lab['id'].'_present', 'no') == 'no' ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $lab['id'] }}_no">ไม่มี</label>
                </div>
            </div>
            <input type="number" 
                   class="form-control lab-input @error($lab['id']) is-invalid @enderror" 
                   id="{{ $lab['id'] }}" 
                   name="{{ $lab['id'] }}" 
                   value="{{ old($lab['id']) }}" 
                   min="{{ $lab['min'] }}" 
                   placeholder="ระบุค่า {{ $lab['id'] }}" 
                   step="1"
                   {{ old($lab['id'].'_present', 'no') == 'no' ? 'disabled' : '' }} 
                   required>
            @error($lab['id'])<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    @endforeach
</div>
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed bg-success text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <i class="fas fa-history me-2"></i> 3. ประวัติ พฤติกรรม และความเสี่ยง (Risk Factors)
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#riskFormAccordion">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
    <label class="form-label">1. Smoking (การสูบบุหรี่)</label><br>
    <div class="d-flex flex-wrap pt-2">
        <div class="form-check form-check-inline me-4">
            <input class="form-check-input" type="radio" name="smoking" id="smoking_1" value="1" {{ old('smoking') == '1' ? 'checked' : '' }} required>
            <label class="form-check-label" for="smoking_1">สูบ</label>
        </div>
        <div class="form-check form-check-inline me-4">
            <input class="form-check-input" type="radio" name="smoking" id="smoking_2" value="2" {{ old('smoking') == '2' ? 'checked' : '' }}>
            <label class="form-check-label" for="smoking_2">กำลังเลิก</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="smoking" id="smoking_0" value="0" {{ old('smoking') == '0' ? 'checked' : '' }}>
            <label class="form-check-label" for="smoking_0">ไม่สูบ</label>
        </div>
    </div>
    @error('smoking')<div class="text-danger mt-1">{{ $message }}</div>@enderror
</div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">2. Exercise (การออกกำลังกาย)</label><br>
                                    <div class="d-flex flex-wrap pt-2">
                                        <div class="form-check form-check-inline me-4">
                                            <input class="form-check-input" type="radio" name="exercise" id="exercise_2" value="2" {{ old('exercise') == '2' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="exercise_2">เป็นประจำ</label>
                                        </div>
                                        <div class="form-check form-check-inline me-4">
                                            <input class="form-check-input" type="radio" name="exercise" id="exercise_1" value="1" {{ old('exercise') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="exercise_1">บางครั้ง</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="exercise" id="exercise_0" value="0" {{ old('exercise') == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="exercise_0">ไม่ออก</label>
                                        </div>
                                    </div>
                                    @error('exercise')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">3. ประวัติครอบครัวเบาหวาน</label><br>
                                    <div class="d-flex flex-wrap pt-2">
                                        <div class="form-check form-check-inline me-4">
                                            <input class="form-check-input" type="radio" name="fam_history_dm" id="fam_history_dm_1" value="1" {{ old('fam_history_dm') == '1' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="fam_history_dm_1">มี</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="fam_history_dm" id="fam_history_dm_0" value="0" {{ old('fam_history_dm') == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fam_history_dm_0">ไม่มี</label>
                                        </div>
                                    </div>
                                    @error('fam_history_dm')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">4. ประวัติครอบครัวหลอดเลือดสมอง</label><br>
                                    <div class="d-flex flex-wrap pt-2">
                                        <div class="form-check form-check-inline me-4">
                                            <input class="form-check-input" type="radio" name="fam_history_stroke" id="fam_history_stroke_2" value="2" {{ old('fam_history_stroke') == '2' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="fam_history_stroke_2">มี</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="fam_history_stroke" id="fam_history_stroke_0" value="0" {{ old('fam_history_stroke') == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fam_history_stroke_0">ไม่มี</label>
                                        </div>
                                        <div class="form-check form-check-inline me-4">
                                            <input class="form-check-input" type="radio" name="fam_history_stroke" id="fam_history_stroke_1" value="1" {{ old('fam_history_stroke') == '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="fam_history_stroke_1">ไม่แน่ใจ</label>
                                        </div>
                                    </div>
                                    @error('fam_history_stroke')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">5. โรคเบาหวานจาก Lab</label><br>
                                    <div class="d-flex flex-wrap pt-2">
                                        <div class="form-check form-check-inline me-4">
                                            <input class="form-check-input" type="radio" name="dm_from_lab" id="dm_from_lab_2" value="2" {{ old('dm_from_lab') == '2' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="dm_from_lab_2">มี</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="dm_from_lab" id="dm_from_lab_0" value="0" {{ old('dm_from_lab') == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="dm_from_lab_0">ไม่มี</label>
                                        </div>
                                    </div>
                                    @error('dm_from_lab')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label">6. โรคความดัน (Hypertension)</label><br>
                                    <div class="d-flex flex-wrap pt-2">
                                        <div class="form-check form-check-inline me-4">
                                            <input class="form-check-input" type="radio" name="hypertension" id="hypertension_1" value="1" {{ old('hypertension') == '1' ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="hypertension_1">มี</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="hypertension" id="hypertension_0" value="0" {{ old('hypertension') == '0' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="hypertension_0">ไม่มี</label>
                                        </div>
                                    </div>
                                    @error('hypertension')<div class="text-danger mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-grid gap-2 mt-5">
                <button type="submit" class="btn btn-primary btn-lg shadow">
                    <i class="fas fa-calculator me-2"></i> บันทึกข้อมูลและคำนวณ Risk Score
                </button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. ส่วนของ Lab Inputs
    const labInputs = ['fbs', 'cholesterol', 'triglyceride', 'hdl'];
    labInputs.forEach(function(lab) {
        const inputField = document.getElementById(lab);
        const radioYes = document.getElementById(lab + '_yes');
        const radioNo = document.getElementById(lab + '_no');
        
        if (!inputField || !radioYes || !radioNo) return; // กัน Error ถ้าหา Element ไม่เจอ

        function updateLabInputState(isYesChecked) {
            if (isYesChecked) {
                inputField.disabled = false;
                inputField.setAttribute('required', 'required');
            } else {
                inputField.disabled = true;
                inputField.removeAttribute('required');
            }
        }
        
        radioYes.addEventListener('change', () => updateLabInputState(true));
        radioNo.addEventListener('change', () => updateLabInputState(false));
    });

    // 2. BMI Calculation
    const weightInput = document.getElementById('weight');
    const heightInput = document.getElementById('height');
    const bmiInput = document.getElementById('bmi');

    function calculateBMI() {
        const weight = parseFloat(weightInput.value); 
        const heightCm = parseFloat(heightInput.value); 
        if (weight > 0 && heightCm > 0) {
            const bmi = weight / ((heightCm / 100) ** 2);
            bmiInput.value = bmi.toFixed(2);
        }
    }
    weightInput.addEventListener('input', calculateBMI);
    heightInput.addEventListener('input', calculateBMI);

    // 3. รอบเอว (นิ้ว <-> ซม.)
    const waistInchInput = document.getElementById('waist_inch');
    const waistCmInput = document.getElementById('waist');

    waistInchInput.addEventListener('input', function() {
        if (this.value > 0) waistCmInput.value = (parseFloat(this.value) * 2.5).toFixed(2);
    });

    waistCmInput.addEventListener('input', function() {
        if (this.value > 0 && document.activeElement === waistCmInput) {
            waistInchInput.value = (parseFloat(this.value) / 2.5).toFixed(2);
        }
    });

    // 4. ค้นหาข้อมูลผู้ป่วยจาก HN (ย้ายเข้ามาอยู่ใน DOMContentLoaded)
    const hnInput = document.getElementById('hn');
    const statusIcon = document.getElementById('hn-status');
    const hnMessage = document.getElementById('hn-message');
    const nameInput = document.getElementById('name');

    if (hnInput) {
        hnInput.addEventListener('blur', function() {
            const hn = this.value.trim();
            if (hn.length > 0) {
                if (statusIcon) statusIcon.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                
                fetch(`/api/patient-info/${hn}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.found) {
                            if (nameInput) nameInput.value = data.name;
                            if (hnMessage) {
                                hnMessage.innerText = "พบข้อมูลผู้ป่วยเก่า";
                                hnMessage.className = "text-success small";
                            }
                            if (statusIcon) statusIcon.innerHTML = '<i class="fas fa-check text-success"></i>';
                            
                            // ติ๊กเพศ
                            if(data.gender !== undefined) {
                                const genderRadio = document.querySelector(`input[name="gender"][value="${data.gender}"]`);
                                if (genderRadio) genderRadio.checked = true;
                            }
                        } else {
                            if (hnMessage) {
                                hnMessage.innerText = "ไม่พบข้อมูล (Visit ใหม่)";
                                hnMessage.className = "text-primary small";
                            }
                            if (statusIcon) statusIcon.innerHTML = '<i class="fas fa-user-plus text-primary"></i>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        if (statusIcon) statusIcon.innerHTML = '<i class="fas fa-exclamation-triangle text-danger"></i>';
                    });
            }
        });
    }
});
</script>
</body>
</html>