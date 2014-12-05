<?php
namespace Tagcade\Tests\Service\Report\PerformanceReport\Display\Billing;

use Tagcade\Bundle\UserBundle\Entity\User;
use Tagcade\Model\User\Role\Publisher;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculator;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\CpmRateGetter;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\CpmRateGetterInterface;

class BillingCalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BillingCalculatorInterface
     */
    protected $billingCalculator;

    /**
     * @var CpmRateGetterInterface
     */
    protected $cpmRateGetter;

    public function setUp()
    {
        $thresholds = [ ['threshold' => 100000, 'cpmRate' => 0.5], ['threshold' => 200000, 'cpmRate' => 0.2], ['threshold' => 150000, 'cpmRate' => 0.3] ];
        $billingConfigs = CpmRateGetter::createConfig($thresholds);

        $cpmRate = new CpmRateGetter(30, $billingConfigs);
        $billingCalculator = new BillingCalculator($cpmRate);

        $this->cpmRateGetter = $cpmRate;
        $this->billingCalculator = $billingCalculator;

    }

    public function testCustomRate()
    {
        $user = new User();
        $user->setBillingRate(0.01);
        $user->addRole('ROLE_PUBLISHER');
        $publisher = new Publisher($user);

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($publisher, 160000);
        $this->assertEquals(0.01, $rateAmount->getRate());
        $this->assertEquals(1.6000, round($rateAmount->getAmount(), 4));
    }

    public function testRateInRange()
    {
        $user = new User();
        $user->addRole('ROLE_PUBLISHER');
        $publisher = new Publisher($user);

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($publisher, 160000);
        $this->assertEquals(0.3, $rateAmount->getRate());
        $this->assertEquals(48, $rateAmount->getAmount());
    }

    public function testRateOutOfRange()
    {
        $user = new User();
        $user->addRole('ROLE_PUBLISHER');
        $publisher = new Publisher($user);

        $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisher($publisher, 100);
        $this->assertEquals(30, $rateAmount->getRate());
        $this->assertEquals(3, $rateAmount->getAmount());
    }
}