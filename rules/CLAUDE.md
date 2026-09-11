# Claude Code Instructions: Laravel ApiQueryBuilder

## Purpose & Overview
This project uses `warrior/api-query-builder` to handle HTTP API queries (filters, search, sorting, eager loading, and pagination) declaratively and securely on Laravel Eloquent models.

## Coding Conventions & Directives

### 1. New API Endpoints
When implementing `index()` methods in API controllers:
- Use `Model::apiQuery($request)` directly.
- Define whitelists:
  ```php
  return User::apiQuery($request)
      ->allowedFilters(['name', 'status', 'roles.name', 'metadata.*'])
      ->defaultFilters(['status' => 'active'])
      ->allowedIncludes(['roles'])
      ->allowedCounts(['comments'])
      ->allowedSearch(['name', 'email'])
      ->allowedSorts(['name', 'created_at'])
      ->defaultSort('-created_at')
      ->allowAll(maxLimitOrUnlimited: 2000)
      ->response(UserResource::class);
  ```

### 2. Forbidden Patterns
- DO NOT manually write `if ($request->has('...'))` or `$query->when(...)` for basic query parameters.
- DO NOT sort on relations (`->allowedSorts(['roles.name'])`). This will trigger a 422 exception.
- DO NOT hardcode WHERE clauses as "defaults" before calling `apiQuery()`; always use `->defaultFilters(['status' => 'active'])`.

### 3. Error Handling
The library throws `ProcessorValidationException` (extends `ValidationException`) when unlisted parameters or invalid operators are passed. This automatically converts to HTTP 422 JSON in Laravel.
