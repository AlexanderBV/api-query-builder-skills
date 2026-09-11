# Guía de Integración Frontend (JavaScript / TypeScript) 🌐

Para sincronizar de manera predecible el estado de filtros del cliente (React, Vue, Angular, Svelte) con `warrior/api-query-builder`, utiliza la librería estándar **`qs`**.

---

## 📦 Instalación de `qs` y `axios`

```bash
npm install qs axios
npm install -D @types/qs
```

---

## ⚙️ Serialización Correcta con `qs`

Para que PHP parsee arrays indexados y grupos lógicos (`or[0]`, `or[1]`) de forma consistente, usa siempre **`arrayFormat: 'indices'`**:

```typescript
import qs from 'qs';

export function serializeApiQuery(params: Record<string, any>): string {
  return qs.stringify(params, {
    arrayFormat: 'indices', // Crucial para or[0], and[0]
    encodeValuesOnly: true, // Mantiene la URL legible
    skipNulls: true,        // Omite filtros con valores null
  });
}
```

---

## ⚡ Configuración de Axios con Parámetros Automáticos

```typescript
import axios from 'axios';
import qs from 'qs';

export const apiClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL || '/api',
  paramsSerializer: (params) => {
    return qs.stringify(params, {
      arrayFormat: 'indices',
      encodeValuesOnly: true,
      skipNulls: true,
    });
  },
});
```

---

## ⚛️ Ejemplo en React con TanStack Table / Query

```typescript
import { useQuery } from '@tanstack/react-query';
import { apiClient } from './apiClient';

interface UserFilters {
  status?: string;
  search?: string;
  role?: string;
  page?: number;
  sort?: string;
}

export function useUsers(filters: UserFilters) {
  return useQuery({
    queryKey: ['users', filters],
    queryFn: async () => {
      const response = await apiClient.get('/users', {
        params: {
          filter: {
            status: filters.status,
            'roles.name': filters.role,
          },
          search: filters.search,
          sort: filters.sort || '-created_at',
          page: filters.page || 1,
          include: 'roles',
        },
      });
      return response.data;
    },
  });
}
```

---

## ⚠️ Manejo Centralizado de Errores HTTP 422

Cuando una petición envía un filtro no autorizado o un operador inválido, `ApiQueryBuilder` responde con **HTTP 422**. Puedes interceptarlo así:

```typescript
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 422) {
      const { errors, message } = error.response.data;
      console.warn('Error de validación en consulta REST:', errors || message);
    }
    return Promise.reject(error);
  }
);
```
