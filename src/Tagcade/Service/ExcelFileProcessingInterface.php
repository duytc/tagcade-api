<?php


namespace Tagcade\Service;


use PHPExcel;

interface ExcelFileProcessingInterface {

    /**
     * @param $path
     * @return mixed contect of excel file in array
     */
    public function loadExcelFile($path);

    /**
     * @param PHPExcel $objPHPExcel
     * @param $sheetName
     * @return mixed
     */
    public function getSheetRawContent(PHPExcel $objPHPExcel, $sheetName);

    /**
     * @param PHPExcel $objPHPExcel
     * @return mixed
     */
    public function getAllSheetsName (PHPExcel $objPHPExcel);

    /**
     * @param $str
     * @param array $noStrip
     * @return mixed
     */
    public static function camelCase($str, array $noStrip = []);

} 