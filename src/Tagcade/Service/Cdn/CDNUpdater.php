<?php

namespace Tagcade\Service\Cdn;

use Tagcade\Cache\V2\TagCacheV2Interface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Touki\FTP\Connection\AnonymousConnection;
use Touki\FTP\Connection\Connection;
use Touki\FTP\ConnectionInterface;
use Touki\FTP\FTPWrapper;

class CDNUpdater implements CDNUpdaterInterface
{
    /**
     * @var ConnectionInterface|Connection
     */
    private $ftpConnection;

    const AD_SLOT_DIR = 'ad_slot_path';
    const RON_AD_SLOT_DIR = 'ron_ad_slot_path';

    const CDN_TEMPLATE = '[%%JSON_P_BEGIN%%]%s[%%JSON_P_END%%]';

    /**
     * @var
     */
    private $config;
    /**
     * @var TagCacheV2Interface
     */
    private $tagCache;

    function __construct(ConnectionInterface $ftpConnection, TagCacheV2Interface $tagCache,  array $config)
    {
        $this->ftpConnection = $ftpConnection;

        if (!array_key_exists(self::AD_SLOT_DIR, $config) || is_null($config[self::AD_SLOT_DIR])) {
            throw new InvalidArgumentException('invalid cdn ad slot directory configuration');
        }

        if (!array_key_exists(self::RON_AD_SLOT_DIR, $config) || is_numeric($config[self::RON_AD_SLOT_DIR])) {
            throw new InvalidArgumentException('invalid cdn ad slot directory configuration');
        }

        $this->config = $config;
        $this->tagCache = $tagCache;
    }

    public function pushAdSlot($adSlotId, $closeConnection = true)
    {
        // Creating stream resource
        $adTags = $this->tagCache->getAdTagsForAdSlot($adSlotId);
        if(false === $adTags) {
            return false; // nothing to push to cdn
        }

        $remotePath = sprintf('%s/%d', $this->config[self::AD_SLOT_DIR], $adSlotId);

        return $this->doPushToCdn($adSlotId, $remotePath, $adTags, $ronSlot = false, $closeConnection);
    }

    public function pushMultipleAdSlots(array $adSlots)
    {
        $adSlotPushedCount = 0;
        $adSlots = array_filter($adSlots, function($adSlot){
            return is_numeric($adSlot) && (int)$adSlot > 0;
        });

        foreach ($adSlots as $adSlotId) {
            if ($this->pushAdSlot($adSlotId, $closeConnection = false)) {
                $adSlotPushedCount ++;
            }

            usleep(50);
        }

        $this->closeFtpConnection();

        return $adSlotPushedCount;
    }

    public function pushRonSlot($ronSlotId, $closeConnection = true)
    {
        // Creating stream resource
        $ronAdTags = $this->tagCache->getAdTagsForRonAdSlot($ronSlotId);
        if(false === $ronAdTags) {
            return false; // nothing to push to cdn
        }

        $remotePath = sprintf('%s/%d', $this->config[self::RON_AD_SLOT_DIR], $ronSlotId);

        return $this->doPushToCdn($ronSlotId, $remotePath, $ronAdTags, $ronSlot = true, $closeConnection);
    }

    public function pushMultipleRonSlots(array $ronSlots)
    {
        $ronAdSlotPushedCount = 0;
        $ronSlots = array_filter($ronSlots, function($ronSlot) {
            return is_numeric($ronSlot) && (int)$ronSlot > 0;
        });

        foreach ($ronSlots as $ronSlot) {
            if ($this->pushRonSlot($ronSlot, $closeConnection = false)) {
                $ronAdSlotPushedCount ++;
            }

            usleep(50);
        }

        $this->closeFtpConnection();

        return $ronAdSlotPushedCount;
    }

    public function closeFtpConnection()
    {
        try {
            if (!$this->ftpConnection instanceof ConnectionInterface) {
                return;
            }

            if (!$this->ftpConnection->isConnected()) {
                return;
            }

            $this->ftpConnection->close();
        }
        catch(\Exception $e) {
        }
    }

    protected function doPushToCdn($id, $remotePath, array $data, $ronSlot = false, $closeConnection = false) {
        try {

            $cdnData = $this->createCdnTranslatedData($data);
            $stream = $this->createFtpStreamFromString($cdnData);

            $ftpWrapper = new FTPWrapper($this->ftpConnection);
            if (!$this->ftpConnection->isConnected()) {
                $this->ftpConnection->open();
            }

            $result = $ftpWrapper->fput($remotePath , $stream);
            if ($result) {
                $this->tagCache->refreshCacheForCdn($id, $cdnData, $ronSlot);
            }

            if (true === $closeConnection) {
                $this->closeFtpConnection();
            }
        }
        catch(\Exception $e) {
            $result = false;
            $this->closeFtpConnection();
        }

        if (true != $result) {
            throw new RuntimeException(sprintf('Could not push data to cdn server. Please make sure your connection or ad slot remote folder %s existence', $remotePath));
        }

        return $result;
    }

    /**
     * Convert string to stream
     * @param $myData
     * @return resource
     */
    protected function createFtpStreamFromString($myData)
    {
        if (!is_string($myData)) {
            throw new InvalidArgumentException('Expect string data');
        }

        $stream = fopen('php://memory','r+');
        fwrite($stream, $myData);
        rewind($stream);

        return $stream;
    }

    /**
     * Translate data containing serverVars to Cnd vars
     * @param array $data
     * @return string
     */
    protected function createCdnTranslatedData(array $data)
    {
        $myData = $this->replaceServerVarsToCdnVars($data);

        return sprintf(self::CDN_TEMPLATE, json_encode($myData));
    }

    protected function replaceServerVarsToCdnVars(array &$data)
    {
        // only replace with dynamic ad slot or dynamic ron slot
        if (!array_key_exists('type', $data) || $data['type'] != 'dynamic') {
            return $data;
        }

        foreach ($data['expressions'] as &$expression) {
            $temp = preg_replace('/\\${COUNTRY}/', '"[%COUNTRY%]"', $expression['expression']);
            $expression['expression'] = $temp;
        }

        return $data;
    }
}