<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = ['job_id', 'freelancer_id', 'cover_letter', 'status'];

    // Relationship with the Job
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    // Relationship with the Freelancer (User)
    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    // Add this method to always get the latest applications for a job
    public function scopeLatestApplications($query)
    {
        return $query->orderBy('created_at', 'desc'); // Orders applications by the latest created at
    }
}
