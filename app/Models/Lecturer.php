<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    protected $fillable = [
        'user_id', 'lecturer_id', 'qualification', 'department_id', 'salary', 'phone', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'invigilator_id');
    }

    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }
}
