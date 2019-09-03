# Redis Cache for SilverStripe

Enables usage of redis cache for SilverStripe.

## Installation and usage

```
  $ composer require pstaender/silverstripe-redis-cache dev-master
```

To enable it in your project add this to your project's config:

```yml
---
After:
  - '#corecache'
  - '#assetscache'
Only:
  envvarset: REDIS_URL
---
SilverStripe\Core\Injector\Injector:
  RedisClient:
    class: Predis\Client
    constructor:
      0: '`REDIS_URL`'
  RedisCacheFactory:
    class: Zeitpulse\RedisCacheFactory
    constructor:
      client: '%$RedisClient'
  SilverStripe\Core\Cache\CacheFactory: '%$RedisCacheFactory'
  Psr\SimpleCache\CacheInterface.InterventionBackend_Manipulations:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.FileShortcodeProvider:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.ImageShortcodeProvider:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.Sha1FileHashingService:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.CMSMain_SiteTreeHints:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.SiteTree_CreatableChildren:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.cacheblock:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.VersionProvider_composerlock:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.RateLimiter:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.InheritedPermissions:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.ThemeResourceLoader:
    factory: RedisCacheFactory
```

The `REDIS_URL` must be the url of the used redis instance, e.g. `tcp://127.0.0.1:6379`.

## Usage with flyststem asset storage

```yaml
---
Name: silverstripes3-flysystem-redis
Only:
  envvarset:
    - REDIS_URL
After:
  - '#silverstripes3-flysystem'
---
SilverStripe\Core\Injector\Injector:
  League\Flysystem\Cached\Storage\Memory.public:
    class: League\Flysystem\Cached\Storage\Predis
  League\Flysystem\Cached\Storage\Adapter.public:
    class: League\Flysystem\Cached\Storage\Predis
  League\Flysystem\Cached\Storage\Adapter.protected:
    class: League\Flysystem\Cached\Storage\Predis
```

LICENSE: MIT
