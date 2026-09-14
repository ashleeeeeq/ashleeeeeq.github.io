<?php

use App\Services\Parsers\EducationIntakeParser;
use App\Services\Parsers\SportsIntakeParser;

test('education parser extracts beneficiary name fields from actual OCR', function () {
    $page1 = "FAIRPLAY FOR ALL FOUNDATION\n32 Bicol Street, Payatas B, Quezon City\nINTAKE SHEET\nMaaring punan ang Form na ito nang malinis at nababasa. Makatitiyak na ang mga impormasyong ilalagay\ndito ay ituturing ng lubos na pag-galang pagiging kumpidensyal.\nI. Kliyente/Benepisyaryo\nPetsa:\nPangalan: (Apelyido)\nSmith\n(Pangalan)\nLiliana\n(Gitnang Pangalan)\nHodge\n(Ext: Jr, Sr)\nPetsa ng kapanganakan:\nMay 1, 2009\nLugar ng kapanganakan:\nQuezon City\nIlan kayo magkakapatid?\n3\nKatayuang Sibil:\n☐ Single\nEdad ng nakakatandang\nkapatid:\nKasal ☐ Others, specify:\nKasalukuyang Tirahan:\n1234 Address St., Quezon City\nTelepono/mobile:\n09123456789\nEmail Address:\nPinakamataas na natamo sa\nedukasyon:\nElementarya ☑ High School\n(Kung nagaaral pa) Pangalan ng paaralan: FAIRALL High School\n(Kung nagaaral pa) Level ng grado: 10\nII. Impormasyon tungkol sa ama\nEdad:\n16\nKasarian:\nLalaki\n21\nEdad ng nakababatang\nkapatid:\n☑ Babae\n12\nliliana@example.com\nSHS Kolehiyo Post-Grad\nPangalan:\nSam Hodge\nPetsa ng kapanganakan:\nMay 1, 1975\nEdad:\n51\nLugar ng kapanganakan:\nQuezon City\nKasarian: ☑ Lalaki\nBabae\nKatayuang Sibil:\n| Single ☑ Kasal\nOthers, specify:\nKasalukuyang Tirahan:\nTelepono/mobile: 0987654321\nEmail Address:\nPinakamataas na natamo sa | Elementarya ☑High School\nedukasyon:\nTrabaho: Grab Driver\nBuhay/Pumanaw:\n☑ Buhay\nPumanaw\nsam@example.com\nSHS ☐ Kolehiyo ☐ Post-Grad\nTinatayang buwanang kita: PHP 25,000\nIII. Impormasyon tungkol sa ina\nPangalan:\nPetsa ng kapanganakan:\nLugar ng kapanagkan:\nPenny Hodge\nJune 1, 1975\nEdad: 51\nKatayuang Sibil:\nSingle\nQuezon City\nKasal\nOthers, specify:\nKasalukuyang Tirahan:\nTelepono/mobile:\nPinakamataas na natamo sa\nQuezon City\nEmail Address:\n0912348765\npenny@example.com\nElementarya\nHigh School SHS Kolehiyo Post-Grad\nedukasyon:\nTrabaho:\nCanteen cook\nBuhay/Pumanaw: ✓ Buhay\nPumanaw\nTinatayang buwanang kita: PHP\n15,000";

    $page2 = "IV. Impormasyon tungkol sa tagapag-alaga (kung naaangkop)\nPangalan:\nPetsa ng kapanganakan:\nLugar ng kapanagkan:\nEdad:\nKatayuang Sibil:\nSingle\nKasal Others, specify:\nKasalukuyang Tirahan:\nTelepono/mobile:\nEmail Address:\nPinakamataas na natamo sa\nElementarya\nHigh School\nedukasyon:\nTrabaho:\n| SHS ☐ Kolehiyo ☐ Post-Grad\nTinatayang buwanang kita: PHP\nV. Application for Fairplay Scholarship\nPlease answer the questions below if applying to become a Fairplay Scholar.\n1. Mayroon ka bang scholarship o education sponsorship mula sa ibang mga organisasyon?\n[ ] OPO/ YES\n[✓] WALA/ NO\nKung \"Opo/ Yes,\" anong organisasyon ang kasalukuyang nagbibigay sa iyo ng scholarship?\n2. Ikaw ba ay isang masipag na estudyante? Bakit mo nasabi yun?\nAko po ay isang masipag na estudyante. Nasabi ko yun dahil kahit na hindi sapat ang kinikita ng magulang\nmagulang ko para kami ay maitaguyod nang walang iniisip na problema sa pera, nagawa kong makapagtapos\nhanggang high school dahil sa tiyaga.\n3. Ano ang pangarap mo sa buhay?\nAng pangarap ko sa buhay ay maging doctor at maiahon ang pamilya ko sa hirap at mapagamot ko ang\naking mga magulang.\n4. Bakit mo gustong maging Fairplay Scholar?\nGusto ko pong maging Fairplay scholar dahil gusto ko pong ituloy ang pag aaral ko hanggang matapos\nako sa kolehiyo kaso hindi sapat ang kinikita ng aking magulang\nPangalan at Pirma ng Magulang/Tagapag-alaga:\nPetsa:\nPlease attach:\n•\nFinal grading card last school year\n•\nMost recent grading card\n•\nPhotocopy of birth certificate";

    $parser = new EducationIntakeParser();
    $result = $parser->parse($page1 . "\n" . $page2);

    // Beneficiary fields
    expect($result->last_name)->toBe('Smith');
    expect($result->first_name)->toBe('Liliana');
    expect($result->middle_name)->toBe('Hodge');
    expect($result->birth_date)->toBe('2009-05-01');
    expect($result->sex)->toBe('male');
    expect($result->contact_number)->toBe('9123456789');
    expect($result->dial_code)->toBe('+63');
    expect($result->address_line)->toBe('1234 Address St.');
    expect($result->city)->toBe('Quezon City');

    // Email should be extracted from right column
    expect($result->email)->toBe('liliana@example.com');
});

test('education parser extracts guardian data from actual OCR', function () {
    $page1 = "FAIRPLAY FOR ALL FOUNDATION\n32 Bicol Street, Payatas B, Quezon City\nINTAKE SHEET\nMaaring punan ang Form na ito nang malinis at nababasa. Makatitiyak na ang mga impormasyong ilalagay\ndito ay ituturing ng lubos na pag-galang pagiging kumpidensyal.\nI. Kliyente/Benepisyaryo\nPetsa:\nPangalan: (Apelyido)\nSmith\n(Pangalan)\nLiliana\n(Gitnang Pangalan)\nHodge\n(Ext: Jr, Sr)\nPetsa ng kapanganakan:\nMay 1, 2009\nLugar ng kapanganakan:\nQuezon City\nIlan kayo magkakapatid?\n3\nKatayuang Sibil:\n☐ Single\nEdad ng nakakatandang\nkapatid:\nKasal ☐ Others, specify:\nKasalukuyang Tirahan:\n1234 Address St., Quezon City\nTelepono/mobile:\n09123456789\nEmail Address:\nPinakamataas na natamo sa\nedukasyon:\nElementarya ☑ High School\n(Kung nagaaral pa) Pangalan ng paaralan: FAIRALL High School\n(Kung nagaaral pa) Level ng grado: 10\nII. Impormasyon tungkol sa ama\nEdad:\n16\nKasarian:\nLalaki\n21\nEdad ng nakababatang\nkapatid:\n☑ Babae\n12\nliliana@example.com\nSHS Kolehiyo Post-Grad\nPangalan:\nSam Hodge\nPetsa ng kapanganakan:\nMay 1, 1975\nEdad:\n51\nLugar ng kapanganakan:\nQuezon City\nKasarian: ☑ Lalaki\nBabae\nKatayuang Sibil:\n| Single ☑ Kasal\nOthers, specify:\nKasalukuyang Tirahan:\nTelepono/mobile: 0987654321\nEmail Address:\nPinakamataas na natamo sa | Elementarya ☑High School\nedukasyon:\nTrabaho: Grab Driver\nBuhay/Pumanaw:\n☑ Buhay\nPumanaw\nsam@example.com\nSHS ☐ Kolehiyo ☐ Post-Grad\nTinatayang buwanang kita: PHP 25,000\nIII. Impormasyon tungkol sa ina\nPangalan:\nPetsa ng kapanganakan:\nLugar ng kapanagkan:\nPenny Hodge\nJune 1, 1975\nEdad: 51\nKatayuang Sibil:\nSingle\nQuezon City\nKasal\nOthers, specify:\nKasalukuyang Tirahan:\nTelepono/mobile:\nPinakamataas na natamo sa\nQuezon City\nEmail Address:\n0912348765\npenny@example.com\nElementarya\nHigh School SHS Kolehiyo Post-Grad\nedukasyon:\nTrabaho:\nCanteen cook\nBuhay/Pumanaw: ✓ Buhay\nPumanaw\nTinatayang buwanang kita: PHP\n15,000";

    $parser = new EducationIntakeParser();
    $result = $parser->parse($page1);

    expect($result->guardians)->toHaveCount(2);

    // Father
    $father = $result->guardians[0];
    expect($father->guardian_type)->toBe('father');
    expect($father->first_name)->toBe('Sam');
    expect($father->last_name)->toBe('Hodge');
    expect($father->birth_date)->toBe('1975-05-01');
    expect($father->sex)->toBe('male');
    expect($father->civil_status)->toBe('Married');
    expect($father->contact_number)->toBe('987654321');
    expect($father->dial_code)->toBe('+63');
    expect($father->job)->toBe('Grab Driver');
    expect($father->highest_education)->toBe('high_school');

    // Mother
    $mother = $result->guardians[1];
    expect($mother->guardian_type)->toBe('mother');
    expect($mother->first_name)->toBe('Penny');
    expect($mother->last_name)->toBe('Hodge');
    expect($mother->birth_date)->toBe('1975-06-01');
    expect($mother->contact_number)->toBe('912348765');
    expect($mother->dial_code)->toBe('+63');
    expect($mother->sex)->toBe('female');
    expect($mother->job)->toBe('Canteen cook');
    expect($mother->civil_status)->toBe('Married');
});

test('education parser extracts intake and scholarship data', function () {
    $page1 = "FAIRPLAY FOR ALL FOUNDATION\n32 Bicol Street, Payatas B, Quezon City\nINTAKE SHEET\nMaaring punan ang Form na ito nang malinis at nababasa. Makatitiyak na ang mga impormasyong ilalagay\ndito ay ituturing ng lubos na pag-galang pagiging kumpidensyal.\nI. Kliyente/Benepisyaryo\nPetsa:\nPangalan: (Apelyido)\nSmith\n(Pangalan)\nLiliana\n(Gitnang Pangalan)\nHodge\n(Ext: Jr, Sr)\nPetsa ng kapanganakan:\nMay 1, 2009\nLugar ng kapanganakan:\nQuezon City\nIlan kayo magkakapatid?\n3\nKatayuang Sibil:\n☐ Single\nEdad ng nakakatandang\nkapatid:\nKasal ☐ Others, specify:\nKasalukuyang Tirahan:\n1234 Address St., Quezon City\nTelepono/mobile:\n09123456789\nEmail Address:\nPinakamataas na natamo sa\nedukasyon:\nElementarya ☑ High School\n(Kung nagaaral pa) Pangalan ng paaralan: FAIRALL High School\n(Kung nagaaral pa) Level ng grado: 10\nII. Impormasyon tungkol sa ama\nEdad:\n16\nKasarian:\nLalaki\n21\nEdad ng nakababatang\nkapatid:\n☑ Babae\n12\nliliana@example.com\nSHS Kolehiyo Post-Grad";

    $page2 = "IV. Impormasyon tungkol sa tagapag-alaga (kung naaangkop)\nPangalan:\nPetsa ng kapanganakan:\nLugar ng kapanagkan:\nEdad:\nKatayuang Sibil:\nSingle\nKasal Others, specify:\nKasalukuyang Tirahan:\nTelepono/mobile:\nEmail Address:\nPinakamataas na natamo sa\nElementarya\nHigh School\nedukasyon:\nTrabaho:\n| SHS ☐ Kolehiyo ☐ Post-Grad\nTinatayang buwanang kita: PHP\nV. Application for Fairplay Scholarship\nPlease answer the questions below if applying to become a Fairplay Scholar.\n1. Mayroon ka bang scholarship o education sponsorship mula sa ibang mga organisasyon?\n[ ] OPO/ YES\n[✓] WALA/ NO\nKung \"Opo/ Yes,\" anong organisasyon ang kasalukuyang nagbibigay sa iyo ng scholarship?\n2. Ikaw ba ay isang masipag na estudyante? Bakit mo nasabi yun?\nAko po ay isang masipag na estudyante. Nasabi ko yun dahil kahit na hindi sapat ang kinikita ng magulang\nmagulang ko para kami ay maitaguyod nang walang iniisip na problema sa pera, nagawa kong makapagtapos\nhanggang high school dahil sa tiyaga.\n3. Ano ang pangarap mo sa buhay?\nAng pangarap ko sa buhay ay maging doctor at maiahon ang pamilya ko sa hirap at mapagamot ko ang\naking mga magulang.\n4. Bakit mo gustong maging Fairplay Scholar?\nGusto ko pong maging Fairplay scholar dahil gusto ko pong ituloy ang pag aaral ko hanggang matapos\nako sa kolehiyo kaso hindi sapat ang kinikita ng aking magulang\nPangalan at Pirma ng Magulang/Tagapag-alaga:\nPetsa:\nPlease attach:\n•\nFinal grading card last school year\n•\nMost recent grading card\n•\nPhotocopy of birth certificate";

    $parser = new EducationIntakeParser();
    $result = $parser->parse($page1 . "\n" . $page2);

    // Text has no complete father/mother/guardian sections
    expect($result->guardians)->toBeEmpty();

    // Intake fields
    expect($result->intake)->not->toBeNull();
    expect($result->intake->age)->toBe(16);
    expect($result->intake->number_of_siblings)->toBe(3);
    expect($result->intake->older_sibling_age)->toBe(21);
    expect($result->intake->younger_sibling_age)->toBe(12);
    expect($result->intake->school_name)->toBe('FAIRALL High School');
    expect($result->intake->grade_level)->toBe('10');

    // Scholarship
    expect($result->intake->other_scholarship)->toBeFalse();
    expect($result->intake->hardworking_question)->toContain('masipag na estudyante');
    expect($result->intake->dream_question)->toContain('pangarap ko');
    expect($result->intake->scholarship_question)->toContain('Fairplay scholar');
});

test('education parser infers highest_education from grade_level when no checkbox detected', function () {
    $text = "FAIRPLAY FOR ALL FOUNDATION\n32 Bicol Street, Payatas B, Quezon City\nINTAKE SHEET\nI. Kliyente/Benepisyaryo\nPetsa:\nPangalan: (Apelyido)\nDoe\n(Pangalan)\nJohn\n(Gitnang Pangalan)\nPetsa ng kapanganakan:\nJan 1, 2010\nLugar ng kapanganakan:\nQuezon City\nKasalukuyang Tirahan:\n123 St., Quezon City\nTelepono/mobile:\n09123456789\n(Kung nagaaral pa) Pangalan ng paaralan: Test School\n(Kung nagaaral pa) Level ng grado: 10";

    $parser = new EducationIntakeParser();
    $result = $parser->parse($text);

    expect($result->intake)->not->toBeNull();
    expect($result->intake->grade_level)->toBe('10');
    expect($result->intake->highest_education)->toBe('elementary');
});

test('education parser infers shs for 1st year grade level', function () {
    $text = "FAIRPLAY FOR ALL FOUNDATION\nINTAKE SHEET\nI. Kliyente/Benepisyaryo\nPetsa:\nPangalan: (Apelyido)\nDoe\n(Pangalan)\nJohn\n(Gitnang Pangalan)\nPetsa ng kapanganakan:\nJan 1, 2010\nKasalukuyang Tirahan:\n123 St.\nTelepono/mobile:\n09123456789\n(Kung nagaaral pa) Pangalan ng paaralan: College\n(Kung nagaaral pa) Level ng grado: 1st Year";

    $parser = new EducationIntakeParser();
    $result = $parser->parse($text);

    expect($result->intake)->not->toBeNull();
    expect($result->intake->grade_level)->toBe('1st Year');
    expect($result->intake->highest_education)->toBe('shs');
});

test('education parser infers high_school for grade 12', function () {
    $text = "FAIRPLAY FOR ALL FOUNDATION\nINTAKE SHEET\nI. Kliyente/Benepisyaryo\nPetsa:\nPangalan: (Apelyido)\nDoe\n(Pangalan)\nJohn\n(Gitnang Pangalan)\nPetsa ng kapanganakan:\nJan 1, 2010\nKasalukuyang Tirahan:\n123 St.\nTelepono/mobile:\n09123456789\n(Kung nagaaral pa) Pangalan ng paaralan: SHS\n(Kung nagaaral pa) Level ng grado: 12";

    $parser = new EducationIntakeParser();
    $result = $parser->parse($text);

    expect($result->intake)->not->toBeNull();
    expect($result->intake->grade_level)->toBe('12');
    expect($result->intake->highest_education)->toBe('high_school');
});

test('education parser infers pre-school for grade 1', function () {
    $text = "FAIRPLAY FOR ALL FOUNDATION\nINTAKE SHEET\nI. Kliyente/Benepisyaryo\nPetsa:\nPangalan: (Apelyido)\nDoe\n(Pangalan)\nJohn\n(Gitnang Pangalan)\nPetsa ng kapanganakan:\nJan 1, 2010\nKasalukuyang Tirahan:\n123 St.\nTelepono/mobile:\n09123456789\n(Kung nagaaral pa) Pangalan ng paaralan: Elem\n(Kung nagaaral pa) Level ng grado: 1";

    $parser = new EducationIntakeParser();
    $result = $parser->parse($text);

    expect($result->intake)->not->toBeNull();
    expect($result->intake->grade_level)->toBe('1');
    expect($result->intake->highest_education)->toBe('pre-school');
});

test('sports parser extracts player and guardian info from actual OCR', function () {
    $text = "FAIRPLAY\nPAYATAS FC\nPLAYER REGISTRATION FORM 2025\nPAYAYAS\nPayatas FC is a football/futsal club organized by the Fairplay for All Foundation Inc. (Fairplay) in\nPayatas, Quezon City. Fairplay is committed to ensuring the safety, development, and well-being of\nall registered players while promoting the values of fun, care, and improvement.\nPLAYER INFORMATION\nFull Name:\nDate of Birth:\nClass Schedule (AM/PM):\nContact No.:\nAddress:\nIN CASE OF EMERGENCIES\nDewey Peterson\nJan. 1, 2008\n09170000000\nPayatas, Quezon City\nAM\nPlease contact (name of parent/legal guardian)\nContact No.:\nAddress:\nFAIRPLAY'S RESPONSIBILITIES\n1.\n3.\nZachery Peterson\n09000000000\nPayatas, Quezon City\nProvide free training sessions for registered players.\n2. Ensure coaching is aligned with our core values: learn, care, and improve, as well as have fun.\nCover essential needs for players participating in football events, including transportation, food,\nand equipment.\n4.\nProvide uniforms, futsal shoes, and playing equipment periodically, as resources allow.\n5. Maintain the Payatas Sports Center.\nPLAYER'S RESPONSIBILITIES\n1.\n2.\nCommitment & Respect: Players must give their best effort, respect teammates and coaches,\nand uphold Fairplay's values.\nAttendance & Selection: Selection for tournaments, leagues, and football events is based on\ntraining attendance and effort.\n3. Training Contribution: Players may be required to engage in non-monetary contributions such\nas fitness activities based on their age group and general cleaning of the Payatas Sports Center.\nTeam Transfers & Agreements:\n4.\na. Players must consult Fairplay before joining another team.\nb. Any transfer must have an agreement between Fairplay and the new team to ensure the\nplayer's safety and development.\nCONSENT AND WAIVER OF LIABILITY\nAssumption of Risk & Waiver of Liability\nBy signing this form, I acknowledge and accept that:\n1. Injuries are inherent in contact sports such as football/futsal, including but not limited to\nsprains, fractures, and concussions.";

    $parser = new SportsIntakeParser();
    $result = $parser->parse($text);

    // Player fields
    expect($result->first_name)->toBe('Dewey');
    expect($result->last_name)->toBe('Peterson');
    expect($result->birth_date)->toBe('2008-01-01');
    expect($result->contact_number)->toBe('9170000000');
    expect($result->dial_code)->toBe('+63');
    expect($result->address_line)->toBe('Payatas');
    expect($result->city)->toBe('Quezon City');

    // Guardian
    expect($result->guardians)->toHaveCount(1);
    $guardian = $result->guardians[0];
    expect($guardian->guardian_type)->toBe('guardian');
    expect($guardian->first_name)->toBe('Zachery');
    expect($guardian->last_name)->toBe('Peterson');
    expect($guardian->contact_number)->toBe('9000000000');
    expect($guardian->dial_code)->toBe('+63');
});

test('parsers handle empty text gracefully', function () {
    $eduParser = new EducationIntakeParser();
    $result = $eduParser->parse('');

    expect($result->first_name)->toBeNull();
    expect($result->last_name)->toBeNull();
    expect($result->guardians)->toBeEmpty();
    expect($result->intake)->toBeNull();

    $sportsParser = new SportsIntakeParser();
    $result = $sportsParser->parse('');

    expect($result->first_name)->toBeNull();
    expect($result->guardians)->toBeEmpty();
});
