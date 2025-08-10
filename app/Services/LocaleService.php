<?php

namespace App\Services;

use App\Repositories\LocaleRepositoryInterface;
use Illuminate\Http\Request;

class LocaleService
{
    protected $localeRepository;

    public function __construct(LocaleRepositoryInterface $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    public function list($perPage = 20)
    {
        return $this->localeRepository->all($perPage);
    }

    public function get($id)
    {
        return $this->localeRepository->find($id);
    }

    public function create(array $data)
    {
        return $this->localeRepository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->localeRepository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->localeRepository->delete($id);
    }
}
