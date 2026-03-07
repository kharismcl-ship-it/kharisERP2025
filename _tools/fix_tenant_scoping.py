#!/usr/bin/env python3
"""
Adds `protected static bool $isScopedToTenant = false;` to every Filament
resource whose model does NOT have a direct company_id column.

Safe to run multiple times — skips files already patched.
"""

import os
import re

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


def find_resource_files():
    files = []
    # Modules
    modules_dir = os.path.join(BASE_DIR, "Modules")
    for module in os.scandir(modules_dir):
        if not module.is_dir():
            continue
        res_dir = os.path.join(module.path, "app", "Filament", "Resources")
        if os.path.isdir(res_dir):
            for f in os.listdir(res_dir):
                if f.endswith("Resource.php"):
                    files.append(os.path.join(res_dir, f))
    # App-level
    app_filament = os.path.join(BASE_DIR, "app", "Filament")
    for root, dirs, filenames in os.walk(app_filament):
        for f in filenames:
            if f.endswith("Resource.php"):
                files.append(os.path.join(root, f))
    return files


def resolve_model_path(resource_content: str) -> str | None:
    """
    Parse the resource file to resolve the model's file path.
    Handles both `$model = ModelName::class` and `$model = 'FQCN'`.
    """
    # Get all use statements: use Foo\Bar\Baz;  or  use Foo\Bar\Baz as Alias;
    use_map = {}  # alias/short_name -> FQCN
    for m in re.finditer(r"^use\s+([\w\\]+)(?:\s+as\s+(\w+))?;", resource_content, re.MULTILINE):
        fqcn = m.group(1)
        alias = m.group(2) if m.group(2) else fqcn.split("\\")[-1]
        use_map[alias] = fqcn

    # Find $model property
    m = re.search(r"protected\s+static\s+\?string\s+\$model\s*=\s*([^;]+);", resource_content)
    if not m:
        return None

    raw = m.group(1).strip().strip("'\"")

    if raw.endswith("::class"):
        short = raw[:-7].strip().split("\\")[-1]
        fqcn = use_map.get(short, raw[:-7])
    else:
        fqcn = raw.replace("/", "\\")

    # Convert FQCN to filesystem path
    parts = fqcn.replace("\\\\", "\\").split("\\")

    if parts[0] == "Modules" and len(parts) >= 4:
        module = parts[1]
        # Skip the namespace segments between module and final class
        # Modules\HR\Models\Employee  ->  Modules/HR/app/Models/Employee.php
        # Find where "Models" or similar sits
        try:
            idx = next(i for i, p in enumerate(parts) if p == "Models")
            remainder = parts[idx + 1:]
        except StopIteration:
            # fallback: last segment is the class name
            remainder = [parts[-1]]
        path = os.path.join(BASE_DIR, "Modules", module, "app", "Models", *remainder) + ".php"
    elif parts[0] in ("App", "app") and "Models" in parts:
        idx = parts.index("Models")
        remainder = parts[idx + 1:]
        path = os.path.join(BASE_DIR, "app", "Models", *remainder) + ".php"
    else:
        return None

    return path if os.path.isfile(path) else None


def model_has_company_id(model_path: str) -> bool:
    with open(model_path, "r", encoding="utf-8") as f:
        content = f.read()
    return "company_id" in content


def patch_file(filepath: str) -> str:
    """Returns 'patched', 'skipped_already', 'skipped_has_company', 'skipped_no_model'."""
    with open(filepath, "r", encoding="utf-8") as f:
        content = f.read()

    if "isScopedToTenant" in content:
        return "skipped_already"

    model_path = resolve_model_path(content)
    if not model_path:
        return "skipped_no_model"

    if model_has_company_id(model_path):
        return "skipped_has_company"

    # Patch: insert after the $model = ...; line
    new_content = re.sub(
        r"(protected\s+static\s+\?string\s+\$model\s*=[^;]+;)",
        (
            r"\1\n\n    /**\n"
            r"     * This model has no direct company_id — Filament's ownership\n"
            r"     * check is skipped. Data isolation is handled via the parent\n"
            r"     * relationship or a custom getEloquentQuery() scope.\n"
            r"     */\n"
            r"    protected static bool $isScopedToTenant = false;"
        ),
        content,
        count=1,
    )

    if new_content == content:
        return "skipped_no_model"

    with open(filepath, "w", encoding="utf-8") as f:
        f.write(new_content)

    return "patched"


results = {"patched": [], "skipped_already": [], "skipped_has_company": [], "skipped_no_model": []}

for filepath in find_resource_files():
    rel = filepath.replace(BASE_DIR + "/", "")
    result = patch_file(filepath)
    results[result].append(rel)

print(f"\n✓ Patched ({len(results['patched'])}):")
for p in sorted(results["patched"]):
    print(f"    + {p}")

print(f"\n→ Already had $isScopedToTenant ({len(results['skipped_already'])}):")
for p in sorted(results["skipped_already"]):
    print(f"    = {p}")

print(f"\n⚠ Could not resolve model ({len(results['skipped_no_model'])}):")
for p in sorted(results["skipped_no_model"]):
    print(f"    ? {p}")

print(f"\nDone. {len(results['patched'])} files patched.")
