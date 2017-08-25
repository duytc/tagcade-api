<?php

namespace Tagcade\Service\Core\VideoWaterfallTag;

class VideoWaterfallTagParam implements VideoWaterfallTagParamInterface
{
    /** @var  bool */
    private $secure;

    /** @var  array */
    private $macros;

    /**
     * @inheritdoc
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @inheritdoc
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMacros()
    {
        return $this->macros;
    }

    /**
     * @inheritdoc
     */
    public function setMacros(array $macros)
    {
        $this->macros = $macros;

        return $this;
    }
}