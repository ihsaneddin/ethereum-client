<?php
namespace Ihsaneddin\Ethereum\Traits;

trait EthPubSubModuleTrait {

  public function eth_subscribe(array $params=array()){
    if (method_exists($this->connection(), 'subscribe')){
      $this->connection()->subscribe(array_merge($params,array("method" => __FUNCTION__)) );
    }else{
      return $this->post(__FUNCTION__, $params);
    }
  }

  public function eth_unsubscribe(array $params= array()){
    return $this->post(__FUNCTION__, $params);
  }

}