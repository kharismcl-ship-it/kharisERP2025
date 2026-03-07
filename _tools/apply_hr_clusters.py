#!/usr/bin/env python3
"""
Updates all HR Filament resources to use clusters or the 'HR Manager' nav group.
Uses lambda replacements to avoid backslash-escape issues in re.sub.
"""

import os
import re

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
RES_DIR  = os.path.join(BASE_DIR, "Modules", "HR", "app", "Filament", "Resources")

NAMESPACE = r"Modules\HR\Filament\Clusters"

RESOURCE_MAP = {
    # Standalone — direct nav items under 'HR Manager'
    "EmployeeResource":               None,
    "LeaveRequestResource":           None,
    "PayrollRunResource":             None,
    "AttendanceRecordResource":       None,
    "PerformanceReviewResource":      None,
    "AnnouncementResource":           None,

    # HrSetupCluster
    "DepartmentResource":             "HrSetupCluster",
    "JobPositionResource":            "HrSetupCluster",
    "SalaryScaleResource":            "HrSetupCluster",
    "AllowanceTypeResource":          "HrSetupCluster",
    "DeductionTypeResource":          "HrSetupCluster",
    "BenefitTypeResource":            "HrSetupCluster",
    "ShiftResource":                  "HrSetupCluster",
    "PublicHolidayResource":          "HrSetupCluster",
    "LeaveTypeResource":              "HrSetupCluster",
    "LeaveApprovalWorkflowResource":  "HrSetupCluster",

    # HrRecruitmentCluster
    "JobVacancyResource":             "HrRecruitmentCluster",

    # HrPerformanceCluster
    "PerformanceCycleResource":       "HrPerformanceCluster",
    "KpiDefinitionResource":          "HrPerformanceCluster",
    "EmployeeGoalResource":           "HrPerformanceCluster",

    # HrRelationsCluster
    "DisciplinaryCaseResource":       "HrRelationsCluster",
    "GrievanceCaseResource":          "HrRelationsCluster",

    # HrLearningCluster
    "TrainingProgramResource":        "HrLearningCluster",
    "CertificationResource":          "HrLearningCluster",

    # HrRecordsCluster
    "EmployeeSalaryResource":         "HrRecordsCluster",
    "EmploymentContractResource":     "HrRecordsCluster",
    "EmployeeDocumentResource":       "HrRecordsCluster",
    "EmployeeLoanResource":           "HrRecordsCluster",
    "LeaveBalanceResource":           "HrRecordsCluster",
    "EmployeeCompanyAssignmentResource": "HrRecordsCluster",
    "HostelStaffAssignmentResource":  "HrRecordsCluster",
}


def strip_old_nav(content: str) -> str:
    """Remove existing $navigationGroup and $cluster declarations and cluster use imports."""
    content = re.sub(r"[ \t]*protected static string\|\\UnitEnum\|null \$navigationGroup\s*=\s*[^;]+;\n", "", content)
    content = re.sub(r"[ \t]*protected static \?string \$cluster\s*=\s*[^;]+;\n", "", content)
    content = re.sub(r"[ \t]*use Modules\\HR\\Filament\\Clusters\\[A-Za-z]+;\n", "", content)
    return content


patched = []
skipped = []

for resource_name, cluster_name in RESOURCE_MAP.items():
    filepath = os.path.join(RES_DIR, resource_name + ".php")
    if not os.path.isfile(filepath):
        skipped.append(f"{resource_name} (not found)")
        continue

    with open(filepath, "r", encoding="utf-8") as f:
        original = f.read()

    content = strip_old_nav(original)

    if cluster_name is None:
        # ── Standalone: inject 'HR Manager' navigationGroup ────────────────────
        group_line = "    protected static string|\\UnitEnum|null $navigationGroup = 'HR Manager';"
        content = re.sub(
            r"(class \w+Resource extends Resource\s*\{)",
            lambda m, gl=group_line: m.group(1) + "\n" + gl,
            content,
            count=1,
        )
    else:
        # ── Clustered: add use import + $cluster property ──────────────────────
        fqcn      = NAMESPACE + "\\" + cluster_name
        use_line  = f"use {fqcn};"
        prop_line = f"    protected static ?string $cluster = {cluster_name}::class;"

        # Add use import right after namespace declaration
        if use_line not in content:
            content = re.sub(
                r"(namespace [^\n]+;\n)",
                lambda m, ul=use_line: m.group(1) + "\n" + ul + "\n",
                content,
                count=1,
            )

        # Add $cluster inside class body
        content = re.sub(
            r"(class \w+Resource extends Resource\s*\{)",
            lambda m, pl=prop_line: m.group(1) + "\n" + pl,
            content,
            count=1,
        )

    if content != original:
        with open(filepath, "w", encoding="utf-8") as f:
            f.write(content)
        patched.append(resource_name)
    else:
        skipped.append(f"{resource_name} (no change)")

print(f"\nPatched ({len(patched)}):")
for p in patched:
    print(f"  + {p}")

print(f"\nSkipped ({len(skipped)}):")
for s in skipped:
    print(f"  - {s}")

print(f"\nDone. {len(patched)} resources updated.")