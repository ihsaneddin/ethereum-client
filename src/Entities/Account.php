<?php
namespace Ihsaneddin\Ethereum\Entities;

use Ihsaneddin\Ethereum\Support\Utils;

class Account extends EthereumRestObject{

  protected static $wei= 1000000000000000000;

  protected $__properties = array(
    "address" => "address",
    "password" => "password",
    "balance" => "balance"
  );

  public function unlock(int $duration = null){
    return $this->ethereum()->rpc(function($http) use($duration) {
      return $http->personal_unlockAccount($this->address, $this->password, $duration);
    })->result();
  }

  public static function create(string $password){
    $address = static::ethereum_instance()->rpc(function($http) use($password) {
      return $http->personal_newAccount($password);
    })->result();
    return new self(array("password" => $password, 'address' => $address));
  }

  public static function parityNewAccountFromSecret(string $secret, string $password){
    $secret = static::add0x($secret);
    $address = static::ethereum_instance()->rpc(function($rpc) use($secret, $password) {
      return $rpc->parity_newAccountFromSecret($secret, $password);
    })->result();
    return new self(array('password' => $password, 'address' => $address));
  }

  public static function parityNewAccountFromJson(string $json, string $password){
    $address = static::ethereum_instance()->rpc(function($rpc) use($json, $password) {
      return $rpc->parity_newAccountFromWallet($json, $password);
    })->result();
    return new self(array('password' => $password, 'address' => $address));
  }

  public static function all(){
    return static::ethereum_instance()->rpc('http', function($http){
      return collect($http->personal_listAccounts()->result())->transform(function($item, $key){ return new Account(array("address" => $item)); });
    });
  }

  public static function unlock_account(string $address, string $password, int $duration=null){
    return static::ethereum_instance()->rpc(function($http) use($address, $password, $duration) {
      return $http->personal_unlockAccount($address, $password, $duration);
    });
  }

  protected $__address;

  protected function init_address($value){
   return $this->__address = $value;
  }

  protected function set_address($value){
    return $this->__address = $value;
  }

  protected function get_address(){ return $this->__address; }

  protected $__password;

  protected function init_password($value){
    return $this->__password = $value;
  }

  protected function set_password($value){
    return $this->init_password($value);
  }

  protected function get_password(){ return $this->__password; }

  public function as_params() : array {
    return array($this->address);
  }

  public function parity_update_password(string $new_password, string $old_password= null){
    if (empty($old_password)){
      $old_password = $this->password;
    }
    $response = $this->ethereum->rpc(function($rpc) use ($new_password, $old_password) {
      return $rpc->parity_changePassword($this->address, $old_password, $new_password);
    });
    $this->password= $new_password;
    return $this;
  }

  public function parity_info(){

  }

  public function parity_set_name(string $name){
    $this->ethereum->rpc(function($rpc) use($name){
      return $rpc->parity_setAccountName($this->address, $name);
    });
    return $this;
  }

  protected $__balance;

  protected function init_balance($value){
    return $this->__balance = $value;
  }

  protected function set_balance($value){
    return $this->init_balance($value);
  }

  protected function get_balance(){ return $this->__balance; }

  public function balance(bool $reload=false, $rounded = true){
    if ($reload || is_null($this->balance)){
      $balance_in_wei = $this->decode_hex($this->ethereum->rpc(function($rpc){ return $rpc->eth_getBalance($this->address)->result(); } ));
      if($rounded){
        $balance = floatval(bcdiv($balance_in_wei / static::$wei, 1, 10));
      }else{
        $balance = bcdiv($balance_in_wei, static::$wei, 18);
      }
      $this->balance= $balance;
    }
    return $this->balance;
  }

  public function export(string $password=null, $type="json_keystore"){
    if (is_null($password)){
      $password = $this->password;
    }
    if($password){
      if($type == 'json_keystore'){
        return $this->ethereum->rpc(function($rpc) use($password){
          return $rpc->parity_exportAccount($this->address, $password);
        })->result();
      }
  
      if($type == "mnemonic_phrase"){
        return $this->ethereum->rpc(function($rpc) use($password){
          return $rpc->parity_generateSecretPhrase($this->address, $password);
        })->result();
      }
    }
  }

  public function getTransactionCount(){
    return $this->ethereum->rpc(function($rpc){
      return $rpc->eth_getTransactionCount($this->address);
    })->result();
  }

}
