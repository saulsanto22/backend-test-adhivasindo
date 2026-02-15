<?php

namespace App\Swagger;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Admin"),
 *     @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *ruktur ini sudah solid 
 * @OA\Schema(
 *     schema="ExternalDataItem",
 *     type="object",
 *     @OA\Property(property="NAMA", type="string", example="Turner Mia"),
 *     @OA\Property(property="YMD", type="string", example="20220713"),
 *     @OA\Property(property="NIM", type="string", example="9352078461")
 * )
 *
 * @OA\Schema(
 *     schema="ExternalSearchResponse",
 *     type="object",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="message", type="string", example="Pencarian berhasil."),
 *     @OA\Property(property="data", type="object",
 *         @OA\Property(property="field", type="string", example="NAMA"),
 *         @OA\Property(property="value", type="string", example="Turner Mia"),
 *         @OA\Property(property="count", type="integer", example=1),
 *         @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ExternalDataItem"))
 *     )
 * )
 */
class Schemas
{
}
