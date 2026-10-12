<?php

namespace App\Exports;

use App\Models\RiskScore;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RiskScoresExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return RiskScore::all();
    }

    // กำหนดหัวตาราง (Headers)
    public function headings(): array
    {
        return [
            'ID', 'ชื่อ-นามสกุล', 'เพศ', 'อายุ', 'ความดันตัวบน', 'ความดันตัวล่าง', 
            'ระดับความเสี่ยง CVD', '% ความเสี่ยง CVD', 'ระดับความเสี่ยง Stroke', 
            'คะแนนความเสี่ยง DM', 'จำนวนปัจจัยเสี่ยง Metabolic', 'วันที่ประเมิน'
        ];
    }

    // กำหนดการแสดงผลข้อมูลแต่ละแถว
    public function map($row): array
    {
        return [
            $row->id,
            $row->name,
            $row->gender == 0 ? 'ชาย' : 'หญิง',
            $row->age,
            $row->bp_sys,
            $row->bp_dias,
            $row->risk_level,
            $row->percent_cvd_risk . '%',
            $row->stroke_risk_level,
            $row->dm_risk_score,
            $row->metabolic_risk_count,
            $row->created_at->format('d/m/Y H:i')
        ];
    }
}