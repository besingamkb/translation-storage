<?php

namespace App\Services;

use App\Models\TranslationKey;
use App\Repositories\TranslationRepositoryInterface;
use App\Models\Locale;
use App\Models\TranslationTag;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    protected $translationRepository;

    public function __construct(TranslationRepositoryInterface $translationRepository)
    {
        $this->translationRepository = $translationRepository;
    }


    public function store(array $validated)
    {
        return DB::transaction(function () use ($validated) {
            $translationKey = TranslationKey::firstOrCreate(
                ['key' => $validated['key']],
                ['description' => $validated['description'] ?? null]
            );

            $locale = Locale::where('code', $validated['locale'])->firstOrFail();

            $translationValue = \App\Models\TranslationValue::where([
                'translation_key_id' => $translationKey->id,
                'locale_id' => $locale->id,
            ])->first();

            if ($translationValue) {
                $translationValue->update(['value' => $validated['value']]);
            } else {
                $translationValue = \App\Models\TranslationValue::create([
                    'translation_key_id' => $translationKey->id,
                    'locale_id' => $locale->id,
                    'value' => $validated['value'],
                ]);
            }

            if (!empty($validated['tags'])) {
                $tagNames = $validated['tags'];
                $existingTags = TranslationTag::whereIn('name', $tagNames)->get();
                $existingTagNames = $existingTags->pluck('name')->all();
                $newTagNames = array_diff($tagNames, $existingTagNames);
                $newTags = [];
                foreach ($newTagNames as $tagName) {
                    $newTags[] = TranslationTag::create(['name' => $tagName]);
                }
                $allTagIds = $existingTags->pluck('id')->merge(collect($newTags)->pluck('id'))->all();
                $translationKey->tags()->syncWithoutDetaching($allTagIds);
            }

            return $translationValue->load('translationKey', 'locale');
        });
    }

    public function update(array $validated, $id, $user = null)
    {
        $translationValue = $this->translationRepository->find($id);
        $oldValue = $translationValue->value;
        $translationValue->value = $validated['value'];
        $translationValue->save();

        if ($user) {
            $translationValue->translationRevisions()->create([
                'old' => $oldValue,
                'new' => $validated['value'],
                'user_id' => $user->id,
            ]);
        }

        return $translationValue->load('translationKey', 'locale');
    }

    public function destroy($id)
    {
        return $this->translationRepository->delete($id);
    }

    public function search(array $filters = [], $perPage = 20)
    {
        $query = $this->translationRepository->searchWithFilters($filters);
        return $query->paginate($perPage);
    }

    public function exportTranslations($locale, $baseUrl)
    {
        $allLocales = DB::table('locales')->pluck('code')->toArray();

        if (!$locale) {
            $locale = $allLocales[0] ?? null;
        }
        if (!$locale) {
            return [
                'error' => 'No locales found.',
                'status' => 404
            ];
        }

        $translations = $this->translationRepository->exportByLocale($locale);

        $meta = [
            'current_locale' => $locale,
            'other_locales' => [],
        ];
        foreach ($allLocales as $loc) {
            if ($loc !== $locale) {
                $meta['other_locales'][$loc] = $baseUrl . '?locale=' . urlencode($loc);
            }
        }

        return [
            'data' => $translations,
            'meta' => $meta,
            'status' => 200
        ];
    }

    /**
     * Get export statistics for performance monitoring
     */
    public function getExportStats($locale)
    {
        return $this->translationRepository->getExportStats($locale);
    }
}
