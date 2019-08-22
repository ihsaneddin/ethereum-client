<?php
namespace Ihsaneddin\Ethereum\Ethplorer;

/*
  full documentation https://github.com/EverexIO/Ethplorer/wiki/Ethplorer-API
*/

use Ihsaneddin\Ethereum\Connection\Connection;
use Ihsaneddin\Ethereum\Interfaces\RequestInterface;
use Ihsaneddin\Ethereum\Interfaces\ResponseInterface;
use Ihsaneddin\Ethereum\Exceptions\NotFound;
use Ihsaneddin\Ethereum\Exceptions\ServiceUnavailable;
use Ihsaneddin\Ethereum\Exceptions\UnknownError;
use Ihsaneddin\Ethereum\Exceptions\ConnectionFailed;

use GuzzleHttp\Psr7\Response as HttpResponse;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\Promise;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Carbon\Carbon;

class Http extends Connection implements RequestInterface {

  public function send(array $options=array()) : ResponseInterface{
    $options = collect($this->validate_request($options));

    $http_method = $options->pull('type');
    $endpoint = $options->pull('endpoint');
    $params = $options->pull("params");

    try {

      $opts = [
                "headers" => [
                  "Accept" => "application/json",
                  "Content-Type" => "application/json"
                ]//,
                //"query" => array_merge($options->all(), array("apiKey" => $this->config->get('ethplorer_api_key')))
              ];

      $this->client= new HttpClient( array( "base_uri" => $this->config->get('ethplorer_api_url'). '/' .$endpoint. '/' .$params. '?apiKey=' .$this->config->get('ethplorer_api_key')) );

      $response = $this->client->request($http_method, null, $opts);

      if ( ($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300) ){

        if (!empty($response->getBody())){
          $raw = json_decode($response->getBody(), true);
          if (isset($raw['error'])){
            $this->response = $raw;
          }else{
            $this->response = array("result" => $raw);
          }
        }
        return $this->response();
      }

    }catch(RequestException $e){
      if ( (int) $e->getCode() == 0 ) throw new ServiceUnavailable($e->getMessage(), 503);

      throw new UnknownError($e->getMessage());


    }catch(ClientException $e){
      if ( !$e->hasResponse() ) throw new ServiceUnavailable($e->getMessage(), 503);

      $code = (int) $e->getResponse()->getStatusCode();

      switch ( $code ) {

        case ( ($code >= 400) && ($code < 500) ):
            throw new NotFound($e->getMessage(), $code);
          break;

        case ( ($code >= 500) ) :
            throw new ServiceUnavailable($e->getMessage(), $code);
          break;

        default:
            throw new UnknownError($e->getMessage(), $code);
          break;
      }

    }catch(TransferException$e){
      if ( !$e->hasResponse() ) throw new ConnectionFailed($e->getMessage(), 500);

      throw new UnknownError($e->getMessage(), (int) $response->getStatusCode());
    }catch(ConnectException $e){
      throw new ServiceUnavailable($e->getMessage(), $e->getResponse()->getStatusCode());
    }

  }

  public function get(array $options=array()) : ResponseInterface {

    return $this->send(collect($options)->merge(array('type' => __FUNCTION__))->all());

  }

  public function post(array $options=array()) : ResponseInterface {

    return $this->send(array_merge($options, array('type' => __FUNCTION__)));

  }

  protected function setup_client($async=false){
    return $this->client= new HttpClient( array( "base_uri" => $this->config->get('ethplorer_api_url'). '?apikey=' .$this->config->get('ethplorer_api_key') ) );
  }

  protected function validate_request(array $params=array())  : array {
    $params = collect($params);

    if (empty($params->get('endpoint')))
    {
      throw new \InvalidArgumentException("Endpoint key must be provided");
    }

    return $params->all();
  }

}