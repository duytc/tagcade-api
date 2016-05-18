<?php


namespace Tagcade\Model\Report\UnifiedReport\Network;
use Tagcade\Model\Report\UnifiedReport\ReportInterface;
interface NetworkDomainAdTagReportInterface extends ReportInterface
{
    /**
     * @return mixed
     */
    public function getDomain();

    /**
     * @param mixed $domain
     * @return self
     */
    public function setDomain($domain);
}