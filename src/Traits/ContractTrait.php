<?php
namespace Ihsaneddin\Ethereum\Traits;

use Ihsaneddin\Ethereum\Contracts\Contract;

trait ContractTrait {

  function init_contract(string $address, array $abi=array()){
    if (empty($abi))
      $abi = $this->etherscan()->getabi($address)->result();

    if (is_array($abi)){
      $abi = json_encode($abi);
    }

    $contract = new Contract($abi);
    $contract->at($address);
    return $contract;
  }

}