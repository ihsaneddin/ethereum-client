<?php

require __DIR__ . '/../../../../../vendor/autoload.php';

use Ihsaneddin\Ethereum;
use Ihsaneddin\Ethereum\Entities\Account;
use Ihsaneddin\Ethereum\Entities\Transaction;

$account = new Account(array("address" => '0x00DCcacc1E98594D49518fFb31DCF79E333D3a94', 'password' => 'password'));
$destination = new Account(array("address" => "0x4a1e201822194bbc20e32Cf4aDB7F1e8A5F14A84"));

var_dump($account->balance());

//$tx = Transaction::create(array("from" => $account, "to" => $destination, "value" => 1000000000000000000));
//var_dump($tx);
#DE0B6B3A7640000
#$res = Ethereum::rpc(function($rpc){
#  return $rpc->personal_sendTransaction(array("from" => '0x00DCcacc1E98594D49518fFb31DCF79E333D3a94', 'to' => '0x4a1e201822194bbc20e32Cf4aDB7F1e8A5F14A84', 'value' => '0x1'), 'password');
#});
//print_r($res);

//print_r(Ethereum::create_new_account("password"));

/*
{"method":"personal_listAccounts","params":[],"id":1,"jsonrpc":"2.0"}
*/