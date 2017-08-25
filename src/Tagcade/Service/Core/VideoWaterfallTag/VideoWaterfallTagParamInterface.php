<?php

namespace Tagcade\Service\Core\VideoWaterfallTag;

interface VideoWaterfallTagParamInterface
{
    const PARAM_SECURE = 'secure';
    const PARAM_MACROS = 'macros';

    /**
     * @return boolean
     */
    public function isSecure();

    /**
     * @param boolean $secure
     * @return self
     */
    public function setSecure($secure);

    /**
     * @return array
     */
    public function getMacros();

    /**
     * @param array $macros
     * @return self
     */
    public function setMacros(array $macros);
}