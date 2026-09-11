# ApiQueryBuilder Agent Skills & Rules 🤖

Paquete de **Skills, Reglas e Instrucciones para Agentes de IA** (Google Gemini / Antigravity, OpenAI Codex / Cursor / Windsurf, Anthropic Claude / Claude Code) para el uso experto de la librería Laravel [**`warrior/api-query-builder`**](https://github.com/AlexanderBV/api-query-builder).

## 📁 Estructura del Repositorio

```text
rest-procesor-skills/
├── skills/
│   └── api-query-builder/
│       ├── SKILL.md                  # Skill principal (compatible con Gemini, Antigravity, Claude)
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
│   └── .cursorrules                  # Reglas para Cursor IDE y Windsurf
├── tests/
│   ├── validate_skill.php            # Validador automatizado de sintaxis y consistencia
│   └── evals/                        # Suite de evaluación con prompts y casos de prueba
└── README.md
```

## 🚀 Instalación y Uso

### 1. Para Google Gemini / Antigravity
Copia o enlaza la carpeta de la skill a tu directorio global o local de agentes:
```bash
# Global para cualquier proyecto:
mkdir -p ~/.gemini/config/skills
cp -r skills/api-query-builder ~/.gemini/config/skills/

# O específico de un proyecto Laravel:
mkdir -p .agents/skills
cp -r skills/api-query-builder .agents/skills/
```

### 2. Para Claude / Claude Code
Copia `rules/CLAUDE.md` a la raíz de tu proyecto Laravel o agrégalo a tu configuración de Claude.

### 3. Para Cursor / Windsurf / Codex
Copia `rules/.cursorrules` o `rules/AGENTS.md` a la raíz de tu proyecto Laravel.
