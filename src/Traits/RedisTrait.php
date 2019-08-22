<?php
namespace Ihsaneddin\Ethereum\Traits;

use Predis\Client as Redis;

trait RedisTrait{

  protected $redis;

  public function redis() : Redis {
    if ($this->redis instanceOf Redis) return $this->redis;

    return $this->redis = new Redis($this->redis_options());
  }

  abstract protected  function redis_options() : array ;

  public function push_to_redis(string $key, $value){
    return $this->redis()->set($key, $value);
  }

  public function pull_from_redis(string $key){
    return $this->redis()->get($key);
  }

}