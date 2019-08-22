<?php
namespace Ihsaneddin\Ethereum\Traits;

trait ParitySetModuleTrait{

  function parity_acceptNonReservedPeers(){
    return $this->post(__FUNCTION__);
  }

  function parity_addReservedPeer(string $enode_address){
    return $this->post(__FUNCTION__, array($enode_address));
  }

  function parity_dappsList(){
    return $this->post(__FUNCTION__);
  }

  function parity_dropNonReservedPeers(){
    return $this->post(__FUNCTION__);
  }

  function parity_executeUpgrade(){
    return $this->post(__FUNCTION__);
  }

  function parity_hashContent(string $url_content){
    return $this->post(__FUNCTION__, array($url_content));
  }

  function parity_removeReservedPeer(string $encoded_node_address){
    return $this->post(__FUNCTION__, array($encoded_node_address));
  }

  function parity_setAuthor(string $address){
    return $this->post(__FUNCTION__, array($address));
  }

  function parity_setChain(string $chain_name){
    return $this->post(__FUNCTION__, array($chain_name));
  }

  function parity_setEngineSigner(string $address, string $password){
    return $this->post(__FUNCTION__, array($address, $password));
  }

  function parity_setExtraData(string $data, $encode_hex=false){
    if ($encode_hex) $data= $this->encode_hex($data);
    return $this->post(__FUNCTION__, array($data));
  }

  function parity_setGasCeilTarget($value = '0x0', $encode_hex=false){
    if ($encode_hex) $value= $this->encode_hex($value);
    return $this->post(__FUNCTION__, array($value));
  }

  function parity_setGasFloorTarget($value = '0x0', $encode_hex=false){
    if ($encode_hex) $value= $this->encode_hex($value);
    return $this->post(__FUNCTION__, array($value));
  }

  function parity_setMaxTransactionGas($value = '0x0', $encode_hex=false){
    if ($encode_hex) $value= $this->encode_hex($value);
    return $this->post(__FUNCTION__, array($value));
  }

  function parity_setMinGasPrice($value = '0x0', $encode_hex=false){
    if ($encode_hex) $value= $this->encode_hex($value);
    return $this->post(__FUNCTION__, array($value));
  }

  function parity_setMode(string $mode='passive'){
    return $this->post(__FUNCTION__, array($mode));
  }

  function parity_setTransactionsLimit($limit, $encode_hex=false){
    if ($encode_hex) $limit= $this->encode_hex($limit);
    return $this->post(__FUNCTION__, array($limit));
  }

  function parity_upgradeReady(){
    return $this->post(__FUNCTION__);
  }

}