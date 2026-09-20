<?php

namespace App\Services\Parsers;

use App\DTOs\ParsedDocument;
use App\DTOs\ParsedGuardian;
use App\DTOs\ParsedIntake;

class EducationIntakeParser extends AbstractIntakeSheetParser
{
    private array $allLabelPatterns = [
        '/^\(Apelyido\)/i', '/^\(Pangalan\)/i', '/^\(Gitnang Pangalan\)/i',
        '/^\(Ext:\s*Jr,\s*Sr\)/i', '/^Petsa ng kapanganakan:/i',
        '/^Lugar ng kapanganakan:/i', '/^Ilan kayo magkakapatid/i',
        '/^Katayuang Sibil:/i', '/^Kasalukuyang Tirahan:/i',
        '/^Telepono\/mobile:/i', '/^Email Address:/i',
        '/^Pinakamataas na natamo/i', '/^\(Kung nagaaral pa\)/i',
        '/^Pangalan:/i', '/^Edad:/i', '/^Kasarian:/i',
    ];

    public function parse(string $rawText): ParsedDocument
    {
        $this->reset();
        $text = $this->normalizeNewlines($rawText);

        $this->extractLeftColumnFields($text);
        $this->extractRightColumnFields($text);
        $this->extractFatherSection($text);
        $this->extractMotherSection($text);
        $this->extractGuardianSection($text);
        $this->extractScholarshipSection($text);

        return $this->assembleDocument();
    }

    private function normalizeNewlines(string $text): string
    {
        return str_replace(["\r\n", "\r"], "\n", $text);
    }

    protected function normalizeLines(string $rawText): array
    {
        return [];
    }

    protected function processLines(array $lines): void
    {
    }

    protected function getProgramType(): string
    {
        return 'education';
    }

    private function looksLikeLabel(string $value): bool
    {
        foreach ($this->allLabelPatterns as $pat) {
            if (preg_match($pat, $value)) {
                return true;
            }
        }
        return false;
    }

    private function extractValueAfterLabel(string $labelPattern, string $text): ?string
    {
        $cleanPat = trim($labelPattern, '/i ');
        $cleanPat = rtrim($cleanPat, '/');

        if (preg_match('/' . $cleanPat . '\s*\n(.+)/i', $text, $m)) {
            $value = trim($m[1]);
            if ($value !== '' && !$this->looksLikeLabel($value)) {
                return $value;
            }
        }
        return null;
    }

    private function extractLeftColumnFields(string $text): void
    {
        $map = [
            '/\(Apelyido\)/i'           => 'last_name',
            '/\(Pangalan\)/i'           => 'first_name',
            '/\(Gitnang Pangalan\)/i'    => 'middle_name',
            '/\(Ext:\s*Jr,\s*Sr\)/i'     => 'name_extension',
            '/Petsa ng kapanganakan:/i'  => 'birth_date',
            '/Lugar ng kapanganakan:/i'  => 'place_of_birth',
            '/Ilan kayo magkakapatid\??/i' => 'number_of_siblings',
            '/Kasalukuyang Tirahan:/i'   => 'address_line',
            '/Telepono\/mobile:/i'       => 'contact_number',
        ];

        foreach ($map as $pattern => $field) {
            $value = $this->extractValueAfterLabel($pattern, $text);
            if ($value !== null) {
                $this->fields[$field] = $value;
            }
        }

        if (preg_match('/Pangalan ng paaralan:\s*(.+)/i', $text, $m)) {
            $v = trim($m[1]);
            if ($v !== '') {
                $this->fields['school_name'] = $v;
            }
        }

        if (preg_match('/Level ng grado:\s*(.+)/i', $text, $m)) {
            $v = trim($m[1]);
            if ($v !== '') {
                $this->fields['grade_level'] = $v;
            }
        }
    }

    private function extractRightColumnFields(string $text): void
    {
        $rcLines = $this->getSectionText($text, 'II\.\s*Impormasyon\s+tungkol\s+sa\s+ama', 'III\.');
        if ($rcLines === null) {
            return;
        }

        $rcLines = $this->linesUpToPangalan($rcLines);
        if ($rcLines === null) {
            return;
        }

        preg_match('/Edad:\s*\n\s*(\d+)/i', $rcLines, $m);
        if (!empty($m[1])) {
            $age = (int) $m[1];
            if ($age > 0 && $age < 120) {
                $this->fields['age'] = $age;
            }
        }

        preg_match('/Kasarian:\s*\n\s*(Lalaki|Babae)/i', $rcLines, $m);
        if (!empty($m[1])) {
            $this->fields['sex'] = $m[1];
        }

        preg_match('/(\d+)\s*\n\s*Edad ng nakababatang/i', $rcLines, $m);
        if (!empty($m[1])) {
            $this->fields['older_sibling_age'] = (int) $m[1];
        }

        preg_match('/Edad ng nakababatang\s*\n\s*kapatid:[\s\S]*?(\d+)/i', $rcLines, $m);
        if (!empty($m[1])) {
            $this->fields['younger_sibling_age'] = (int) $m[1];
        }

        preg_match('/\b([A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,})\b/', $rcLines, $m);
        if (!empty($m[1]) && empty($this->fields['email'])) {
            $this->fields['email'] = $m[1];
        }

        $this->fields['highest_education'] = $this->findHighestEducation($rcLines);
    }

    private function getSectionText(string $text, string $rawHeaderPattern, string $nextHeaderPattern): ?string
    {
        if (!preg_match('/' . $rawHeaderPattern . '(.+?)(?=\n' . $nextHeaderPattern . '|\z)/si', $text, $m)) {
            return null;
        }
        return $m[1];
    }

    private function linesUpToPangalan(string $text): ?string
    {
        if (preg_match('/^(.+?)(?=\nPangalan:\s*\n)/si', $text, $m)) {
            return $m[1];
        }
        return null;
    }

    private function extractFatherSection(string $text): void
    {
        $section = $this->getSectionText($text, 'II\.\s*Impormasyon\s+tungkol\s+sa\s+ama', 'III\.');
        if ($section === null) {
            return;
        }

        $parts = explode("\n", $section);
        $nameLine = null;
        $foundPangalan = false;
        $afterPangalanValues = [];

        $collectAfterPangalan = false;
        foreach ($parts as $line) {
            $trimmed = trim($line);
            if (preg_match('/^Pangalan:\s*$/i', $trimmed)) {
                $collectAfterPangalan = true;
                continue;
            }
            if ($collectAfterPangalan) {
                if (!$this->looksLikeLabel($trimmed) && $trimmed !== '') {
                    if ($nameLine === null && preg_match('/^[A-Z][a-z]+(?:\s+[A-Z][a-z]+)+$/', $trimmed)
                        && !preg_match('/School|Elementarya|SHS|Kolehiyo|Post|Grad|Single|Kasal|Quezon|City|Buhay|Pumanaw|Others|specify/i', $trimmed)) {
                        $nameLine = $trimmed;
                    } else {
                        $afterPangalanValues[] = $trimmed;
                    }
                }
            }
        }

        if ($nameLine === null) {
            return;
        }

        $sectionText = implode("\n", array_merge([$nameLine], $afterPangalanValues));

        $nameParts = $this->splitFullName($nameLine);
        $guardian = [
            'guardian_type' => 'father',
            'first_name' => $nameParts['first_name'],
            'middle_name' => $nameParts['middle_name'],
            'last_name' => $nameParts['last_name'],
            'birth_date' => $this->normalizeDate($this->extractValueAfterLabel('/Petsa ng kapanganakan:/i', $section)),
            'place_of_birth' => $this->extractValueAfterLabel('/Lugar ng kapanganakan:/i', $section),
            'contact_number' => ltrim($this->findPhoneInText($section) ?? '', '0+63'),
            'dial_code' => '+63',
            'job' => $this->extractValueAfterLabel('/Trabaho:\s*(.+)/i', $section),
        ];

        $guardian['sex'] = $this->findCheckboxValue($section, ['Lalaki', 'Babae'], 'Kasarian');
        $guardian['civil_status'] = $this->findCheckboxValue($section, ['Single', 'Kasal', 'Married'], 'Katayuang Sibil');
        $guardian['estimated_salary'] = $this->findSalaryInText($sectionText);
        $guardian['highest_education'] = $this->findEducationLevels($section);

        $this->fields['father_data'] = $guardian;
    }

    private function extractMotherSection(string $text): void
    {
        $section = $this->getSectionText($text, 'III\.\s*Impormasyon\s+tungkol\s+sa\s+ina', 'IV\.');
        if ($section === null) {
            return;
        }

        $lines = explode("\n", $section);
        $valueLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;
            if ($this->looksLikeLabel($trimmed)) continue;
            $valueLines[] = $trimmed;
        }

        $nameLine = null;
        foreach ($valueLines as $vl) {
            if ($nameLine === null && preg_match('/^[A-Z][a-z]+\s+[A-Z][a-z]+$/', $vl)
                && !preg_match('/School|Elementarya|SHS|Kolehiyo|Post|Grad|Single|Kasal|Quezon|City|Buhay|Pumanaw|Others|specify/i', $vl)) {
                $nameLine = $vl;
                break;
            }
        }

        if ($nameLine === null) {
            return;
        }

        $valuesText = implode("\n", $valueLines);
        $nameParts = $this->splitFullName($nameLine);

        $guardian = [
            'guardian_type' => 'mother',
            'first_name' => $nameParts['first_name'],
            'middle_name' => $nameParts['middle_name'],
            'last_name' => $nameParts['last_name'],
            'birth_date' => $this->normalizeDate($this->findDateInText($valuesText)),
            'place_of_birth' => $this->findPobInValues($valueLines),
            'contact_number' => ltrim($this->findPhoneInText($valuesText) ?? '', '0+63'),
            'dial_code' => '+63',
            'job' => $this->findJobInSection($section),
        ];

        $guardian['sex'] = $this->findCheckboxValue($section, ['Lalaki', 'Babae'], 'Kasarian');
        $guardian['civil_status'] = $this->findCheckboxValue($section, ['Single', 'Kasal', 'Married'], 'Katayuang Sibil');
        $guardian['estimated_salary'] = $this->findSalaryInText($valuesText);
        $guardian['highest_education'] = $this->findEducationLevels($section);
        // if ($guardian['highest_education'] === null) {
        //     $guardian['highest_education'] = $this->findMotherEducationFallback($section);
        // }

        $this->fields['mother_data'] = $guardian;
    }

    private function extractGuardianSection(string $text): void
    {
        $section = $this->getSectionText($text, 'IV\.\s*Impormasyon\s+tungkol\s+sa\s+tagapag.alaga', 'V\.');
        if ($section === null) {
            return;
        }

        $section = preg_replace('/IV\.\s*Impormasyon\s+tungkol\s+sa\s+tagapag\.alaga\s*\(kung naaangkop\)/i', '', $section);

        $lines = explode("\n", $section);
        $nameLine = null;
        $valueLines = [];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;
            if ($this->looksLikeLabel($trimmed)) continue;
            $valueLines[] = $trimmed;
        }

        foreach ($valueLines as $vl) {
            if ($nameLine === null && preg_match('/^[A-Z][a-z]+\s+[A-Z][a-z]+$/', $vl)
                && !preg_match('/School|Elementarya|SHS|Kolehiyo|Post|Grad|Single|Kasal|Quezon|City|Buhay|Pumanaw|Others|specify/i', $vl)) {
                $nameLine = $vl;
                break;
            }
        }

        if ($nameLine === null) {
            return;
        }

        $valuesText = implode("\n", $valueLines);
        $nameParts = $this->splitFullName($nameLine);
        $guardian = [
            'guardian_type' => 'guardian',
            'first_name' => $nameParts['first_name'],
            'middle_name' => $nameParts['middle_name'],
            'last_name' => $nameParts['last_name'],
            'birth_date' => $this->normalizeDate($this->findDateInText($valuesText)),
            'contact_number' => ltrim($this->findPhoneInText($valuesText) ?? '', '0+63'),
            'dial_code' => '+63',
            'job' => $this->findJobInSection($section),
        ];

        $this->fields['guardian_data'] = $guardian;
    }

    private function extractScholarshipSection(string $text): void
    {
        if (!preg_match('/V\.\s*Application for Fairplay Scholarship(.+)/si', $text, $m)) {
            return;
        }

        $section = $m[1];

        if (preg_match('/\[✓\]\s*WALA|WALA.*\[✓\]/i', $section)) {
            $this->fields['other_scholarship'] = false;
        } elseif (preg_match('/\[✓\]\s*OPO|OPO.*\[✓\]/i', $section)) {
            $this->fields['other_scholarship'] = true;
        }

        $flatten = fn($v) => preg_replace('/\s+/', ' ', trim($v));

        preg_match('/Kung.*Yes.*\n(.+?)(?=\n\d+\.|\z)/si', $section, $m);
        if (!empty($m[1])) {
            $v = $flatten($m[1]);
            if ($v !== '' && !preg_match('/^[A-Z][a-z]/', $v)) {
                $this->fields['scholarship_org_question'] = $v;
            }
        }

        preg_match('/2\.\s*Ikaw ba.*?\n(.+?)(?=\n3\.|\z)/si', $section, $m);
        if (!empty($m[1])) {
            $v = $flatten($m[1]);
            if ($v !== '') {
                $this->fields['hardworking_question'] = $v;
            }
        }

        preg_match('/3\.\s*Ano ang.*?\n(.+?)(?=\n4\.|\z)/si', $section, $m);
        if (!empty($m[1])) {
            $v = $flatten($m[1]);
            if ($v !== '') {
                $this->fields['dream_question'] = $v;
            }
        }

        preg_match('/4\.\s*Bakit mo.*?\n(.+?)(?=\nPangalan at Pirma|\z)/si', $section, $m);
        if (!empty($m[1])) {
            $v = $flatten($m[1]);
            if ($v !== '') {
                $this->fields['scholarship_question'] = $v;
            }
        }
    }

    private function findPhoneInText(string $text): ?string
    {
        if (preg_match('/\b(\d{11})\b/', $text, $m)) return $m[1];
        if (preg_match('/\b(\d{7,})\b/', $text, $m)) return $m[1];
        if (preg_match('/Telepono\/mobile:\s*(.+)/i', $text, $m)) return trim($m[1]);
        return null;
    }

    private function findDateInText(string $text): ?string
    {
        if (preg_match('/[A-Z][a-z]+\.?\s+\d{1,2},?\s+\d{4}/', $text, $m)) {
            return $m[0];
        }
        return null;
    }

    private function findPobInValues(array $valueLines): ?string
    {
        foreach ($valueLines as $vl) {
            if (preg_match('/^Quezon City|^Manila|^[A-Z][a-z]+\s+(City|Town)/i', $vl)) {
                return $vl;
            }
        }
        return null;
    }

    private function findJobInSection(string $section): ?string
    {
        if (preg_match('/Trabaho:\s*(.+)/i', $section, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function findSalaryInText(string $text): ?int
    {
        if (preg_match('/PHP\s*([\d,]+)/i', $text, $m)) {
            return (int) str_replace(',', '', $m[1]);
        }
        return null;
    }

    private function findCheckboxValue(string $text, array $options, string $context): ?string
    {
        $marksClass = '[☑✓✔✅]';

        foreach ($options as $opt) {
            if (preg_match('/' . $context . '.*' . $marksClass . '\s*' . $opt . '/iu', $text)) {
                return $opt;
            }
            if (preg_match('/' . $context . '.*\n.*' . $marksClass . '\s*' . $opt . '/iu', $text)) {
                return $opt;
            }
        }

        foreach ($options as $opt) {
            if (preg_match('/' . $context . '.*' . $opt . '.*' . $marksClass . '/iu', $text)) {
                return $opt;
            }
        }

        $contextPos = mb_stripos($text, $context);
        if ($contextPos !== false) {
            $afterLabel = substr($text, $contextPos + strlen($context));
            $lines = explode("\n", $afterLabel);
            $lastMatch = null;
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || $this->looksLikeLabel($line)) continue;
                foreach ($options as $opt) {
                    if (strcasecmp($line, $opt) === 0) {
                        $lastMatch = $opt;
                    }
                }
            }
            if ($lastMatch !== null) {
                return $lastMatch;
            }
        }

        return null;
    }

    private function findEducationLevels(string $text): ?string
    {
        return $this->findHighestEducation($text);
    }

    private function findHighestEducation(string $text): ?string
    {
        $marks = '[☑✓✔✅]';
        $sp = '\h*';
        $flags = 'iu';

        $levels = [
            'post-grad'  => preg_match('/Post\.?Grad' . $sp . $marks . '|' . $marks . $sp . 'Post\.?Grad/' . $flags, $text),
            'college'    => preg_match('/Kolehiyo' . $sp . $marks . '|' . $marks . $sp . 'Kolehiyo/' . $flags, $text),
            'shs'        => preg_match('/SHS' . $sp . $marks . '|' . $marks . $sp . 'SHS/' . $flags, $text),
            'high_school' => preg_match('/High' . $sp . 'School' . $sp . $marks . '|' . $marks . $sp . 'High' . $sp . 'School/' . $flags, $text),
            'elementary' => preg_match('/Elementarya' . $sp . $marks . '|' . $marks . $sp . 'Elementarya/' . $flags, $text),
            'pre-school' => preg_match('/Pre.?School' . $sp . $marks . '|' . $marks . $sp . 'Pre.?School/' . $flags, $text),
        ];

        foreach ($levels as $key => $matched) {
            if ($matched) {
                return $key;
            }
        }

        return null;
    }

    // private function findMotherEducationFallback(string $text): ?string
    // {
    //     if (preg_match('/Post\s*[-–—]?\s*Grad/i', $text)) return 'post-grad';
    //     if (preg_match('/Kolehiyo/i', $text)) return 'college';
    //     if (preg_match('/\bSHS\b/i', $text)) return 'shs';
    //     if (preg_match('/High\s*School/i', $text)) return 'high_school';
    //     if (preg_match('/Elementarya/i', $text)) return 'elementary';
    //     if (preg_match('/Pre\s*[-–—]?\s*School/i', $text)) return 'pre-school';
    //     return null;
    // }

    protected function buildGuardians(): array
    {
        $guardians = [];
        foreach (['father', 'mother', 'guardian'] as $type) {
            $key = $type . '_data';
            if (isset($this->fields[$key]) && is_array($this->fields[$key])) {
                $data = $this->fields[$key];
                if (isset($data['sex'])) {
                    $data['sex'] = $this->normalizeSex($data['sex']);
                }
                if (!isset($data['sex']) || $data['sex'] === null) {
                    $data['sex'] = match ($data['guardian_type'] ?? null) {
                        'father' => 'male',
                        'mother' => 'female',
                        default => null,
                    };
                }
                if (isset($data['civil_status'])) {
                    $data['civil_status'] = $this->normalizeCivilStatus($data['civil_status']);
                }
                if (isset($data['contact_number'])) {
                    $data['contact_number'] = $this->normalizePhoneNumber(
                        $data['contact_number'],
                        $data['dial_code'] ?? '+63'
                    );
                }
                $guardians[] = new ParsedGuardian(...$data);
            }
        }
        return $guardians;
    }

    protected function buildIntake(): ?ParsedIntake
    {
        $intakeFields = [
            'age', 'place_of_birth', 'number_of_siblings',
            'older_sibling_age', 'younger_sibling_age',
            'civil_status', 'highest_education', 'school_name', 'grade_level',
            'other_scholarship', 'scholarship_org_question',
            'hardworking_question', 'dream_question', 'scholarship_question',
        ];

        $data = [];
        foreach ($intakeFields as $field) {
            if (array_key_exists($field, $this->fields)) {
                $data[$field] = $this->fields[$field];
            }
        }

        if (empty($data)) return null;

        if ((!isset($data['highest_education']) || $data['highest_education'] === null) && isset($data['grade_level'])) {
            $data['highest_education'] = $this->educationLevelForGradeLevel($data['grade_level']);
        }

        return new ParsedIntake(...$data);
    }

    private function educationLevelForGradeLevel(string $gradeLevel): ?string
    {
        $gl = trim(strtolower($gradeLevel));
        return match (true) {
            in_array($gl, ['1st year', '2nd year', '3rd year', '4th year', '5th year']) => 'shs',
            in_array($gl, ['11', '12']) => 'high_school',
            in_array($gl, ['7', '8', '9', '10']) => 'elementary',
            in_array($gl, ['1', '2', '3', '4', '5', '6']) => 'pre-school',
            default => null,
        };
    }
}
