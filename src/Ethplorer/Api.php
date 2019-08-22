<?php
namespace Ihsaneddin\Ethereum\Ethplorer;

use Ihsaneddin\Ethereum\Interfaces\ApiInterface;
use Ihsaneddin\Ethereum\Interfaces\RequestInterface;
use Ihsaneddin\Ethereum\API\Api as BaseApi;
use Ihsaneddin\Ethereum\Ethplorer\Traits\TokenTrait;
use Ihsaneddin\Ethereum\Traits\SingletonTrait;

use Illuminate\Support\Collection;

class Api extends BaseApi {

  use TokenTrait;
  use SingletonTrait;

  protected $connection;

  protected function boot_construct(){
    $this->connection();
  }

  public function connection() : RequestInterface{
    if (!($this->connection instanceOf RequestInterface)) {
      $connection_class = $this->connection_class();
      $this->connection = new $connection_class;
    }
    return $this->connection;
  }

  protected function post(string $endpoint, $params=array()){
    return $this->connection->post(array_merge(array( "params" => $params), array("endpoint" => $endpoint)));
  }

  protected function get(string $endpoint, $params=array()){
   return $this->connection->get(array_merge($params, array("endpoint" => $endpoint)));
  }

  protected function connection_class(){
    return Http::class;
  }

}