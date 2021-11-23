<?php
require_once("config.php");
require_once("fpdf/fpdf.php");
require_once("PdfConverter.php");

$worker = new PdfConverter($pathInput, $pathOutput);
$res = $worker->makeAllPdf();
