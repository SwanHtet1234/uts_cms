<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardScheme extends Model
{
    /** @use HasFactory<\Database\Factories\CardSchemeFactory> */
    use HasFactory;

    protected $fillable = ['scheme_name'];

    public function types()
    {
        return $this->hasMany(CardTypeScheme::class, 'scheme_id');
    }
}
