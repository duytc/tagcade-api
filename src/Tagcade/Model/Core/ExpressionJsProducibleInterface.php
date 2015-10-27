<?php

namespace Tagcade\Model\Core;


use Tagcade\Model\ModelInterface;

interface ExpressionJsProducibleInterface extends ModelInterface
{

    /**
     * @return array
     */
    public function getDescriptor();
}