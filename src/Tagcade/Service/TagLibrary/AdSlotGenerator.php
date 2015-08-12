<?php
namespace Tagcade\Service\TagLibrary;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\Expression;
use Tagcade\Entity\Core\NativeAdSlot;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

class AdSlotGenerator implements AdSlotGeneratorInterface
{

    /**
     * @var AdSlotManagerInterface
     */
    private $adSlotManager;

    public function setAdSlotManager(AdSlotManagerInterface $adSlotManager) {
        $this->adSlotManager = $adSlotManager;
    }
    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @param SiteInterface $site
     *
     * @return DynamicAdSlotInterface
     */
    public function getProspectiveDynamicAdSlotForLibraryAndSite(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot, SiteInterface $site)
    {
        $dynamicAdSlot = new DynamicAdSlot();
        $dynamicAdSlot->setSite($site);


        $this->generateTrueDefaultAdSlotAndExpressionsForLibraryDynamicAdSlotBySite($libraryDynamicAdSlot, $site);

        $libraryExpressions = $libraryDynamicAdSlot->getLibraryExpressions();
        /**
         * @var LibraryExpressionInterface $libraryExpression
         */
        foreach($libraryExpressions as $libraryExpression) {
            $expectAdSlot = $this->adSlotManager->getReferencedAdSlotsForSite($libraryExpression->getExpectLibraryAdSlot(), $site);
            if (!$expectAdSlot instanceof ReportableAdSlotInterface) {
                throw new LogicException('expect existing expect ad slot for expression');
            }

            $libraryExpression->getExpressions()->clear();
            $expression = new Expression();
            $expression->setExpectAdSlot($expectAdSlot);
            $expression->setLibraryExpression($libraryExpression);

            $libraryExpression->getExpressions()->add($expression);
            $expression->setDynamicAdSlot($dynamicAdSlot);
            $dynamicAdSlot->getExpressions()->add($expression);
        }

        $dynamicAdSlot->setLibraryAdSlot($libraryDynamicAdSlot);
        $defaultAdSlot = $this->adSlotManager->getReferencedAdSlotsForSite($libraryDynamicAdSlot->getDefaultLibraryAdSlot(), $site);
        if (!$defaultAdSlot instanceof ReportableAdSlotInterface) {
            throw new LogicException('expect existing default ad slot for expression');
        }

        $dynamicAdSlot->setDefaultAdSlot($defaultAdSlot);

        return $dynamicAdSlot;
    }

    /**
     * Generate expression and default ad slot associated to this site
     *
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @param SiteInterface $site
     * @return void
     */
    public function generateTrueDefaultAdSlotAndExpressionsForLibraryDynamicAdSlotBySite(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot, SiteInterface $site)
    {
        // Step 1. Get library default ad slot associated this dynamic ad slot and check if there is any ad slot for this site refer to this library
        $defaultLibraryAdSlot = $libraryDynamicAdSlot->getDefaultLibraryAdSlot();
        $currentReference = $this->adSlotManager->getReferencedAdSlotsForSite($defaultLibraryAdSlot, $site);

        // Step 2. generate default ad slot if step 1 return empty (generate default ad slot base on default library ad slot in library dynamic ad slot)
        if (null === $currentReference) {
            $this->createReportableAdSlotForSite($site, $defaultLibraryAdSlot);
        }
        // Step 3. Get expressions associated to this dynamic ad slot
        $libraryExpressions = $libraryDynamicAdSlot->getLibraryExpressions();

        // step 4. generate expect ad slot in expression if no ad slot refer to expect library (generation is based on expect library ad slot in each library expression)
        foreach ($libraryExpressions as $libraryExpression) {
            /**
             * @var LibraryExpressionInterface $libraryExpression
             */
            $libraryExpectAdSlot = $libraryExpression->getExpectLibraryAdSlot();
            $currentReference = $this->adSlotManager->getReferencedAdSlotsForSite($libraryExpectAdSlot, $site);

            if (null === $currentReference) {
                $this->createReportableAdSlotForSite($site, $libraryExpectAdSlot);
            }
        }
    }

    /**
     * Create new display ad slot from site and library
     *
     * @param SiteInterface $site
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return ReportableAdSlotInterface
     */
    protected function createReportableAdSlotForSite(SiteInterface $site, BaseLibraryAdSlotInterface $libraryAdSlot)
    {
        if ($libraryAdSlot instanceof LibraryDynamicAdSlotInterface) {
            throw new InvalidArgumentException('expect display or native library');
        }

        $adSlot = $libraryAdSlot instanceof LibraryDisplayAdSlotInterface ? new DisplayAdSlot() : new NativeAdSlot();
        $adSlot->setLibraryAdSlot($libraryAdSlot);
        $adSlot->setSite($site);
        $this->adSlotManager->save($adSlot);

        return $adSlot;
    }


}