<?php
namespace Ihsaneddin\Ethereum\Traits;

trait PersonalModuleTrait{

  public function personal_listAccounts(){
    return $this->post(__FUNCTION__);
  }

  public function personal_newAccount(string $password){
    return $this->post(__FUNCTION__, array($password));
  }

  public function personal_sendTransaction(array $transaction, string $password){
    return $this->post(__FUNCTION__, array($transaction, $password));
  }

  public function personal_signTransaction(array $transaction, string $password){
    return $this->post(__FUNCTION__, array($transaction, $password));
  }

  public function personal_unlockAccount(string $address, string $password, $for_how_long=null){
    return $this->post(__FUNCTION__, array($address. $password. $for_how_long));
  }

  public function personal_sign(string $data, string $address, string $password){
    return $this->post(__FUNCTION__, array($data, $address, $password));
  }

  public function personal_ecRecover(string $hash_signed, string $signed_data){
    return $this->post(__FUNCTION__, array($hash_signed, $signed_data));
  }

}