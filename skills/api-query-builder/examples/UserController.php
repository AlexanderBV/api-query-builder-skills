<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Warrior\ApiQueryBuilder\Filters\Filter;

class UserController extends Controller
{
    /**
     * Display a listing of the resource with declarative API querying.
     */
    public function index(Request $request): JsonResponse
    {
        return User::apiQuery($request)
            // 1. Columnas y relaciones autorizadas
            ->allowedFilters([
                'name',
                'email',
                'status',
                'roles.name',               // Relación whereHas
                'department.*',             // Comodín para departamento
                'active',                   // Local scope scopeActive()
                'trashed',                  // Local scope scopeTrashed()
                Filter::custom('verified_after', function ($query, $value) {
                    $query->where('email_verified_at', '>=', $value);
                }),
            ])
            // 2. Filtro por defecto en caso de omisión del cliente
            ->defaultFilters([
                'status' => 'active',
            ])
            // 3. Eager Loading seguro bajo demanda (sin N+1)
            ->allowedIncludes([
                'roles',
                'department',
            ])
            // 4. Conteos virtuales (withCount)
            ->allowedCounts([
                'comments',
                'orders',
            ])
            // 5. Búsqueda multi-columna (?search=Carlos)
            ->allowedSearch([
                'name',
                'email',
                'roles.name',
            ])
            // 6. Ordenamiento seguro con desempate por id
            ->allowedSorts([
                'name',
                'created_at',
            ])
            ->defaultSort('-created_at')
            // 7. Habilitar exportación sin paginar con tope de seguridad
            ->allowAll(maxLimitOrUnlimited: 2000)
            // 8. Transformar con API Resource
            ->response(UserResource::class);
    }
}
