<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;

final class SeederSupport
{
    public const CREATOR_STAFF_ID = 1;

    public static function yearFromActivityName(string $name): ?int
    {
        if (preg_match('/(\d{4})$/', $name, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    /**
     * joined_at for an activity participant, or null when program start is after the activity year.
     *
     * @param  mixed  $programStartAt  Probation scholar or registered player start date
     */
    public static function joinedAtForActivityYear(mixed $programStartAt, int $activityYear): ?Carbon
    {
        if ($programStartAt === null) {
            return null;
        }

        $started = Carbon::parse($programStartAt);
        $yearStart = Carbon::create($activityYear, 1, 1, 8, 0, 0);
        $yearEnd = Carbon::create($activityYear, 12, 31, 23, 59, 59);

        if ($started->gt($yearEnd)) {
            return null;
        }

        return $started->lte($yearStart) ? $yearStart : $started;
    }

    public static function ageAtYear(mixed $birthDate, int $year): int
    {
        $birth = Carbon::parse($birthDate);
        $onDate = Carbon::create($year, 12, 31);

        return (int) $birth->diffInYears($onDate);
    }

    public static function matchesSportsAgeGroup(int $age, string $ageGroup): bool
    {
        return match ($ageGroup) {
            'U12' => $age <= 12,
            'U14' => $age >= 13 && $age <= 14,
            'U16' => $age >= 15 && $age <= 16,
            default => $age >= 17,
        };
    }

    public static function activityYearTimestamp(int $year): Carbon
    {
        return Carbon::create($year, 1, 1, 0, 0, 0);
    }

    public static function gradeOrder(): array
    {
        return ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year'];
    }

    public static function gradeIndex(?string $gradeLevel): ?int
    {
        if ($gradeLevel === null) {
            return null;
        }

        $index = array_search((string) $gradeLevel, self::gradeOrder(), true);

        return $index === false ? null : $index;
    }

    public static function nextGradeLevel(string $gradeLevel): ?string
    {
        $index = self::gradeIndex($gradeLevel);

        if ($index === null) {
            return null;
        }

        return self::gradeOrder()[$index + 1] ?? null;
    }

    public static function educationLevelForGradeLevel(string $gradeLevel): string
    {
        return match ($gradeLevel) {
            '1', '2', '3', '4', '5', '6' => 'elementary',
            '7', '8', '9', '10' => 'high_school',
            '11', '12' => 'shs',
            default => 'college',
        };
    }

    public static function schoolNameForGrade(string $gradeLevel): string
    {
        if (str_contains($gradeLevel, 'Year')) {
            return 'FAIRALL College';
        }

        $numericGrade = (int) $gradeLevel;

        if ($numericGrade >= 1 && $numericGrade <= 6) {
            return 'FAIRALL Elementary School';
        }

        if ($numericGrade >= 7 && $numericGrade <= 10) {
            return 'FAIRALL Junior High School';
        }

        if ($numericGrade >= 11 && $numericGrade <= 12) {
            return 'FAIRALL Senior High School';
        }

        return 'FAIRALL College';
    }

    public static function termsForGradeLevel(string $gradeLevel): array
    {
        return self::educationLevelForGradeLevel($gradeLevel) === 'college'
            ? ['1st', '2nd']
            : ['1st', '2nd', '3rd', '4th'];
    }

    public static function academicRecordCountForGradeLevel(string $gradeLevel): int
    {
        return count(self::termsForGradeLevel($gradeLevel));
    }

    public static function intakeAgeRangeForGradeLevel(string $gradeLevel): array
    {
        return match ($gradeLevel) {
            '1' => [6, 7],
            '2' => [7, 8],
            '3' => [8, 9],
            '4' => [9, 10],
            '5' => [10, 11],
            '6' => [11, 12],
            '7' => [12, 13],
            '8' => [13, 14],
            '9' => [14, 15],
            '10' => [15, 16],
            '11' => [16, 17],
            '12' => [17, 18],
            '1st Year' => [18, 19],
            '2nd Year' => [19, 20],
            '3rd Year' => [20, 21],
            '4th Year' => [21, 22],
            '5th Year' => [22, 23],
            default => [6, 7],
        };
    }

    public static function ageForIntakeGradeLevel(string $gradeLevel): int
    {
        [$minimum, $maximum] = self::intakeAgeRangeForGradeLevel($gradeLevel);

        return random_int($minimum, $maximum);
    }

    public static function birthDateForIntakeGradeLevel(string $gradeLevel, int $academicYear = 2020): Carbon
    {
        $age = self::ageForIntakeGradeLevel($gradeLevel);

        return Carbon::create($academicYear, 6, 1)
            ->subYears($age)
            ->subDays(random_int(0, 120))
            ->startOfDay();
    }

    public static function gwaForGradeHistory(int $index): float
    {
        return round(84.5 + (($index % 8) * 0.9), 2);
    }

    public static function randomGrade(float $min = 80.0, float $max = 100.0, int $precision = 1): float
    {
        $scale = pow(10, $precision);
        return round(mt_rand((int)($min * $scale), (int)($max * $scale)) / $scale, $precision);
    }

    public static function subjectsForGrade(string $gradeLevel): array
    {
        if (str_contains($gradeLevel, 'Year')) {
            return [
                ['name' => 'General Education 1', 'grade' => self::randomGrade()],
                ['name' => 'General Education 2', 'grade' => self::randomGrade()],
                ['name' => 'Major Subject 1', 'grade' => self::randomGrade()],
                ['name' => 'Major Subject 2', 'grade' => self::randomGrade()],
                ['name' => 'Major Subject 3', 'grade' => self::randomGrade()],
                ['name' => 'Elective', 'grade' => self::randomGrade()],
            ];
        }

        $numericGrade = (int) $gradeLevel;

        if ($numericGrade == 1) {
            return [
                ['name' => 'Language', 'grade' => self::randomGrade()],
                ['name' => 'Reading and Literacy', 'grade' => self::randomGrade()],
                ['name' => 'Mathematics', 'grade' => self::randomGrade()],
                ['name' => 'Makabansa', 'grade' => self::randomGrade()],
                ['name' => 'GMRC', 'grade' => self::randomGrade()],
            ];
        }

        if ($numericGrade == 2) {
            return [
                ['name' => 'English', 'grade' => self::randomGrade()],
                ['name' => 'Filipino', 'grade' => self::randomGrade()],
                ['name' => 'Mathematics', 'grade' => self::randomGrade()],
                ['name' => 'Makabansa', 'grade' => self::randomGrade()],
                ['name' => 'GMRC', 'grade' => self::randomGrade()],
            ];
        }

        if ($numericGrade == 3) {
            return [
                ['name' => 'English', 'grade' => self::randomGrade()],
                ['name' => 'Filipino', 'grade' => self::randomGrade()],
                ['name' => 'Science', 'grade' => self::randomGrade()],
                ['name' => 'Mathematics', 'grade' => self::randomGrade()],
                ['name' => 'Makabansa', 'grade' => self::randomGrade()],
                ['name' => 'GMRC', 'grade' => self::randomGrade()],
            ];
        }

        if ($numericGrade >= 11) {
            return [
                ['name' => 'Oral Communication', 'grade' => self::randomGrade()],
                ['name' => 'Reading and Writing', 'grade' => self::randomGrade()],
                ['name' => '21st-century Literature from the Philippines and the World', 'grade' => self::randomGrade()],
                ['name' => 'Contemporary Philippine Arts from the Regions', 'grade' => self::randomGrade()],
                ['name' => 'Media and Information Literacy', 'grade' => self::randomGrade()],
                ['name' => 'General Mathematics', 'grade' => self::randomGrade()],
                ['name' => 'Statistics and Probability', 'grade' => self::randomGrade()],
                ['name' => 'Earth and Life Science', 'grade' => self::randomGrade()],
                ['name' => 'Personal Development', 'grade' => self::randomGrade()],
            ];
        }

        // grade 4-10

        return [
                ['name' => 'English', 'grade' => self::randomGrade()],
                ['name' => 'Filipino', 'grade' => self::randomGrade()],
                ['name' => 'Science', 'grade' => self::randomGrade()],
                ['name' => 'Mathematics', 'grade' => self::randomGrade()],
                ['name' => 'Araling Panlipunan', 'grade' => self::randomGrade()],
                ['name' => 'MAPEH', 'grade' => self::randomGrade()],
                ['name' => 'TLE/EPP', 'grade' => self::randomGrade()],
        ];
    }

    public static function previousEducationStage(string $stage): string
    {
        return match ($stage) {
            'pre-school' => 'pre-school',
            'elementary' => 'pre-school',
            'high_school' => 'elementary',
            'shs' => 'high_school',
            'college' => 'shs',
            'post-grad' => 'college',
            default => 'pre-school',
        };
    }
}
