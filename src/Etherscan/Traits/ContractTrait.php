<?php
namespace Ihsaneddin\Ethereum\Etherscan\Traits;

trait ContractTrait {

  public function getabi(string $address){
    return $this->post('contract', array('address' => $address, 'action' => __FUNCTION__));
  }

}