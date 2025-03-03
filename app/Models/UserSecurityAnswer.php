<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSecurityAnswer extends Model
{
    /** @use HasFactory<\Database\Factories\UserSecurityAnswerFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'question_id', 'answer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function question()
    {
        return $this->belongsTo(SecurityQuestion::class, 'question_id');
    }
}
