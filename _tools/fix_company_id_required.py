#!/usr/bin/env python3
"""
Remove ->required() from company_id relationship Select fields in every
Filament Resource form. The BelongsToCompany trait now handles auto-stamping
company_id via its creating observer, so no per-Create-page changes are needed.
"""
import re
import os

ALREADY_FIXED = {
    'Modules/ClientService/app/Filament/Resources/CsAttendanceResource.php',
    'Modules/ClientService/app/Filament/Resources/CsVisitorResource.php',
}

# Match the company_id Select chain up to (but not including) ->required().
# Uses negative lookahead so we don't cross into a following Select::make block.
COMPANY_REQUIRED_RE = re.compile(
    r'(Select::make\([\'"]company_id[\'"]\)(?:(?!Select::make).)*?)->required\(\)',
    re.DOTALL,
)


def fix_resource(path: str) -> bool:
    content = open(path).read()
    new_content = COMPANY_REQUIRED_RE.sub(r'\1', content)
    if new_content != content:
        open(path, 'w').write(new_content)
        return True
    return False


def main():
    fixed = 0
    skipped = 0

    for root, _dirs, files in os.walk('Modules'):
        for f in files:
            if not f.endswith('Resource.php'):
                continue
            path = os.path.join(root, f)

            if path in ALREADY_FIXED:
                skipped += 1
                continue

            content = open(path).read()
            if ('company_id' not in content
                    or "relationship('company'" not in content
                    or '->required()' not in content):
                continue

            if fix_resource(path):
                print(f'  Fixed: {path}')
                fixed += 1

    print(f'\nDone. Resources patched: {fixed}  |  Skipped (already done): {skipped}')


if __name__ == '__main__':
    main()
