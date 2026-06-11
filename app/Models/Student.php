<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'student_id', 'gender', 'dob', 'phone', 'address',
        'department_id', 'enrollment_date', 'status', 'photo_path', 'academic_history'
    ];

    protected $casts = [
        'dob' => 'date',
        'enrollment_date' => 'date',
        'academic_history' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    public function allocations()
    {
        return $this->hasMany(RoomAllocation::class);
    }
}
