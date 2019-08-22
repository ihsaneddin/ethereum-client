<?php
namespace Ihsaneddin\Ethereum\Ethplorer\Traits;

trait TokenTrait{

  public function getTokenInfo(string $address){
    return $this->post(__FUNCTION__, $address);
  }

}