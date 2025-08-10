<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationKey extends Model
{
    /** @use HasFactory<\Database\Factories\LocaleFactory> */
    use HasFactory;

    protected $fillable = ['key', 'description'];

    public function translationValues()
    {
        return $this->hasMany(TranslationValue::class);
    }

    public function tags()
    {
        return $this->belongsToMany(TranslationTag::class, 'translation_key_translation_tag')
            ->withTimestamps();
    }
}
