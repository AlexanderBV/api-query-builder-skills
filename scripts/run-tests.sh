#!/usr/bin/env bash
set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd )"

echo "Running Skill Verification..."
php "$DIR/tests/validate_skill.php"
