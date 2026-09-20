<?php
declare(strict_types=1);

function xlsxEscape(string $value): string
{
    return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function xlsxColumn(int $index): string
{
    $result = '';
    while ($index > 0) {
        $index--;
        $result = chr(65 + ($index % 26)) . $result;
        $index = intdiv($index, 26);
    }
    return $result;
}

function xlsxCell(string $reference, mixed $value, int $style = 0, bool $number = false): string
{
    $styleAttribute = $style ? ' s="' . $style . '"' : '';
    if ($value === null || $value === '') return '<c r="' . $reference . '"' . $styleAttribute . '/>';
    if ($number && is_numeric($value)) return '<c r="' . $reference . '"' . $styleAttribute . '><v>' . $value . '</v></c>';
    return '<c r="' . $reference . '" t="inlineStr"' . $styleAttribute . '><is><t xml:space="preserve">' . xlsxEscape((string) $value) . '</t></is></c>';
}

function xlsxSheet(array $rows, array $widths, array $headerRows = [], bool $landscape = false): string
{
    $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane ySplit="' . count($headerRows) . '" topLeftCell="A' . (count($headerRows) + 1) . '" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews><cols>';
    foreach ($widths as $index => $width) $xml .= '<col min="' . ($index + 1) . '" max="' . ($index + 1) . '" width="' . $width . '" customWidth="1"/>';
    $xml .= '</cols><sheetData>';
    foreach ($rows as $rowIndex => $row) {
        $number = $rowIndex + 1;
        $xml .= '<row r="' . $number . '" ht="20" customHeight="1">';
        foreach ($row as $columnIndex => $cell) {
            [$value, $style, $isNumber] = is_array($cell) ? [$cell[0], $cell[1] ?? 0, $cell[2] ?? false] : [$cell, 0, false];
            $xml .= xlsxCell(xlsxColumn($columnIndex + 1) . $number, $value, $style, $isNumber);
        }
        $xml .= '</row>';
    }
    $lastColumn = xlsxColumn(count($widths));
    $lastRow = max(1, count($rows));
    $tableHeader = count($headerRows) ? max($headerRows) : 1;
    $xml .= '</sheetData><autoFilter ref="A' . $tableHeader . ':' . $lastColumn . $lastRow . '"/><printOptions horizontalCentered="1"/><pageMargins left="0.3" right="0.3" top="0.5" bottom="0.5" header="0.2" footer="0.2"/><pageSetup orientation="' . ($landscape ? 'landscape' : 'portrait') . '" paperSize="9" fitToWidth="1" fitToHeight="0"/><pageSetupPr fitToPage="1"/></worksheet>';
    return $xml;
}

function xlsxArchive(array $files): string
{
    $data = '';
    $central = '';
    $offset = 0;
    foreach ($files as $name => $content) {
        $name = str_replace('\\', '/', $name);
        $crc = crc32($content);
        if ($crc < 0) $crc += 4294967296;
        $size = strlen($content);
        $record = pack('VvvvvvVVVvv', 0x04034b50, 20, 0, 0, 0, 0, $crc, $size, $size, strlen($name), 0) . $name . $content;
        $data .= $record;
        $central .= pack('VvvvvvvVVVvvvvvVV', 0x02014b50, 20, 20, 0, 0, 0, 0, $crc, $size, $size, strlen($name), 0, 0, 0, 0, 0, $offset) . $name;
        $offset += strlen($record);
    }
    return $data . $central . pack('VvvvvVVv', 0x06054b50, 0, 0, count($files), count($files), strlen($central), strlen($data), 0);
}

function xlsxDownload(string $filename, array $sheets): never
{
    $workbookSheets = '';
    $relationships = '';
    $contentTypes = '';
    $files = [
        '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>',
        '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
        'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><numFmts count="1"><numFmt numFmtId="164" formatCode="[$Rp-421] #,##0"/></numFmts><fonts count="3"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><color rgb="FFFFFFFF"/><name val="Calibri"/></font><font><b/><sz val="15"/><color rgb="FF061A43"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF1459CF"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="2"><border><left/><right/><top/><bottom/></border><border><left style="thin"><color rgb="FFD7DEE8"/></left><right style="thin"><color rgb="FFD7DEE8"/></right><top style="thin"><color rgb="FFD7DEE8"/></top><bottom style="thin"><color rgb="FFD7DEE8"/></bottom></border></borders><cellXfs count="5"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="1" applyFont="1" applyFill="1" applyBorder="1"/><xf numFmtId="0" fontId="2" fillId="0" borderId="0" applyFont="1"/><xf numFmtId="0" fontId="0" fillId="0" borderId="1" applyBorder="1"/><xf numFmtId="164" fontId="0" fillId="0" borderId="1" applyNumberFormat="1" applyBorder="1"/></cellXfs></styleSheet>',
    ];
    foreach ($sheets as $index => $sheet) {
        $id = $index + 1;
        $workbookSheets .= '<sheet name="' . xlsxEscape($sheet['name']) . '" sheetId="' . $id . '" r:id="rId' . $id . '"/>';
        $relationships .= '<Relationship Id="rId' . $id . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $id . '.xml"/>';
        $contentTypes .= '<Override PartName="/xl/worksheets/sheet' . $id . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        $files['xl/worksheets/sheet' . $id . '.xml'] = xlsxSheet($sheet['rows'], $sheet['widths'], $sheet['headers'], $sheet['landscape']);
    }
    $files['[Content_Types].xml'] .= $contentTypes . '</Types>';
    $files['xl/workbook.xml'] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>' . $workbookSheets . '</sheets></workbook>';
    $files['xl/_rels/workbook.xml.rels'] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . $relationships . '<Relationship Id="rId' . (count($sheets) + 1) . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($output = xlsxArchive($files)));
    header('Cache-Control: private, no-store, max-age=0');
    echo $output;
    exit;
}
