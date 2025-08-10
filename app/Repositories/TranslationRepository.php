<?php

namespace App\Repositories;

use App\Models\TranslationValue;
use Illuminate\Support\Facades\DB;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function all($perPage = 20)
    {
        return TranslationValue::with(['translationKey.tags', 'locale'])->paginate($perPage);
    }

    public function find($id)
    {
        return TranslationValue::find($id);
    }

    public function create(array $data)
    {
        return TranslationValue::create($data);
    }

    public function update($id, array $data)
    {
        $translation = TranslationValue::find($id);
        if ($translation) {
            $translation->update($data);
        }
        return $translation;
    }

    public function delete($id)
    {
        $translation = TranslationValue::find($id);
        if ($translation) {
            $translation->delete();
            return true;
        }
        return false;
    }

    public function searchWithFilters($filters = [])
    {
        
        $query = DB::table('translation_values')
            ->join('locales', 'translation_values.locale_id', '=', 'locales.id')
            ->join('translation_keys', 'translation_values.translation_key_id', '=', 'translation_keys.id')
            ->leftJoin('translation_key_translation_tag', 'translation_keys.id', '=', 'translation_key_translation_tag.translation_key_id')
            ->leftJoin('translation_tags', 'translation_key_translation_tag.translation_tag_id', '=', 'translation_tags.id')
            ->select(
                'translation_values.*',
                'translation_keys.key',
                'translation_keys.description as key_description',
                'locales.code as locale_code',
                'locales.name as locale_name'
            );

        if (!empty($filters['tag'])) {
            $query->where('translation_tags.name', $filters['tag']);
        }
        
        if (!empty($filters['key'])) {
            $query->where('translation_keys.key', 'like', '%' . $filters['key'] . '%');
        }
        
        if (!empty($filters['content'])) {
            if (DB::connection()->getDriverName() === 'pgsql') {
                
                $query->whereRaw("to_tsvector('english', translation_values.value) @@ plainto_tsquery('english', ?)", [$filters['content']]);
            } else {
                
                $query->where('translation_values.value', 'like', '%' . $filters['content'] . '%');
            }
        }
        
        if (!empty($filters['locale'])) {
            $query->where('locales.code', $filters['locale']);
        }

        return $query->distinct();
    }

    /**
     * Optimized export method that leverages database indexes and reduces memory usage
     */
    public function exportByLocale($locale)
    {
        
        
        
        if (DB::connection()->getDriverName() === 'pgsql') {
            
            
            $localeCode = $locale;
            if (strlen($locale) === 2) {
                
                $localeRecord = DB::table('locales')->where('code', 'like', $locale . '_%')->first();
                if ($localeRecord) {
                    $localeCode = $localeRecord->code;
                }
            }
            
            $result = DB::table('translation_values')
                ->join('locales', 'translation_values.locale_id', '=', 'locales.id')
                ->join('translation_keys', 'translation_values.translation_key_id', '=', 'translation_keys.id')
                ->select(
                    DB::raw("json_object_agg(translation_keys.key, translation_values.value) as translations")
                )
                ->where('locales.code', $localeCode)
                ->first();
            
            if ($result && $result->translations) {
                return json_decode($result->translations, true) ?: [];
            }
            
            
            return $this->exportByLocaleChunked($localeCode);
        } else {
            
            return $this->exportByLocaleChunked($locale);
        }
    }

    /**
     * Chunked export method as fallback
     */
    private function exportByLocaleChunked($locale)
    {
        $translations = [];
        
        
        DB::table('translation_values')
            ->join('locales', 'translation_values.locale_id', '=', 'locales.id')
            ->join('translation_keys', 'translation_values.translation_key_id', '=', 'translation_keys.id')
            ->select('translation_keys.key', 'translation_values.value')
            ->where('locales.code', $locale)
            ->orderBy('translation_keys.key')
            ->chunk(1000, function ($rows) use (&$translations) {
                foreach ($rows as $row) {
                    $translations[$row->key] = $row->value;
                }
            });
        
        return $translations;
    }

    /**
     * Get export statistics for performance monitoring
     */
    public function getExportStats($locale)
    {
        
        $localeCode = $locale;
        if (strlen($locale) === 2) {
            $localeRecord = DB::table('locales')->where('code', 'like', $locale . '_%')->first();
            if ($localeRecord) {
                $localeCode = $localeRecord->code;
            }
        }
        
        return DB::table('translation_values')
            ->join('locales', 'translation_values.locale_id', '=', 'locales.id')
            ->where('locales.code', $localeCode)
            ->select(
                DB::raw('COUNT(*) as total_translations'),
                DB::raw('COUNT(DISTINCT translation_key_id) as unique_keys'),
                DB::raw('AVG(LENGTH(value)) as avg_value_length')
            )
            ->first();
    }
}
