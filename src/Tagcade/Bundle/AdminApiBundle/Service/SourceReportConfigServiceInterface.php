<?php

namespace Tagcade\Bundle\AdminApiBundle\Service;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Bundle\AdminApiBundle\Model\SourceReportEmailConfigInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface SourceReportConfigServiceInterface
{
    /**
     * generate source_report_config JSON from $emailConfigs
     *
     * @return array:
     *
     * {
     *      "reports": {
     *            "yoursite.com": {
     *                  "domain": "yoursite.com",
     *                  "username": "myuser",
     *                  "pub_id": 1,
     *                  "site_id": 1
     *            },
     *            "yoursite1.com": {
     *                  "domain": "yoursite.com",
     *                  "username": "myuser",
     *                  "pub_id": 1,
     *                  "site_id": 2
     *            },
     *              "yoursite2.com": {
     *                  "domain": "yoursite.com",
     *                  "username": "myuser",
     *                  "pub_id": 2,
     *                  "site_id": 3
     *            }
     *     },
     *
     *     "recipients": [
     *         {
     *             "email": "youremail@address.com",
     *             "reports": ["*"]
     *         },
     *         {
     *             "email": "youremail@address.com",
     *             "reports": ["yoursite.com"]
     *         }
     *     ]
     * }
     */
    public function getAllSourceConfigAsJSON();
} 