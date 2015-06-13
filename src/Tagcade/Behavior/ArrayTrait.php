<?php

namespace Tagcade\Behavior;


trait ArrayTrait {
    public function sliceArray(array $array, $limit = null, $offset = null)
    {
        if (is_int($offset) && is_int($limit)) {
            return array_slice($array, $offset, $limit);
        }

        if (is_int($offset)) {
            return array_slice($array, $offset);
        }

        return $array;
    }
} 