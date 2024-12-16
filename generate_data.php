<?php

$fileMap = [];
$miscFilePointer = 0;

$files = array_filter(scandir('img/dated'), fn($n) => $n[0] !== '.');
foreach ($files as $f) {
    $basename = explode('.', $f)[0];
    $fileMap[$basename] = $f;
}

$miscFiles = array_values(array_filter(scandir('img/misc'), fn($n) => $n[0] !== '.'));
mt_srand(12345);
shuffle($miscFiles);

$rows = [];
foreach (file('labels.csv') as $line) {
    $rows[] = str_getcsv($line);
}
array_shift($rows);

$result = [];

foreach ($rows as $row) {
    $month = $row[0];
    $day = $row[1];
    $header = $row[2];
    $subheader = $row[3];

    $d = new DateTime("$month $day");
    $basename = $d->format('md');

    if (isset($fileMap[$basename])) {
        $path = 'img/dated/' . $fileMap[$basename];
    } else {
        $path = 'img/misc/' . $miscFiles[$miscFilePointer++];
    }

    $monthIndex = $d->format('m');
    $dayIndex = $d->format('d');

    $result["$monthIndex-$dayIndex"] = [
        'header' => $header,
        'subheader' => $subheader,
        'imagePath' => $path,
    ];
}

file_put_contents('data.json', json_encode($result));