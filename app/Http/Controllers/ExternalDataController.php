<?php

namespace App\Http\Controllers;

use App\Http\Requests\External\SearchExternalRequest;
use App\Services\ExternalDataService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ExternalDataController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ExternalDataService $externalDataService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/external/name/{name}",
     *     tags={"External Data"},
     *     summary="Cari data berdasarkan NAMA",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="name", in="path", required=true, @OA\Schema(type="string"), description="Nama yang dicari", example="Turner Mia"),
     *     @OA\Response(
     *         response=200,
     *         description="Pencarian berhasil",
     *         @OA\JsonContent(ref="#/components/schemas/ExternalSearchResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=502, description="Gagal mengambil data eksternal")
     * )
     */
    public function searchByName(string $name): JsonResponse
    {
        return $this->searchByField('NAMA', $name);
    }

    /**
     * @OA\Get(
     *     path="/api/external/nim/{nim}",
     *     tags={"External Data"},
     *     summary="Cari data berdasarkan NIM",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="nim", in="path", required=true, @OA\Schema(type="string"), description="NIM yang dicari", example="9352078461"),
     *     @OA\Response(
     *         response=200,
     *         description="Pencarian berhasil",
     *         @OA\JsonContent(ref="#/components/schemas/ExternalSearchResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=502, description="Gagal mengambil data eksternal")
     * )
     */
    public function searchByNim(string $nim): JsonResponse
    {
        return $this->searchByField('NIM', $nim);
    }

    /**
     * @OA\Get(
     *     path="/api/external/ymd/{ymd}",
     *     tags={"External Data"},
     *     summary="Cari data berdasarkan YMD",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="ymd", in="path", required=true, @OA\Schema(type="string"), description="Tanggal YMD yang dicari", example="20230405"),
     *     @OA\Response(
     *         response=200,
     *         description="Pencarian berhasil",
     *         @OA\JsonContent(ref="#/components/schemas/ExternalSearchResponse")
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=502, description="Gagal mengambil data eksternal")
     * )
     */
    public function searchByYmd(string $ymd): JsonResponse
    {
        return $this->searchByField('YMD', $ymd);
    }

    /**
     * @OA\Get(
     *     path="/api/external/search",
     *     tags={"External Data"},
     *     summary="Cari data (best practice - query param)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="name", in="query", required=false, @OA\Schema(type="string"), description="Cari berdasarkan NAMA"),
     *     @OA\Parameter(name="nim", in="query", required=false, @OA\Schema(type="string"), description="Cari berdasarkan NIM"),
     *     @OA\Parameter(name="ymd", in="query", required=false, @OA\Schema(type="string"), description="Cari berdasarkan YMD"),
     *     @OA\Response(
     *         response=200,
     *         description="Pencarian berhasil",
     *         @OA\JsonContent(ref="#/components/schemas/ExternalSearchResponse")
     *     ),
     *     @OA\Response(response=422, description="Harus mengisi tepat satu parameter"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=502, description="Gagal mengambil data eksternal")
     * )
     */
    public function search(SearchExternalRequest $request): JsonResponse
    {
        return $this->searchByField(
            $request->getSearchField(),
            $request->getSearchValue()
        );
    }

    private function searchByField(string $field, string $value): JsonResponse
    {
        $result = $this->externalDataService->searchByField($field, $value);

        return $this->success($result, 'Pencarian berhasil.');
    }
}
