<?php
#
# check if laravel or config() function exists?
#

$composer = collect(json_decode(file_get_contents(__DIR__.'/../composer.json')));

$version = $composer->get("version", null);

$host_path = collect(posix_getpwuid(posix_getuid()))->get('dir');

$default_config = [
  "http_endpoint" => "http://52.221.19.47:8545",
  "websocket_endpoint" => env('ETH_WS_ENDPOINT', "ws://127.0.0.1:8546"),
  "socket_endpoint" => $host_path. '/.local/share/io.parity.ethereum/jsonrpc.ipc',
  "rpc_version" => "2.0",
  "response_adapter" => \Ihsaneddin\Ethereum\Connection\Response::class,
  "preferred_rpc" => ["http"],#,"websocket", "socket"],
  "redis_client" => [ "host" => "127.0.0.1", "port" => 6379],
  "etherscan_api_url" => 'https://api-kovan.etherscan.io/api',
  'etherscan_api_key' => 'YGQ9Z2D3488C17W6W78ABKPW67XEUD6EIV',
  "ethplorer_api_url" => "https://api.ethplorer.io",
  "ethplorer_api_key" => "freekey"
];

if (function_exists('config')){
  #
  # handle laravel config
  #config.php
  try{
    $_ethereum = is_null(config('ethereum_client')) ? array() : config('ethereum_client');
    $_ethereum = array_merge($default_config, $_ethereum);
    return collect($_ethereum);
  }catch( Exception $e){
    return collect($default_config);
  }
}else{
  #
  # handle env config
  #
  if (isset($_ENV["ethereum_client"])){
    $_ethereum = json_decode($_ENV['ethereum_client']);
    $ethereum_client = array_merge($default_config, $_ethereum);
  }else{
    return collect($default_config);
  }
}
