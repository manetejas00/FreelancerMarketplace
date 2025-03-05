<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'title', 'description', 'budget', 'category'];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Latest applied users for a job
    public function appliedUsers()
    {
        return $this->belongsToMany(User::class, 'job_applications', 'job_id', 'freelancer_id')
            ->withTimestamps()
            ->orderBy('job_applications.created_at', 'desc'); // Ensure applied users are ordered by latest application
    }
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}
