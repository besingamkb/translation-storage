<?php

namespace App\Repositories;

use App\Models\Locale;

class LocaleRepository implements LocaleRepositoryInterface
{
    public function all($perPage = 20)
    {
        return Locale::paginate($perPage);
    }

    public function find($id)
    {
        return Locale::find($id);
    }

    public function create(array $data)
    {
        return Locale::create($data);
    }

    public function update($id, array $data)
    {
        $locale = Locale::find($id);
        if ($locale) {
            $locale->update($data);
        }
        return $locale;
    }

    public function delete($id)
    {
        $locale = Locale::find($id);
        if ($locale) {
            $locale->delete();
            return true;
        }
        return false;
    }
}
