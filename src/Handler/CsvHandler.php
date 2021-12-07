<?php

namespace App\Handler;

use function count;

class CsvHandler
{
    public function getNameFromNumber($num)
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = (int)($num / 26);
        if ($num2 > 0) {
            return $this->getNameFromNumber($num2 - 1) . $letter;
        }

        return $letter;
    }

    public function getArrayCsv(array $dataCsv)
    {
        $countKeys = count($dataCsv);
        $data = [];

        for ($i = 0; $i < $countKeys; $i++) {
            $data[$this->getNameFromNumber($i)] = $dataCsv[$i];
        }

        return $data;
    }
}