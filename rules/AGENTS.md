# Universal Agent Instructions: Laravel ApiQueryBuilder

When working in this Laravel codebase or whenever implementing/refactoring API endpoints, adhere strictly to the following standards for **`warrior/api-query-builder`**.

## Core Guidelines

1. **Package Namespace & Classes:**
   - Always import: `use Warrior\ApiQueryBuilder\ApiQueryBuilder;`
   - Custom filter builder: `use Warrior\ApiQueryBuilder\Filters\Filter;`
   - Enums: `use Warrior\ApiQueryBuilder\Enums\Operator;`

2. **Controller Implementation Pattern:**
   - Prefer the Eloquent macro: `Model::apiQuery($request)` or relationship `$user->posts()->apiQuery($request)`.
   - Alternatively use `ApiQueryBuilder::for(Model::class, $request)`.
   - Always terminate with `->response(OptionalResource::class)` for clean JSON responses, or `->get()` / `->paginate()` if further processing in PHP is required.

3. **Whitelisting Rules:**
   - Every filterable field must be listed in `->allowedFilters([...])`.
   - For relations, use dot notation: `'roles.name'` (this automatically builds safe `whereHas` subqueries; NEVER write manual JOINs or duplicate `whereHas`).
   - For JSON fields, use dot notation: `'metadata.code'` (automatically translates to `metadata->code`).
   - For wildcard authorization: `'*'` for base columns, `'relation.*'` for relations, `'json_col.*'` for JSON.
   - For Local Scopes: If model has `scopeActive()`, add `'active'` to `allowedFilters`.

4. **Default Filters Rule:**
   - NEVER do `$query = User::where('status', 'active'); return $query->apiQuery()...`.
   - ALWAYS use `->defaultFilters(['status' => 'active'])` to keep the base query immutable so clients can override it.

5. **Sorting Rules:**
   - Only allow base table columns in `->allowedSorts(['col1', 'col2'])`.
   - NEVER put relation columns in `allowedSorts` (e.g. `roles.name` is prohibited to prevent pagination breakdown).
   - Set a default sort: `->defaultSort('-created_at')`. The package automatically injects the primary key as final tie-breaker.

6. **Eager Loading & Performance:**
   - Use `->allowedIncludes([...])` for on-demand `with()`.
   - Use `->allowedCounts([...])` for `withCount()`.
   - To support exports / unpaginated requests: `->allowAll(maxLimitOrUnlimited: 2000)`.

7. **Official Package Naming:**
   - Package name: `warrior/api-query-builder`
   - Class name: `Warrior\ApiQueryBuilder\ApiQueryBuilder`
   - Macro name: `apiQuery()` (or alias `apiQueryBuilder()`)
