<?php


namespace Tagcade\Service;


use Tagcade\Exception\InvalidArgumentException;

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

    /**
     * @param $domain
     * @return mixed|string
     */
    protected function extractDomain($domain)
    {
        if (false !== stripos($domain, 'http')) {
            $domain = parse_url($domain, PHP_URL_HOST); // remove http part, get only domain
        }

        // remove the 'www' prefix
        if (0 === stripos($domain, 'www.')) {
            $domain = substr($domain, 4);
        }

        $slashPos = strpos($domain, '/');
        if (false !== $slashPos) {
            $domain = substr($domain, 0, $slashPos);
        }

        if (!$this->validateDomain($domain)) {
            throw new InvalidArgumentException(sprintf('The value "%s" is not a valid domain.', $domain));
        }

        return $domain;
    }
}