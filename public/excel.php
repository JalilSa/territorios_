<?php
session_start();
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['usuario_id'])) {
    header("location: index.php");
    exit();
}

include_once '../src/db/config.php';

$sql = "SELECT id_territorio, nombre_territorio, estado, ultima_fecha_completo, ultima_fecha_asignado FROM territorios";
$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Nombre del Territorio');
$sheet->setCellValue('C1', 'Estado');
$sheet->setCellValue('D1', 'Última Fecha Completo');
$sheet->setCellValue('E1', 'Última Fecha Asignado');

// Llenar datos
$rowCount = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowCount, $row['id_territorio']);
    $sheet->setCellValue('B' . $rowCount, $row['nombre_territorio']);
    $sheet->setCellValue('C' . $rowCount, $row['estado']);
    $sheet->setCellValue('D' . $rowCount, $row['ultima_fecha_completo']);
    $sheet->setCellValue('E' . $rowCount, $row['ultima_fecha_asignado']);
    $rowCount++;
}

// Redirigir la salida al navegador
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="territorios.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
$conn->close();
?>
