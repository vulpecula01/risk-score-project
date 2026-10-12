@extends('layouts.app')

@section('title', 'รายการประเมินความเสี่ยง')

@section('styles')
<style>
    :root {
        --risk-low-bg: #e8f5e9;
        --risk-low-text: #2e7d32;
        --risk-med-bg: #fff8e1;
        --risk-med-text: #f57f17;
        --risk-high-bg: #ffebee;
        --risk-high-text: #c62828;
    }

    .page-header {
        background: #fff;
        padding: 1.5rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        border-left: 5px solid #0d6efd;
    }

    .table-container {
        background: #fff;
        border-radius: 1rem;
        overflow: hidden;
    }

    .table-custom {
        margin-bottom: 0;
    }

    .table-custom thead th {
        background: #f8f9fa;
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        color: #6c757d;
        padding: 1.25rem 1rem;
        border-top: none;
    }

    .table-custom tbody tr {
        transition: all 0.2s ease;
    }

    .table-custom tbody tr:hover {
        background-color: #f1f4f9;
    }

    .table-custom td {
        padding: 1.25rem 1rem;
        border-color: #f1f1f1;
        vertical-align: middle;
    }

    /* Patient Identity Section */
    .patient-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .patient-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .avatar-male { background: #e3f2fd; color: #1976d2; }
    .avatar-female { background: #fce4ec; color: #c2185b; }

    /* Modern Risk Badges */
    .badge-modern {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .badge-low { background: var(--risk-low-bg); color: var(--risk-low-text); }
    .badge-med { background: var(--risk-med-bg); color: var(--risk-med-text); }
    .badge-high { background: var(--risk-high-bg); color: var(--risk-high-text); }

    /* Compact Score Pills */
    .score-pill {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        padding: 0.2rem 0.6rem;
        border-radius: 6px;
        font-size: 0.8rem;
        color: #495057;
        font-weight: 500;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-view { background: #eef2ff; color: #4338ca; }
    .btn-view:hover { background: #4338ca; color: #fff; }

    .btn-del { background: #fff1f2; color: #e11d48; border: none; }
    .btn-del:hover { background: #e11d48; color: #fff; }

    .search-wrapper {
        position: relative;
    }
    .search-wrapper i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
    }
    .search-input {
        padding-left: 2.75rem;
        border-radius: 12px;
        border: 1px solid #e9ecef;
        height: 45px;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header d-flex flex-wrap justify-content-between align-items-center shadow-sm">
    <div>
        <h3 class="fw-bold text-dark mb-1">ประเมินความเสี่ยง</h3>
        <p class="text-muted mb-0"><i class="far fa-calendar-alt me-1"></i> ข้อมูลอัปเดตล่าสุด: {{ date('d/m/Y') }}</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="{{ route('risk_scores.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> สร้างการประเมินใหม่
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <!-- Filter Bar -->
        <div class="p-4 border-bottom">
            <form action="{{ route('risk_scores.index') }}" method="GET" class="row g-3">
                <div class="col-md-5">
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" class="form-control search-input" placeholder="ค้นหาชื่อผู้ประเมิน หรือรหัส..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-dark w-100 h-100 rounded-3" type="submit">กรองข้อมูล</button>
                </div>
            </form>
        </div>

        @if ($riskScores->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-folder-open fa-4x text-light"></i>
                </div>
                <h5 class="text-muted">ไม่พบข้อมูลการประเมินในขณะนี้</h5>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th style="width: 25%">ข้อมูลผู้รับการประเมิน</th>
                            <th style="width: 20%">ผลตรวจร่างกาย</th>
                            <th class="text-center">CVD Risk (10y)</th>
                            <th class="text-center">Stroke Risk</th>
                            <th class="text-center">DM Risk Score</th>
                            <th class="text-center">Metabolic</th>
                            <th class="text-end" style="padding-right: 2rem;">เครื่องมือ</th>
                        </tr>
                    </thead>
                    <tbody>
    @foreach ($riskScores as $score)
        @php
            $isMale = $score->gender == 0;
            $getRiskBadge = function($level) {
                $lvl = strtolower($level);
                if (str_contains($lvl, 'สูง') || str_contains($lvl, 'high')) return 'badge-high';
                if (str_contains($lvl, 'ปานกลาง') || str_contains($lvl, 'caution') || str_contains($lvl, 'น้อย - ปานกลาง')) return 'badge-med';
                return 'badge-low';
            };
        @endphp
        <tr>
            <td>
                <div class="patient-info">
                    <div class="patient-avatar {{ $isMale ? 'avatar-male' : 'avatar-female' }}">
                        {{ $isMale ? 'M' : 'F' }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark">{{ $score->name }}</div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-secondary border small fw-normal">
                                <i class="fas fa-id-card me-1"></i>{{ $score->hn ?? 'No HN' }}
                            </span>
                        </div>
                        <span class="text-muted small">อายุ {{ $score->age }} ปี • {{ $isMale ? 'ชาย' : 'หญิง' }}</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="d-flex flex-wrap gap-1">
                    <span class="score-pill" title="ความดันโลหิต">BP: {{ $score->bp_sys }}/{{ $score->bp_dias }}</span>
                    <span class="score-pill" title="ดัชนีมวลกาย">BMI: {{ number_format($score->bmi, 1) }}</span>
                    <span class="score-pill" title="คอเลสเตอรอล">CHL: {{ $score->cholesterol }}</span>
                </div>
            </td>
            <td class="text-center">
                <span class="badge-modern {{ $getRiskBadge($score->risk_level) }}">
                    <i class="fas fa-heartbeat"></i> {{ number_format($score->percent_cvd_risk, 1) }}%
                </span>
            </td>
          
             <td class="text-center">
                <span class="badge-modern {{ $getRiskBadge($score->stroke_risk_level) }}">
                    <i class="fas fa-brain"></i> {{ Str::replace(' Risk', '', $score->stroke_risk_level) }}
                </span>
            </td>
             <td class="text-center">
                <span class="badge-modern {{ $getRiskBadge($score->dm_risk_level) }}">
                    <i class="fas fa-crutch"></i> {{ $score->dm_risk_level ?? 'ปกติ' }}
                </span>
            </td>
            <td class="text-center">
                <div class="d-flex flex-column align-items-center">
                    <span class="fw-bold {{ $score->metabolic_risk_count >= 3 ? 'text-danger' : 'text-success' }}">
                        {{ $score->metabolic_risk_count ?? '0' }}/5
                    </span>
                    <div class="progress mt-1" style="width: 40px; height: 4px;">
                        <div class="progress-bar {{ $score->metabolic_risk_count >= 3 ? 'bg-danger' : 'bg-success' }}" 
                             style="width: {{ ($score->metabolic_risk_count / 5) * 100 }}%"></div>
                    </div>
                </div>
            </td>
            <td class="text-end" style="padding-right: 2rem;">
                <div class="d-flex justify-content-end gap-2">
                    @if($score->hn)
                    <a href="{{ route('risk_scores.history', ['hn' => $score->hn]) }}" class="action-btn btn-outline-primary" title="ดูประวัติเก่า">
                        <i class="fas fa-history"></i>
                    </a>
                    @endif

                    <a href="{{ route('risk_scores.show', $score->id) }}" class="action-btn btn-view" title="ดูรายละเอียด">
                        <i class="fas fa-file-alt"></i>
                    </a>
                    @can('admin-only')
                    @if(auth()->check())
                        <button type="button" 
                                class="action-btn btn-del" 
                                data-bs-toggle="modal" 
                                data-bs-target="#confirmDeleteModal"
                                data-score-id="{{ $score->id }}" 
                                data-score-name="{{ $score->name }}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        
                        <form id="delete-form-{{ $score->id }}" action="{{ route('risk_scores.destroy', $score->id) }}" method="POST" style="display:none;">
                            @csrf
                            @method('DELETE')
                        </form>
                        @endcan
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
                </table>
            </div>
            <!-- Pagination Space (ถ้ามี) -->
            <div class="p-4">
                {{-- $riskScores->links() --}}
            </div>
        @endif
    </div>
</div>

{{-- Modal ยืนยันการลบ --}}
@if(auth()->check())
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-body p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                </div>
                <h4 class="fw-bold">คุณแน่ใจหรือไม่?</h4>
                <p class="text-muted">คุณกำลังจะลบข้อมูลของ <span id="modal-score-name" class="fw-bold text-dark"></span> ข้อมูลนี้จะไม่สามารถกู้คืนได้</p>
                <div class="d-flex gap-2 justify-content-center mt-4">
                    <button type="button" class="btn btn-light px-4 py-2 rounded-3" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-danger px-4 py-2 rounded-3" id="confirmDeleteButton">ลบข้อมูลทันที</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('confirmDeleteModal');
        if (!modal) return;

        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        let currentScoreId = null;

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            currentScoreId = button.getAttribute('data-score-id');
            const scoreName = button.getAttribute('data-score-name');
            document.getElementById('modal-score-name').textContent = scoreName;
        });

        confirmDeleteButton.addEventListener('click', function() {
            if (currentScoreId) {
                const form = document.getElementById('delete-form-' + currentScoreId);
                if (form) {
                    form.submit();
                }
            }
        });
    });
</script>
@endsection