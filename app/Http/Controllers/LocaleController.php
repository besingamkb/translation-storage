<?php

namespace App\Http\Controllers;

use App\Models\Locale;
use App\Services\LocaleService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLocaleRequest;
use App\Http\Requests\UpdateLocaleRequest;

/**
 * @OA\Schema(
 *   schema="Locale",
 *   type="object",
 *   required={"id", "name", "code"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="English"),
 *   @OA\Property(property="code", type="string", example="en"),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00Z")
 * )
 *
 * @OA\Tag(
 *     name="Locales",
 *     description="Locale management"
 * )
 */
class LocaleController extends Controller
{
    protected $service;

    public function __construct(LocaleService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/locales",
     *     summary="Get all locales",
     *     tags={"Locales"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Locale"))
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        $locales = $this->service->list();
        return response()->json($locales);
    }

    /**
     * @OA\Get(
     *     path="/api/locales/{id}",
     *     summary="Get a locale by ID",
     *     tags={"Locales"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(ref="#/components/schemas/Locale")
     *     ),
     *     @OA\Response(response=404, description="Locale not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show($id)
    {
        $locale = $this->service->get($id);
        if (!$locale) {
            return response()->json(['message' => 'Locale not found'], 404);
        }
        return response()->json($locale);
    }

    /**
     * @OA\Post(
     *     path="/api/locales",
     *     summary="Create a new locale",
     *     tags={"Locales"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Locale")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Locale created",
     *         @OA\JsonContent(ref="#/components/schemas/Locale")
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(StoreLocaleRequest $request)
    {
        $locale = $this->service->create($request->validated());
        return response()->json($locale, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/locales/{id}",
     *     summary="Update a locale",
     *     tags={"Locales"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/Locale")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Locale updated",
     *         @OA\JsonContent(ref="#/components/schemas/Locale")
     *     ),
     *     @OA\Response(response=404, description="Locale not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(UpdateLocaleRequest $request, $id)
    {
        $locale = $this->service->get($id);
        if (!$locale) {
            return response()->json(['message' => 'Locale not found'], 404);
        }
        $locale = $this->service->update($id, $request->validated());
        return response()->json($locale);
    }

    /**
     * @OA\Delete(
     *     path="/api/locales/{id}",
     *     summary="Delete a locale",
     *     tags={"Locales"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Locale deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Locale deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Locale not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy($id)
    {
        $locale = $this->service->get($id);
        if (!$locale) {
            return response()->json(['message' => 'Locale not found'], 404);
        }
        $this->service->delete($id);
        return response()->json(['message' => 'Locale deleted successfully']);
    }
}
