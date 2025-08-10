<?php

use App\Models\TranslationTag;
use App\Models\TranslationKey;

beforeEach(function () {
    $this->translationTag = new TranslationTag();
});

describe('TranslationTag Model', function () {
    it('has correct fillable attributes', function () {
        expect($this->translationTag->getFillable())->toBe(['name', 'description']);
    });

    it('uses HasFactory trait', function () {
        expect(class_uses($this->translationTag))->toContain('Illuminate\Database\Eloquent\Factories\HasFactory');
    });

    it('can be created with fillable attributes', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        expect($tag->name)->toBe('UI');
        expect($tag->description)->toBe('User interface related translations');
        expect($tag->id)->toBeGreaterThan(0);
    });

    it('can be updated with fillable attributes', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $tag->update(['description' => 'Updated description']);

        expect($tag->fresh()->description)->toBe('Updated description');
    });

    it('has translationKeys relationship', function () {
        expect($this->translationTag->translationKeys())->toBeInstanceOf('Illuminate\Database\Eloquent\Relations\BelongsToMany');
    });

    it('can access translationKeys relationship', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test key'
        ]);

        $tag->translationKeys()->attach($translationKey->id);

        expect($tag->translationKeys)->toBeInstanceOf('Illuminate\Database\Eloquent\Collection');
        expect($tag->translationKeys->count())->toBe(1);
        expect($tag->translationKeys->first()->id)->toBe($translationKey->id);
    });

    it('can sync translation keys without detaching', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $key1 = TranslationKey::create([
            'key' => 'key1',
            'description' => 'First key'
        ]);

        $key2 = TranslationKey::create([
            'key' => 'key2',
            'description' => 'Second key'
        ]);

        $tag->translationKeys()->syncWithoutDetaching([$key1->id, $key2->id]);

        expect($tag->translationKeys->count())->toBe(2);
    });

    it('can sync translation keys with detaching', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $key1 = TranslationKey::create([
            'key' => 'key1',
            'description' => 'First key'
        ]);

        $key2 = TranslationKey::create([
            'key' => 'key2',
            'description' => 'Second key'
        ]);

        $tag->translationKeys()->attach([$key1->id, $key2->id]);
        expect($tag->translationKeys->count())->toBe(2);

        $tag->translationKeys()->sync([$key1->id]);
        expect($tag->fresh()->translationKeys->count())->toBe(1);
    });

    it('can attach translation keys', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test key'
        ]);

        $tag->translationKeys()->attach($translationKey->id);

        expect($tag->translationKeys->count())->toBe(1);
        expect($tag->translationKeys->first()->id)->toBe($translationKey->id);
    });

    it('can detach translation keys', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $translationKey = TranslationKey::create([
            'key' => 'test.key',
            'description' => 'Test key'
        ]);

        $tag->translationKeys()->attach($translationKey->id);
        expect($tag->translationKeys->count())->toBe(1);

        $tag->translationKeys()->detach($translationKey->id);
        expect($tag->fresh()->translationKeys->count())->toBe(0);
    });

    it('can detach all translation keys', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $key1 = TranslationKey::create([
            'key' => 'key1',
            'description' => 'First key'
        ]);

        $key2 = TranslationKey::create([
            'key' => 'key2',
            'description' => 'Second key'
        ]);

        $tag->translationKeys()->attach([$key1->id, $key2->id]);
        expect($tag->translationKeys->count())->toBe(2);

        $tag->translationKeys()->detach();
        expect($tag->fresh()->translationKeys->count())->toBe(0);
    });

    it('has timestamps', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        expect($tag->created_at)->not->toBeNull();
        expect($tag->updated_at)->not->toBeNull();
    });

    it('can be found by name', function () {
        TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $foundTag = TranslationTag::where('name', 'UI')->first();

        expect($foundTag)->not->toBeNull();
        expect($foundTag->name)->toBe('UI');
    });

    it('can be found by description', function () {
        TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $foundTag = TranslationTag::where('description', 'User interface related translations')->first();

        expect($foundTag)->not->toBeNull();
        expect($foundTag->description)->toBe('User interface related translations');
    });

    it('can be deleted', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $tagId = $tag->id;
        $tag->delete();

        expect(TranslationTag::find($tagId))->toBeNull();
    });

    it('can be mass assigned', function () {
        $tagData = [
            'name' => 'Email',
            'description' => 'Email related translations'
        ];

        $tag = TranslationTag::create($tagData);

        expect($tag->name)->toBe('Email');
        expect($tag->description)->toBe('Email related translations');
    });

    it('can be updated via mass assignment', function () {
        $tag = TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        $tag->fill(['description' => 'Updated description']);
        $tag->save();

        expect($tag->fresh()->description)->toBe('Updated description');
    });

    it('can handle null description', function () {
        $tag = TranslationTag::create([
            'name' => 'UI'
        ]);

        expect($tag->description)->toBeNull();
    });

    it('can be found by partial name match', function () {
        TranslationTag::create([
            'name' => 'User Interface',
            'description' => 'User interface related translations'
        ]);

        $foundTags = TranslationTag::where('name', 'like', '%Interface%')->get();

        expect($foundTags->count())->toBeGreaterThan(0);
        expect($foundTags->first()->name)->toContain('Interface');
    });

    it('can be found by partial description match', function () {
        TranslationTag::create([
            'name' => 'unique_tag_' . uniqid(),
            'description' => 'User interface related translations'
        ]);

        $foundTags = TranslationTag::where('description', 'like', '%interface%')->get();

        expect($foundTags->count())->toBeGreaterThan(0);
        expect($foundTags->first()->description)->toContain('interface');
    });

    it('can handle multiple tags with same description', function () {
        TranslationTag::create([
            'name' => 'UI',
            'description' => 'Common description'
        ]);

        TranslationTag::create([
            'name' => 'UX',
            'description' => 'Common description'
        ]);

        $tagsWithSameDescription = TranslationTag::where('description', 'Common description')->get();

        expect($tagsWithSameDescription->count())->toBe(2);
    });

    it('can be ordered by name', function () {
        TranslationTag::create([
            'name' => 'Zebra',
            'description' => 'Zebra related'
        ]);

        TranslationTag::create([
            'name' => 'Alpha',
            'description' => 'Alpha related'
        ]);

        TranslationTag::create([
            'name' => 'Beta',
            'description' => 'Beta related'
        ]);

        $orderedTags = TranslationTag::orderBy('name', 'asc')->get();

        expect($orderedTags->first()->name)->toBe('Alpha');
        expect($orderedTags->last()->name)->toBe('Zebra');
    });

    it('can be ordered by creation date', function () {
        $firstTag = TranslationTag::create([
            'name' => 'First',
            'description' => 'First tag',
            'created_at' => now()->subDays(2)
        ]);

        $secondTag = TranslationTag::create([
            'name' => 'Second',
            'description' => 'Second tag',
            'created_at' => now()->subDay(1)
        ]);

        $thirdTag = TranslationTag::create([
            'name' => 'Third',
            'description' => 'Third tag',
            'created_at' => now()
        ]);

        $orderedTags = TranslationTag::orderBy('created_at', 'asc')->get();

        expect($orderedTags->first()->id)->toBe($firstTag->id);
        expect($orderedTags->last()->id)->toBe($thirdTag->id);
    });

    it('can handle long descriptions', function () {
        $longDescription = str_repeat('This is a very long description that should be handled properly. ', 10);

        $tag = TranslationTag::create([
            'name' => 'Long Description Tag',
            'description' => $longDescription
        ]);

        expect($tag->description)->toBe($longDescription);
        expect(strlen($tag->description))->toBeGreaterThan(500);
    });

    it('can be found by multiple criteria', function () {
        TranslationTag::create([
            'name' => 'UI',
            'description' => 'User interface related translations'
        ]);

        TranslationTag::create([
            'name' => 'UX',
            'description' => 'User experience related translations'
        ]);

        $foundTags = TranslationTag::where('name', 'like', '%U%')
            ->where('description', 'like', '%related%')
            ->get();

        expect($foundTags->count())->toBe(2);
    });
}); 