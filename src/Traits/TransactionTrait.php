<?php
namespace Ihsaneddin\Ethereum\Traits;

use Ihsaneddin\Ethereum\Entities\Transaction;
use Ihsaneddin\Ethereum\Entities\Account;

use Carbon\Carbon;

trait TransactionTrait{

  public function find_tx(string $hash){
    return Transaction::find($hash);
  }

  public function get_current_block(){
    return $this->rpc(function($rpc){
      return $this->decode_hex($rpc->eth_blockNumber()->result());
     }); 
  }

  public function get_transactions_by_block_number($block, $full=true){
    return $this->rpc(function($rpc){
      return $rpc->eth_getBlockByNumber($block, $full)->result();
     }); 
  }

  public function submit_tx(string $password, array $attributes=array()){
    $options = collect($attributes);
    if (isset($attributes["from"])){
      $options->put("from", array("address" => $attributes["from"], "password" => $password));
    }

    if (isset($attributes["to"])){
      $options->put("to", array("address" => $attributes["to"]));
    }

    return Transaction::create($options->all());
  }

  public function poll_pending_transactions(array $options=array(), $delay = 0, Carbon $poll_until=null){
    Transaction::poll_parity_pending_transactions($options, $delay, $poll_until);
  }

  public function estimate_tx_fee_in_wei(){

    $gas_price_in_wei = $this->rpc(function($rpc){
     return $this->decode_hex($rpc->eth_gasPrice()->result());
    });

    $gas_estimation = $this->rpc(function($rpc){
      return $this->decode_hex($rpc->eth_estimateGas()->result());
    });

    return $gas_price_in_wei * $gas_estimation;

  }

}
