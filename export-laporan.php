<?php
include('koneksi.php');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Laporan Buku Tamu');

// ============ JUDUL LAPORAN ============
$sheet->mergeCells('A1:G1');
$sheet->setCellValue('A1', 'LAPORAN BUKU TAMU');

// ============ HEADER TABEL (baris 3) ============
$sheet->setCellValue('A3', 'No');
$sheet->setCellValue('B3', 'TANGGAL');
$sheet->setCellValue('C3', 'NAMA TAMU');
$sheet->setCellValue('D3', 'ALAMAT');
$sheet->setCellValue('E3', 'NO Tlpn/HP');
$sheet->setCellValue('F3', 'BERTEMU DENGAN');
$sheet->setCellValue('G3', 'KEPENTINGAN');

// ============ AMBIL DATA (cuma sekali) ============
if (isset($_GET['cari'])) {
    $p_awal = $_GET['p_awal'];
    $p_akhir = $_GET['p_akhir'];
    $data = mysqli_query($koneksi, "SELECT * FROM buku_tamu WHERE tanggal BETWEEN '$p_awal' AND '$p_akhir' ORDER BY tanggal DESC");
} else {
    $data = mysqli_query($koneksi, "SELECT * FROM buku_tamu ORDER BY tanggal DESC");
}

// ============ ISI DATA (mulai baris 4) ============
$i = 4;
$no = 1;
while ($d = mysqli_fetch_array($data)) {
    $sheet->setCellValue('A' . $i, $no++);
    $sheet->setCellValue('B' . $i, $d['tanggal']);
    $sheet->setCellValue('C' . $i, $d['nama_tamu']);
    $sheet->setCellValue('D' . $i, $d['alamat']);

    // paksa No HP jadi string (agar tidak berubah jadi notasi ilmiah)
    $sheet->setCellValueExplicit('E' . $i, (string)$d['no_hp'], DataType::TYPE_STRING);

    $sheet->setCellValue('F' . $i, $d['bertemu']);
    $sheet->setCellValue('G' . $i, $d['kepentingan']);
    $i++;
}

$lastRow = $i - 1;

// ============ STYLING JUDUL ============
$sheet->getStyle('A1:G1')->getFont()->setBold(true);
$sheet->getStyle('A1:G1')->getFont()->setSize(16);
$sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID);
$sheet->getStyle('A1:G1')->getFill()->getStartColor()->setRGB('FFD966');
$sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:G1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(30);

// ============ STYLING HEADER TABEL ============
$sheet->getStyle('A3:G3')->getFont()->setBold(true);
$sheet->getStyle('A3:G3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A3:G3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getStyle('A3:G3')->getFill()->setFillType(Fill::FILL_SOLID);
$sheet->getStyle('A3:G3')->getFill()->getStartColor()->setRGB('D9E2F3');
$sheet->getRowDimension(3)->setRowHeight(25);

// ============ BORDER & PERATAAN DATA ============
if ($lastRow >= 4) {
    // border seluruh tabel (header + data)
    $sheet->getStyle('A3:G' . $lastRow)
        ->getBorders()
        ->getAllBorders()
        ->setBorderStyle(Border::BORDER_THIN);

    // perataan tengah kolom No, Tanggal, No HP
    $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('B4:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E4:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // wrap text + vertical center untuk semua data
    $sheet->getStyle('A3:G' . $lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A3:G' . $lastRow)->getAlignment()->setWrapText(true);
}

// ============ LEBAR KOLOM ============
$sheet->getColumnDimension('A')->setWidth(8);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(25);
$sheet->getColumnDimension('D')->setWidth(35);
$sheet->getColumnDimension('E')->setWidth(18);
$sheet->getColumnDimension('F')->setWidth(22);
$sheet->getColumnDimension('G')->setWidth(30);

// ============ EXPORT / DOWNLOAD ============
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan Buku Tamu.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;