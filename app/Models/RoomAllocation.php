<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAllocation extends Model
{
    protected $fillable = ['room_id', 'student_id', 'academic_year_id', 'semester', 'allocated_date', 'vacated_date', 'status'];

    protected $casts = [
        'allocated_date' => 'date',
        'vacated_date' => 'date',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
