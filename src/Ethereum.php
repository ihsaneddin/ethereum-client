<?php
namespace Ihsaneddin;

use Ihsaneddin\Ethereum\API\Socket;
use Ihsaneddin\Ethereum\API\Http;
use Ihsaneddin\Ethereum\API\WebSocket;
use Ihsaneddin\Ethereum\Connection\Response;

use Ihsaneddin\Ethereum\Etherscan\Api as Etherscan;
use Ihsaneddin\Ethereum\Ethplorer\Api as Ethplorer;

use Ihsaneddin\Ethereum\Interfaces\HasConfigInterface as HasConfig;
use Ihsaneddin\Ethereum\Interfaces\SingletonInterface;

use Ihsaneddin\Ethereum\Traits\ConfigTrait;
use Ihsaneddin\Ethereum\Traits\SingletonTrait;
use Ihsaneddin\Ethereum\Traits\EncodeHexTrait;
use Ihsaneddin\Ethereum\Traits\AccountTrait;
use Ihsaneddin\Ethereum\Traits\TransactionTrait;
use Ihsaneddin\Ethereum\Traits\TransactionReceiptTrait;
use Ihsaneddin\Ethereum\Traits\ContractTrait;

use Ihsaneddin\Ethereum\Exceptions\RpcNotAvailable;
use Ihsaneddin\Ethereum\Exceptions\ServiceUnavailable;
use Ihsaneddin\Ethereum\Exceptions\SocketConnectError;
use Illuminate\Support\Facades\Log;

class Ethereum implements HasConfig {

  use AccountTrait;
  use TransactionTrait;
  use TransactionReceiptTrait;
  use ContractTrait;

  use SingletonTrait{
    initialize as trait_initialize;
    get_instance as trait_get_instance;
  }

  use EncodeHexTrait;

  protected function boot_construct(array $config=array()){
    $this->setup_config($config, dirname(__FILE__). '/config.php');
  }

  public static function get_instance( $config= array() ){
    if ( !isset(self::$instance) ){
      self::$instance = new self($config);
    }
    return self::$instance;
  }

  public static function initialize( array $config= array() ){
    return !static::$instance ?  self::get_instance($config) : static::$instance;
  }

  use ConfigTrait;

  public function http(){
    return Http::get_instance();
  }

  public function websocket(){
    return WebSocket::get_instance();
  }

  public function socket(){
    return Socket::get_instance();
  }

  public function etherscan(){
    return Etherscan::get_instance();
  }

  public function ethplorer(){
    return Ethplorer::get_instance();
  }

  /**
   * Get preferred RPC API object.
   *
   * @return Ihsaneddin\Ethereum\Interfaces\ApiInterface
   * @throws Ihsaneddin\Ethereum\Exceptions\RpcNotAvailable
 */
  public function rpc(){

     $num_of_arguments = func_num_args();

    if ( ( $num_of_arguments > 2)  || ($num_of_arguments < 1) ) throw new \InvalidArgumentException("Wrong number of argument");

    if ($num_of_arguments == 1){
      list($callback) = func_get_args();
    }

    if ($num_of_arguments == 2){
      list($preferred_rpc, $callback) = func_get_args();
    }

    if (!isset($preferred_rpc)) $preferred_rpc = null;

    if (is_null($preferred_rpc)) $preferred_rpc = self::get_instance()->preferred_rpc();

    if (!is_array($preferred_rpc)) $preferred_rpc = array($preferred_rpc);

    foreach ($preferred_rpc as $rpc) {
      try{
        $result = $callback(self::get_instance()->$rpc());
        break;
      }catch(ServiceUnavailable $e){
        continue;
      }catch(SocketConnectError $e){
        continue;
      }
    }
    if (isset($result)) return $result;
    return new Response(array("message" => "Service is not available", "error" => 503));
  }

  protected $preferred_rpc = ['http'];

  protected function preferred_rpc(){
    return $this->config->get('preferred_rpc', self::get_instance()->preferred_rpc);
  }

  public function preferred_rpc_setting($preferred_rpc){
    $this->preferred_rpc = $preferred_rpc;
    return $this;
  }

}
