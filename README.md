# Redis Cache for SilverStripe

Enables usage of redis cache for SilverStripe.

![status](https://github.com/pstaender/silverstripe-redis-cache/actions/workflows/ci.yml/badge.svg)

## Requirements

  * SilverStripe 4.0.0 or higher
  * PHP 7 or higher
  * Redis

## Pre-install

Ensure you have redis installed in your environment and configured within your php ini files to ensure PHP knows where to access Redis.

To install Redis, a quick Google with your OS name and version, your PHP version, and server and it's version that your're working with (E.G. Apache or NGINX) should yield a number of installation instructions such as [this installation instruction example.](https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-redis-on-ubuntu-18-04)

Add the following to your php.ini or conf.d/{your-custom-php-config-file}.ini to let PHP know where to communicate with Redis to store session data:
**Note:** Missing this step will lead to login issues when working with providers such as AWS where you have your site running on multiple pods but need sessions to be unified across them.
```
session.save_handler  = redis
session.save_path     = {your_redis_url}
```

## Installation and usage

Use composer to pull this module into your project:

```
  $ composer require pstaender/silverstripe-redis-cache dev-master
```

To enable Redis cache in your SilverStripe project, add one or both of the following yaml configs to your project under `/app/_config/` in either their own yaml file, or in an existing file such as `mysite.yml`.

**Note:** The `REDIS_URL` must be the url of the used redis instance, e.g. `tcp://127.0.0.1:6379`.

## Usage in your project

```yml
---
Name: silverstripe-redis-cache
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

  # vendor/silverstripe/assets/_config/assetscache.yml
  Psr\SimpleCache\CacheInterface.InterventionBackend_Manipulations:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.FileShortcodeProvider:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.ImageShortcodeProvider:
    factory: RedisCacheFactory

  # vendor/silverstripe/assets/_config/assetscache.yml
  Psr\SimpleCache\CacheInterface.Sha1FileHashingService:
    factory: RedisCacheFactory

  # vendor/silverstripe/cms/_config/cache.yml
  Psr\SimpleCache\CacheInterface.CMSMain_SiteTreeHints:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.SiteTree_CreatableChildren:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.SiteTree_PageIcons:
    factory: RedisCacheFactory

  # vendor/silverstripe/cms/_config/permissions.yml +
  # vendor/silverstripe/framework/_config/cache.yml
  Psr\SimpleCache\CacheInterface.InheritedPermissions:
    factory: RedisCacheFactory

  # vendor/silverstripe/framework/_config/cache.yml
  Psr\SimpleCache\CacheInterface.cacheblock:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.VersionProvider_composerlock:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.RateLimiter:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.ThemeResourceLoader:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.DatabaseAdapterRegistry:
    factory: RedisCacheFactory
  Psr\SimpleCache\CacheInterface.EmbedShortcodeProvider:
    factory: RedisCacheFactory
```

## Usage with flysystem asset storage

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

## Licence

MIT
