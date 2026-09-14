<?php

namespace App\DTOs;

class ParsedIntake
{
    public function __construct(
        public readonly ?int $age = null,
        public readonly ?string $place_of_birth = null,
        public readonly ?int $number_of_siblings = null,
        public readonly ?int $older_sibling_age = null,
        public readonly ?int $younger_sibling_age = null,
        public readonly ?string $civil_status = null,
        public readonly ?string $highest_education = null,
        public readonly ?string $school_name = null,
        public readonly ?string $grade_level = null,
        public readonly ?bool $other_scholarship = null,
        public readonly ?string $scholarship_org_question = null,
        public readonly ?string $hardworking_question = null,
        public readonly ?string $dream_question = null,
        public readonly ?string $scholarship_question = null,
    ) {}

    public function toArray(): array
    {
        return [
            'age' => $this->age,
            'place_of_birth' => $this->place_of_birth,
            'number_of_siblings' => $this->number_of_siblings,
            'older_sibling_age' => $this->older_sibling_age,
            'younger_sibling_age' => $this->younger_sibling_age,
            'civil_status' => $this->civil_status,
            'highest_education' => $this->highest_education,
            'school_name' => $this->school_name,
            'grade_level' => $this->grade_level,
            'other_scholarship' => $this->other_scholarship,
            'scholarship_org_question' => $this->scholarship_org_question,
            'hardworking_question' => $this->hardworking_question,
            'dream_question' => $this->dream_question,
            'scholarship_question' => $this->scholarship_question,
        ];
    }
}
