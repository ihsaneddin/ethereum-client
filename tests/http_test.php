<?php

require __DIR__ . '/../vendor/autoload.php';

use Ihsaneddin\Ethereum\Connection\Http;


$response = (new Http())->post(['method' => 'net_version', 'params' => []]);

var_dump($response);

/*
{"method":"personal_listAccounts","params":[],"id":1,"jsonrpc":"2.0"}
*/