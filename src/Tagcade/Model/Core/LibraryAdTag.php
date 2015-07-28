<?php

namespace Tagcade\Model\Core;


class LibraryAdTag implements LibraryAdTagInterface{

    protected $id;

    protected $html;

    protected $visible = false;
    /** int - type of AdTags*/
    protected $adType = 0;
    /** array - json_array, descriptor of AdTag*/
    protected $descriptor;
    /**
     * @var AdNetworkInterface
     */
    protected $adNetwork;

    protected $adTags;

    protected $referenceName;
    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @inheritdoc
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * @inheritdoc
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }


    /**
     * @inheritdoc
     */
    public function setAdNetwork($adNetwork)
    {
        $this->adNetwork = $adNetwork;
    }

    /**
     * @inheritdoc
     */
    public function getAdNetwork()
    {
        return $this->adNetwork;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * @inheritdoc
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }


    /**
     * @inheritdoc
     */
    public function getVisible()
    {
        return $this->visible;
    }

    function __toString()
    {
        return $this->html;
    }

    /**
     * @inheritdoc
     */
    public function getAdTags()
    {
        return $this->adTags;
    }

    /**
     * This indicate ad tag type: image, custom, etc..
     * get AdType
     * @return int
     */
    public function getAdType()
    {
        return $this->adType;
    }

    /**
     * set AdType
     * @param int $adType
     */
    public function setAdType($adType)
    {
        $this->adType = $adType;
    }

    /**
     * get Descriptor as json_array
     * @return array
     */
    public function getDescriptor()
    {
        return $this->descriptor;
    }

    /**
     * set Descriptor formatted as json_array
     * @param array $descriptor
     */
    public function setDescriptor($descriptor)
    {
        $this->descriptor = $descriptor;
    }

    /**
     * @return mixed
     */
    public function getReferenceName()
    {
        return $this->referenceName;
    }

    /**
     * @param mixed $referenceName
     */
    public function setReferenceName($referenceName)
    {
        $this->referenceName = $referenceName;
    }

    public function isReferenced() {
        return $this->adTags != null && $this->adTags->count() > 0;
    }
}