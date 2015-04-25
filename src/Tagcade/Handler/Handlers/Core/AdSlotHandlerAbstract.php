<?php

namespace Tagcade\Handler\Handlers\Core;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Handler\RoleHandlerAbstract;
use Tagcade\Model\Core\AdSlotInterface;

abstract class AdSlotHandlerAbstract extends RoleHandlerAbstract
{
    /**
     * @inheritdoc
     *
     * Auto complete helper method
     *
     * @return AdSlotManagerInterface
     */
    protected function getDomainManager()
    {
        return parent::getDomainManager();
    }

    /**
     * get all AdSlotConfig as json_array
     * @param AdSlotInterface $adSlot
     * @return string - json_array
     */
    public function getAdSlotVariableDescriptor(AdSlotInterface $adSlot)
    {
        return $adSlot->getVariableDescriptor();
    }

    /**
     * get AdSlot Expression for adSlot
     *
     * variableDescriptor:
     * {
     *    "expressions":[
     *       {
     *          "expression":{
     *             "AND":[
     *                {
     *                   "var":"a",
     *                   "cmp":">",
     *                   "val":"1"
     *                },
     *                {
     *                   "OR":[
     *                      {
     *                         "var":"b",
     *                         "cmp":"<=",
     *                         "val":"2"
     *                      },
     *                      {
     *                         "var":"c",
     *                         "cmp":"==",
     *                         "val":"true"
     *                      }
     *                   ]
     *                },
     *                {
     *                   "var":"d",
     *                   "cmp":"!=",
     *                   "val":"abc"
     *                }
     *             ]
     *          },
     *          "expectVal":"1"
     *       },
     *       {
     *          "expression":{
     *             "AND":[
     *                {
     *                   "var":"e",
     *                   "cmp":">",
     *                   "val":"1"
     *                },
     *                {
     *                   "var":"f",
     *                   "cmp":">",
     *                   "val":"1"
     *                }
     *             ]
     *          },
     *          "expectVal":"2"
     *       }
     *    ],
     *    "defaultVal":"3"
     * }
     *
     * to expressions (built automatically preUpdate/prePersis event):
     * "
     * {
     *   'expressions' :
     *   [
     *     {
     *       'expression' : '($a > '1' and ($b <= '3' or $c == 'true') and $d != 'abc'',
     *       'expectVal' : '1'
     *     },
     *     {
     *       'expression' : '$e > '1' and $f > '1'',
     *       'expectVal' : '2';
     *     },
     *   ],
     *   'defaultVal' : '3';
     * }
     * "
     *
     * @param AdSlotInterface $adSlot
     * @return string
     */
    public function getAdSlotConfigExpression(AdSlotInterface $adSlot)
    {
        return $adSlot->getExpressions();
    }

    /**
     * add all AdSlotConfig for AdSlot
     * @param AdSlotInterface $adSlot
     * @param array $adSlotConfigs
     * @return boolean
     */
    public function updateVariableDescriptor(AdSlotInterface $adSlot, array $adSlotConfigs)
    {
        //check if enable variable
        if (null === $adSlot || !$adSlot->getEnableVariable()
        ) {
            return false;
        }

        //validate adSlotConfigs (formatted as json): already at form submit

        //save
        $adSlot->setVariableDescriptor($adSlotConfigs);
        $this->getDomainManager()->save($adSlot);

        return true;
    }
}
