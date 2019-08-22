<?php
namespace Ihsaneddin\Ethereum\Connection;

use Exception;

use Ihsaneddin\Ethereum\Exceptions\RequestError as ResponseError;
use Ihsaneddin\Ethereum\Exceptions\NotFound;
use Ihsaneddin\Ethereum\Exceptions\ServiceUnavailable;
use Ihsaneddin\Ethereum\Exceptions\UnknownError;
use Ihsaneddin\Ethereum\Exceptions\ConnectionFailed;

use Ihsaneddin\Ethereum\Interfaces\RequestInterface;
use Ihsaneddin\Ethereum\Interfaces\ResponseInterface;
use Ihsaneddin\Ethereum\Interfaces\RedisInterface;

use Ihsaneddin\Ethereum\Traits\RedisTrait;

use \Ihsaneddin\Ethereum\Connection\Response as Response;

use Ratchet\Client\WebSocket as RatchetWebsocket;
use \Ratchet\RFC6455\Messaging\MessageInterface as RatchetMessage;
use \React\EventLoop\Factory as Loop;
use \React\Socket\Connector as ReactConnector;
use Ratchet\Client\Connector as RatchetConnector;
use \GuzzleHttp\Psr7\Response as RawResponse;
use Illuminate\Support\Collection;

use function \Ratchet\Client\connect;


class WebSocket extends Connection implements RequestInterface, RedisInterface {

  protected $last_block;

  public function __construct(array $options=array()){
    parent::__construct($options);
    $this->response_key= uniqid();
  }

  use RedisTrait;

  protected $response_key;
  protected static $callbaks = ['on_message', 'on_close', 'on_error'];

  protected function redis_options() : array {
    return $this->config->get('redis_client', ['host' => '127.0.0.1', 'port' => 6379]);
  }

  protected static $default_send_options = array( "method" => null, "params" => array() );

  public function send(array $options=array()) : ResponseInterface {
    $this->post($options);
  }

  public function post(array $options=array()) : ResponseInterface {

    $options = collect($this->validate_request($options));

    $on_message= $options->pull('on_message');
    $on_close= $options->pull('on_close');
    $on_error= $options->pull('on_error');

    $params = $options->all();

    $response_key = $this->response_key;

    connect($this->client)->then( function($connection) use($params, $on_message, $on_error, $on_close, $response_key) {

      $connection->send(json_encode($params));

      $connection->on("message", function($msg) use($on_message, $connection, $response_key) {

        $connection->close();

        $raw = new RawResponse($status= 200, array(), $body= $msg->getPayload());

        if (is_null($raw->getBody()))
          $response = array("result" => [], "message" => "No response body");
        else if (is_string($raw->getBody()))
          $response = array("result" => $raw->getBody);
        else
          $response = json_decode($raw->getBody(), true);

        //print_r($response);

        if (is_callable($on_message)){
          $res = new Response($response);
          $on_message($res);
          if(!empty($res->result('blockNumber'))){
            $this->last_block= $res->result('blockNumber');
          }
        }else{
          $this->push_to_redis($response_key, json_encode($response));
        }

      });

      $connection->on("close", function($code=null, $reason=null) use($on_close) {
        if (is_callable($on_close)){
          $on_close($code, $reason);
        }
      });

    }, function($e) use($on_error) {

      if (is_callable($on_error))
        $on_error($e);
      else
        echo "Could not connect: ".$e->getMessage()."\n";

    });

    return $this->response();

  }

  public function get(array $options=array()) : ResponseInterface {
    $this->post($options);
  }

  public function response() : ResponseInterface {
    $response = is_null($this->response) ? $this->stored_response() : $this->response;
    $response_adapter = \Ihsaneddin\Ethereum\Connection\Response::class;

    if ($this->response_adapter)
      $response_adapter = $this->response_adapter;

    return new $response_adapter($response);
  }

  public function raw_response() : Collection {
    if (is_null($this->response))
      return collect($this->blank_response_default());
    return collect($this->response);
  }

  protected function stored_response(){
    $stored_response = $this->pull_from_redis($this->response_key);
    if ($stored_response){
      return json_decode($stored_response, true);
    }else{
      return $this->blank_response_default();
    }
  }

  protected function setup_client(){
    $this->client = $this->config->get('websocket_endpoint', 'ws://127.0.0.1:8546');
  }

  protected function blank_response_default(){
    return array("result" => [], 'message' => "No response body");
  }

  protected function validate_request(array $options= array()) : array {
    $default = parent::validate_request($options);
    $callbacks = collect($options)->only(static::$callbaks);
    return $callbacks->merge($default)->all();
  }


  public function subscribe(array $options= array()){

    $options = collect($this->validate_request($options));

    $on_message= $options->pull('on_message');
    $on_close= $options->pull('on_close');
    $on_error= $options->pull('on_error');

    $params = $options->all();

    $loop = Loop::create();

    $react_connector = new ReactConnector($loop, [
        'dns' => '8.8.8.8',
        'timeout' => 30
    ]);

    $connector = new RatchetConnector($loop, $react_connector);

    $connector($this->client, array(), [])
      ->then( function(RatchetWebSocket $connection) use($on_message, $on_close, $params) {

          $connection->send(json_encode($params));

          $connection->on('message', function(RatchetMessage $msg) use ($connection, $on_message) {
            $raw = new RawResponse($status= 200, array(), $body= $msg->getPayload());

            if (is_null($raw->getBody()))
              $response = array("result" => [], "message" => "No response body");
            else
              $response = json_decode($raw->getBody(), true);

            if (is_array($response) && isset($response['params'])){
              $response = $response['params'];
            }

            if (is_callable($on_message)){
              $res = new Response($response, false);
              $on_message($res);
              if(!empty($res->result('blockNumber'))){
                $this->last_block= $res->result('blockNumber');
              }
            }
          });

          $connection->on("close", function($code=null, $reason= null) use($on_close, $params) {

            self::post("unsubscribe", $params);

            if (is_callable($on_close))
              $on_close($code, $reason, $this->last_block);
            else
              echo "Connection closed ({$code} - {$reason})\n";
          });
      }, function($e) use($loop, $on_error) {
        if (is_callable($on_error))
          $on_error($e, $loop, $this->last_block);
        else
          echo "Could not connect: {$e->getMessage()}\n";

        $loop->stop();
      });


    $loop->run();

  }

}