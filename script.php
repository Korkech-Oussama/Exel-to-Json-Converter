<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

function xlsToJson($file) {
    // Load the .xls file
    $spreadsheet = IOFactory::load($file);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    // Convert to JSON
    $json = json_encode($sheetData,JSON_PRETTY_PRINT);

    return $json;
}

// Example usage
$filePath = './test.xls';
$jsonData = xlsToJson($filePath);

header('Content-Type: application/json');
// Output or save the JSON data
echo $jsonData;


?> 