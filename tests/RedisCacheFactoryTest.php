<?php

namespace Zeitpulse\Tests\CacheTest;

use SilverStripe\Core\Injector\Injector;
use SilverStripe\Dev\SapphireTest;

class RedisCacheFactoryTest extends SapphireTest
{
    protected function setUp(): void
    {
        parent::setUp();
        Injector::inst()
            ->load([
                'RedisClient' => [
                    'class' => \Predis\Client::class,
                    'constructor' => [
                        'REDIS_URL'
                    ],
                ],
                'RedisCacheFactory' => [
                    'class' => \Zeitpulse\RedisCacheFactory::class,
                    'constructor' => [
                        'client' => '%$RedisClient'
                    ]
                ],
            ]);
    }

    public function testRedisCacheFactory()
    {
        $cache = Injector::inst()->get('RedisCacheFactory');
        $class = new \ReflectionClass($cache);
        $this->assertEquals((array) $class->getProperty('redis_client'), [
            'name' => 'redis_client',
            'class' => 'Zeitpulse\RedisCacheFactory',
        ]);
    }

    public function testRedisCacheWithRedisDatabase()
    {
        $cache = Injector::inst()
            ->createWithArgs(
                \Symfony\Component\Cache\Simple\RedisCache::class,
                [
                    new \Predis\Client(''),
                    'TestRedisCache_' . sha1(BASE_PATH),
                    1000
                ]
            );
        $cache->set('foo', 'bar');
        $this->assertEquals($cache->get('foo'), 'bar');
    }
}
