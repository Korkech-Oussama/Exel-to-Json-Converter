<?php

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = './test.xls';

function xlsToJson($file) {
    $spreadsheet = IOFactory::load($file);
    $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

    $jsonData = []; 
    function isColumnAfterE($column) {
        // Convert Excel-like column label to a numeric index
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
        return $columnIndex >= \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString('E');
    }

    // Extract date headers from row 4
    $dateHeaders = [];

    foreach ($sheetData[4] as $column => $cellValue) {
        if (isColumnAfterE($column)) {
            $dateHeaders[$column] = $cellValue; 
        }
    }

    // Process each data row from row 5 onward
    foreach ($sheetData as $rowIndex => $row) {
        if ($rowIndex < 5) { // Skip until data rows
            continue;
        }

        

        if (!empty($row['C'])) { // Check if there's a name
            $dataEntry = [
                "Name" => $row['C'],
                "times" => []
            ];

            // Loop through date headers and get times for each date
            foreach ($dateHeaders as $column => $date) {
                $time = isset($row[$column]) ? $row[$column] : "--:--"; // Default if empty
                // Split the time string by newline and then space
                $timeEntries = explode("\n",$time);
                // Map each time entry to an array of time parts
                $timeArray = array_merge(...array_map(function($entry) {
                    return explode(" ", $entry);
                }, $timeEntries));
                // Add the times to the data entry
                $dataEntry["times"][] = [
                    "day" => $date,
                    "time" => $timeArray
                ];
            }

            $jsonData[] = $dataEntry;
        }
    }

    return json_encode($jsonData, JSON_PRETTY_PRINT);
}

$jsonData = xlsToJson($file);
header('Content-Type: application/json');
echo $jsonData;

?>
