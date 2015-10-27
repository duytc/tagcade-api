<?php


namespace Tagcade\Service;


trait StringUtilTrait
{
    /**
     * Check if the given string is a valid domain
     *
     * @param $domain
     * @return bool
     */
    protected function validateDomain($domain)
    {
        return preg_match('/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/', $domain) > 0;
    }
}