#!/usr/bin/env python3
"""
Adds `use App\Models\Concerns\BelongsToCompany;` import and
`use BelongsToCompany;` trait statement to every PHP model file
that contains `'company_id'` in its source (indicating it owns a
company_id column).

Safe to run multiple times — skips files that already have the trait.
"""

import os
import re
import sys
import subprocess

BASE = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "Modules")
IMPORT_LINE = "use App\\Models\\Concerns\\BelongsToCompany;"
TRAIT_USE   = "    use BelongsToCompany;"

# Collect only model files (files under app/Models/ in each module)
# that contain 'company_id' in their source.
result = subprocess.run(
    ["grep", "-rl", "'company_id'", "--include=*.php", BASE],
    capture_output=True, text=True
)
all_matches = [f.strip() for f in result.stdout.splitlines() if f.strip()]

# Filter: only keep files whose path contains /app/Models/
files = [f for f in all_matches if "/app/Models/" in f]

modified = 0
skipped  = 0

for path in sorted(files):
    with open(path, "r", encoding="utf-8") as fh:
        content = fh.read()

    # Skip if already patched
    if "BelongsToCompany" in content:
        skipped += 1
        continue

    # ── 1. Add the import ────────────────────────────────────────────────────
    # Insert just before the `class ` declaration line.
    # Handles blank lines between last import and class.
    class_pattern = re.compile(r'^(class\s)', re.MULTILINE)
    match = class_pattern.search(content)
    if not match:
        print(f"  SKIP (no class): {path}")
        skipped += 1
        continue

    insert_pos = match.start()
    # Find the last non-blank line before the class declaration
    before_class = content[:insert_pos]
    # Append the import on a new line before the class
    content = before_class.rstrip('\n') + "\n" + IMPORT_LINE + "\n\n" + content[insert_pos:]

    # ── 2. Add the trait use ─────────────────────────────────────────────────
    # Find the opening brace of the class body and insert after it.
    # Pattern: class Foo ... {\n  (existing stuff or nothing)
    # We look for the first `{` after the class keyword and insert after it.
    class_body_pattern = re.compile(
        r'(class\s+\w[\w\s,<>\\]*\{)',
        re.MULTILINE | re.DOTALL
    )
    match2 = class_body_pattern.search(content)
    if not match2:
        print(f"  SKIP (no class body): {path}")
        skipped += 1
        continue

    open_brace_end = match2.end()
    # Check what immediately follows — if there's already a `use ` trait line,
    # we append to it; otherwise insert a new line.
    after_brace = content[open_brace_end:]

    # Check if there's an existing `use Trait1, Trait2;` pattern right after
    existing_use_pattern = re.compile(r'^(\s*use\s+)([\w\\,\s]+)(;)', re.MULTILINE)
    use_match = existing_use_pattern.search(after_brace)
    if use_match and use_match.start() < 200:  # within first 200 chars after {
        # Append BelongsToCompany to the existing use statement
        new_use = use_match.group(1) + use_match.group(2).rstrip() + ", BelongsToCompany" + use_match.group(3)
        new_after = after_brace[:use_match.start()] + new_use + after_brace[use_match.end():]
        content = content[:open_brace_end] + new_after
    else:
        # No existing trait use — insert a fresh one
        content = content[:open_brace_end] + "\n" + TRAIT_USE + "\n" + content[open_brace_end:]

    with open(path, "w", encoding="utf-8") as fh:
        fh.write(content)

    modified += 1
    print(f"  PATCHED: {os.path.relpath(path)}")

print(f"\nDone. {modified} files patched, {skipped} skipped.")