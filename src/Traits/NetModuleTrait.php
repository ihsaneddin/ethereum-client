<?php
namespace Ihsaneddin\Ethereum\Traits;

trait NetModuleTrait {

  public function net_listening(){
    return $this->post(__FUNCTION__);
  }

  public function net_peerCount(){
    return $this->post(__FUNCTION__);
  }

  public function net_version(){
    return $this->post(__FUNCTION__);
  }

}