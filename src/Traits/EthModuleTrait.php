<?php
namespace Ihsaneddin\Ethereum\Traits;

use Illuminate\Support\Collection;

use Ihsaneddin\Ethereum\Interfaces\ResponseInterface;

trait EthModuleTrait {

  public function eth_accounts(){
    return $this->post(__FUNCTION__);
  }

  public function eth_blockNumber(){
    return $this->post(__FUNCTION__);
  }

  public function eth_chainId($decode_hex = true){
    $response = $this->post(__FUNCTION__);
    $response->alter_result($decode_hex, $this->decode_hex($response->result()));
    return $response;
  }

  /**
    * @param params,
    * @param
    * @return Ihsaneddin\Ethereum\Connection\Response
  **/
  public function eth_call(array $params=array(), string $block= "latest"){
    //$params[] = $block;
    return $this->post(__FUNCTION__, array($params, $block));
  }

  public function eth_coinbase(){
    return $this->post(__FUNCTION__);
  }

  public function eth_estimateGas(array $params=array(), $block="latest"){
    //$params[] = $block;
    if (empty($params))
      $params = array("value" => "0x1");

    return $this->post(__FUNCTION__, array($params, $block));
  }

  public function eth_gasPrice(){
    return $this->post(__FUNCTION__);
  }

  public function eth_getBalance(string $address, $decode_hex= false, $block= 'latest'){
    $response = $this->post(__FUNCTION__, array($address, $block));

    $response->alter_result($decode_hex, function($result){
      return $this->decode_hex($result);
    });

    return $response;
  }

  public function eth_getBlockByHash(string $hash, $decode_hex= false, $full_tx = true){
    $response = $this->post(__FUNCTION__, array($hash, $full_tx));

    $response->alter_result(($response->result() instanceOf Collection) && $decode_hex, function($result) {
      return $this->decode_tx_object($result);
    });

    return $response;
  }

  public function eth_getBlockByNumber($block = "latest", $encode_hex=false, $decode_hex=false, $full_tx=true){
    if ($encode_hex) $block = $this->encode_hex($block);
    $response = $this->post(__FUNCTION__, array($block, $full_tx));

    $response->alter_result(($response->result() instanceOf Collection) && $decode_hex, function($result) {
      return $this->decode_tx_object($result);
    });

    return $response;
  }

  public function eth_getBlockTransactionCountByHash(string $tx_hash, $decode_hex=false){
    $response = $this->post(__FUNCTION__, array($tx_hash));
    $response->alter_result($decode_hex, $this->decode_hex($response->result()));
    return $response;
  }

  public function eth_getBlockTransactionCountByNumber($block="latest", $encode_hex=false, $decode_hex=false){
    if ($encode_hex) $block = $this->encode_hex($block);
    $response = $this->post(__FUNCTION__, array($block));
    $response->alter_result($decode_hex, $this->decode_hex($response->result()));
    return $response;
  }

  public function eth_getCode(string $address, $block="latest", $encode_hex=false){
    if ($encode_hex) $block = $this->encode_hex($block);
    return $this->post(__FUNCTION__, array($address, $block));
  }

  public function eth_getFilterChanges($filter_id, $encode_hex=false){
    if ($encode_hex) $filter_id = $this->encode_hex($filter_id);
    return $this->post(__FUNCTION__, array($filter_id));
  }

  public function eth_getFilterLogs($filter_id, $encode_hex=false){
    if ($encode_hex) $filter_id = $this->encode_hex($filter_id);
    return $this->post(__FUNCTION__, array($filter_id));
  }

  public function eth_getLogs($filter){
    return $this->post(__FUNCTION__, array($filter));
  }

  public function eth_getStorageAt(string $address, $position, $block='latest', $encode_hex=false){
    if ($encode_hex){
      $position = $this->encode_hex($position);
      $block = $this->encode_hex($block);
    }
    return $this->post(__FUNCTION__, array($address, $position, $block));
  }

  public function eth_getTransactionByBlockHashAndIndex(string $hash, $index="0x0", $encode_hex=false, $decode_hex = false){
    if ($encode_hex) $index= $this->encode_hex($index);
    $response = $this->post(__FUNCTION__, array($hash, $index));
    $response->alter_result(($response->result() instanceOf Collection) && $decode_hex, function($result) {
      return $this->decode_tx_object($result);
    });
    return $response;
  }

  public function eth_getTransactionByBlockNumberAndIndex($block="latest", $index, $encode_hex=false){
    if ($encode_hex){
      $block = $this->encode_hex($block);
      $index= $this->encode_hex($index);
    }
    $response = $this->post(__FUNCTION__, array($block, $index));
    $response->alter_result(($response->result() instanceOf Collection) && $decode_hex, function($result) {
      return $this->decode_tx_object($result);
    });
    return $response;
  }

  public function eth_getTransactionByHash(string $hash, $decode_hex=false){
    $response = $this->post(__FUNCTION__, array($hash));
    $response->alter_result(($response->result() instanceOf Collection) && $decode_hex, function($result) {
      return $this->decode_tx_object($result);
    });
    return $response;
  }

  public function eth_getTransactionCount(string $address, $block='latest', $encode_hex=false, $decode_hex=true){
    if ($encode_hex) $block = $this->encode_hex($block);
    $response = $this->post(__FUNCTION__, array($address, $block));
    $response->alter_result($decode_hex, function($result) { return $this->decode_hex($result); } );
    return $response;
  }

  public function eth_getTransactionReceipt(string $hash, $decode_hex=false){
    $response = $this->post(__FUNCTION__, array($hash));
    $response->alter_result(($response->result() instanceOf Collection) && $decode_hex, function($result) {
      return $this->decode_tx_object($result);
    });
    return $response;
  }

  public function eth_getUncleByBlockHashAndIndex(string $hash, $index="0x0", $encode_hex=false, $decode_hex=false){
    if ($encode_hex) $index = $this->encode_hex($index);

    $response = $this->post(__FUNCTION__, array($hash, $index));
    $response->alter_result(($response->result() instanceOf Collection) && $decode_hex, function($result) {
      return $this->decode_tx_object($result);
    });

    return $response;
  }

  public function eth_getUncleByBlockNumberAndIndex($block="latest", $index="0x0", $encode_hex=false, $decode_hex=false){
    if ($encode_hex){
     $index = $this->encode_hex($index);
     $block = $this->encode_hex($index);
    }

    $response = $this->post(__FUNCTION__, array($hash));
    $response->alter_result(($response->result() instanceOf Collection) && $decode_hex, function($result) {
      return $this->decode_tx_object($result);
    });
    return $response;
  }

  public function eth_getUncleCountByBlockHash(string $hash, $decode_hex=false){
    $response = $this->post(__FUNCTION__, array($hash));
    $response->alter_result($decode_hex, $this->decode_hex($response->result()));
    return $response;
  }

  public function eth_getUncleCountByBlockNumber($block="latest", $encode_hex=false, $decode_hex=false){
    if ($encode_hex) $block= $this->encode_hex($block);
    $response = $this->post(__FUNCTION__, array($hash));
    $response->alter_result($decode_hex, $this->decode_hex($response->result()));
    return $response;
  }

  public function eth_getWork(){
    return $this->post(__FUNCTION__);
  }

  public function eth_hashrate(){
    return $this->post(__FUNCTION__);
  }

  public function eth_mining(){
    return $this->post(__FUNCTION__);
  }

  public function eth_newBlockFilter(){
   return $this->post(__FUNCTION__);
  }

  public function eth_newFilter($filter, $decode_hex=false){
    $response = $this->post(__FUNCTION__, array($filter));
    $response->alter_result($decode_hex, function($result){ return $this->decode($result); });
    return $response;
  }

  public function eth_newPendingTransactionFilter($decode_hex=false){
    $response =  $this->post(__FUNCTION__);
    $response->alter_result($decode_hex, $this->decode_hex($response->result()));
    return $response;
  }

  public function eth_protocolVersion($decode_hex=false){
    $response =  $this->post(__FUNCTION__);
    $response->alter_result($decode_hex, $this->decode_hex($$response->result()));
    return $response;
  }

  public function eth_sendRawTransaction(string $signed_transaction_data){
    return $this->post(__FUNCTION__, array($signed_transaction_data));
  }

  public function eth_sendTransaction(array $transaction){
    return $this->post(__FUNCTION__, array($transaction));
  }

  public function eth_sign(string $address, $data){
    return $this->post(__FUNCTION__, array($address, $data));
  }

  public function eth_signTransaction(array $transaction){
    return $this->post(__FUNCTION__, array($transaction));
  }

  public function eth_submitHashrate(string $hash_rate, string $client_id){
    return $this->post(__FUNCTION__, array($hash_rate, $client_id));
  }

  public function eth_submitWork(string $nounce, string $header, string $mix_digest){
    return $this->post(__FUNCTION__, array($nounce, $header, $mix_digest));
  }

  public function eth_syncing($decode_hex=false){
    $response = $this->post(__FUNCTION__);
    $response->alter_result($decode_hex, function($result){
      return $this->decode_tx_object($result);
    });
    return $response;
  }

  public function eth_uninstallFilter(string $filter_id, $encode_hex=false){
    if ($encode_hex) $filter_id = $this->encode_hex($filter_id);
    return $this->post(__FUNCTION__, array($filter_id));
  }

  private function decode_tx_object(Collection $result){
    return collect($this->encode_hex($result));
  }

}