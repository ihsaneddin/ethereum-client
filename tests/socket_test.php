<?php

require __DIR__ . '/../vendor/autoload.php';

use Ihsaneddin\Ethereum\Connection\Socket;
$socket = new Socket;

$response = $socket->post(array('method' => 'personal_listAccounts', 'params' => []));

print_r($response);

/*
{"method":"personal_listAccounts","params":[],"id":1,"jsonrpc":"2.0"}


$sock = socket_create(AF_UNIX, SOCK_STREAM, 0);
socket_connect($sock, "/home/jerry/.local/share/io.parity.ethereum/jsonrpc.ipc",1);
$myBuf = null;
$msg = "{\"jsonrpc\":\"2.0\",\"method\":\"rpc_modules\",\"params\":[],\"id\":1}";
socket_send($sock, $msg, strlen($msg), MSG_EOF);
socket_recv ( $sock , $myBuf ,  100 ,0     );
socket_close($sock);

print_r($myBuf);*/