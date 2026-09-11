# Catálogo Completo de 23 Operadores SQL 📊

Todos los operadores están tipados en el Enum `Warrior\ApiQueryBuilder\Enums\Operator`.

| Operador | Enum | Descripción | Ejemplo HTTP | Cláusula SQL Generada |
|---|---|---|---|---|
| `eq` | `EQUALS` | Igualdad exacta (implícito si se omite operador) | `?filter[status]=active` o `?filter[status][eq]=active` | `WHERE status = 'active'` |
| `neq` | `NOT_EQUALS` | Desigualdad / exclusión | `?filter[status][neq]=archived` | `WHERE status != 'archived'` |
| `gt` | `GREATER_THAN` | Mayor estricto (`>`) | `?filter[score][gt]=80` | `WHERE score > 80` |
| `gte` | `GREATER_THAN_OR_EQUAL` | Mayor o igual (`>=`) | `?filter[score][gte]=80` | `WHERE score >= 80` |
| `lt` | `LESS_THAN` | Menor estricto (`<`) | `?filter[price][lt]=100` | `WHERE price < 100` |
| `lte` | `LESS_THAN_OR_EQUAL` | Menor o igual (`<=`) | `?filter[stock][lte]=5` | `WHERE stock <= 5` |
| `in` | `IN` | Pertenencia a lista | `?filter[status][in]=active,pending` | `WHERE status IN ('active', 'pending')` |
| `not_in` | `NOT_IN` | Exclusión de lista | `?filter[role][not_in]=banned,guest` | `WHERE role NOT IN ('banned', 'guest')` |
| `between` | `BETWEEN` | Rango cerrado inclusivo (requiere 2 valores) | `?filter[score][between]=10,50` | `WHERE score BETWEEN 10 AND 50` |
| `not_between` | `NOT_BETWEEN` | Fuera de rango (requiere 2 valores) | `?filter[age][not_between]=18,65` | `WHERE age NOT BETWEEN 18 AND 65` |
| `contains` | `CONTAINS` | Búsqueda parcial de texto | `?filter[name][contains]=carlos` | `WHERE name LIKE '%carlos%'` |
| `not_contains`| `NOT_CONTAINS` | Negación de búsqueda parcial | `?filter[email][not_contains]=spam` | `WHERE email NOT LIKE '%spam%'` |
| `starts_with` | `STARTS_WITH` | Prefijo de texto | `?filter[code][starts_with]=CLI-` | `WHERE code LIKE 'CLI-%'` |
| `not_starts_with`| `NOT_STARTS_WITH`| No comienza con prefijo | `?filter[code][not_starts_with]=TEMP` | `WHERE code NOT LIKE 'TEMP%'` |
| `ends_with` | `ENDS_WITH` | Sufijo de texto | `?filter[email][ends_with]=@acme.com` | `WHERE email LIKE '%@acme.com'` |
| `not_ends_with`| `NOT_ENDS_WITH` | No termina con sufijo | `?filter[file][not_ends_with]=.pdf` | `WHERE file NOT LIKE '%.pdf'` |
| `is_null` | `IS_NULL` | Comprobación de valor NULL | `?filter[deleted_at][is_null]=true` | `WHERE deleted_at IS NULL` |
| `is_not_null` | `IS_NOT_NULL` | Comprobación de valor NO NULL | `?filter[email_verified_at][is_not_null]=1` | `WHERE email_verified_at IS NOT NULL` |
| `is_empty` | `IS_EMPTY` | Cadena de texto vacía | `?filter[bio][is_empty]=true` | `WHERE bio = ''` |
| `is_not_empty`| `IS_NOT_EMPTY` | Cadena de texto no vacía | `?filter[bio][is_not_empty]=true` | `WHERE bio != ''` |
| `date_eq` | `DATE_EQUALS` | Comparación por fecha (ignora hora) | `?filter[created_at][date_eq]=2026-05-01` | `WHERE DATE(created_at) = '2026-05-01'` |
| `date_between`| `DATE_BETWEEN` | Rango de fechas (requiere 2 fechas) | `?filter[created_at][date_between]=2026-01-01,2026-01-31` | `WHERE DATE(created_at) >= '2026-01-01' AND DATE(created_at) <= '2026-01-31'` |
| `year` | `YEAR` | Filtrado por año calendario | `?filter[created_at][year]=2026` | `WHERE YEAR(created_at) = 2026` |

---

## 🌳 Grupos Lógicos `and` / `or`

La librería soporta árboles booleanos arbitrarios para constructores de filtros en el frontend:

### Cláusula Disyuntiva `or`:
```http
GET /api/users?filter[or][0][status]=active&filter[or][1][score][gte]=90
```
**SQL Generado:**
```sql
WHERE (status = 'active' OR score >= 90)
```

### Anidación `and` dentro de `or`:
```http
GET /api/users?filter[or][0][and][0][role]=Admin&filter[or][0][and][1][score][gte]=80&filter[or][1][status]=trial
```
**SQL Generado:**
```sql
WHERE ((role = 'Admin' AND score >= 80) OR (status = 'trial'))
```
