<?php

namespace Tagcade\Service\Cdn;


use Touki\FTP\Connection\AnonymousConnection;
use Touki\FTP\Connection\Connection;

class FtpConnectionFactory implements FtpConnectionFactoryInterface
{
    const ENABLE_SECURITY = 'secured';
    const HOST = 'host';
    const USER_NAME = 'username';
    const PASSWORD = 'username';
    const PORT = 'port';
    const TIME_OUT = 'timeout';
    /**
     * @var array
     */
    private $config;

    function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return AnonymousConnection|Connection
     */
    public function getConnection()
    {
        if (true === $this->config[self::ENABLE_SECURITY]) {
            return new Connection($this->config[self::HOST], $this->config[self::USER_NAME], $this->config[self::PASSWORD],  $this->config[self::PORT], $this->config[self::TIME_OUT]);
        }

        return new AnonymousConnection($this->config[self::HOST], $this->config[self::PORT], $this->config[self::TIME_OUT]);
    }
}