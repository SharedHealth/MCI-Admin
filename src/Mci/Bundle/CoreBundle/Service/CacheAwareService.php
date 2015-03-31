<?php
namespace Mci\Bundle\CoreBundle\Service;

use Doctrine\Common\Cache\Cache;

class CacheAwareService
{
    /** @var  Cache */
    private $cache;

    /**
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param Cache $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

}