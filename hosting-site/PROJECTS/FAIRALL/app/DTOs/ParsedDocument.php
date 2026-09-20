<?php

namespace App\DTOs;

class ParsedDocument
{
    public function __construct(
        public readonly ?string $first_name = null,
        public readonly ?string $middle_name = null,
        public readonly ?string $last_name = null,
        public readonly ?string $name_extension = null,
        public readonly ?string $birth_date = null,
        public readonly ?string $sex = null,
        public readonly ?string $email = null,
        public readonly ?string $contact_number = null,
        public readonly ?string $dial_code = null,
        public readonly ?string $address_line = null,
        public readonly ?string $country = null,
        public readonly ?string $province = null,
        public readonly ?string $city = null,
        public readonly ?string $zip = null,
        public readonly ?bool $form_given = null,
        public readonly ?bool $with_disability = null,
        public readonly array $guardians = [],
        public readonly ?ParsedIntake $intake = null,
        public readonly array $low_confidence = [],
        public readonly string $program_type = 'education',
    ) {}

    public function toArray(): array
    {
        return [
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'name_extension' => $this->name_extension,
            'birth_date' => $this->birth_date,
            'sex' => $this->sex,
            'email' => $this->email,
            'contact_number' => $this->contact_number,
            'dial_code' => $this->dial_code,
            'address_line' => $this->address_line,
            'country' => $this->country,
            'province' => $this->province,
            'city' => $this->city,
            'zip' => $this->zip,
            'form_given' => $this->form_given,
            'with_disability' => $this->with_disability,
            'guardians' => array_map(fn(ParsedGuardian $g) => $g->toArray(), $this->guardians),
            'intake' => $this->intake?->toArray(),
            'low_confidence' => $this->low_confidence,
            'program_type' => $this->program_type,
        ];
    }
}
