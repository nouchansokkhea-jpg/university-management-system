<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'isbn', 'category', 'total_copies', 'available_copies', 'location_shelf'];

    public function borrows()
    {
        return $this->hasMany(BookBorrow::class);
    }
}
