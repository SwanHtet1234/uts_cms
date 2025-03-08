<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardScheme extends Model
{
    /** @use HasFactory<\Database\Factories\CardSchemeFactory> */
    use HasFactory;

    protected $fillable = ['scheme_name'];

    // Define relationships
    public function cardTypeSchemes()
    {
        return $this->hasMany(CardTypeScheme::class, 'scheme_id');
    }
}
