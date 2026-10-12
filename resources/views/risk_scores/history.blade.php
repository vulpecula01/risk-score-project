@extends('layouts.app') {{-- หรือชื่อ layout ที่คุณใช้ --}}

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>ประวัติการประเมินความเสี่ยง: HN {{ $hn }}</h4>
        <a href="{{ route('risk_scores.index') }}" class="btn btn-secondary">กลับหน้าหลัก</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>วันที่ประเมิน</th>
                            <th>ชื่อ-นามสกุล</th>
                            <th class="text-center">CVD Risk</th>
                            <th class="text-center">Stroke Risk</th>
                            <th class="text-center">DM Risk</th>
                            <th>เครื่องมือ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                        <tr>
                            <td>{{ $history->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $history->name }}</td>
                            <td class="text-center">{{ number_format($history->percent_cvd_risk, 1) }}%</td>
                            <td class="text-center">{{ $history->stroke_risk_level }}</td>
                            <td class="text-center">{{ $history->dm_risk_level }}</td>
                            <td>
                                <a href="{{ route('risk_scores.show', $history->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye"></i> ดูรายละเอียด
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">ไม่พบประวัติการประเมินสำหรับ HN นี้</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection