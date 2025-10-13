<?php
require '../../../vendor/autoload.php';
include '../../../config.php';
include '../../../koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('ID sekolah tidak valid');
}

// Ambil data siswa
$q = mysqli_query($conn, "SELECT * FROM data_siswa WHERE sekolah_id = $id ORDER BY nama_siswa");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$headers = ['No', 'NISN', 'Nama', 'Jenis Kelamin', 'Kelas', 'Jenis Inklusi'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// Style header
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => '1A237E']],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'E3EAFD']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '90CAF9']
        ]
    ]
];
$sheet->getStyle('A1:F1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(24);

$rowNum = 2;
$no = 1;
while ($data = mysqli_fetch_assoc($q)) {
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('B' . $rowNum, $data['nisn']);
    $sheet->setCellValue('C' . $rowNum, $data['nama_siswa']);
    $sheet->setCellValue('D' . $rowNum, $data['jenis_kelamin']);
    $sheet->setCellValue('E' . $rowNum, $data['kelas']);
    $sheet->setCellValue('F' . $rowNum, $data['jenis_inklusi']);
    // Zebra striping
    if ($rowNum % 2 == 0) {
        $sheet->getStyle('A'.$rowNum.':F'.$rowNum)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F6F8FA');
    }
    // Border
    $sheet->getStyle('A'.$rowNum.':F'.$rowNum)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheet->getStyle('A'.$rowNum.':F'.$rowNum)->getBorders()->getAllBorders()->getColor()->setRGB('B0BEC5');
    // Alignment nomor tengah
    $sheet->getStyle('A'.$rowNum)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $rowNum++;
}

// Auto width
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="data_siswa_sekolah_inklusi.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;