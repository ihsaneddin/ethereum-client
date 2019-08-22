<?php
namespace Ihsaneddin\Ethereum\Traits;

trait ParityAccountsModuleTrait{

  public function parity_allAccountsInfo(){
    return $this->post(__FUNCTION__);
  }

  public function parity_changePassword(string $address, string $old_password, string $new_password){
    return $this->post(__FUNCTION__, array($address, $old_password, $new_password));
  }

  public function parity_deriveAddressHash(string $address, string $password, array $hash, $save=false){
    return $this->post(__FUNCTION__, array($address, $password, $hash, (bool)$save));
  }

  public function parity_deriveAddressIndex(string $address, string $password, array $sequence, $save=false){
    return $this->post(__FUNCTION__, array($address, $password, $sequence, (bool)$save ));
  }

  public function parity_exportAccount(string $address, string $password){
    return $this->post(__FUNCTION__, array($address, $password));
  }

  public function parity_getDappAddresses(string $id, $decode_hex=false){
    return $this->post(__FUNCTION__, array($id))->alter_result($decode_hex, function($result){ return $this->decode_hex($result); });
  }

  public function parity_getDappDefaultAddress(string $id, $decode_hex=false){
    return $this->post(__FUNCTION__, array($id))->alter_result($decode_hex, function($result){ return $this->decode_hex($result); });
  }

  public function parity_getNewDappsAddresses($decode_hex=false){
    return $this->post(__FUNCTION__)->alter_result($decode_hex, function($result){ return $this->decode_hex($result); });
  }

  public function parity_getNewDappsDefaultAddress($decode_hex=false){
    return $this->post(__FUNCTION__)->alter_result($decode_hex, function($result){ return $this->decode_hex($result); });
  }

  public function parity_importGethAccounts(array $addresses){
    return $this->post(__FUNCTION__, array($addresses));
  }

  public function parity_killAccount(string $address, string $password){
    return $this->post(__FUNCTION__, array($address, $password));
  }

  public function parity_listGethAccounts(){
    return $this->post(__FUNCTION__);
  }

  public function parity_listRecentDapps(){
    return $this->post(__FUNCTION__);
  }

  public function parity_newAccountFromPhrase(string $phrase, string $password){
    return $this->post(__FUNCTION__, array($phrase, $password));
  }

  public function parity_newAccountFromSecret(string $secret, string $password){
    return $this->post(__FUNCTION__, array($secret, $password));
  }

  public function parity_newAccountFromWallet($wallet, string $password){
    if (is_array($wallet)) $wallet = json_encode($wallet);
    return $this->post(__FUNCTION__, array($wallet, $password));
  }

  public function parity_removeAddress(string $address){
    return $this->post(__FUNCTION__, array($address));
  }

  public function parity_setAccountMeta(string $address, $meta_json){
    if (is_array($meta_json)) $meta_json = json_encode($meta_json);
    return $this->post(__FUNCTION__, array($address, $meta_json));
  }

  public function parity_setAccountName(string $address, string $name){
    return $this->post(__FUNCTION__, array($address, $name));
  }

  public function parity_setDappAddresses(string $dapp_id, array $addresses=null){
    return $this->post(__FUNCTION__, array($dapp_id, $addresses));
  }

  public function parity_setDappDefaultAddress(string $dapp_id, string $address){
    return $this->post(__FUNCTION__, array($dapp_id, $address));
  }

  public function parity_setNewDappsAddresses(array $addresses=null){
    return $this->post(__FUNCTION__, array($addresses));
  }

  public function parity_setNewDappsDefaultAddress(string $address){
    return $this->post(__FUNCTION__, array($address));
  }

  public function parity_testPassword(string $address, string $password){
    return $this->post(__FUNCTION__, array($address, $password));
  }

}