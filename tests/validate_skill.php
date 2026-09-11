<?php

declare(strict_types=1);

/**
 * Automated Test & Validation Suite for ApiQueryBuilder Skill.
 *
 * Verifies:
 * 1. YAML frontmatter validity (name, description).
 * 2. File existence of all cross-references.
 * 3. PHP syntax validity of all example files and code blocks.
 * 4. Zero occurrences of forbidden / deprecated terms (RestProcessor, processRest).
 */

$rootDir = dirname(__DIR__);
$skillFile = $rootDir . '/skills/api-query-builder/SKILL.md';

$errors = [];
$passed = 0;

function assertCondition(bool $condition, string $message, array &$errors, int &$passed): void
{
    if (!$condition) {
        $errors[] = "❌ FAIL: $message";
    } else {
        $passed++;
        echo "✅ PASS: $message\n";
    }
}

echo "=== Running ApiQueryBuilder Skill Verification Suite ===\n\n";

// 1. Verify SKILL.md exists
assertCondition(file_exists($skillFile), "SKILL.md exists", $errors, $passed);

$skillContent = file_get_contents($skillFile);

// 2. Verify Frontmatter
assertCondition((bool) preg_match('/^---\s*\nname:\s*api-query-builder\b/m', $skillContent), "SKILL.md has valid name in frontmatter", $errors, $passed);
assertCondition((bool) preg_match('/^description:\s*>-?\s*\n\s+/m', $skillContent), "SKILL.md has description block in frontmatter", $errors, $passed);

// 3. Verify No Deprecated Terms
$deprecatedTerms = ['RestProcessor', 'processRest', 'rest-processor'];
foreach ($deprecatedTerms as $term) {
    $hasTerm = str_contains($skillContent, $term);
    assertCondition(!$hasTerm, "SKILL.md does NOT contain deprecated term '$term'", $errors, $passed);
}

// 4. Verify References Exist
$references = [
    'references/operators.md',
    'references/frontend-guide.md',
    'references/architecture.md',
    'examples/UserController.php',
    'examples/OrderController.php',
];

foreach ($references as $ref) {
    $refPath = $rootDir . '/skills/api-query-builder/' . $ref;
    assertCondition(file_exists($refPath), "Referenced file '$ref' exists", $errors, $passed);
}

// 5. Verify PHP Syntax of Example Files
$phpFiles = [
    $rootDir . '/skills/api-query-builder/examples/UserController.php',
    $rootDir . '/skills/api-query-builder/examples/OrderController.php',
];

foreach ($phpFiles as $phpFile) {
    $output = [];
    $returnCode = 0;
    exec('php -l ' . escapeshellarg($phpFile) . ' 2>&1', $output, $returnCode);
    $basename = basename($phpFile);
    assertCondition($returnCode === 0, "PHP syntax check passes for $basename", $errors, $passed);
}

// 6. Verify Rules files exist
$rulesFiles = [
    $rootDir . '/rules/AGENTS.md',
    $rootDir . '/rules/CLAUDE.md',
    $rootDir . '/rules/.cursorrules',
    $rootDir . '/tests/evals/test_cases.json',
];

foreach ($rulesFiles as $ruleFile) {
    $basename = basename($ruleFile);
    assertCondition(file_exists($ruleFile), "Rule / Eval file '$basename' exists", $errors, $passed);
}

echo "\n--------------------------------------------------\n";
echo "Results: $passed assertions passed.\n";

if (!empty($errors)) {
    echo "\nFailures:\n" . implode("\n", $errors) . "\n";
    exit(1);
}

echo "🎉 ALL CHECKS PASSED SUCCESSFULLY!\n";
exit(0);
