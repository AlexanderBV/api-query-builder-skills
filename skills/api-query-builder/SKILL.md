---
name: api-query-builder
description: >-
  Expert guidance, code generation, and best practices for building declarative, secure, and high-performance REST API endpoints in Laravel using warrior/api-query-builder (ApiQueryBuilder). Activate this skill whenever the user is creating or refactoring Laravel API controllers, building CRUD endpoints, adding query filters, full-text search, sorting, eager loading (includes/counts), pagination, or handling HTTP 422 query validation.
---

# Laravel ApiQueryBuilder Skill 🚀

This skill guides the AI assistant in designing, generating, and maintaining clean, production-grade API endpoints in **Laravel 10, 11, and 12** using [**`warrior/api-query-builder`**](https://github.com/AlexanderBV/api-query-builder).

---

## 🎯 When to Use This Skill

Activate this skill when:
- Creating or refactoring Laravel API controllers that accept query parameters (`filter`, `search`, `sort`, `include`, `count`, `page`, `per_page`, `all`).
- Eliminating boilerplate `if ($request->filled(...))` or manual Eloquent query filters.
- Preventing $N+1$ query issues when frontends need dynamic relations.
- Implementing safe multi-column search, sorting with deterministic tie-breakers, or unpaginated exports (`?all=true`).
- Guiding frontend integration (React, Vue, Inertia) on how to serialize query strings for Laravel APIs.

Do **NOT** use when:
- Writing raw non-HTTP background queries (e.g., console commands, queue jobs) where dynamic URL query handling is not needed.
- Building GraphQL APIs.

---

## 📦 Installation & Setup

If the package is not yet installed in the Laravel project:

```bash
composer require warrior/api-query-builder
```

Optional config publication:
```bash
php artisan vendor:publish --tag="api-query-builder-config"
```
*(Creates `config/api-query-builder.php` to customize default parameters and limits).*

---

## 🏛️ Core API & Entrypoints

The library provides two equivalent, fluent ways to initialize the builder:

### 1. Eloquent Macro (Recommended for Developer Experience)
Available on any Eloquent `Builder` or `Relation`:
```php
use App\Models\User;

// On Model / Builder:
return User::apiQuery()->response();

// On Relationship:
return auth()->user()->posts()->apiQuery()->response();
```

### 2. Fluent Builder Class
```php
use Warrior\ApiQueryBuilder\ApiQueryBuilder;
use App\Models\User;

return ApiQueryBuilder::for(User::class, $request)->response();
```

---

## 🔧 Method Chaining Reference

### 1. `allowedFilters(array $filters): self`
Defines the strict whitelist of authorized filters. Any parameter not in this list causes an **HTTP 422** error.

Supports:
- **Direct columns:** `'name'`, `'status'`, `'email'`
- **Relational filters (dot notation):** `'roles.name'`, `'department.code'` *(uses subquery `whereHas`, zero table row multiplication)*
- **JSON nested attributes:** `'metadata.client.code'`, `'settings.theme'` *(translated to `metadata->client->code`)*
- **Productivity Wildcards:**
  - `'*'`: Authorizes all direct columns of the base table.
  - `'roles.*'`: Authorizes all columns of the `roles` relation.
  - `'metadata.*'`: Authorizes all properties within the `metadata` JSON column.
- **Local Scopes by Convention:** If the model defines `scopeActive($query)` or `scopeTrashed($query, $bool)`, passing `'active'` or `'trashed'` automatically routes to that scope with boolean cast normalization (`'true'` -> `true`).
- **Custom Filters (Ad-Hoc Closures):**
  ```php
  use Warrior\ApiQueryBuilder\Filters\Filter;

  ->allowedFilters([
      'name',
      Filter::custom('has_active_subscription', function ($query, $value) {
          if ($value) {
              $query->whereHas('subscriptions', fn($q) => $q->where('ends_at', '>', now()));
          }
      }),
  ])
  ```

### 2. `defaultFilters(array $defaults): self`
Injects fallback filter values **only when the client omits the parameter**. 
> [!IMPORTANT]
> Keeps the base query builder immutable. If the client explicitly passes a value, it overrides the default. If the client passes empty (`?filter[status]=`), the filter is cleanly dropped.

```php
->defaultFilters([
    'status' => 'active',
])
```

### 3. `allowedIncludes(array $includes): self`
Defines authorized relations for demand-driven Eager Loading (`with()`), eliminating $N+1$ queries:
```php
->allowedIncludes(['roles', 'department', 'roles.permissions'])
```
*Client sends:* `GET /api/users?include=roles,department`

### 4. `allowedCounts(array $counts): self`
Authorizes virtual aggregate counts (`withCount()`) without hydrating models into memory:
```php
->allowedCounts(['comments', 'orders'])
```
*Client sends:* `GET /api/users?count=comments` *(Result has `comments_count` property)*.

### 5. `allowedSearch(array $columns): self`
Enables the global `?search=` parameter across multiple columns:
- Grouped safely in SQL parentheses: `WHERE (col1 LIKE ? OR col2 LIKE ? OR EXISTS (...))`
- Automatically uses `ILIKE` on PostgreSQL, `LIKE` on MySQL/SQLite.
- Enforces a 200-character safety limit to mitigate DoS attacks.
```php
->allowedSearch(['name', 'email', 'roles.name'])
```

### 6. `allowedSorts(array $sorts): self` & `defaultSort(string $column, string $direction = 'asc'): self`
Safe ordering with deterministic Primary Key tie-breaker:
```php
->allowedSorts(['name', 'created_at'])
->defaultSort('-created_at') // Prefix '-' denotes descending order
```
*Client sends:* `?sort=-created_at,name`

> [!WARNING]
> Sorting by relations (`?sort=roles.name`) is strictly rejected with HTTP 422 in V1 to avoid `LEFT JOIN` row multiplication that breaks pagination math.

### 7. `allowAll(int|bool $maxLimitOrUnlimited = 5000): self`
Explicitly enables unpaginated flat array exports (`?all=true`):
- Returns a flat array: `[ {...}, {...} ]` (or wrapped through the Resource).
- Executes an optimized count beforehand; aborts with HTTP 422 if table count exceeds safety threshold (prevents PHP Out-Of-Memory errors).
```php
->allowAll(maxLimitOrUnlimited: 5000)
```

### 8. Terminal Resolution Methods
- `->response(?string $resourceClass = null): JsonResponse`
  Executes pipeline and returns a ready-to-return `JsonResponse`. Wraps in Resource collection if `$resourceClass` is provided.
- `->get(): LengthAwarePaginator|Paginator|CursorPaginator|Collection`
  Returns raw Laravel data structure for post-processing before sending response.
- `->paginate(?int $perPage = null)`
  Semantic alias of `get()`.

---

## ⚡ Complete Controller Blueprint

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Warrior\ApiQueryBuilder\Filters\Filter;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return User::apiQuery($request)
            // 1. Whitelisted filters
            ->allowedFilters([
                'name',
                'status',
                'email',
                'roles.name',               // Relational filter
                'settings.theme',           // JSON column filter
                'metadata.*',               // JSON wildcard
                'active',                   // Local scope: scopeActive()
                Filter::custom('is_vip', function ($query, $value) {
                    if ($value) $query->where('lifetime_spend', '>=', 10000);
                }),
            ])
            // 2. Default fallback filter
            ->defaultFilters(['status' => 'active'])
            // 3. Dynamic Eager Loading
            ->allowedIncludes(['roles', 'profile'])
            // 4. Virtual Counts
            ->allowedCounts(['orders'])
            // 5. Global Search
            ->allowedSearch(['name', 'email'])
            // 6. Sorting with default
            ->allowedSorts(['name', 'created_at'])
            ->defaultSort('-created_at')
            // 7. Safety-capped export mode
            ->allowAll(maxLimitOrUnlimited: 2000)
            // 8. Transform with API Resource
            ->response(UserResource::class);
    }
}
```

---

## 🚫 Critical Anti-Patterns & Pitfalls

1. **Always use official class and macro names:**
   - ✅ `Warrior\ApiQueryBuilder\ApiQueryBuilder` / `User::apiQuery()`
   - ❌ Do not invent custom macro names or unnamespaced helpers.

2. **NEVER mutate the query builder when setting default filters:**
   - ❌ `$query = User::where('status', 'active'); return $query->apiQuery()...`
   *(Prevents client from ever querying `?filter[status]=inactive`)*
   - ✅ `User::apiQuery()->defaultFilters(['status' => 'active'])`

3. **NEVER allow relational sorting in `allowedSorts`:**
   - ❌ `->allowedSorts(['roles.name'])` *(Throws 422)*
   - ✅ Only base table columns in `allowedSorts`.

4. **NEVER duplicate `whereHas` or `with()` manually:**
   - The pipeline handles subqueries and eager loading automatically through `allowedFilters` and `allowedIncludes`.

5. **Client List Formatting:**
   - The package supports both CSV (`?filter[status][in]=a,b,c`) and array notation (`?filter[status][in][]=a&filter[status][in][]=b`). Recommend CSV for cleaner URLs.

---

## 📚 Deep Dive References

- [Catálogo de 23 Operadores](./references/operators.md)
- [Guía de Integración Frontend (qs, React, Vue)](./references/frontend-guide.md)
- [Arquitectura Interna y Pipeline](./references/architecture.md)
- [Ejemplo: UserController Empresarial](./examples/UserController.php)
- [Ejemplo: OrderController Avanzado](./examples/OrderController.php)
