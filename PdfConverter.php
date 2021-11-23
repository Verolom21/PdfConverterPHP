<?php

class PdfConverter {
    public $pathInput;
    public $pathOutput;

    public function __construct($pathInput, $pathOutput)
    {
        $this->pathInput = $pathInput;
        $this->pathOutput = $pathOutput;
    }

    function makeAllPdf($pathInput=NULL, $pathOutput=NULL)
    {
        // массив, хранящий возвращаемое значение
        $files = [];

        if (isset($pathInput)) $this->pathInput = $pathInput;
        if (isset($pathOutput)) $this->pathOutput = $pathOutput;


        // добавить конечный слеш, если его нет
        if (substr($this->pathInput, -1) != "/") $this->pathInput .= "/";
        if (substr($this->pathOutput, -1) != "/") $this->pathOutput .= "/";

        // указание директории и считывание списка файлов
        try {
            $d = dir($this->pathInput) ;
        } catch (Exception $ex) {
            die("getFileList: Не удалось открыть каталог $this->pathInput для чтения");
        }

        if (!file_exists($this->pathOutput)) {
            mkdir($this->pathOutput);
        }

        while ($entry = $d->read()) {

            
            $currentFile = "{$this->pathInput}{$entry}";
            $outputFile = str_replace($this->pathInput, $this->pathOutput, $currentFile);
            
            // пропустить скрытые файлы
            if ($entry[0] == ".") continue;
            if (is_dir($currentFile)) {
                $files[0][] = "$currentFile/";


                if (is_readable("$currentFile/")) {
                    $recurseRes = self::makeAllPdf("$currentFile/", $outputFile);
                }
            } elseif (is_readable($currentFile)) {
                $files[1][] = $currentFile;
                self::makeOnePdf($currentFile, $outputFile);
            }
        }
        $d->close();

    }

    private function makeOnePdf($currentFile, $outputFile) {
        print_r("Найден файл по пути {$currentFile}".PHP_EOL);
        $html = file_get_contents($currentFile);
        $pdf = new FPDF();
        define('FPDF_FONTPATH',"./fpdf/font/");
        $pdf->AddFont('Arial','','arial.php');
        $pdf->SetFont('Arial');
        $pdf->SetFontSize(10);
        $pdf->SetTextColor(0,0,0);
        $pdf->AddPage('P');
        $pdf->SetDisplayMode('real','default');
        $pdf->SetXY(10,10);
        $pdf->Write(5,iconv('utf-8', 'windows-1251',$html));
        $pdf->Output( "$outputFile.pdf", "F");
        print_r("Создан новый файл по пути {$outputFile}.pdf".PHP_EOL);
    }
}
