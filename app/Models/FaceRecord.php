<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaceRecord extends Model
{
    protected $fillable = ['user_id', 'face_descriptor', 'photo_path'];

    protected $casts = [
        'face_descriptor' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
