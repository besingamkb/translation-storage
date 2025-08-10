<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationRevision extends Model
{
    /** @use HasFactory<\Database\Factories\TranslationRevisionFactory> */
    use HasFactory;

    protected $fillable = [
        'translation_value_id',
        'old',
        'new',
        'user_id'
    ];

    public function translationValue()
    {
        return $this->belongsTo(TranslationValue::class);
    }
}
