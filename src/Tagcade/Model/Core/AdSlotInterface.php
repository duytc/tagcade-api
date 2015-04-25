<?php

namespace Tagcade\Model\Core;

use Doctrine\Common\Collections\ArrayCollection;
use Tagcade\Model\ModelInterface;

interface AdSlotInterface extends ModelInterface
{
    /**
     * @return SiteInterface|null
     */
    public function getSite();

    /**
     * @param SiteInterface $site
     * @return self
     */
    public function setSite(SiteInterface $site);

    /**
     * @return int|null
     */
    public function getSiteId();


    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string $name
     * @return self
     */
    public function setName($name);

    /**
     * @return int|null
     */
    public function getWidth();

    /**
     * @param int $width
     * @return self
     */
    public function setWidth($width);

    /**
     * @return int|null
     */
    public function getHeight();

    /**
     * @param int $height
     * @return self
     */
    public function setHeight($height);

    /**
     * @return ArrayCollection
     */
    public function getAdTags();

    /**
     * @return boolean
     */
    public function getEnableVariable();

    /**
     * @param boolean $enableVariable
     */
    public function setEnableVariable($enableVariable);

    /**
     * @return array, formatted as array of pair {expression [], expectVal}
     *
     */
    public function getExpressions();

    /**
     * @param array $expressions
     */
    public function setExpressions($expressions);

    /**
     *{
     * defaultVal: 500,
     * expressions: [
     * {
     *   expression: {AND: [],
     *               },
     *   expectVal: 100
     * },
     * {
     *   expression: {OR: [],
     *               },
     *   expectVal: 150
     * },
     * {
     *    expression: {
     *                  var: username,
     *                  cmp: =,
     *                  val: test
     *                 },
     *    expectVal: 200
     * }
     * }
     * ]
     *
     * @return array
     */
    public function getVariableDescriptor();

    /**
     * @param array $variableDescriptor
     */
    public function setVariableDescriptor($variableDescriptor);
}