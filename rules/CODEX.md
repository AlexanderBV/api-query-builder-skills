# OpenAI Codex Instructions: Laravel ApiQueryBuilder

This document provides specialized directives for **OpenAI Codex CLI**, ChatGPT, and Codex-powered developer agents when working with [**`warrior/api-query-builder`**](https://github.com/AlexanderBV/api-query-builder).

## 📁 Installation in `.codex/` (Project Level)

To enable Codex to recognize this skill in any Laravel repository, place the skill folder in your project's `.codex` directory:

```bash
mkdir -p .codex/api-query-builder
cp -r skills/api-query-builder/* .codex/api-query-builder/
```

Or download it directly without cloning:
```bash
mkdir -p .codex/api-query-builder
curl -sSL https://raw.githubusercontent.com/AlexanderBV/api-query-builder-skills/main/skills/api-query-builder/SKILL.md -o .codex/api-query-builder/SKILL.md
```

---

## 🎯 Codex System Prompt & Core Directives

When prompting Codex or setting up custom instructions, inject the following system directive:

```text
You are an expert Laravel engineer specializing in "warrior/api-query-builder".
Always adhere to the following architecture rules:
1. Prefer Model::apiQuery($request) macro or ApiQueryBuilder::for(Model::class, $request).
2. All query parameters must be explicitly whitelisted via allowedFilters([...]), allowedSorts([...]), allowedIncludes([...]), allowedCounts([...]), allowedSearch([...]).
3. Relation filters use dot notation ('roles.name') and auto-generate safe whereHas subqueries.
4. JSON fields use dot notation ('metadata.code') and auto-translate to MySQL/Postgres JSON paths.
5. Base queries must remain immutable: NEVER write Model::where('status', 'active')->apiQuery(). ALWAYS use ->defaultFilters(['status' => 'active']).
6. NEVER sort on relations (e.g. allowedSorts(['roles.name']) is prohibited to prevent pagination issues).
7. Terminate with ->response(Resource::class) or ->get() / ->paginate().
8. Unlisted parameters or invalid operators throw ProcessorValidationException (HTTP 422).
```

---

## 🛠️ Codex Workflow & Verification

Codex can execute the following commands to test query endpoints and run PHPUnit:

```bash
# Run unit tests:
php artisan test --filter=QueryTest

# Static analysis:
./vendor/bin/phpstan analyse
```
