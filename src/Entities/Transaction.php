<?php
namespace Ihsaneddin\Ethereum\Entities;

use Ihsaneddin\Ethereum\Exceptions\ResponseError;
use kornrunner\Ethereum\Transaction as TransactionSigner;

use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use kornrunner\Keccak;
use kornrunner\Secp256k1;
use RuntimeException;
use Web3p\RLP\RLP;

class Transaction extends EthereumRestObject {

  protected $__properties = array(
    "from" => "from",
    "account" => "from",
    "source" => "from",
    "to" => "to",
    "destination" => "to",
    "gas" => "gas",
    "gasprice" => "gas_price",
    "gas_price" => "gas_price",
    "value" => "value",
    "data" => "data",
    "nonce" => "nonce",
    "condition" => "condition",
    "data" => "data",
    "hash" => "hash",
    "transaction_index" => "transaction_index",
    "transactionindex" => "transaction_index",
    "block_hash" => "block_hash",
    "blockhash" => "block_hash",
    "block_number" => "block_number",
    "blocknumber" => "block_number",
    "input" => "data",
    "v" => "v",
    "standard_v" => "standard_v",
    "standardv" => "standard_v",
    "r" => "r",
    "raw" => "raw",
    "public_key" => "public_key",
    "publickey" => "public_key",
    "newwork_id" => "network_id",
    "networkid" => "network_id",
    "creates" => "creates",
    "condition" => "condition",
  );

  protected static $can_be_encoded_keys = array('value', "gas", "gas_price", 'nonce', "gasPrice");

  protected $__hash;

  protected function init_hash($value){
    return $this->__hash= $value;
  }

  protected function get_hash(){ return $this->__hash; }

  protected function set_hash($value){ return $this->init_hash($value); }

  protected $__signed_hash;

  protected function init_signed_hash($value){
    return $this->__signed_hash= $value;
  }

  protected function get_signed_hash(){ return $this->__signed_hash; }

  protected function set_signed_hash($value){ return $this->init_signed_hash($value); }

  protected $__from;

  protected function init_from($value){
    if ($value instanceOf Account)
      return $this->__from=  $value;
    if (!is_array($value)){
      $value = array("address" => $value);
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
      $value = array("address" => $value);
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

  protected $__gas;

  protected function init_gas($value){
    return $this->__gas= $this->decode_hex($value);
  }

  protected function set_gas($value){
    return $this->init_gas($value);
  }

  protected function get_gas(){
    return $this->__gas;
  }

  protected $__gas_price;

  protected function init_gas_price($value){
    return $this->__gas_price= $this->decode_hex($value);
  }

  protected function set_gas_price($value){
    return $this->init_gas_price($value);
  }

  protected function get_gas_price(){ return $this->__gas_price; }

  protected $__value;

  protected function init_value($value){
    return $this->__value= $this->decode_hex($value);
  }

  protected function set_value($value){
    return $this->init_value($value);
  }

  protected function get_value(){ return $this->__value; }


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

  public static function create($params=array(), string $password=null, bool $encode=true){
    if(isset($params["private_key"])){
      $private_key =   $params["private_key"];
      unset($params["private_key"]);
    }
    if ($encode){
      $params = array_merge($params, static::encode_to_hex($params));
    }
    if(!isset($params["gasPrice"]) && !isset($params["gas_price"])){
      // if (!isset($params["gasPrice"]) || is_null($params["gasPrice"])){
      //   $params["gasPrice"] = static::getGasPrice();
      // }
      $params["gasPrice"] = static::getGasPrice();
    }
    if (!isset($params["gas"]) || is_null($params["gas"])){
      $params["gas"] = static::getGasEstimation();
    }
    $tx = new self($params);
    if (is_null($password)){
      $password = $tx->from->password;
    }
    if(isset($private_key)){
      $signed_hash = $tx->signWithPrivateKey($private_key);
      #$signed_hash = $tx->offlineSign($private_key);
      $tx->sendRawTransaction($signed_hash);
    }else{
      $tx->personal_send($password);
    }
    return $tx;
  }

  public static function getGasPrice(bool $decode = false){
    $gas_price = static::ethereum_instance()->rpc(function($rpc){ return $rpc->eth_gasPrice(); })->result();
    return $decode ? static::decode_from_hex($gas_price) : $gas_price;
  }

  public static function getGasEstimation(array $attributes = array(), bool $decode = false){
    if(!empty($attributes)){
      if(isset($attributes["gas"])){
        $attributes["gas"] = static::encode_to_hex($attributes["gas"]);
      }
      if(isset($attributes["gasPrice"])){
        $attributes["gasPrice"] = static::encode_to_hex($attributes["gasPrice"]);
      }
      if(isset($attributes["value"])){
        $attributes["value"] = static::encode_to_hex($attributes["value"]);
      }
      $gas = static::ethereum_instance()->rpc(function($rpc) use($attributes) { return $rpc->eth_estimateGas($attributes); })->result();
    }else{
      $gas = static::ethereum_instance()->rpc(function($rpc){ return $rpc->eth_estimateGas(); })->result();
    }
    return $decode ? static::decode_from_hex($gas) : $gas;
  }

  public function signWithPrivateKey(string $private_key){
    #$private_key = static::add0x($private_key);
    $params = $this->getInput();
    $transaction_signer = new TransactionSigner($params["nonce"], $params["gasPrice"], $params["gasLimit"], $params["to"], $params["value"], $params["data"]);
    $chainId = $this->ethereum()->rpc(function($rpc){
      return $rpc->eth_chainId()->result();
    });
    return $transaction_signer->getRaw($private_key, $chainId);
  }

  public function sign(string $password=null){
    try{
      if (is_null($password)) $password = $this->from->password;
    }catch(\Exception $e){
      throw new \InvalidArgumentException("Password must be supplied");
    }
    return $this->__signed_hash = $this->ethereum()->rpc(function($rpc) use($password) {
      return $rpc->personal_signTransaction($this->as_params(), $password);
    })->result('raw');

  }

  public function sendRawTransaction(string $signed_hash){
    $signed_hash = static::add0x($signed_hash);
    $hash = $this->ethereum()->rpc(function($rpc) use($signed_hash){
      return $rpc->eth_sendRawTransaction($signed_hash)->result();
    });
    $this->hash = $hash;
    return $this;
  }

  public function submit(string $signed_hash = null){

    if (is_null($signed_hash)) $signed_hash = $this->__signed_hash;

    if (is_null($signed_hash))
      $signed_hash = $this->sign();

    if ($signed_hash){
      $attributes = $this->ethereum()->rpc(function($rpc) use($signed_hash){
        return $rpc->eth_sendRawTransaction($signed_hash);
      });
    }
  }

  public function personal_send(string $password = null){

    try{
      if (is_null($password)) $password = $this->from->password;
    }catch(\Exception $e){
      throw new \InvalidArgumentException("Password must be supplied");
    }
    //var_dump($this->as_params());
    if(is_null(($this->gas))){
      $this->gasPrice();
      $this->estimateGas();
    }
    $response = $this->ethereum()->rpc(function($rpc) use ($password) {
      return $rpc->personal_sendTransaction($this->as_params(), $password);
    });
    return $this->hash = $response->result();
  }

  public function estimateGas(){
    try{
      $estimateGas = $this->ethereum()->rpc(function($rpc){ return $rpc->eth_estimateGas($this->as_params()); })->result();
      $this->set_gas($estimateGas);
    }catch(\Ihsaneddin\Ethereum\Exceptions\ResponseError $e){
      $estimateGas = $this->encode_hex(4700036); #  gas limit
      $this->set_gas($estimateGas);
    }
    if(isset($estimateGas))
      return $estimateGas;
  }

  public function gasPrice(){
    $gasPrice = $this->ethereum()->rpc(function($rpc){
      return $rpc->eth_gasPrice();
    })->result();
    $this->set_gas_price($gasPrice);
    return $gasPrice;
  }

  public static function find(string $hash){
    //try{
      return new self(self::ethereum_instance()->rpc(function($rpc) use($hash) {
        return $rpc->eth_getTransactionByHash($hash);
      })->result()->all());
    // }catch(ResponseError $e){
    //   return;
    // }
  }

  public static function getReceipt(string $hash){
    return TransactionReceipt::find($hash);
  }

  public function getTransactionReceipt(){
    if($this->hash){
      return static::getReceipt($this->hash);
    }
  }

  public static function checkStatus(string $hash){
    $receipt = static::getReceipt($hash);
    if($receipt){
      return $receipt->success();
    }
  }

  public function getStatus(){
    if (!$this->block_number){
      return "pending";
    }
    if($this->block_number){
      $receipt = TransactionReceipt::find($this->hash);
      if($receipt){
        if($receipt->status > 0){
          if ($receipt->checkContractAddress()){
            if($this->gas == $receipt->gas_used){
              //return "failed";
            }
          }
          return 'success';
        }else{
          return "failed";
        }
      }
      return "confirmed";
    }
  } 

  public function as_params() : array {

    //var_dump($this->value);

    return array_filter(array(
      "from" => $this->from->address,
      "to" => $this->to->address,
      "gas" => $this->encode_hex($this->gas),
      "gasPrice" => $this->encode_hex($this->gas_price),
      "value" => $this->encode_hex($this->value),
      #"nonce" => $this->encode_hex($this->nonce ? $this->nonce : 0),
      "condition" => $this->condition,
      "data" => $this->data ? $this->data : null 
    ));
  }

  public function signed_hash(){
    return $this->__signed_hash;
  }

  public function get_total_fees(){
    if($this->block_number){
      $receipt = TransactionReceipt::find($this->hash);
      if($receipt){
        if($receipt->gas_used){
          if($this->gas_price){
            return bcmul($this->gas_price, $receipt->gas_used, 18);
          }else{
            return bcmul(static::find($this->hash)->gas_price, $receipt->gas_used, 18);
          }
        }
        if($receipt->cumulative_gas_used){
          if($this->gas_price){
            return bcmul($this->gas_price, $receipt->cumulative_gas_used, 18);
          }else{
            return bcmul(static::find($this->hash)->gas_price, $receipt->cumulative_gas_used, 18);
          }
        }
      }
    }
  }

  public static function poll_parity_pending_transactions(array $options=array(), int $delay=0, Carbon $poll_until=null){

    if (isset($options["on_message"])){
      $on_message_callback = $options["on_message"];
      if (is_callable($on_message_callback)){
        $options["on_message"] = function($res) use ($on_message_callback) {
          if (!$res->result()->isEmpty()){
            foreach($res->result() as $raw_tx){
              $tx = new \Ihsaneddin\Ethereum\Entities\Transaction($raw_tx);
              $on_message_callback($tx);
            }
          }
        };
      }
    }
    self::ethereum_instance()->http()->poll("parity_pendingTransactions", $options, $delay, $poll_until);
  }


  public function get_confirmation_count(string $hash = null)
  {
    if(is_null($hash)){
      $hash = $this->hash;
    }
    $blockNumber = $this->ethereum()->rpc(function ($rpc) {
      return $rpc->eth_blockNumber();
    })->result();

    $hashBlockNumber = $this->ethereum()->rpc(function ($rpc) use ($hash) {
      return $rpc->eth_getTransactionByHash($hash);
    })->result();
    $blockNumberInt = $this->ethereum()->decode_hex($blockNumber);
    $hashBlockNumberInt = $this->ethereum()->decode_hex($hashBlockNumber['blockNumber']);

    return $blockNumberInt - $hashBlockNumberInt;

  }
  public static function get_transactions_by_block($block, $full = true){
    return collect(self::ethereum_instance()->rpc(function($rpc) use ($block, $full){
      return $rpc->eth_getBlockByNumber($block, $full)->result()["transactions"];
    }))->map(function($raw){
      return new static($raw);
    }); 
  }

  public static function poll_eth_block_by_number(array $options= array(), int $delay= 0, Carbon $poll_until= null){
    if (isset($options["on_message"])){
      $on_message_callback = $options["on_message"];
      if (is_callable($on_message_callback)){
        $options["on_message"] = function($res) use ($on_message_callback) {
          if (!$res->result()->isEmpty()){
            if(isset($res->result()["transactions"])){
              $txs = $res->result()["transactions"];
              foreach($txs as $raw_tx){
                $tx = new \Ihsaneddin\Ethereum\Entities\Transaction($raw_tx);
                $on_message_callback($tx);
              }
            }
          }
        };
      }
    }
    self::ethereum_instance()->http()->poll("eth_getBlockByNumber", $options, $delay, $poll_until);
  }

  public static function get_current_block()
  {
    return self::ethereum_instance()->get_current_block();
  }

  public function hasInput(){
    if($this->input){
      if($this->input != "0x" || !empty($this->input)){
        return true;
      }
    }
    if(isset($this->data()["data"])){
      if($this->data()["data"] != "0x" || !empty($this->data()["data"])){
        return true;
      }
    }
  }

  public function getTimestamp(int $block = null, bool $decode = true){
    try{
      $timestamp= $this->getBlock($block, false)->get('timestamp');
      return $decode ? $this->decode_hex($timestamp) : $timestamp;
    }catch(\Exception $e){
      return;
    }
  }

  public function getBlock(int $block = null, bool $full = true){
    if(is_null($block)){
      $block = $this->block_number;
    }
    return collect(self::ethereum_instance()->rpc(function($rpc) use ($block, $full){
      return $rpc->eth_getBlockByNumber($block, true, false, $full)->result();
    }));
  }

  public function offlineSign($privateKey, $chainId = null){
    if(is_null($chainId)){
      $chainId = $this->ethereum()->rpc(function($rpc){
        return $rpc->eth_chainId()->result();
      });
    }

    if ($chainId < 0) {
      throw new RuntimeException('ChainID must be positive');
    }

    if (strlen($privateKey) != 64) {
      throw new RuntimeException('Incorrect private key');
    }

    $input = $this->getInput();
    $hash      = $this->hash($chainId, $input);

    $secp256k1 = new Secp256k1();
    $signed    = $secp256k1->sign($hash, $privateKey);
    $input['r']   = $this->hexup(gmp_strval($signed->getR(), 16));
    $input['s']   = $this->hexup(gmp_strval($signed->getS(), 16));
    $input['v']   = dechex ($signed->getRecoveryParam ($hash, $privateKey) + 27 + ($chainId ? $chainId * 2 + 8 : 0));

    return $this->serialize($input);
  }

  public function getInput(){
    return array(
      "nonce" => $this->from ? $this->from->getTransactionCount() : '0',
      "gasPrice" => static::strip0x($this->encode_hex($this->gas_price)),
      "gasLimit" => static::strip0x($this->encode_hex($this->gas)),
      "to" => static::strip0x($this->to->address),
      "value" => static::strip0x($this->encode_hex($this->value)),
      "data" => $this->data ? $this->data : '',
      'v' => '',
      'r' => '',
      's' => '',
    );
  }

  private function hash(int $chainId, array $input=[]): string {
    if ($chainId > 0) {
        $input['v'] = $chainId;
        $input['r'] = '';
        $input['s'] = '';
    } else {
        unset($input['v']);
        unset($input['r']);
        unset($input['s']);
    }
    $encoded = $this->RLPencode($input);
    return Keccak::hash(hex2bin($encoded), 256);
  }

  private function RLPencode(array $input): string {
    $rlp  = new RLP;
    $data = [];
    foreach ($input as $item) {
        $value  = strpos ($item, '0x') !== false ? substr ($item, strlen ('0x')) : $item;
        $data[] = $value ? '0x' . $this->hexup($value) : '';
    }
    return $rlp->encode($data)->toString('hex');
  }
  private function hexup(string $value): string {
      return strlen ($value) % 2 === 0 ? $value : "0{$value}";
  }

  private function serialize(array $input): string {
    return $this->RLPencode($input);
  }

}
