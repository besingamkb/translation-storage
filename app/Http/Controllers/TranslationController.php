<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationValueRequest;
use App\Services\TranslationService;
use Illuminate\Http\Request;
use App\Models\TranslationKey;
use App\Models\TranslationValue;
use App\Models\Locale;
use App\Models\TranslationTag;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Schema(
 *   schema="TranslationValue",
 *   type="object",
 *   required={"key", "value", "locale"},
 *   @OA\Property(property="key", type="string", example="key.name"),
 *   @OA\Property(property="value", type="string", example="Hello World"),
 *   @OA\Property(property="locale", type="string", example="en_US"),
 *   @OA\Property(property="tags", type="array", @OA\Items(type="string"), example={"tag1","tag2"})
 * )
 *
 * @OA\Tag(
 *     name="Translations",
 *     description="Translation management"
 * )
 */
class TranslationController extends Controller
{
    /**
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     summary="Delete a translation value",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Translation deleted successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Translation deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy($id, TranslationService $service)
    {
        try {
            $service->destroy($id);
            return response()->json(['message' => 'Translation deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     summary="Create a new translation",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TranslationValue")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Translation saved successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Translation saved successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/TranslationValue")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(StoreTranslationRequest $request, TranslationService $service)
    {
        try {
            $data = $service->store($request->validated());
            return response()->json([
                'message' => 'Translation saved successfully.',
                'data' => $data,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     summary="Update an existing translation value",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/TranslationValue")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Translation updated successfully.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Translation updated successfully."),
     *             @OA\Property(property="data", ref="#/components/schemas/TranslationValue")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(UpdateTranslationValueRequest $request, $id, TranslationService $service)
    {
        try {
            $user = $request->user();
            $data = $service->update($request->validated(), $id, $user);
            return response()->json([
                'message' => 'Translation updated successfully.',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/translations",
     *     summary="List/search translations by tag, key, or content",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="tag", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="key", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="content", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="locale", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TranslationValue")),
     *             @OA\Property(property="links", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index(Request $request, TranslationService $service)
    {
        $filters = [
            'tag' => $request->input('tag'),
            'key' => $request->input('key'),
            'content' => $request->input('content'),
            'locale' => $request->input('locale'),
        ];
        $translations = $service->search($filters, 20);
        return response()->json($translations);
    }


    /**
     * @OA\Get(
     *     path="/api/translations/export",
     *     summary="Export translations as JSON for a single locale, with meta links to other locales",
     *     tags={"Translations"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="locale", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function export(Request $request, TranslationService $service)
    {
        try {
            $locale = $request->input('locale');
            $baseUrl = $request->getSchemeAndHttpHost() . $request->getBaseUrl() . $request->getPathInfo();
            
            // Get export statistics for monitoring
            $stats = $service->getExportStats($locale);
            
            $result = $service->exportTranslations($locale, $baseUrl);
            if (isset($result['error'])) {
                return response()->json(['error' => $result['error']], $result['status'] ?? 500);
            }
            
            // Add performance metrics to response
            $result['meta']['performance'] = [
                'total_translations' => $stats->total_translations ?? 0,
                'unique_keys' => $stats->unique_keys ?? 0,
                'avg_value_length' => round($stats->avg_value_length ?? 0, 2)
            ];
            
            Log::info("Export: finished JSON for locale $locale", [
                'total_translations' => $stats->total_translations ?? 0,
                'locale' => $locale
            ]);
            
            return response()->json([
                'data' => $result['data'],
                'meta' => $result['meta'],
            ]);
        } catch (\Throwable $e) {
            Log::error('Export translations error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'error' => 'Failed to export translations.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
