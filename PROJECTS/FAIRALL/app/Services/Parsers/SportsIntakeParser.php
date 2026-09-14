<?php

namespace App\Services\Parsers;

use App\DTOs\ParsedDocument;
use App\DTOs\ParsedGuardian;

class SportsIntakeParser extends AbstractIntakeSheetParser
{
    public function parse(string $rawText): ParsedDocument
    {
        $this->reset();
        $text = str_replace(["\r\n", "\r"], "\n", $rawText);
        $lines = $this->normalizeLines($rawText);

        $this->extractPlayerInfo($text, $lines);
        $this->extractGuardianInfo($text, $lines);

        return $this->assembleDocument();
    }

    protected function getProgramType(): string
    {
        return 'sports';
    }

    private function extractPlayerInfo(string $text, array $lines): void
    {
        if (preg_match('/IN CASE OF EMERGENCIES\s*\n(.+?)(?=\nPlease contact)/si', $text, $m)) {
            $valueBlock = trim($m[1]);
            $valueLines = explode("\n", $valueBlock);
            $valueLines = array_values(array_filter($valueLines, fn($l) => trim($l) !== ''));
        } else {
            return;
        }

        foreach ($valueLines as $line) {
            $line = trim($line);

            if (!isset($this->fields['first_name']) && preg_match('/^[A-Z][a-z]+\s+[A-Z][a-z]+/', $line)) {
                $nameParts = $this->splitFullName($line);
                $this->fields['first_name'] = $nameParts['first_name'];
                $this->fields['middle_name'] = $nameParts['middle_name'];
                $this->fields['last_name'] = $nameParts['last_name'];
                continue;
            }

            if (!isset($this->fields['birth_date']) && preg_match('/^[A-Z][a-z]+\.?\s+\d{1,2},?\s+\d{4}$/', $line)) {
                $this->fields['birth_date'] = $line;
                continue;
            }

            if (!isset($this->fields['contact_number']) && preg_match('/^\d{7,}$/', $line)) {
                $this->fields['contact_number'] = $line;
                $this->fields['dial_code'] = '+63';
                continue;
            }

            if (!isset($this->fields['address_line']) && preg_match('/[A-Za-z]/', $line) && str_contains($line, ',')) {
                $this->fields['address_line'] = $line;
                continue;
            }

            if (!isset($this->fields['class_schedule']) && preg_match('/^(AM|PM)$/i', $line)) {
                $this->fields['class_schedule'] = strtoupper($line);
                continue;
            }
        }
    }

    private function extractGuardianInfo(string $text, array $lines): void
    {
        $guardianLabelsFound = false;
        $inResponsibilities = false;
        $guardianValueLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if (preg_match('/Please contact/i', $trimmed)) {
                $guardianLabelsFound = true;
                continue;
            }

            if ($guardianLabelsFound && preg_match('/^Contact No/i', $trimmed)) {
                continue;
            }

            if ($guardianLabelsFound && preg_match('/^Address\s*$/i', $trimmed)) {
                continue;
            }

            if ($guardianLabelsFound && preg_match('/FAIRPLAY.S RESPONSIBILITIES/i', $trimmed)) {
                $inResponsibilities = true;
                continue;
            }

            if (!$guardianLabelsFound) {
                continue;
            }

            if (preg_match('/^[A-Z][a-z]+\s+[A-Z][a-z]+/', $trimmed)) {
                $guardianValueLines[] = $trimmed;
                continue;
            }
            if (preg_match('/^\d{7,}$/', $trimmed)) {
                $guardianValueLines[] = $trimmed;
                continue;
            }
            if (preg_match('/[A-Za-z]/', $trimmed) && str_contains($trimmed, ',')) {
                $guardianValueLines[] = $trimmed;
            }
        }

        if (empty($guardianValueLines)) {
            return;
        }

        $nameFound = false;
        $guardian = ['guardian_type' => 'guardian'];

        foreach ($guardianValueLines as $line) {
            if (!$nameFound && preg_match('/^[A-Z][a-z]+\s+[A-Z][a-z]+/', $line)) {
                $nameParts = $this->splitFullName($line);
                $guardian['first_name'] = $nameParts['first_name'];
                $guardian['middle_name'] = $nameParts['middle_name'];
                $guardian['last_name'] = $nameParts['last_name'];
                $nameFound = true;
                continue;
            }

            if (!isset($guardian['contact_number']) && preg_match('/^\d{7,}$/', $line)) {
                $guardian['contact_number'] = $line;
                $guardian['dial_code'] = '+63';
                continue;
            }

            if (!isset($guardian['address_line']) && preg_match('/[A-Za-z]/', $line) && str_contains($line, ',')) {
                $parts = $this->parseAddress($line);
                $guardian['address_line'] = $parts['address_line'];
                $guardian['city'] = $parts['city'];
                $guardian['province'] = $parts['province'];
                $guardian['country'] = $parts['country'];
            }
        }

        if (!empty($guardian['first_name']) || !empty($guardian['last_name'])) {
            $guardianKey = 'guardian_data';
            $this->fields[$guardianKey] = $guardian;
        }
    }

    protected function buildGuardians(): array
    {
        if (isset($this->fields['guardian_data'])) {
            $data = $this->fields['guardian_data'];
            if (isset($data['civil_status'])) {
                $data['civil_status'] = $this->normalizeCivilStatus($data['civil_status']);
            }
            if (isset($data['contact_number'])) {
                $data['contact_number'] = $this->normalizePhoneNumber(
                    $data['contact_number'],
                    $data['dial_code'] ?? '+63'
                );
            }
            return [new ParsedGuardian(...$data)];
        }
        return [];
    }
}
