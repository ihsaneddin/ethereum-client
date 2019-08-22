<?php
namespace Ihsaneddin\Ethereum\Etherscan;

use Ihsaneddin\Ethereum\Interfaces\ApiInterface;
use Ihsaneddin\Ethereum\Interfaces\RequestInterface;
use Ihsaneddin\Ethereum\API\Api as BaseApi;
use Ihsaneddin\Ethereum\Etherscan\Traits\ContractTrait;
use Ihsaneddin\Ethereum\Traits\SingletonTrait;

use Illuminate\Support\Collection;

class Api extends BaseApi {

  use ContractTrait;
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

  protected function post(string $module, $params=array()){
    $params = array_merge($params, array('module' => $module));
    return $this->connection->post($params);
  }

  protected function connection_class(){
    return Http::class;
  }

}