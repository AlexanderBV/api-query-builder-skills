# Arquitectura Interna de ApiQueryBuilder 🏗️

`warrior/api-query-builder` está construido sobre el patrón **Pipeline** nativo de Laravel (`Illuminate\Pipeline\Pipeline`).

---

## 🔄 Flujo de Ejecución de Pipes

Cada petición HTTP pasa secuencialmente por una serie de *Pipes* desacoplados:

```text
Request ──> [FilterPipe] ──> [SearchPipe] ──> [SortPipe] ──> [IncludePipe] ──> [PaginationPipe] ──> Response
```

1. **`FilterPipe`:**
   - Extrae `?filter[...]` y valida contra `allowedFilters`.
   - Inyecta `defaultFilters` si el cliente no envió filtros para campos con valores por defecto.
   - Resuelve operadores (`Operator`), relaciones `whereHas`, columnas JSON `->`, y scopes locales / closures custom.
   - Aplica grupos lógicos `and` / `or` con paréntesis exactos en SQL.
2. **`SearchPipe`:**
   - Valida la longitud del término `?search=` (máx. 200 caracteres).
   - Agrupa condiciones `OR` dentro de un bloque aislado `WHERE (...)` para no contaminar filtros de tenencia previa.
   - Detecta el dialecto de BD: `ILIKE` en PostgreSQL, `LIKE` en MySQL/SQLite.
3. **`SortPipe`:**
   - Extrae `?sort=col1,-col2`.
   - Valida contra `allowedSorts`.
   - Aplica dirección (prefijo `-` para DESC, normal para ASC).
   - Inyecta desempate determinista automático con la clave primaria del modelo (`$model->getKeyName()`).
4. **`IncludePipe`:**
   - Aplica Eager Loading seguro con `with()` para `?include=`.
   - Aplica conteos virtuales optimizados con `withCount()` para `?count=`.
5. **`PaginationPipe`:**
   - Si se solicita `?all=true`, ejecuta validación de conteo preventivo y retorna colección plana `Collection`.
   - Si no, ejecuta el paginador solicitado (`page` -> `LengthAwarePaginator`, `simple` -> `SimplePaginator`, `cursor` -> `CursorPaginator`).
   - Normaliza `per_page` contra los límites seguros configurados (máx. 100 por defecto).
