<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\TestLevel;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class QuestionImportService
{
    /**
     * Generate and download a sample template (Native XLSX by default, or CSV) for importing questions.
     */
    public function downloadSampleTemplate(string $format = 'xlsx'): mixed
    {
        if (strtolower($format) === 'csv') {
            return $this->downloadCsvSampleTemplate();
        }

        return $this->downloadXlsxSampleTemplate();
    }

    /**
     * Download native XLSX template with formatting and Korean text in UTF-8.
     * Microsoft Excel opens .xlsx with native UTF-8 support without any character corruption.
     */
    public function downloadXlsxSampleTemplate(): BinaryFileResponse
    {
        $filename = 'sample_questions_import_' . date('Y_m_d') . '.xlsx';
        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sample_questions_' . uniqid() . '.xlsx';

        $zip = new ZipArchive();
        if ($zip->open($tempPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            // Fallback to CSV if zip cannot be created
            return $this->downloadCsvSampleTemplate();
        }

        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
</Types>';

        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';

        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>';

        $workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Questions" sheetId="1" r:id="rId1"/>
  </sheets>
</workbook>';

        $styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="2">
    <font><name val="Calibri"/><sz val="11"/></font>
    <font><b/><name val="Calibri"/><sz val="11"/><color rgb="FFFFFFFF"/></font>
  </fonts>
  <fills count="3">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/></patternFill></fill>
  </fills>
  <borders count="1">
    <border><left/><right/><top/><bottom/></border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="2">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="0" applyFont="1" applyFill="1"/>
  </cellXfs>
</styleSheet>';

        $headers = [
            'Test Level (ID or Name)',
            'Question Text',
            'Question Type',
            'Marks',
            'Option A',
            'Option B',
            'Option C',
            'Option D',
            'Correct Option',
            'Explanation',
            'Status'
        ];

        // Fetch first level ID or fallback to 1
        $sampleLevelId = (string)(TestLevel::query()->first()?->id ?? '1');

        $data = [
            [
                $sampleLevelId,
                "한국어 인사말 '안녕하세요'의 올바른 뜻은 무엇입니까?",
                'mcq',
                '1',
                'Hello / Hi',
                'Goodbye',
                'Thank you',
                'Sorry',
                'A',
                '가장 기본적인 한국어 일상 인사말입니다.',
                'active'
            ],
            [
                $sampleLevelId,
                '대한민국의 수도(Capital City)는 어디입니까?',
                'mcq',
                '1',
                '부산 (Busan)',
                '서울 (Seoul)',
                '인천 (Incheon)',
                '대구 (Daegu)',
                'B',
                '대한민국의 수도는 서울특별시입니다.',
                'active'
            ],
            [
                $sampleLevelId,
                '다음 중 과일(Fruit)에 해당하는 단어는 무엇입니까?',
                'mcq',
                '1',
                '사과 (Apple)',
                '책상 (Desk)',
                '자동차 (Car)',
                '의자 (Chair)',
                'A',
                '사과는 과일입니다.',
                'active'
            ],
            [
                $sampleLevelId,
                '한국어로 \'감사합니다\'는 무슨 뜻입니까?',
                'mcq',
                '1',
                'Thank you',
                'Excuse me',
                'Please',
                'Welcome',
                'A',
                '감사의 마음을 표현하는 대표적인 인사말입니다.',
                'active'
            ]
        ];

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $sheetXml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";
        $sheetXml .= '  <cols>' . "\n";
        $sheetXml .= '    <col min="1" max="1" width="18" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="2" max="2" width="50" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="3" max="3" width="16" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="4" max="4" width="10" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="5" max="5" width="22" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="6" max="6" width="22" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="7" max="7" width="22" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="8" max="8" width="22" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="9" max="9" width="16" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="10" max="10" width="40" customWidth="1"/>' . "\n";
        $sheetXml .= '    <col min="11" max="11" width="14" customWidth="1"/>' . "\n";
        $sheetXml .= '  </cols>' . "\n";
        $sheetXml .= '  <sheetData>' . "\n";

        // Header row with dark fill
        $sheetXml .= '    <row r="1">' . "\n";
        foreach ($headers as $colIdx => $header) {
            $colLetter = $this->indexToColRef($colIdx);
            $escaped = htmlspecialchars($header, ENT_XML1, 'UTF-8');
            $sheetXml .= '      <c r="' . $colLetter . '1" t="inlineStr" s="1"><is><t>' . $escaped . '</t></is></c>' . "\n";
        }
        $sheetXml .= '    </row>' . "\n";

        // Data rows
        foreach ($data as $rowIdx => $row) {
            $rNum = $rowIdx + 2;
            $sheetXml .= '    <row r="' . $rNum . '">' . "\n";
            foreach ($row as $colIdx => $val) {
                $colLetter = $this->indexToColRef($colIdx);
                $escaped = htmlspecialchars((string)$val, ENT_XML1, 'UTF-8');
                $sheetXml .= '      <c r="' . $colLetter . $rNum . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>' . "\n";
            }
            $sheetXml .= '    </row>' . "\n";
        }

        $sheetXml .= '  </sheetData>' . "\n";
        $sheetXml .= '</worksheet>';

        $zip->addFromString('[Content_Types].xml', $contentTypes);
        $zip->addFromString('_rels/.rels', $rels);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);
        $zip->addFromString('xl/workbook.xml', $workbook);
        $zip->addFromString('xl/styles.xml', $styles);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->close();

        return response()->download($tempPath, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Download sample CSV template fallback.
     */
    public function downloadCsvSampleTemplate(): StreamedResponse
    {
        $filename = 'sample_questions_import_' . date('Y_m_d') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fwrite($file, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($file, [
                'Test Level',
                'Question Text',
                'Question Type',
                'Marks',
                'Option A',
                'Option B',
                'Option C',
                'Option D',
                'Correct Option',
                'Explanation',
                'Status',
            ]);

            $sampleLevelName = TestLevel::query()->first()?->name ?? 'EPS-TOPIK 1';

            fputcsv($file, [
                $sampleLevelName,
                '한국어 인사말 \'안녕하세요\'의 올바른 뜻은 무엇입니까?',
                'mcq',
                '1',
                'Hello / Hi',
                'Goodbye',
                'Thank you',
                'Sorry',
                'A',
                '가장 기본적인 한국어 일상 인사말입니다.',
                'active',
            ]);

            fputcsv($file, [
                $sampleLevelName,
                '대한민국의 수도(Capital City)는 어디입니까?',
                'mcq',
                '1',
                '부산 (Busan)',
                '서울 (Seoul)',
                '인천 (Incheon)',
                '대구 (Daegu)',
                'B',
                '대한민국의 수도는 서울특별시입니다.',
                'active',
            ]);

            fputcsv($file, [
                $sampleLevelName,
                '다음 중 과일(Fruit)에 해당하는 단어는 무엇입니까?',
                'mcq',
                '1',
                '사과 (Apple)',
                '책상 (Desk)',
                '자동차 (Car)',
                '의자 (Chair)',
                'A',
                '사과는 과일입니다.',
                'active',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import questions from uploaded CSV, XLS or XLSX file.
     */
    public function import(UploadedFile $file, ?int $defaultLevelId = null): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if ($extension === 'xlsx') {
            $rows = $this->parseXlsx($path);
        } elseif ($extension === 'xls') {
            $rows = $this->parseXls($path);
        } else {
            $rows = $this->parseCsv($path);
        }

        if (empty($rows)) {
            return [
                'success'  => false,
                'imported' => 0,
                'skipped'  => 0,
                'errors'   => ['The uploaded file is empty or could not be parsed.'],
            ];
        }

        // Header mapping
        $headerRow = array_shift($rows);
        $headerMap = $this->mapHeaders($headerRow);

        if (!isset($headerMap['question_text'])) {
            return [
                'success'  => false,
                'imported' => 0,
                'skipped'  => 0,
                'errors'   => ['Required column "Question Text" was not found in the file. Please ensure column headers match the sample template.'],
            ];
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];
        $rowNum = 1; // 1 is header

        // Cache all levels for fast lookup
        $levels = TestLevel::all();

        foreach ($rows as $row) {
            $rowNum++;

            // Skip empty rows
            if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                continue;
            }

            $questionText = trim($this->getVal($row, $headerMap, 'question_text') ?? '');
            if ($questionText === '') {
                $skipped++;
                $errors[] = "Row {$rowNum}: Skipped because Question Text is empty.";
                continue;
            }

            // Resolve Test Level ID
            $levelVal = trim($this->getVal($row, $headerMap, 'test_level') ?? '');
            $levelId = null;

            if ($levelVal !== '') {
                if (is_numeric($levelVal)) {
                    $found = $levels->firstWhere('id', (int)$levelVal);
                    if ($found) {
                        $levelId = $found->id;
                    }
                }
                if (!$levelId) {
                    $found = $levels->first(function ($l) use ($levelVal) {
                        return strcasecmp($l->name, $levelVal) === 0 || stripos($l->name, $levelVal) !== false;
                    });
                    if ($found) {
                        $levelId = $found->id;
                    }
                }
            }

            if (!$levelId) {
                $levelId = $defaultLevelId ?: $levels->first()?->id;
            }

            if (!$levelId) {
                $skipped++;
                $errors[] = "Row {$rowNum}: No valid Test Level found. Please select a Default Level or specify a valid level name in the file.";
                continue;
            }

            // Extract question attributes
            $questionType = strtolower(trim($this->getVal($row, $headerMap, 'question_type') ?? 'mcq'));
            if (!in_array($questionType, ['mcq', 'true_false', 'image_based', 'audio_based'])) {
                $questionType = 'mcq';
            }

            $marks = (int)($this->getVal($row, $headerMap, 'marks') ?? 1);
            if ($marks < 1) {
                $marks = 1;
            }

            $explanation = trim($this->getVal($row, $headerMap, 'explanation') ?? '');
            $status = strtolower(trim($this->getVal($row, $headerMap, 'status') ?? 'active'));
            if (!in_array($status, ['active', 'inactive'])) {
                $status = 'active';
            }

            // Extract Options
            $optA = trim($this->getVal($row, $headerMap, 'option_a') ?? '');
            $optB = trim($this->getVal($row, $headerMap, 'option_b') ?? '');
            $optC = trim($this->getVal($row, $headerMap, 'option_c') ?? '');
            $optD = trim($this->getVal($row, $headerMap, 'option_d') ?? '');

            // Correct option mapping: supports A, B, C, D or 1, 2, 3, 4
            $correctRaw = strtoupper(trim($this->getVal($row, $headerMap, 'correct_option') ?? 'A'));
            $numToLetter = ['1' => 'A', '2' => 'B', '3' => 'C', '4' => 'D'];
            $correctOption = $numToLetter[$correctRaw] ?? $correctRaw;
            if (!in_array($correctOption, ['A', 'B', 'C', 'D'])) {
                $correctOption = 'A';
            }

            // If options are missing (at least 2 required)
            if ($optA === '' && $optB === '') {
                $skipped++;
                $errors[] = "Row {$rowNum}: Skipped because at least 2 options (Option A & B) are required.";
                continue;
            }

            // Create Question
            $question = Question::create([
                'test_level_id' => $levelId,
                'question_text' => $questionText,
                'question_type' => $questionType,
                'marks'         => $marks,
                'explanation'   => $explanation ?: null,
                'status'        => $status,
            ]);

            // Create Options
            $optionsList = [
                'A' => $optA,
                'B' => $optB,
                'C' => $optC,
                'D' => $optD,
            ];

            foreach ($optionsList as $label => $text) {
                if ($text === '') {
                    continue;
                }
                QuestionOption::create([
                    'question_id'  => $question->id,
                    'option_text'  => $text,
                    'option_label' => $label,
                    'is_correct'   => ($label === $correctOption),
                ]);
            }

            $imported++;
        }

        return [
            'success'  => true,
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors,
        ];
    }

    /**
     * Parse CSV / TXT file.
     */
    protected function parseCsv(string $path): array
    {
        $rows = [];
        $handle = fopen($path, 'r');
        if (!$handle) {
            return [];
        }

        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = ',';
        if (substr_count($firstLine, "\t") > substr_count($firstLine, $delimiter)) {
            $delimiter = "\t";
        } elseif (substr_count($firstLine, ';') > substr_count($firstLine, $delimiter)) {
            $delimiter = ';';
        }

        while (($data = fgetcsv($handle, 4096, $delimiter)) !== false) {
            if (empty($rows) && isset($data[0])) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
            }
            $rows[] = $data;
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Parse XLS file (HTML table format or plain text).
     */
    protected function parseXls(string $path): array
    {
        $content = @file_get_contents($path);
        if ($content && stripos($content, '<table') !== false) {
            $dom = new \DOMDocument();
            @$dom->loadHTML('<?xml encoding="UTF-8">' . $content, LIBXML_NOERROR | LIBXML_NOWARNING);
            $rows = [];
            $trList = $dom->getElementsByTagName('tr');
            foreach ($trList as $tr) {
                $row = [];
                foreach ($tr->childNodes as $cell) {
                    if (in_array(strtolower($cell->nodeName), ['td', 'th'])) {
                        $row[] = trim($cell->textContent);
                    }
                }
                if (!empty($row)) {
                    $rows[] = $row;
                }
            }
            if (!empty($rows)) {
                return $rows;
            }
        }

        return $this->parseCsv($path);
    }

    /**
     * Parse native XLSX file using built-in ZipArchive and SimpleXML.
     * Accurately preserves column alignments using cell coordinates (e.g. A1, B1, C1).
     */
    protected function parseXlsx(string $path): array
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return [];
        }

        // 1. Read shared strings
        $sharedStrings = [];
        $sharedStringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedStringsXml) {
            $xml = @simplexml_load_string($sharedStringsXml);
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $text = '';
                        foreach ($si->r as $r) {
                            $text .= (string)$r->t;
                        }
                        $sharedStrings[] = $text;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // 2. Read sheet1.xml
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if (!$sheetXml) {
            return [];
        }

        $xml = @simplexml_load_string($sheetXml);
        if (!$xml || !isset($xml->sheetData)) {
            return [];
        }

        $rows = [];

        foreach ($xml->sheetData->row as $row) {
            $rowData = [];
            $maxCol = 0;

            foreach ($row->c as $c) {
                $attr = $c->attributes();
                $cellRef = isset($attr['r']) ? (string)$attr['r'] : '';
                $colIdx = $this->colRefToIndex($cellRef);

                $type = isset($attr['t']) ? (string)$attr['t'] : '';
                $val = '';

                if ($type === 's') {
                    $sIdx = (int)$c->v;
                    $val = $sharedStrings[$sIdx] ?? '';
                } elseif ($type === 'inlineStr') {
                    $val = (string)($c->is->t ?? '');
                } else {
                    $val = (string)($c->v ?? '');
                }

                if ($colIdx >= 0) {
                    $rowData[$colIdx] = $val;
                    if ($colIdx > $maxCol) {
                        $maxCol = $colIdx;
                    }
                } else {
                    $rowData[] = $val;
                    $maxCol = max($maxCol, count($rowData) - 1);
                }
            }

            // Fill gaps so all indices up to maxCol exist
            $normalizedRow = [];
            for ($i = 0; $i <= $maxCol; $i++) {
                $normalizedRow[$i] = $rowData[$i] ?? '';
            }

            $rows[] = $normalizedRow;
        }

        return $rows;
    }

    /**
     * Map Excel cell reference like "A1" or "K2" to a 0-based column index (A=0, B=1, ...).
     */
    protected function colRefToIndex(string $cellRef): int
    {
        if (preg_match('/^([A-Z]+)\d+$/i', trim($cellRef), $matches)) {
            $colStr = strtoupper($matches[1]);
            $len = strlen($colStr);
            $index = 0;
            for ($i = 0; $i < $len; $i++) {
                $index = $index * 26 + (ord($colStr[$i]) - 64);
            }
            return $index - 1;
        }
        return -1;
    }

    /**
     * Map 0-based column index to Excel column letters (0 => A, 1 => B, 26 => AA, ...).
     */
    protected function indexToColRef(int $index): string
    {
        $letters = '';
        while ($index >= 0) {
            $letters = chr(65 + ($index % 26)) . $letters;
            $index = intdiv($index, 26) - 1;
        }
        return $letters;
    }

    /**
     * Map header labels to normalized keys.
     */
    protected function mapHeaders(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $index => $label) {
            $clean = strtolower(trim(preg_replace('/[^a-zA-Z0-9]/', '', (string)$label)));

            if (in_array($clean, ['testlevel', 'level', 'levelid', 'testlevelid', 'testlevelidorname', 'levelidorname'])) {
                $map['test_level'] = $index;
            } elseif (in_array($clean, ['questiontext', 'question', 'questiontitle'])) {
                $map['question_text'] = $index;
            } elseif (in_array($clean, ['questiontype', 'type'])) {
                $map['question_type'] = $index;
            } elseif (in_array($clean, ['marks', 'mark', 'points', 'pts'])) {
                $map['marks'] = $index;
            } elseif (in_array($clean, ['optiona', 'opta', 'a'])) {
                $map['option_a'] = $index;
            } elseif (in_array($clean, ['optionb', 'optb', 'b'])) {
                $map['option_b'] = $index;
            } elseif (in_array($clean, ['optionc', 'optc', 'c'])) {
                $map['option_c'] = $index;
            } elseif (in_array($clean, ['optiond', 'optd', 'd'])) {
                $map['option_d'] = $index;
            } elseif (in_array($clean, ['correctoption', 'correct', 'correctanswer', 'answer'])) {
                $map['correct_option'] = $index;
            } elseif (in_array($clean, ['explanation', 'explain'])) {
                $map['explanation'] = $index;
            } elseif (in_array($clean, ['status', 'isactive'])) {
                $map['status'] = $index;
            }
        }

        return $map;
    }

    protected function getVal(array $row, array $map, string $key): ?string
    {
        if (isset($map[$key]) && isset($row[$map[$key]])) {
            return (string)$row[$map[$key]];
        }
        return null;
    }
}
