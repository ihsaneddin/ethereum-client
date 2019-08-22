<?php
namespace Ihsaneddin\Ethereum\Traits;

use Illuminate\Support\Collection;

trait ConfigTrait{

  protected $config;

  protected function setup_config(array $config=array(), $file='config.php'){
    $default = include $file;
    $this->config = collect($default)->merge($config);
  }

  public function set_config(array $config){
    $this->setup_config($config);
  }

  public function config() : Collection {
    if (is_null($this->config))
      $this->setup_config();
    return is_null($this->config) ? collect() : $this->config;
  }

  public function config_for(string $key, $default=null){
    return $this->config->get($key, $default);
  }

}