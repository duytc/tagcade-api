<?php

//[{"0":0.8},{"100":0.7}]

printf(" Test Value %s \n", json_encode(['test' => 333, 'test2' => 1]));

printf(" Test Value %s \n", json_encode( [
   [
      "cpmRate"=>0.34,
      "threshold"=>100000
   ],
   [
      "cpmRate" => 0.31,
      "threshold" =>200000
   ]
]));



