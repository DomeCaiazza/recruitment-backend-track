<?php

namespace App\Swagger\Schemas;

/**
 * @OA\SecurityScheme(
 *     securityScheme="ApiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="x-api-key"
 * )
 */

class SecuritySchema{}


/**
 * @OA\Parameter(
 *     parameter="xApiKeyHeader",
 *     name="x-api-key",
 *     in="header",
 *     description="API key authentication",
 *     required=true,
 *     @OA\Schema(
 *         type="string",
 *         example="secret"
 *     )
 * )
 */
class ApiKeyParameter {}