<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\SecurityQuestionFactory> */
    use HasFactory;

    protected $fillable = ['question'];

    public function userAnswers()
    {
        return $this->hasMany(UserSecurityAnswer::class, 'question_id');
    }
}
