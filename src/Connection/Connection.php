<?php
namespace Ihsaneddin\Ethereum\Connection;

use Ihsaneddin\Ethereum\Traits\EthereumTrait;
use Ihsaneddin\Ethereum\Interfaces\ResponseInterface;

use Illuminate\Support\Collection;

abstract class Connection {

  use EthereumTrait;

  protected $client, $response_adapter, $config;
  protected $response = array();
  protected static $default_send_options = array( "method" => null, "params" => array() );
  protected static $default_rpc_version = '2.0';
  protected static $allowed_null_params_on_methods = ["eth_newFilter", "eth_getLogs"];

  public function __construct(array $options=array()){
    $this->config = $this->ethereum()->config()->merge($options);
    $this->setup_client();
    $this->response_adapter = isset($options['response_adapter']) ? $options['response_adapter'] : $this->config->get('response_adapter');
  }

  public function response() : ResponseInterface {
    $response = is_null($this->response) ? $this->blank_response_default() : $this->response;
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

  abstract protected function setup_client();

  protected function reject_null(array $params=array()){
    $array = array();
    foreach ($params as $key => $value) {
      if (!is_null($value))
        $array[$key]= is_array($value) ? $this->reject_null($value) : $value;
    }
    return $array;
  }

  protected function validate_request(array $options=array()) : array {
    $options = collect(static::$default_send_options)->merge($options);

    if (!$options->get('method'))
      throw new \InvalidArgumentException("The method option can't be blank or null");

    if (!empty($options->get('params'))){
      if(!\in_array($options->get("method"), static::$allowed_null_params_on_methods)){
        $options->put('params', $this->reject_null($options->get('params')));
      }
    }

    if (!$options->get('id'))
      $options = $options->merge(array('id' => mt_rand(10,100)));

    if (!$options->get("jsonrpc"))
      $options = $options->merge(array('jsonrpc' => $this->config->get('rpc_version', static::$default_rpc_version)));

    $options = $options->only(array_merge(array_keys(static::$default_send_options), ['jsonrpc', 'id'] ));

    return $options->all();
  }

  protected function blank_response_default(){
    return array("result" => [], 'message' => "No response body");
  }

}