<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/pimpinan-report.php';
define('FPDF_FONTPATH', dirname(__DIR__, 2) . '/includes/fpdf-font/');
require_once dirname(__DIR__, 2) . '/includes/fpdf.php';
requireRole(4);

final class PimpinanReportPdf extends FPDF
{
    private array $filters;
    private string $printedAt;
    private array $widths = [8, 22, 22, 57, 20, 30, 26, 28, 17];

    public function __construct(array $filters, string $printedAt)
    {
        parent::__construct('L', 'mm', 'A4');
        $this->filters = $filters;
        $this->printedAt = $printedAt;
        $this->SetMargins(8, 10, 8);
        $this->SetAutoPageBreak(true, 15);
    }

    function Header(): void
    {
        $logo = dirname(__DIR__, 2) . '/assets/images/logofix.png';
        if (is_file($logo)) $this->Image($logo, 9, 9, 25);
        $this->SetFont('Arial', 'B', 14); $this->SetTextColor(15, 42, 94); $this->SetXY(38, 10); $this->Cell(0, 6, 'e-ARSIP KAI DIVRE III', 0, 1);
        $this->SetFont('Arial', '', 8); $this->SetTextColor(71, 85, 105); $this->SetX(38); $this->Cell(0, 5, 'Web Pengarsipan Digital', 0, 1);
        $this->SetDrawColor(37, 99, 235); $this->SetLineWidth(.7); $this->Line(8, 28, 289, 28);
        $this->SetFont('Arial', 'B', 12); $this->SetTextColor(15, 42, 94); $this->SetXY(8, 32); $this->Cell(0, 6, 'LAPORAN DATA ARSIP', 0, 1);
        $this->SetFont('Arial', '', 7); $this->SetTextColor(51, 65, 85);
        $lines = [
            'Periode: ' . $this->filters['period'] . '    |    Unit: ' . $this->filters['unit'],
            'Jenis Surat: ' . $this->filters['type'] . '    |    Klasifikasi: ' . $this->filters['class'],
            'Status: ' . $this->filters['status'] . ($this->filters['q'] !== '' ? '    |    Pencarian: ' . $this->filters['q'] : '') . '    |    Tanggal Export: ' . $this->printedAt,
        ];
        foreach ($lines as $line) { $this->SetX(8); $this->Cell(0, 4, $this->reportText($line), 0, 1); }
        $this->Ln(2);
    }

    function Footer(): void
    {
        $this->SetY(-11); $this->SetDrawColor(203, 213, 225); $this->Line(8, $this->GetY(), 289, $this->GetY());
        $this->SetY(-8); $this->SetFont('Arial', '', 7); $this->SetTextColor(100, 116, 139);
        $this->Cell(0, 4, 'e-ARSIP KAI DIVRE III  |  Laporan Data Arsip  |  Dicetak pada: ' . $this->printedAt, 0, 0, 'L');
        $this->Cell(0, 4, 'Halaman ' . $this->PageNo() . ' dari {nb}', 0, 0, 'R');
    }

    function tableHeader(): void
    {
        $headers = ['No', 'No. Registrasi', 'No. Surat', 'Perihal', 'Tanggal Surat', 'Unit', 'Jenis Surat', 'Klasifikasi', 'Status'];
        $this->SetFont('Arial', 'B', 6.5); $this->SetFillColor(15, 42, 94); $this->SetTextColor(255, 255, 255);
        foreach ($headers as $i => $header) $this->Cell($this->widths[$i], 7, $header, 1, 0, 'C', true);
        $this->Ln(); $this->SetTextColor(30, 41, 59);
    }

    function row(array $values, int $index): void
    {
        $values = array_map(fn($value) => $this->reportText((string) $value), $values);
        $height = 5;
        foreach ($values as $i => $value) $height = max($height, 3.5 * $this->lines($this->widths[$i], $value));
        if ($this->GetY() + $height > 190) { $this->AddPage(); $this->tableHeader(); }
        $x = $this->GetX(); $y = $this->GetY();
        $this->SetFont('Arial', '', 6.5);
        foreach ($values as $i => $value) {
            $this->SetXY($x, $y); $this->MultiCell($this->widths[$i], 3.5, $value, 1, $i === 0 || $i === 4 || $i === 8 ? 'C' : 'L');
            $this->SetXY($x + $this->widths[$i], $y); $x += $this->widths[$i];
        }
        $this->SetXY(8, $y + $height);
    }

    private function lines(float $width, string $text): int { return max(1, (int) ceil($this->GetStringWidth($text) / max(1, $width - 2))); }
    private function reportText(string $value): string { return iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $value) ?: ''; }
}

extract(pimpinanReportData($pdo, $_GET));
$printedAt = date('d-m-Y H:i');
$period = ($from !== '' ? date('d-m-Y', strtotime($from)) : 'Awal data') . ' s.d. ' . ($to !== '' ? date('d-m-Y', strtotime($to)) : 'Saat ini');
$pdf = new PimpinanReportPdf(['period' => $period, 'unit' => $labels['unit'], 'type' => $labels['type'], 'class' => $labels['class'], 'status' => $status !== '' ? $status : 'Semua Status', 'q' => $q], $printedAt);
$pdf->AliasNbPages(); $pdf->AddPage();
$pdf->SetFillColor(239, 246, 255); $pdf->SetTextColor(15, 42, 94); $pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(35, 8, 'TOTAL ARSIP', 1, 0, 'C', true); $pdf->Cell(18, 8, (string) $summary['total'], 1, 0, 'C', true);
foreach ([['AKTIF', $summary['aktif']], ['PERMANEN', $summary['permanen']], ['MUSNAH', $summary['musnah']]] as [$label, $value]) { $pdf->Cell(25, 8, $label, 1, 0, 'C', true); $pdf->Cell(14, 8, (string) $value, 1, 0, 'C', true); }
$pdf->Ln(12); $pdf->tableHeader();
foreach ($archives as $index => $archive) $pdf->row([$index + 1, $archive['no_registrasi'], $archive['no_surat'], $archive['perihal'], $archive['tanggal_surat'] ? date('d-m-Y', strtotime($archive['tanggal_surat'])) : '-', $archive['nama_unit'], $archive['nama_jenis'], $archive['nama_klasifikasi'], $archive['status_arsip']], $index);
if (!$archives) { $pdf->SetFont('Arial', 'I', 8); $pdf->Cell(array_sum([8, 22, 22, 57, 20, 30, 26, 28, 17]), 8, 'Tidak ada data arsip sesuai filter.', 1, 1, 'C'); }
$pdf->Output('D', 'Laporan_Arsip_e-ARSIP_KAI_DIVRE_III_' . date('Y-m-d') . '.pdf');
