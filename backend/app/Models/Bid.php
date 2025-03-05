<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'rate',
        'cover_letter',
        'status',
    ];

    // Define the job relationship (a bid belongs to a job)
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    // Define the user relationship (a bid belongs to a user, freelancer)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
