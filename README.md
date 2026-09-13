# ApiQueryBuilder Agent Skills & Rules 🤖

Paquete oficial de **Skills, Reglas e Instrucciones para Agentes de IA** (OpenAI Codex, Google Gemini / Antigravity, Anthropic Claude / Claude Code, Cursor / Windsurf, GitHub Copilot) para el uso experto de la librería Laravel [**`warrior/api-query-builder`**](https://github.com/AlexanderBV/api-query-builder).

## 📥 Opciones de Descarga e Instalación

### Opción A: Descarga Manual (.ZIP)
Puedes descargar todo el paquete de skills listo para descomprimir:
- 📦 [**Descargar api-query-builder-skills.zip (Última versión)**](https://github.com/AlexanderBV/api-query-builder-skills/archive/refs/heads/main.zip)

---

### Opción B: Instalación Rápida con cURL (1 solo comando, sin clonar Git)

::: code-group
```bash [Para OpenAI Codex (.codex)]
mkdir -p .codex/api-query-builder
curl -sSL https://raw.githubusercontent.com/AlexanderBV/api-query-builder-skills/main/skills/api-query-builder/SKILL.md -o .codex/api-query-builder/SKILL.md
```

```bash [Para Cursor / Windsurf (.cursorrules)]
curl -sSL https://raw.githubusercontent.com/AlexanderBV/api-query-builder-skills/main/rules/.cursorrules -o .cursorrules
```

```bash [Para Claude Code (CLAUDE.md)]
curl -sSL https://raw.githubusercontent.com/AlexanderBV/api-query-builder-skills/main/rules/CLAUDE.md -o CLAUDE.md
```

```bash [Estándar Universal (AGENTS.md)]
curl -sSL https://raw.githubusercontent.com/AlexanderBV/api-query-builder-skills/main/rules/AGENTS.md -o AGENTS.md
```
:::

---

### Opción C: Clonar el Repositorio Completo

```bash
git clone https://github.com/AlexanderBV/api-query-builder-skills.git
```

---

## 🚀 Integración por Entorno de IA

### 1. OpenAI Codex (`.codex/`)
Copia la skill completa a tu carpeta `.codex`:
```bash
mkdir -p .codex/api-query-builder
cp -r skills/api-query-builder/* .codex/api-query-builder/
```

### 2. Google Gemini / Antigravity
Copia o enlaza la carpeta de la skill a tu directorio global o local de agentes:
```bash
# Global para cualquier proyecto:
mkdir -p ~/.gemini/config/skills
cp -r skills/api-query-builder ~/.gemini/config/skills/

# O específico de un proyecto Laravel:
mkdir -p .agents/skills
cp -r skills/api-query-builder .agents/skills/
```

### 3. Anthropic Claude / Claude Code
Copia `rules/CLAUDE.md` a la raíz de tu proyecto Laravel o agrégalo a tu configuración de Claude.

### 4. Cursor / Windsurf
Copia `rules/.cursorrules` o `rules/AGENTS.md` a la raíz de tu proyecto Laravel.

---

## 📁 Estructura del Repositorio

```text
rest-procesor-skills/
├── skills/
│   └── api-query-builder/
│       ├── SKILL.md                  # Skill principal (compatible con Gemini, Codex, Antigravity, Claude)
│       ├── references/
│       │   ├── operators.md          # Catálogo detallado de los 23 operadores SQL
│       │   ├── frontend-guide.md     # Guía de serialización frontend con qs, React y Vue
│       │   └── architecture.md       # Arquitectura interna de Pipes y Ciclo de Vida
│       └── examples/
│           ├── UserController.php    # Ejemplo completo de controlador CRUD empresarial
│           └── OrderController.php   # Ejemplo con JSON, rangos, fechas y grupos OR
├── rules/
│   ├── AGENTS.md                     # Estándar universal de instrucciones para agentes
│   ├── CLAUDE.md                     # Instrucciones específicas para Claude Code
│   ├── CODEX.md                      # Instrucciones para OpenAI Codex y CLI
│   └── .cursorrules                  # Reglas para Cursor IDE y Windsurf
├── scripts/
│   └── run-tests.sh                  # Runner de verificación
├── tests/
│   ├── validate_skill.php            # Validador automatizado de sintaxis y consistencia
│   └── evals/                        # Suite de evaluación con prompts y casos de prueba
└── README.md
```

---

## 🧪 Pruebas y Validación

Ejecuta el script de verificación automatizado para validar YAML frontmatter, sintaxis PHP de los ejemplos y coherencia de las referencias:

```bash
bash scripts/run-tests.sh
# O directamente:
php tests/validate_skill.php
```
