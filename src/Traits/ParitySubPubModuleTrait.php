<?php
namespace Ihsaneddin\Ethereum\Traits;

trait ParitySubPubModuleTrait{

  public function parity_subscribe(array $params=array()){
    if (method_exists($this, 'subscribe')){
      $this->subscribe($params);
    }else{
      return $this->post(__FUNCTION__, $params);
    }
  }

  public function parity_unsubscribe(array $params= array()){
    return $this->post(__FUNCTION__, $params);
  }

}