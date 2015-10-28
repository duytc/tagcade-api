<?php

namespace Tagcade\Service\Cdn;

use Touki\FTP\Connection\AnonymousConnection;
use Touki\FTP\Connection\Connection;

/**
 * @return AnonymousConnection|Connection
 */
interface FtpConnectionFactoryInterface {

    public function getConnection();

} 