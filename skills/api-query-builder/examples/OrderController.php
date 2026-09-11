<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Warrior\ApiQueryBuilder\Filters\Filter;

class OrderController extends Controller
{
    /**
     * List orders with dates, ranges, JSON payloads, and logical groups.
     */
    public function index(Request $request): JsonResponse
    {
        return Order::apiQuery($request)
            // Columnas directas, atributos JSON anidados y filtros numéricos/fechas
            ->allowedFilters([
                'id',
                'order_number',
                'status',
                'total_amount',             // Soporta ?filter[total_amount][between]=50,200
                'created_at',               // Soporta ?filter[created_at][date_between]=2026-01-01,2026-06-30
                'customer.email',           // Relación cliente
                'customer.country_code',
                'payment_info.method',      // JSON column: payment_info->method
                'payment_info.gateway',     // JSON column: payment_info->gateway
                'metadata.*',               // Comodín para cualquier atributo en JSON metadata
                Filter::custom('min_items_count', function ($query, $value) {
                    $query->has('items', '>=', (int) $value);
                }),
            ])
            ->defaultFilters([
                'status' => 'completed',
            ])
            ->allowedIncludes([
                'customer',
                'items.product',
                'payments',
            ])
            ->allowedCounts([
                'items',
            ])
            ->allowedSearch([
                'order_number',
                'customer.email',
            ])
            ->allowedSorts([
                'id',
                'order_number',
                'total_amount',
                'created_at',
            ])
            ->defaultSort('-id')
            ->allowAll(maxLimitOrUnlimited: 1000)
            ->response(OrderResource::class);
    }
}
