<?php

namespace Tagcade\Entity\Core;

use Tagcade\Model\Core\Blacklist as BlacklistModel;

class Blacklist extends BlacklistModel
{
    protected $id;
    protected $name;
    protected $domains;
    protected $publisher;
    protected $suffixKey;
}