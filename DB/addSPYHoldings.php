<?php

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mydb = new mysqli('127.0.0.1','baseUser','12345','baseDB');

$excelfile = IOFactory::load("holdings-spy.xlsx");

$excelsheet = $excelfile->getActiveSheet();

//$query = "INSERT INTO testStocks (ticker, weight) VALUES (?, ?);";
//$stmt = $mydb->query($query);

for ($row=6; $row<=509; $row++) {
	$cellB = $excelsheet->getCell('B'.$row)->getValue();
	$cellE = $excelsheet->getCell('E'.$row)->getValue();

	$query = "INSERT INTO SPYHoldings (ticker, weight) VALUES ('$cellB', '$cellE');";
	$stmt = $mydb->query($query);

	//$stmt->bind_param('ss', $cellB, $cellE);
	//$stmt->execute();
	}

