<?php
namespace Ihsaneddin\Ethereum\Interfaces;

use Illuminate\Support\Collection;
use Predis\Client as Redis;

interface RedisInterface{

  public function redis() : Redis;

  public function push_to_redis(string $key, $value);

  public function pull_from_redis(string $key);

}