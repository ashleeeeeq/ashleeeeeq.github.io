<?php

namespace App\Services\Parsers;

use App\DTOs\ParsedDocument;
use App\DTOs\ParsedGuardian;
use App\DTOs\ParsedIntake;

abstract class AbstractIntakeSheetParser
{
    protected array $lowConfidence = [];

    protected string $currentSection = 'beneficiary';

    protected array $fields = [];

    protected bool $fatherContentStarted = false;

    public function parse(string $rawText): ParsedDocument
    {
        $this->reset();

        $lines = $this->normalizeLines($rawText);
        $this->processLines($lines);
        $this->postProcess();

        return $this->assembleDocument();
    }

    protected function reset(): void
    {
        $this->lowConfidence = [];
        $this->fields = [];
        $this->currentSection = 'beneficiary';
        $this->fatherContentStarted = false;
    }

    protected function assembleDocument(): ParsedDocument
    {
        $guardians = $this->buildGuardians();
        $intake = $this->buildIntake();

        $addressParts = $this->parseAddress($this->fields['address_line'] ?? null);

        return new ParsedDocument(
            first_name: $this->fields['first_name'] ?? null,
            middle_name: $this->fields['middle_name'] ?? null,
            last_name: $this->fields['last_name'] ?? null,
            name_extension: $this->fields['name_extension'] ?? null,
            birth_date: $this->normalizeDate($this->fields['birth_date'] ?? null),
            sex: $this->normalizeSex($this->fields['sex'] ?? null),
            email: $this->fields['email'] ?? null,
            contact_number: $this->normalizePhoneNumber($this->fields['contact_number'] ?? null),
            dial_code: $this->fields['dial_code'] ?? '+63',
            address_line: $addressParts['address_line'],
            country: $addressParts['country'],
            province: $addressParts['province'],
            city: $addressParts['city'],
            zip: $this->fields['zip'] ?? null,
            form_given: true,
            with_disability: $this->fields['with_disability'] ?? null,
            guardians: $guardians,
            intake: $intake,
            low_confidence: $this->lowConfidence,
            program_type: $this->getProgramType(),
        );
    }

    abstract protected function getProgramType(): string;

    protected function normalizeLines(string $rawText): array
    {
        $text = str_replace("\r\n", "\n", $rawText);
        $text = str_replace("\r", "\n", $text);
        $lines = explode("\n", $text);
        return array_values(array_filter($lines, fn($l) => trim($l) !== ''));
    }

    protected function processLines(array $lines): void
    {
    }

    protected function postProcess(): void
    {
    }

    protected function parseAddress(?string $addressLine): array
    {
        $result = [
            'address_line' => null,
            'city' => null,
            'province' => null,
            'country' => null,
        ];

        if (empty($addressLine)) {
            return $result;
        }

        $parts = array_map('trim', explode(',', $addressLine));
        $result['address_line'] = $parts[0] ?? null;
        $result['city'] = $parts[1] ?? null;
        if (count($parts) > 2) {
            $result['province'] = $parts[2] ?? null;
        }
        if (count($parts) > 3) {
            $result['country'] = $parts[3] ?? null;
        }

        return $result;
    }

    protected function buildGuardians(): array
    {
        return [];
    }

    protected function buildIntake(): ?ParsedIntake
    {
        return null;
    }

    protected function normalizeDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        $date = trim($date);

        $formats = ['Y-m-d', 'm/d/Y', 'm-d-Y', 'd/m/Y', 'F j, Y', 'j F Y', 'M. j, Y', 'M j, Y'];

        foreach ($formats as $fmt) {
            $dt = \DateTime::createFromFormat($fmt, $date);
            if ($dt && $dt->format($fmt) === $date) {
                return $dt->format('Y-m-d');
            }
        }

        return $date;
    }

    protected function normalizeCivilStatus(?string $status): ?string
    {
        if (empty($status)) {
            return null;
        }

        $lower = strtolower(trim($status));

        return match ($lower) {
            'kasal', 'married' => 'Married',
            'single' => 'Single',
            'divorced' => 'Divorced',
            default => $status,
        };
    }

    protected function normalizeSex(?string $sex): ?string
    {
        if (empty($sex)) {
            return null;
        }

        $lower = strtolower(trim($sex));

        return match ($lower) {
            'lalaki', 'male', 'm', 'boy' => 'male',
            'babae', 'female', 'f', 'girl' => 'female',
            default => $sex,
        };
    }

    protected function splitFullName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName));

        return [
            'first_name' => $parts[0] ?? null,
            'middle_name' => count($parts) > 2 ? implode(' ', array_slice($parts, 1, -1)) : null,
            'last_name' => count($parts) > 1 ? end($parts) : null,
        ];
    }

    protected function normalizePhoneNumber(?string $number, string $dialCode = '+63'): ?string
    {
        if ($number === null) return null;
        if ($dialCode === '+63') {
            $number = preg_replace('/^\+?63/', '', $number);
            $number = ltrim($number, '0');
        }
        return $number !== '' ? $number : null;
    }
}
