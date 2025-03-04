<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'company_name', 'projects', 'number_of_developers'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
