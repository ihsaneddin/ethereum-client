<?php
namespace Ihsaneddin\Ethereum\Entities;

use Ihsaneddin\Ethereum\Exceptions\ResponseError;
use Ihsaneddin\Ethereum\Support\Utils;

class TransactionReceipt extends EthereumRestObject{

  protected $__properties = array(
    "block_hash" => "block_hash",
    "blockhash" => "block_hash",
    "contract_address" => "contract_address",
    "contractaddress" => "contract_address",
    "cumulative_gas_used" => "cumulative_gas_used",
    "cumulativegasused" => "cumulative_gas_used",
    "gas_used" => "gas_used",
    "gasused" => "gas_used",
    "logs" => "logs",
    "logsbloom" => "logs_bloom",
    "logs_bloom" => "logs_bloom",
    "root" => "root",
    "status" => "status",
    "transaction_hash" => "transaction_hash",
    "transactionhash" => "transaction_hash",
    "transaction_index" => "transaction_index",
    "transactionindex" => "transaction_index",
    "block_number" => "block_number",
    "blocknumber" => "block_number",
    "to" => "to",
    "from" => "from"
  );

  protected static $can_be_encoded_keys = array('gas_used', "cumulative_gas_used");

  protected $__contract_address;
  protected  function init_contract_address($value){
    return $this->__contract_address= $value;
  }

  protected function set_contract_address($value){
    return $this->init_contract_address($value);
  }

  protected function get_contract_address(){ return $this->__contract_address; }

  protected $__transaction_hash;
  protected  function init_transaction_hash($value){
    return $this->__transaction_hash= $value;
  }

  protected function set_transaction_hash($value){
    return $this->init_transaction_hash($value);
  }

  protected function get_transaction_hash(){ return $this->__transaction_hash; }

  protected $__logs;

  protected  function init_logs($value){
    return $this->__logs= $value;
  }

  protected function set_logs($value){
    return $this->init_logs($value);
  }

  protected function get_logs(){ return $this->__logs; }

  protected $__status;
  protected  function init_status($value){
    return $this->__status= $this->decode_hex($value);
  }

  protected function set_status($value){
    return $this->init_status($value);
  }

  protected function get_status(){ return $this->__status; }

  protected $__gas_used;
  protected  function init_gas_used($value){
    return $this->__gas_used= $this->decode_hex($value);
  }

  protected function set_gas_used($value){
    return $this->init_gas_used($value);
  }

  protected function get_gas_used(){ return $this->__gas_used; }

  protected $__cumulative_gas_used;
  protected  function init_cumulative_gas_used($value){
    return $this->__cumulative_gas_used= $this->decode_hex($value);
  }

  protected function set_cumulative_gas_used($value){
    return $this->init_cumulative_gas_used($value);
  }

  protected function get_cumulative_gas_used(){ return $this->__cumulative_gas_used; }

  protected $__transaction_index;

  protected  function init_transaction_index($value){
    return $this->__transaction_index= $this->decode_hex($value);
  }

  protected function set_transaction_index($value){
    return $this->init_transaction_index($value);
  }

  protected function get_transaction_index(){ return $this->__transaction_index; }

  protected $__block_number;

  protected function init_block_number($value){
    return $this->__block_number= $this->decode_hex($value);
  }

  protected function set_block_number($value){ return $this->init_block_number($value); }

  protected function get_block_number(){ return $this->__block_number; }

  protected $__block_hash;

  protected function init_block_hash($value){
    return $this->__block_hash= $value;
  }

  protected function set_block_hash($value){ return $this->init_block_hash($value); }

  protected function get_block_hash(){ return $this->__block_hash; }

  protected $__from;

  protected function init_from($value){
    if ($value instanceOf Account)
      return $this->__from=  $value;
    if (!is_array($value)){
      if(is_null($value) && isset($this->__data["transactionHash"])){
        $tx = Transaction::find($this->__data["transactionHash"]);
        $value = array("address" => $tx->from->address);
      }else{
        $value = array("address" => $value);
      }
    }
    $value = collect($value)->only(["address", "password"])->all();
    return $this->__from= new Account($value);
  }

  protected function set_from($value){
    return $this->init_from($value);
  }

  protected function get_from(){
    return $this->__from;
  }

  protected $__to;

  protected function init_to($value){
    if ($value instanceOf Account)
      return $this->__to=  $value;
    if (!is_array($value)){
      if(is_null($value) && isset($this->__data["transactionHash"])){
        $tx = Transaction::find($this->__data["transactionHash"]);
        $value = array("address" => $tx->to->address);
      }else{
        $value = array("address" => $value);
      }
    }
    $value = collect($value)->only(["address", "password"])->all();
    return $this->__to= new Account($value);
  }

  protected function set_to($value){
    return $this->init_to($value);
  }

  protected function get_to(){
    return $this->__to;
  }

  public function success(){
    return $this->status == 1;

  }

  public function failed(){
   return ($this->status == 0);
  }

  public static function find(string $hash){
    //try{
      return new self(self::ethereum_instance()->rpc(function($rpc) use($hash) {
        return $rpc->eth_getTransactionReceipt($hash);
      })->result()->all());
    // }catch(ResponseError $e){
    //   return;
    // }
  }

  public function checkContractAddress(){
    if($this->to instanceOf Account){
      $code = $this->ethereum()->rpc(function($rpc){
        return $rpc->eth_getCode($this->to->address);
      })->result();
      return $code != '0x';
    }
  }

  public function as_params() : array {
    return array();
  }

  public function getLogMatchWithTopic(string $topic){
    return collect($this->logs)->first(function($log, $index) use($topic){
      if (isset($log["topics"]) && is_array($log["topics"])){
        return collect($log["topics"])->first(function($topik) use ($topic){
          return $topic == $topik;
        });
      }
    });
  }

  public function getLogsMatchWithEventSignature(string $topic, $encode = false){
    $logs = !is_array($this->logs) ? $this->logs : [];
    if($encode){
      $topic = Utils::sha3($topic);
    }
    return collect($this->logs)->reject(function($log, $index) use($topic){
      if (isset($log["topics"]) && is_array($log["topics"])){
        return array_search($topic, $log["topics"]) === false;
      }
      return false;
    });
  }
}


/*
all: [
       "blockHash" => "0x2347849ce2dabc4840d1c362e5f5ebba756ce92331e81dfbd9c13acd612fde61",
       "blockNumber" => "0x645b46",
       "contractAddress" => null,
       "cumulativeGasUsed" => "0x5208",
       "gasUsed" => "0x5208",
       "logs" => [],
       "logsBloom" => "0x00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000",
       "root" => null,
       "status" => "0x1",
       "transactionHash" => "0x3226e3bffe43ae97dcb6f7c2d927cbd6ec55205af76af6fce03b19ac835336c3",
       "transactionIndex" => "0x0",
     ],
*/
