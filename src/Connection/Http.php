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
use React\EventLoop\Factory;
use Carbon\Carbon;

class Http extends Connection implements RequestInterface {

  protected static $accepted_types = ['get', 'post'];

  protected static $default_send_options = array( "method" => null, "type" => "post", "params" => array() );

  public function send(array $options = array()) : ResponseInterface {
    $options = collect($this->validate_request($options));
    $http_method = $options->pull('type');

    if (!$http_method)
      throw new \InvalidArgumentException("The type option can't be blank or null");
    else{
      if (!in_array($http_method, static::$accepted_types))
        throw new \InvalidArgumentException("The type value is invalid");
    }

    try {

      $opts = [
                "headers" => [
                  "Accept" => "application/json",
                  "Content-Type" => "application/json"
                ],
                "body" => json_encode($options->all())
              ];
      $response = $this->client->request($http_method, null, $opts);

      if ( ($response->getStatusCode() >= 200) && ($response->getStatusCode() < 300) ){

        if (!empty($response->getBody())){
          $this->response = json_decode($response->getBody(), true);
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

    return $this->send($options->merge(array('type' => __FUNCTION__)));

  }

  public function post(array $options=array()) : ResponseInterface {

    return $this->send(array_merge($options, array('type' => __FUNCTION__)));

  }

  protected function setup_client($async=false){
    if ($async){
      return;
    }
    else{
      return $this->client= new HttpClient( array( "base_uri" => $this->config->get('http_endpoint') ) );
    }
  }

  public function poll(array $options= array(), int $poll_in_seconds= 1, $poll_until=null){

    $options = collect($options);

    $on_message= $options->pull('on_message');
    $on_close= $options->pull('on_close');
    $on_error= $options->pull('on_error');
    $before_request = $options->pull("before_request");
    $after_request = $options->pull("after_request");

    $options = collect($this->validate_request($options->all()));
    $http_method = $options->pull('type');

    if (!$http_method)
      throw new \InvalidArgumentException("The type option can't be blank or null");
    else{
      if (!in_array($http_method, static::$accepted_types))
        throw new \InvalidArgumentException("The type value is invalid");
    }
    $params = $options->all();

    $opts = [
                "headers" => [
                  "Accept" => "application/json",
                  "Content-Type" => "application/json"
                ],
                "body" => json_encode($params)
              ];

    $loop = Factory::create();
    // Create a Guzzle handler that integrates with React
    $handler = new CurlMultiHandler();

    // Create a Guzzle client that uses our special handler
    $client = new HttpClient([
        'handler' => HandlerStack::create($handler),
        'base_uri' => $this->config->get('http_endpoint')
    ]);

    $timer = $loop->addPeriodicTimer($poll_in_seconds, \Closure::bind(function () use (&$timer, $poll_until, $client, $http_method, &$opts, $on_message, $on_close, $on_error, $before_request, $after_request) {
      if(is_callable($before_request)){
        $opts = $before_request($opts);
      }
      $client->requestAsync($http_method, null, $opts)
      ->then(function (PsrResponseInterface $raw) use($on_message, $after_request, $opts)  {
          if (is_null($raw->getBody()))
            $response = array("result" => [], "message" => "No response body");
          else
            $response = json_decode($raw->getBody(), true);
          if (is_callable($on_message)){
            $res = new Response($response, false);
            $on_message($res);
          }
          if(is_callable($after_request)){
            $after_request();
          }

      }, function(RequestException $e) use ($on_error, $timer) {
        //print_r($e->getMessage());
        $timer->cancel();
        if (is_callable($on_error)){
          $on_error($e, $timer);
        }
      });


      $this->tick();

      if (($poll_until instanceOf Carbon) && (now()->copy()->gte($poll_until))){
        if (empty($this->handles) && Promise\queue()->isEmpty()) {
          $timer->cancel();
        }
      }
    }, $handler, $handler));

    // Run everything to completion!
    $loop->run();
  }

}
