<?php


namespace Tagcade\Service;


use Tagcade\Exception\InvalidArgumentException;

trait StringUtilTrait
{
    /*
     * domain = abc.def.ghi => length = 3+1+3+1+3 = 11, domain labels = [abc, def, ghi]
     */
    public static $DOMAIN_MIN_LENGTH = 4; // minimum is 'a.bc'
    public static $DOMAIN_MAX_LENGTH = 255; // include '.' and all labels. Format: (63 letters).(63 letters).(63 letters).(62 letters)

    /**
     * Check if the given string is a valid domain
     *
     * @param $domain
     * @param int $maxSubDomains
     * @return bool
     */
    protected function validateDomain($domain, $maxSubDomains = 6)
    {
        // sure domain is string
        if (!is_string($domain)) {
            return false;
        }

        // validate domain length
        if (strlen($domain) < self::$DOMAIN_MIN_LENGTH || strlen($domain) > self::$DOMAIN_MAX_LENGTH) {
            return false;
        }

        // validate domain labels number
        $labels = explode('.', $domain);
        if (count($labels) < 1 || count($labels) > $maxSubDomains) {
            return false;
        }

        // validate overview format
        // also validate domain labels length with regex {1,63}, {2,62}
        return preg_match('/^(?:[-A-Za-z0-9]{1,63}+\.)+[A-Za-z]{2,62}$/', $domain) > 0;
    }

    /**
     * Check if the given string is a valid domain but allow wildcard
     *
     * @param $domain
     * @param int $maxSubDomains
     * @return bool
     */
    protected function validateDomainAllowWildcard($domain, $maxSubDomains = 6)
    {
        // sure domain is string
        if (!is_string($domain)) {
            return false;
        }

        // validate domain length
        if (strlen($domain) < self::$DOMAIN_MIN_LENGTH || strlen($domain) > self::$DOMAIN_MAX_LENGTH) {
            return false;
        }

        // validate domain labels number
        $labels = explode('.', $domain);
        if (count($labels) < 1 || count($labels) > $maxSubDomains) {
            return false;
        }

        // also validate to allow wildcard domain
        return preg_match('/^(?:[\*]?\.)?(?:[-A-Za-z0-9]{1,63}+\.)+[A-Za-z]{2,62}$/', $domain) > 0;
    }

    /**
     * @param $domain
     * @return bool
     */
    protected function validateDomainWithHTTP($domain) {
        $domain = strtolower($domain);
        $domain = str_replace(" ", "", $domain);

        if ((strpos($domain, "http://") !== false) || (strpos($domain, "https://") !== false)) {
            return true;
        }

        return false;
    }

    /**
     * @param $domain
     * @param $throwException
     * @return mixed|string
     */
    protected function extractDomain($domain, $throwException = true)
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
        if (!empty($domain)){
            if (!$this->validateDomain($domain)) {
                if ($throwException === true) {
                    throw new InvalidArgumentException(sprintf('The value "%s" is not a valid domain.', $domain));
                } else {
                    $domain = false;
                }
            }
        }

        
        $domain = strtolower($domain);

        return $domain;
    }

    /**
     * @param $domain
     * @param $throwException
     * @return mixed|string
     */
    protected function extractDomainAllowWildcard($domain, $throwException = true)
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

        if (!empty($domain)) {
            if (!$this->validateDomainAllowWildcard($domain)) {
                if ($throwException === true) {
                    throw new InvalidArgumentException(sprintf('The value "%s" is not a valid domain.', $domain));
                } else {
                    $domain = false;
                }
            }
        }

        $domain = strtolower($domain);

        return $domain;
    }

    protected function normalizeName($name)
    {
        $name = strtolower($name);

        $string = str_replace(' ', '-', $name); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    public static function generateUuidV4() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,
            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * extract parent id form formatted string
     * @param $formattedResult
     * @return mixed
     * @throws \Exception
     */
    public function extractParentId($formattedResult)
    {
        // validate input format
        $tmp = explode(':', $formattedResult);
        if (!is_array($tmp) || count($tmp) < 2) {
            throw new \Exception('Not valid input format');
        }

        $parentId = $tmp[0];

        return $parentId;
    }
}