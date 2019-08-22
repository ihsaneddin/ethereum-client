<?php
namespace Ihsaneddin\Ethereum\API;

use Ihsaneddin\Ethereum\Interfaces\RequestInterface;

use Ihsaneddin\Ethereum\Traits\SingletonTrait;

use Ihsaneddin\Ethereum\Connection\Http as Connection;

class Http extends Api{

  use SingletonTrait;

  protected function boot_construct(){
    $this->connection();
  }

  protected function connection_class(){ return Connection::class; }

  public function poll(string $method, $params=array(), $delay=0, $poll_until=null){

    /*if (!method_exists($this, $method));
    {
      throw new \InvalidArgumentException("method not found");
    }*/

    if (!isset($params["on_message"]) || !is_callable($params["on_message"])){
      throw new \InvalidArgumentException("Expect params 'on_message' closure");
    }

    $default = ["method" => $method, "type" => "post"];

    $options = array_merge($params, $default);

    $this->connection->poll($options, $delay, $poll_until);
  }

}