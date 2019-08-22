<?php
namespace Ihsaneddin\Ethereum\Traits;

use Ihsaneddin\Ethereum\Entities\Account;

trait AccountTrait{

  public function create_new_account(string $password){
    return Account::create($password);
  }

  public function new_account(array $attrs){
    return new Account($attrs);
  }

  public function parityNewAccountFromSecret(string $secret, string $password){
    return Account::parityNewAccountFromSecret($secret, $password);
  }

  public function parityNewAccountFromWallet(string $wallet, $password){
    return Account::parityNewAccountFromJson($wallet, $password);
  }

}