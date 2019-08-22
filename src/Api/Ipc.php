<?php
namespace Ihsaneddin\Ethereum\API;

use Ihsaneddin\Ethereum\Interfaces\RequestInterface;

use Ihsaneddin\Ethereum\Traits\SingletonTrait;

use Ihsaneddin\Ethereum\Connection\Socket as Connection;

class Socket extends Api{

  use SingletonTrait;

  protected function boot_construct(){
    $this->connection();
  }

  protected function connection_class(){ return Connection::class; }

}