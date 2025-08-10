<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationValue extends Model
{
    /** @use HasFactory<\Database\Factories\TranslationValueFactory> */
    use HasFactory;

    protected $fillable = ['translation_key_id', 'locale_id', 'value'];

    public function translationKey()
    {
        return $this->belongsTo(TranslationKey::class);
    }

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    public function translationRevisions()
    {
        return $this->hasMany(TranslationRevision::class);
    }
}
