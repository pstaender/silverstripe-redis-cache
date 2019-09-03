<?php

namespace Zeitpulse;

use Predis\Client;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Cache\CacheFactory;
use Symfony\Component\Cache\Simple\RedisCache;

class RedisCacheFactory implements CacheFactory
{
    protected $redisClient;

    public function __construct(Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    public function create($service, array $params = array())
    {
        $namespace = isset($params['namespace'])
            ? $params['namespace'].'_'.md5(BASE_PATH)
            : md5(BASE_PATH);
        $defaultLifetime = isset($params['defaultLifetime']) ? $params['defaultLifetime'] : 0;

        return Injector::inst()->createWithArgs(RedisCache::class, [
            $this->redisClient,
            $namespace,
            $defaultLifetime,
        ]);
    }
}
