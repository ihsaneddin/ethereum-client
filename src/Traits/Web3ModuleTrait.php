<?php
namespace Ihsaneddin\Ethereum\Traits;

trait Web3ModuleTrait {

  public function web3_clientVersion(){
    return $this->post(__FUNCTION__);
  }

  public function web3_sha3(string $string){
    return $this->post(__FUNCTION__, array($string));
  }

}