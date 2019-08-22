<?php

require __DIR__ . '/../vendor/autoload.php';

use Ihsaneddin\Ethereum\Connection\Websocket;

$on_message = function($response){
  print_r($response);
};

$on_error = function($e){
  print_r($e->getMessage());
};


/*$ws = new Websocket;

$ws->post(['on_message' => null, 'on_close' => null, 'on_error' => $on_error, 'method' => 'web3_clientVersion', 'params' => []]);

print_r($ws->response());
*/
$ws = new Websocket;

$ws->post(['on_message' => $on_message, 'on_close' => null, 'on_error' => $on_error, 'method' => 'net_version', 'params' => []]);

/*
{"method":"personal_listAccounts","params":[],"id":1,"jsonrpc":"2.0"}
*/