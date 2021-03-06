<?php


namespace Tagcade\Behaviors;


trait ParserUtilTrait
{
    /**
     * check if a rowData and a needleData are matched. Also convert rowData to ASCII Encoding before comparing
     * @param array $rowData row data read from file
     * @param array $needleData expected data to compare
     * @return bool
     */
    protected function matchRow(array $rowData, array $needleData)
    {
        $rowData = array_map(function ($val) {
            return trim(strtolower($val));
        }, $rowData);

        $rowData = array_filter($rowData, function ($val) {
            return !empty($val);
        });

        $rowData = $this->convertEncodingToASCII($rowData);

        return $this->arrayCompare($rowData, $needleData);
    }

    /**
     * convert a string To ASCII Encoding
     * @param array $data
     * @return array
     */
    protected function convertEncodingToASCII(array $data)
    {
        foreach ($data as &$item) {
            if (!mb_check_encoding($item, 'ASCII')) {
                $item = $this->convert_ascii($item);
            }
        }

        return $data;
    }

    /**
     * Remove any non-ASCII characters and convert known non-ASCII characters
     * to their ASCII equivalents, if possible.
     *
     * @param string $string
     * @return string $string
     * @author Jay Williams <myd3.com>
     * @license MIT License
     * @link http://gist.github.com/119517
     */
    protected function convert_ascii($string)
    {
        // Replace Single Curly Quotes
        $search[] = chr(226) . chr(128) . chr(152);
        $replace[] = "'";
        $search[] = chr(226) . chr(128) . chr(153);
        $replace[] = "'";
        // Replace Smart Double Curly Quotes
        $search[] = chr(226) . chr(128) . chr(156);
        $replace[] = '"';
        $search[] = chr(226) . chr(128) . chr(157);
        $replace[] = '"';
        // Replace En Dash
        $search[] = chr(226) . chr(128) . chr(147);
        $replace[] = '--';
        // Replace Em Dash
        $search[] = chr(226) . chr(128) . chr(148);
        $replace[] = '---';
        // Replace Bullet
        $search[] = chr(226) . chr(128) . chr(162);
        $replace[] = '*';
        // Replace Middle Dot
        $search[] = chr(194) . chr(183);
        $replace[] = '*';
        // Replace Ellipsis with three consecutive dots
        $search[] = chr(226) . chr(128) . chr(166);
        $replace[] = '...';
        // Apply Replacements
        $string = str_replace($search, $replace, $string);
        // Remove any non-ASCII Characters
        $string = preg_replace("/[^\x01-\x7F]/", "", $string);
        return $string;
    }

    /**
     * @param array $array1
     * @param array $array2
     * @return bool
     */
    protected function arrayCompare(array $array1, array $array2)
    {
        if (count($array1) !== count($array2)) {
            return false;
        }

        return (!array_diff($array1, $array2) && !array_diff($array2, $array1));
    }

    /**
     * @param $val
     * @return \DateTime|null
     */
    protected function extractDate($val)
    {
        $array1 = explode(":", strtolower($val));
        if (isset($array1[1])) {
            $array2 = explode("to", $array1[1]);
            if (isset($array2[0])) {
                $date = date_parse($array2[0]);
                if (is_array($date)) {
                    $dateTime = new \DateTime();
                    $dateTime->setDate($date['year'], $date['month'], $date['day']);
                    return $dateTime;
                }
            }
        }

        return null;
    }

    /**
     * @param $val
     * @return mixed
     */
    protected function removeDollarSign($val)
    {
        return str_replace("$", "", $val);
    }

    /**
     * @param $val
     * @return mixed
     */
    protected function removeComma($val)
    {
        return str_replace(",", "", $val);
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function getAdTagIdFromAdTagName($name)
    {
        $pattern = '#\((.*?)\)#';
        preg_match($pattern, $name, $match);

        return ['id'=>(int)$match[1], 'name'=>preg_replace($pattern, '', $name)];
    }

    /**
     * @param array $columns
     * @return array
     */
    protected function normalizeColumns(array $columns)
    {
        foreach ($columns as &$column) {
            $column = $this->normalizeColumn($column);
        }

        return $columns;
    }

    /**
     * @param $column
     * @return mixed|string
     */
    protected function normalizeColumn($column)
    {
        $column = strtolower($column);
        $column = str_replace(" ", "", $column);
        $column = $this->removeDollarSign($column);
        $column = $this->removeComma($column);

        return $column;
    }
}