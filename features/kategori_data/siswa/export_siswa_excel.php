<?php
require '../vendor/autoload.php';
include '../config.php';
include '../koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('ID sekolah tidak valid');
}

// Ambil data siswa
$q = mysqli_query($conn, "SELECT * FROM data_siswa WHERE sekolah_id = $id ORDER BY nama_siswa");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header
$sheet->setCellValue('A1', 'No');
$sheet->setCellValue('B1', 'NISN');
$sheet->setCellValue('C1', 'Nama');
$sheet->setCellValue('D1', 'Jenis Kelamin');
$sheet->setCellValue('E1', 'Kelas');
$sheet->setCellValue('F1', 'Jenis Inklusi');

$rowNum = 2;
$no = 1;
while ($data = mysqli_fetch_assoc($q)) {
    $sheet->setCellValue('A' . $rowNum, $no++);
    $sheet->setCellValue('B' . $rowNum, $data['nisn']);
    $sheet->setCellValue('C' . $rowNum, $data['nama_siswa']);
    $sheet->setCellValue('D' . $rowNum, $data['jenis_kelamin']);
    $sheet->setCellValue('E' . $rowNum, $data['kelas']);
    $sheet->setCellValue('F' . $rowNum, $data['jenis_inklusi']);
    $rowNum++;
}

// Set header untuk download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="data_siswa_sekolah_inklusi.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;