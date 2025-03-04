<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'title', 'description', 'budget', 'category', 'status'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function applicants()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('cover_letter')  // Store the cover letter in the pivot table
                    ->withTimestamps();          // Automatically manage created_at / updated_at
    }
}

