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

$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'TANGGAL');
$sheet->setCellValue('C1', 'NAMA TAMU');
$sheet->setCellValue('D1', 'ALAMAT');
$sheet->setCellValue('E1', 'NO TELEPON/HP');
$sheet->setCellValue('F1', 'BERTEMU DENGAN');
$sheet->setCellValue('G1', 'KEPENTINGAN');

if (isset($_GET['cari'])) {
    $p_awal = $_GET['p_awal'];
    $p_akhir = $_GET['p_akhir'];
    $data = mysqli_query($koneksi, "SELECT * FROM buku_tamu WHERE tanggal BETWEEN '$p_awal' AND '$p_akhir'");
} else {
    $data = mysqli_query($koneksi, "SELECT * FROM buku_tamu");
}

$i = 2;
$no = 1;
while ($d = mysqli_fetch_array($data)) {
    $sheet->setCellValue('A' . $i, $no++);
    $sheet->setCellValue('B' . $i, $d['tanggal']);
    $sheet->setCellValue('C' . $i, $d['nama_tamu']);
    $sheet->setCellValue('D' . $i, $d['alamat']);
    $sheet->setCellValue('E' . $i, $d['no_hp']);
    $sheet->setCellValue('F' . $i, $d['bertemu']);
    $sheet->setCellValue('G' . $i, $d['kepentingan']);
    $i++;
}
// styling header
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4E73DF'],
    ],
];
$sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
$sheet->getRowDimension('1')->setRowHeight(25);

// 2. query data
if (isset($_GET['cari'])) {
    $p_awal = $_GET['p_awal'];
    $p_akhir = $_GET['p_akhir'];
    $data = mysqli_query($koneksi, "SELECT * FROM buku_tamu WHERE tanggal BETWEEN '$p_awal' AND '$p_akhir'");
} else {
    $data = mysqli_query($koneksi, "SELECT * FROM buku_tamu");
}

// 3. populate data
$i = 2;
$no = 1;
while ($d = mysqli_fetch_array($data)) {
    $sheet->setCellValue('A' . $i, $no++);
    $sheet->setCellValue('B' . $i, $d['tanggal']);
    $sheet->setCellValue('C' . $i, $d['nama_tamu']);
    $sheet->setCellValue('D' . $i, $d['alamat']);

    // paksa Nomor HP menjadi STR (agar tidak menjadi format ilmiah)
    $sheet->setCellValueExplicit('E' . $i, (string)$d['no_hp'], DataType::TYPE_STRING);

    $sheet->setCellValue('F' . $i, $d['bertemu']);
    $sheet->setCellValue('G' . $i, $d['kepentingan']);

    // perataan tangah untuk kolom No dan Tanggal
    $sheet->getStyle('A' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('B' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('E' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

    $i++;
}

$lastRow = $i - 1;

// 4. tambahkan border ke seluruh tabel
if ($lastRow >= 2) {
    $borderStyle = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'D1D3E2'],
            ],
        ],
    ];
    $sheet->getStyle('A1:G' . $lastRow)->applyFromArray($borderStyle);
};

// 5. auto-size lebar kolom
foreach (range('A', 'G') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
};


// set header supaya browser langsung download file (bukan buka tab kosong)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan Buku Tamu.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
