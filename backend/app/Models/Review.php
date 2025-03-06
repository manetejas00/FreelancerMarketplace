<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['freelancer_id', 'rating', 'comment','client_id'];

    public function freelancer()
    {
        return $this->belongsTo(FreelancerProfile::class, 'freelancer_id');
    }
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id'); // Link review to the client (user)
    }
}
