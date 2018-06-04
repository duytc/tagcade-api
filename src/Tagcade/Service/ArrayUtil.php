<?php

namespace Tagcade\Service;

use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\ModelInterface;

class ArrayUtil implements ArrayUtilInterface
{
    /**
     * @inheritdoc
     */
    public function array_unique_object(array $objects)
    {
        if (count($objects) < 2) {
            return $objects;
        }

        $mappedObjects = [];
        foreach ($objects as $obj) {
            if (!$obj instanceof ModelInterface) {
                throw new InvalidArgumentException('expect instance of ModelInterface');
            }

            $mappedObjects[$obj->getId()] = $obj;
        }

        return array_values($mappedObjects);
    }

    /**
     * @param $array
     * @param array $return
     * @return array
     */
    function array_flatten($array, $return = array())
    {
        foreach ($array AS $key => $value) {
            if (is_array($value)) {
                $return = $this->array_flatten($value, $return);
            } else {
                if ($value) {
                    $return[] = $value;
                }
            }
        }

        return $return;
    }

}