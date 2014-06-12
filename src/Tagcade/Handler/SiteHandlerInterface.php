<?php

namespace Tagcade\Handler;

use Tagcade\Model\SiteInterface;

interface SiteHandlerInterface
{
    /**
     * Get a Site.
     *
     * @param mixed $id
     *
     * @return SiteInterface
     */
    public function get($id);

    /**
     * Get a list of Sites.
     *
     * @param int $limit the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Create a new Site.
     *
     * @param array $parameters
     *
     * @return SiteInterface
     */
    public function post(array $parameters);

    /**
     * Edit a Site.
     *
     * @param SiteInterface $site
     * @param array $parameters
     *
     * @return SiteInterface
     */
    public function put(SiteInterface $site, array $parameters);

    /**
     * Partially update a Site.
     *
     * @param SiteInterface $site
     * @param array $parameters
     *
     * @return SiteInterface
     */
    public function patch(SiteInterface $site, array $parameters);
}