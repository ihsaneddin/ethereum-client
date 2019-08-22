<?php
namespace Ihsaneddin\Ethereum\Traits;

use Illuminate\Support\Collection;

trait ParityModuleTrait {

  function parity_cidV0(string $data){
    return $this->post(__FUNCTION__, array($data));
  }

  function parity_composeTransaction(array $transaction=array(), $decode_hex=false){
    $response = $this->post($transaction);
    $response->alter_result($decode_hex, function($result){
      return collect($this->encode_hex($result));
    });
  }

  function parity_consensusCapability(){
    return $this->post(__FUNCTION__);
  }

  function parity_decryptMessage(string $address, string $encrypted_message, $decode_hex=false){
    return $this->post(__FUNCTION__, array($address, $encrypted_message))->alter_result($decode_hex, function($result){ return $this->decode_hex($result); });
  }

  function parity_encryptMessage(string $hash, string $message){
    return $this->post(__FUNCTION__, array($hash, $message));
  }

  function parity_futureTransactions($decode_hex=false){
    return $this->post(__FUNCTION__)->alter_result($decode_hex, function($result){
        if ($result instanceOf Collection){
          $result = $result->all();
          $decoded_tx = [];
          foreach ($result as $tx) {
            $decoded_tx[] = $this->decode_hex($tx);
          }
          $result = collect($decoded_tx);
        }
        return $result;
     });
  }

  function parity_getBlockHeaderByNumber($block="latest", $encode_hex=false, $decode_hex=false){
    if ($encode_hex) $block = $this->encode_hex($block);
    return $this->post(__FUNCTION__, array($block))->alter_result($decode_hex, function($result) { return $this->decode_hex($result); } );
  }

  function parity_listOpenedVaults(){
    return $this->post(__FUNCTION__);
  }

  function parity_listStorageKeys(string $address, int $per=5, string $offset=null, $block="latest"){
    return $this->post(__FUNCTION__, array($address. $per, $offset, $block));
  }

  function parity_listVaults(){
    return $this->post(__FUNCTION__);
  }

  function parity_localTransactions(){
    return $this->post(__FUNCTION__);
  }

  function parity_releasesInfo(){
    return $this->post(__FUNCTION__);
  }

  function parity_signMessage(string $address, string $password, string $data){
    return $this->post($address, $password, $data);
  }

  function parity_versionInfo(){
    return $this->post(__FUNCTION__);
  }

  function parity_changeVault(string $address, string $vault_name){
    return $this->post(__FUNCTION__, array($address. $vault_name));
  }

  function parity_changeVaultPassword(string $vault_name, string $password){
    return $this->post(__FUNCTION__, array($vault_name, $password));
  }

  function parity_closeVault(string $vault_name){
    return $this->post(__FUNCTION__, $vault_name);
  }

  function parity_getVaultMeta(string $vault_name){
    return $this->post(__FUNCTION__, array($vault_name));
  }

  function parity_newVault(string $vault_name, string $password){
    return $this->post(__FUNCTION__, array($vault_name, $password));
  }

  function parity_openVault(string $vault_name, string $password){
    return $this->post(__FUNCTION__, array($vault_name, $password));
  }

  function parity_setVaultMeta(string $vault_name, $json){
    if (is_array($json)) $json = json_encode($json);
    return $this->post(__FUNCTION__, array($vault_name, $json));
  }

  function parity_accountsInfo(){
    return $this->post(__FUNCTION__);
  }

  function parity_checkRequest($request_id, $encode_hex=false){
    if ($encode_hex) $id_request = $this->encode_hex($id_request);
    return $this->post(__FUNCTION__, array($request_id));
  }

  function parity_defaultAccount(){
    return $this->post(__FUNCTION__);
  }

  function parity_generateSecretPhrase(){
    return $this->post(__FUNCTION__);
  }

  function parity_hardwareAccountsInfo(){
    return $this->post(__FUNCTION__);
  }

  function parity_listAccounts(int $per=5, string $offset_address=null, $block="latest", $encode_hex=false){
    if ($encode_hex) $block = $this->encode_hex($block);
    return $this->post(__FUNCTION__, array($per, $offset_address, $block));
  }

  function parity_phraseToAddress(string $phrase){
    return $this->post(__FUNCTION__, array($phrase));
  }

  function parity_postSign(string $address, string $data){
    $this->post(__FUNCTION__, array($address, $data));
  }

  function parity_postTransaction(array $transaction, $decode_hex=false){
    return $this->post(__FUNCTION__, array($transaction))->alter_result($decode_hex, function($result) { return $this->decode_hex($result); } );
  }

  function parity_defaultExtraData(){
    return $this->post(__FUNCTION__);
  }

  function parity_extraData(){
    return $this->post(__FUNCTION__);
  }

  function parity_gasCeilTarget(){
    return $this->post(__FUNCTION__);
  }

  function parity_gasFloorTarget(){
    return $this->post(__FUNCTION__);
  }

  function parity_minGasPrice(){
    return $this->post(__FUNCTION__);
  }

  function parity_transactionsLimit(){
    return $this->post(__FUNCTION__);
  }

  function parity_devLogs(){
    return $this->post(__FUNCTION__);
  }

  function parity_devLogsLevels(){
    return $this->post(__FUNCTION__);
  }

  function parity_chain(){
    return $this->post(__FUNCTION__);
  }

  function parity_chainStatus(){
    return $this->post(__FUNCTION__);
  }

  function parity_gasPriceHistogram(){
    return $this->post(__FUNCTION__);
  }

  function parity_netChain(){
    return $this->parity_chain();
  }

  function parity_netPeers(){
    return $this->post(__FUNCTION__);
  }

  function parity_netPort(){
    return $this->post(__FUNCTION__);
  }

  function parity_nextNonce(string $address){
    return $this->post(__FUNCTION__, array($address));
  }

  function parity_pendingTransactions(){
    return $this->post(__FUNCTION__);
  }

  function parity_pendingTransactionsStats(){
    return $this->post(__FUNCTION__);
  }

  function parity_registryAddress(){
    return $this->post(__FUNCTION__);
  }

  function parity_removeTransaction(string $hash){
    return $this->post(__FUNCTION__, array($hash));
  }

  function parity_rpcSettings(){
    return $this->post(__FUNCTION__);
  }

  function parity_unsignedTransactionsCount(){
    return $this->post(__FUNCTION__);
  }

  function parity_dappsUrl(){
    return $this->post(__FUNCTION__);
  }

  function parity_enode(){
    return $this->post(__FUNCTION__);
  }

  function parity_mode(){
    return $this->post(__FUNCTION__);
  }

  function parity_nodeKind(){
    return $this->post(__FUNCTION__);
  }

  function parity_nodeName(){
    return $this->post(__FUNCTION__);
  }

  function parity_wsUrl(){
    return $this->post(__FUNCTION__);
  }

}