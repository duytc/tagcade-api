<?php


namespace Tagcade\Service;
use Akeneo\Component\SpreadsheetParser\SpreadsheetInterface;
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
use Psr\Log\LoggerInterface;


class ExcelFileProcessing {


    private $logger;

    function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param $path
     * @return array
     */
    public function getAllContentExcelFile($path)
    {
        $fileContents = [];
        $objPHPExcel = $this->loadExcelFile($path);
        $sheetNames = $this->getAllSheetsName($objPHPExcel);

        foreach ($sheetNames as $sheetName)
        {
            $sheetNameRawData = $this->getSheetRawContent($objPHPExcel, $sheetName);
            $fileContents[$sheetName] = $sheetNameRawData;
        }

        return $fileContents;
    }

    /**
     * @param $path
     * @return \Akeneo\Component\SpreadsheetParser\SpreadsheetInterface
     */
    public function loadExcelFile($path) {
        return SpreadsheetParser::open($path);
    }

    /**
     * @inheritdoc
     */
    public function getSheetRawContent(SpreadsheetInterface $objPHPExcel, $sheetName)
    {
        $contentFiles =[];

        $worksheetIndex = $objPHPExcel->getWorksheetIndex($sheetName);
        foreach ($objPHPExcel->createRowIterator($worksheetIndex) as $rowIndex => $values) {
            $contentFiles[] = $values;
        }
        return $contentFiles;
    }

    /**
     * @param SpreadsheetInterface $objPHPExcel
     * @return mixed
     */
    public function getAllSheetsName (SpreadsheetInterface $objPHPExcel)
    {
        return $objPHPExcel->getWorksheets();
    }

    /**
     * @param $str
     * @param array $noStrip
     * @return mixed|string
     */
    public static function camelCase($str, array $noStrip = [])
    {
        // non-alpha and non-numeric characters become spaces
        $str = preg_replace('/[^a-z0-9' . implode("", $noStrip) . ']+/i', ' ', $str);
        $str = trim($str);
        // uppercase the first character of each word
        $str = ucwords($str);
        $str = str_replace(" ", "", $str);
        $str = lcfirst($str);

        return $str;
    }

} 