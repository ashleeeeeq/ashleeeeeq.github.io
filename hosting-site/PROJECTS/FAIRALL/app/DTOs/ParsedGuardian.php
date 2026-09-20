<?php

namespace App\DTOs;

class ParsedGuardian
{
    public function __construct(
        public readonly ?string $guardian_type = null,
        public readonly ?string $first_name = null,
        public readonly ?string $middle_name = null,
        public readonly ?string $last_name = null,
        public readonly ?string $birth_date = null,
        public readonly ?string $place_of_birth = null,
        public readonly ?string $address_line = null,
        public readonly ?string $city = null,
        public readonly ?string $province = null,
        public readonly ?string $country = null,
        public readonly ?string $zip = null,
        public readonly ?string $sex = null,
        public readonly ?string $civil_status = null,
        public readonly ?string $contact_number = null,
        public readonly ?string $dial_code = null,
        public readonly ?string $highest_education = null,
        public readonly ?string $job = null,
        public readonly ?int $estimated_salary = null,
        public readonly ?bool $deceased = null,
    ) {}

    public function toArray(): array
    {
        return [
            'guardian_type' => $this->guardian_type,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'birth_date' => $this->birth_date,
            'place_of_birth' => $this->place_of_birth,
            'address_line' => $this->address_line,
            'city' => $this->city,
            'province' => $this->province,
            'country' => $this->country,
            'zip' => $this->zip,
            'sex' => $this->sex,
            'civil_status' => $this->civil_status,
            'contact_number' => $this->contact_number,
            'dial_code' => $this->dial_code,
            'highest_education' => $this->highest_education,
            'job' => $this->job,
            'estimated_salary' => $this->estimated_salary,
            'deceased' => $this->deceased,
        ];
    }
}
