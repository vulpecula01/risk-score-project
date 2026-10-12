@extends('layouts.app')

@section('title', 'Dashboard ระบบคัดกรองความเสี่ยง')

@section('styles')
<style>
    :root {
        --risk-high: #dc3545;
        --risk-med: #ffc107;
        --risk-low: #198754;
    }
    .dashboard-wrapper { padding: 1.5rem; background-color: #f4f7f6; min-height: 100vh; }
    .stat-card { border: none; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: 0.3s; }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
    .nav-pills .nav-link { color: #6c757d; font-weight: 500; border-radius: 10px; padding: 10px 20px; }
    .nav-pills .nav-link.active { background-color: #0d6efd; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2); }
    .badge-risk { padding: 6px 12px; border-radius: 8px; font-weight: 600; font-size: 0.8rem; }
    .bg-high { background-color: #fee2e2; color: var(--risk-high); }
    .bg-med { background-color: #fff8e1; color: #856404; }
    .bg-low { background-color: #e8f5e9; color: var(--risk-low); }
    .chart-container { background: white; border-radius: 15px; padding: 20px; height: 100%; }
</style>
@endsection

@section('content')
<div class="dashboard-wrapper">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">Risk Assessment Dashboard</h2>
            <p class="text-muted">สรุปข้อมูลการคัดกรองสุขภาพและประเมินความเสี่ยง</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="window.location.reload()">
                <i class="fas fa-sync-alt me-2"></i>อัปเดตข้อมูล
            </button>
        </div>
    </div>

    <!-- สรุปตัวเลขสำคัญ -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-white h-100">
                <div class="card-body">
                    <div class="text-muted small mb-2 text-uppercase fw-bold">ผู้เข้ารับการประเมิน</div>
                    <h2 class="fw-bold mb-0">{{ number_format($totalCount) }} <small class="fs-6 fw-normal">คน</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-white h-100 border-start border-danger border-4">
                <div class="card-body">
                    <div class="text-muted small mb-2 text-uppercase fw-bold">ความเสี่ยงสูง (CVD)</div>
                    <h2 class="fw-bold text-danger mb-0">{{ number_format($highRiskCount) }} <small class="fs-6 fw-normal">คน</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-white h-100 border-start border-danger border-4">
                <div class="card-body">
                    <div class="text-muted small mb-2 text-uppercase fw-bold">ความเสี่ยง Stroke สูง</div>
                    <h2 class="fw-bold text-danger mb-0">{{ number_format($strokeHighCount) }} <small class="fs-6 fw-normal">คน</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card bg-white h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="text-muted small mb-2 text-uppercase fw-bold">Metabolic Syndrome</div>
                    <h2 class="fw-bold text-warning mb-0">{{ number_format($metabolicCount) }} <small class="fs-6 fw-normal">คน</small></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- กราฟแนวโน้ม -->
        <div class="col-xl-8">
            <div class="chart-container shadow-sm">
                <h5 class="fw-bold mb-4">แนวโน้มความเสี่ยงรายเดือน (CVD Risk)</h5>
                <canvas id="trendChart" style="max-height: 350px;"></canvas>
            </div>
        </div>
        <!-- ตารางด่วน -->
        <div class="col-xl-4">
            <div class="chart-container shadow-sm">
                <h5 class="fw-bold mb-3">รายการล่าสุด</h5>
                <div class="list-group list-group-flush">
                    @forelse($riskScores as $score)
                    <div class="list-group-item px-0 border-0 mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small">{{ $score->name }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $score->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="badge-risk {{ str_contains($score->risk_level, 'สูง') ? 'bg-high' : (str_contains($score->risk_level, 'กลาง') ? 'bg-med' : 'bg-low') }}">
                                {{ $score->risk_level }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted my-4">ไม่มีข้อมูลล่าสุด</p>
                    @endforelse
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-sm btn-light w-100">ดูทั้งหมด</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ระบบ Tabs แยกข้อมูล -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
        <div class="card-header bg-white border-0 pt-4 px-4">
            <ul class="nav nav-pills" id="riskTabs" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="cvd-tab" data-bs-toggle="pill" data-bs-target="#cvd-content">CVD Risk Score</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="stroke-tab" data-bs-toggle="pill" data-bs-target="#stroke-content">Stroke Risk</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="dm-tab" data-bs-toggle="pill" data-bs-target="#dm-content">DM / HT Risk</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="meta-tab" data-bs-toggle="pill" data-bs-target="#meta-content">Metabolic Syndrome</button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="riskTabsContent">
                <!-- Tab: CVD Risk -->
                <div class="tab-pane fade show active" id="cvd-content">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>CVD Risk Level</th>
                                    <th>ค่าดัชนีมวลกาย (BMI)</th>
                                    <th>ผลประเมิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riskScores as $score)
                                <tr>
                                    <td>{{ $score->name }}</td>
                                    <td>
                                        <span class="badge-risk {{ str_contains($score->risk_level, 'สูง') ? 'bg-high' : (str_contains($score->risk_level, 'กลาง') ? 'bg-med' : 'bg-low') }}">
                                            {{ $score->risk_level }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($score->bmi, 1) }}</td>
                                    <td><small class="text-muted">{{ $score->result_note ?? '-' }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Stroke Risk -->
                <div class="tab-pane fade" id="stroke-content">
                    <div class="alert alert-info">ข้อมูลความเสี่ยงโรคหลอดเลือดสมอง (Stroke Risk Score)</div>
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ชื่อ-นามสกุล</th>
                                <th>ความดันโลหิต</th>
                                <th>Stroke Risk Level</th>
                                <th>คำแนะนำ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riskScores as $score)
                            <tr>
                                <td>{{ $score->name }}</td>
                                <td>{{ $score->sys }}/{{ $score->dia }}</td>
                                <td>
                                    <span class="badge-risk {{ str_contains($score->stroke_risk_level, 'สูง') ? 'bg-high' : 'bg-low' }}">
                                        {{ $score->stroke_risk_level ?? 'ปกติ' }}
                                    </span>
                                </td>
                                <td>{{ $score->stroke_recommendation ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Tab: DM/HT Risk -->
                <div class="tab-pane fade" id="dm-content">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ชื่อ-นามสกุล</th>
                                    <th>ระดับน้ำตาล (FBS)</th>
                                    <th>ความเสี่ยงเบาหวาน</th>
                                    <th>ความเสี่ยงความดัน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($riskScores as $score)
                                <tr>
                                    <td>{{ $score->name }}</td>
                                    <td>{{ $score->fbs ?? '-' }} mg/dL</td>
                                    <td><span class="badge-risk {{ str_contains($score->dm_risk_level, 'สูง') ? 'bg-high' : 'bg-low' }}">{{ $score->dm_risk_level ?? 'ปกติ' }}</span></td>
                                    <td><span class="badge-risk {{ str_contains($score->ht_risk_level, 'สูง') ? 'bg-high' : 'bg-low' }}">{{ $score->ht_risk_level ?? 'ปกติ' }}</span></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: Metabolic -->
                <div class="tab-pane fade" id="meta-content">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ชื่อ-นามสกุล</th>
                                <th>รอบเอว</th>
                                <th>จำนวนปัจจัยเสี่ยง</th>
                                <th>สรุปผล</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riskScores as $score)
                            <tr>
                                <td>{{ $score->name }}</td>
                                <td>{{ $score->waistline ?? '-' }} ซม.</td>
                                <td><span class="fw-bold">{{ $score->metabolic_risk_count ?? 0 }}/5</span></td>
                                <td>
                                    @if(($score->metabolic_risk_count ?? 0) >= 3)
                                        <span class="text-danger fw-bold"><i class="fas fa-exclamation-triangle me-1"></i>เป็นกลุ่มอาการเมแทบอลิก</span>
                                    @else
                                        <span class="text-success">ปกติ</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('trendChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [
                    {
                        label: 'เสี่ยงสูง',
                        data: @json($chartData['high']),
                        backgroundColor: '#dc3545',
                        borderRadius: 5
                    },
                    {
                        label: 'เสี่ยงปานกลาง',
                        data: @json($chartData['med']),
                        backgroundColor: '#ffc107',
                        borderRadius: 5
                    },
                    {
                        label: 'เสี่ยงน้อย',
                        data: @json($chartData['low']),
                        backgroundColor: '#198754',
                        borderRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true, grid: { display: false } },
                    y: { stacked: true, beginAtZero: true }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endsection 