<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerProfile extends Model
{
    use HasFactory;
    protected $table = 'freelancer_profiles';
        protected $fillable = [
        'user_id', 'skills', 'experience', 'portfolio', 'hourly_rate', 'company_name', 'project_details', 'working_developers_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Latest job applications for freelancer
    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class, 'freelancer_id')
                    ->orderBy('created_at', 'desc'); // Order job applications by latest first
    }

    // Latest jobs related to freelancer via job applications
    public function jobs()
    {
        return $this->hasManyThrough(Job::class, JobApplication::class, 'freelancer_id', 'id', 'id', 'job_id')
                    ->orderBy('created_at', 'desc'); // Order jobs by latest first
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'freelancer_id');
    }
}


