<?php
    $fileName = "BORME-A-2022-4-04.pdf";
    $filePath = 'https://www.boe.es/borme/dias/2022/01/07/pdfs/BORME-A-2022-4-04.pdf';
    // Define headers
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$fileName");
    header("Content-Type: application/zip");
    header("Content-Transfer-Encoding: binary");
    
    // Read the file
    readfile($filePath);
    exit;
?>