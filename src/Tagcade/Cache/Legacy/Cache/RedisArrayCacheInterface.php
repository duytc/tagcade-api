<?php

namespace Tagcade\Cache\Legacy\Cache;

use Doctrine\Common\Cache\Cache;

interface RedisArrayCacheInterface extends Cache
{
    /**
     * Gets a value from the hash stored at key.
     * If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
     *
     * @param   string  $hash
     * @param   string  $field
     * @return  string  The value, if the command executed successfully BOOL FALSE in case of failure
     */
    public function hFetch($hash, $field);


    public function hSave($hash, $field, $data);

     /**
      * Removes a values from the hash stored at key.
      * If the hash table doesn't exist, or the key doesn't exist, FALSE is returned.
      *
      * @param   string  $hash
      * @param   string  $field
      *
      * @return  int     Number of deleted fields
      */
    public function hDelete($hash, $field);
}