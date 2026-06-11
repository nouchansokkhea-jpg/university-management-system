<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['subject_id', 'academic_year_id', 'name', 'type', 'exam_date', 'max_marks', 'invigilator_id'];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function invigilator()
    {
        return $this->belongsTo(Lecturer::class, 'invigilator_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
