<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{
    /** @use HasFactory<\Database\Factories\LocaleFactory> */
    use HasFactory;

    protected $fillable = ['code', 'name'];

    public function translationValues()
    {
        return $this->hasMany(TranslationValue::class);
    }

    public function translationKeys()
    {
        return $this->belongsToMany(TranslationKey::class, 'translation_values', 'locale_id', 'translation_key_id');
    }
}
