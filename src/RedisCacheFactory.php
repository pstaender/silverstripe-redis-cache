<?php

namespace Zeitpulse;

use Predis\Client;
use SilverStripe\Core\Cache\CacheFactory;
use SilverStripe\Core\Injector\Injector;
use Symfony\Component\Cache\Simple\RedisCache;

class RedisCacheFactory implements CacheFactory
{
    protected $redis_client;

    public function __construct(Client $redis_client)
    {
        $this->redis_client = $redis_client;
    }

    public function create($service, array $params = array())
    {
        $namespace = isset($params['namespace'])
            ? $params['namespace'] . '_' . md5(BASE_PATH)
            : md5(BASE_PATH);

        $defaultLifetime = isset($params['defaultLifetime'])
            ? $params['defaultLifetime']
            : 0;

        return Injector::inst()
            ->createWithArgs(
                RedisCache::class,
                [
                    $this->redis_client,
                    $namespace,
                    $defaultLifetime,
                ]
            );
    }
}
