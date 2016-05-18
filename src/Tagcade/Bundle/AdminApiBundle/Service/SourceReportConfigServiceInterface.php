<?php

namespace Tagcade\Bundle\AdminApiBundle\Service;

interface SourceReportConfigServiceInterface
{
    /**
     * generate source_report_config JSON from $emailConfigs
     *
     * @return array:
     *
     * {
     *      "reports": {
     *            "yourSite.com": {
     *                  "domain": "yourSite.com",
     *                  "username": "myUser",
     *                  "pub_id": 1,
     *                  "site_id": 1
     *            },
     *            "yourSite1.com": {
     *                  "domain": "yourSite.com",
     *                  "username": "myUser",
     *                  "pub_id": 1,
     *                  "site_id": 2
     *            },
     *              "yourSite2.com": {
     *                  "domain": "yourSite.com",
     *                  "username": "myUser",
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
     *             "reports": ["yourSite.com"]
     *         }
     *     ]
     * }
     */
    public function getAllSourceReportConfig();
}