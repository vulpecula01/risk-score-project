<?php
namespace App\Imports;

use App\Models\RiskScore;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RiskImport implements ToModel, WithHeadingRow
{
    protected $controller;

    public function __construct($controller) {
        $this->controller = $controller;
    }

    public function model(array $row)
    {
        // 1. เตรียมข้อมูลจำลองเหมือนส่งมาจาก Request Form
        $data = [
           'hn' => $row['hn'],
            'name' => $row['name'],
            'age' => $row['age'],
            'gender' => $row['gender'], // 0=ชาย, 1=หญิง
            'weight' => $row['weight'],
            'height' => $row['height'],
            'waist' => $row['waist'], // หน่วยตามที่ตกลง (ซม. หรือ นิ้ว)
            'bmi' => $row['weight'] / (($row['height']/100) ** 2),
            'fbs' => $row['fbs'] ?? 0,
            'bp_sys' => $row['bp_sys'],
            'bp_dias' => $row['bp_dias'],
            'fam_history_dm' => $row['fam_history_dm'] ?? 0,
            // ... ใส่ค่าอื่นๆ ให้ครบตามที่ Controller ต้องการ ...
        ];

        // 2. เรียกใช้ฟังก์ชันคำนวณเดิมใน Controller (ไม่ต้องเขียนใหม่!)
        // ใช้ฟังก์ชัน calculateAndRespond ที่มีอยู่แล้วเพื่อประมวลผล Logic ทั้งหมด
        $calculatedData = $this->controller->calculateAndRespond(new \Illuminate\Http\Request($data), true);

        // 3. ส่งค่ากลับไปบันทึกลงฐานข้อมูล
        return new RiskScore($calculatedData);
    }
}