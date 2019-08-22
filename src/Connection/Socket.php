<?php
namespace Ihsaneddin\Ethereum\Connection;

use Ihsaneddin\Ethereum\Interfaces\RequestInterface;
use Ihsaneddin\Ethereum\Interfaces\ResponseInterface;

use Ihsaneddin\Ethereum\Exceptions\SocketConnectError;
use Ihsaneddin\Ethereum\Exceptions\ResponseError;


class Socket extends Connection implements RequestInterface{

  protected $socket;

  protected static $default_send_options = array( "method" => null, "params" => array() );

  public function send(array $options=array()) : ResponseInterface {
    return $this->post($options);
  }

  public function post(array $options=array()) : ResponseInterface {
    $options = $this->validate_request($options);

    $this->connect_socket();
    $this->write_to_socket($this->create_message($options));
    $response = $this->fetch_from_socket();
    $this->close_socket();

    if (!empty($response))
      $this->response = json_decode($response, true);

    return $this->response();
  }

  public function get(array $options=array()) : ResponseInterface {
    return $this->post($options);
  }

  protected function setup_client(){
    $host_path = collect(posix_getpwuid(posix_getuid()))->get('dir');
    $this->client = $this->config->get('socket_endpoint', $host_path. '/.local/share/io.parity.ethereum/jsonrpc.ipc');
  }

  protected function connect_socket(string $ipc_path=null){

    $ipc_path = is_null($ipc_path) ? $this->client : $ipc_path;

    $this->socket = socket_create(AF_UNIX, SOCK_STREAM, 0);

    if (!socket_connect($this->socket, $ipc_path,1)){
      $socket_error = socket_last_error();
      throw new SocketConnectError(socket_strerror($socket_error));
    }
  }

  protected function write_to_socket(string $params){
    #socket_sendto($this->client, $params, strlen($params), 0, $this->ipc_path);
    //print_r($params);
    //$params = "{\"jsonrpc\":\"2.0\",\"method\":\"personal_listAccounts\",\"params\":[],\"id\":1}";
    socket_send($this->socket, $params, strlen($params), MSG_EOF);
  }

  protected function close_socket(){
    socket_close($this->socket);
    $this->socket = null;
  }

  protected function fetch_from_socket(){
    //if (socket_recvfrom($this->client, $buf , 1024 , 0, $source) === false)
    //  throw new ResponseError("Read failed for". $this->ipc_path);
    $buf = null;
    socket_recv ( $this->socket, $buf, 100, 64*1024);
    return $buf;

  }

  private function create_message(array $params=array())
  {
    $json = new \StdClass;
    foreach ($params as $key => $value) {
      $json->$key = $value;
    }
    return json_encode($json);
  }

}