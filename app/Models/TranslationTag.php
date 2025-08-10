<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationTag extends Model
{
    /** @use HasFactory<\Database\Factories\TranslationTagFactory> */
    use HasFactory;

    protected $fillable = ['name', 'description'];

    public function translationKeys()
    {
        return $this->belongsToMany(TranslationKey::class, 'translation_key_translation_tag')
            ->withTimestamps();
    }
}
