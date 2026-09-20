<?php

namespace App\Http\Middleware;

use App\Enums\ReportType;
use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportTypeAccess
{
    public function handle(Request $request, Closure $next, ?string $type = null)
    {
        $user = Auth::user();

        if (!$user || !$user->staff) {
            abort(403, 'Access denied.');
        }

        $staff = $user->staff;
        $allowedTypes = $this->allowedTypesForStaff($staff);

        if ($type !== null && !in_array($type, $allowedTypes, true)) {
            abort(403, 'You do not have access to this report type.');
        }

        $request->attributes->set('allowed_report_types', $allowedTypes);

        return $next($request);
    }

    public static function allowedTypesForStaff(?Staff $staff): array
    {
        if (!$staff) {
            return [];
        }

        if ($staff->hasAnyRole([Staff::ROLE_ADMINISTRATOR, Staff::ROLE_EXECUTIVE_DIRECTOR])) {
            return ReportType::values();
        }

        if ($staff->hasAnyRole([Staff::ROLE_DONOR_MANAGER, Staff::ROLE_ADMIN_FINANCE_STAFF])) {
            return [ReportType::Funding->value];
        }

        if ($staff->role === Staff::ROLE_PROGRAM_MANAGER) {
            $program = $staff->staffProgram();

            if ($program) {
                $programName = strtolower(trim($program->program_name));

                return match ($programName) {
                    'education' => [ReportType::Education->value],
                    'sports' => [ReportType::Sports->value],
                    default => [],
                };
            }
        }

        return [];
    }
}
