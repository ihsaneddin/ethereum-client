<?php
namespace Ihsaneddin\Ethereum\API;

use Ihsaneddin\Ethereum\Interfaces\RequestInterface;

use Ihsaneddin\Ethereum\Traits\SingletonTrait;

use Ihsaneddin\Ethereum\Connection\WebSocket as Connection;

class WebSocket extends Api{

  use SingletonTrait;

  protected function boot_construct(){
    $this->connection();
  }

  protected function connection_class(){ return Connection::class; }

}