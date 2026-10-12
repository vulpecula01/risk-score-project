<?php

// app/Models/RiskScore.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'hn','name', 'gender', 'age', 'bp_sys', 'bp_dias', 'pulse',
        'waist', 'height', 'bmi', 'cholesterol', 'triglyceride',
        'hdl', 'fbs', 'smoking', 'exercise', 'fam_history_dm',
        'fam_history_stroke', 'dm_from_lab', 'hypertension', 'dm_wh_score' ,
        
        // ** คอลัมน์ใหม่สำหรับ CVD Risk Score **
        'age_cal',
        'gender_cal',
        'sbp_cal',
        'cholesterol_cal',
        'smoking_cal',
        'dm_cal',
        'full_score_sum', // แทน total_sum
        'percent_cvd_risk',
        'risk_level',

        'stroke_score_sum',
        'percent_stroke_risk',
        'stroke_risk_level',

        'metabolic_risk_count',
        'metabolic_risk_level',
        'meta_bp_risk',
        'meta_waist_risk',
        'meta_triglyceride_risk',
        'meta_hdl_risk',
        'meta_fbs_risk',
        'dm_risk_score',

        'dm_risk_level',
        'dm_age_score',
        'dm_bp_score',
        'dm_fam_dm_score',
        'dm_bmi_score',
        'dm_fbs_score',
    ];
    
}