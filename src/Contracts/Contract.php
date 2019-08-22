<?php
namespace Ihsaneddin\Ethereum\Contracts;

use Ihsaneddin\Ethereum\Traits\EthereumTrait;

use InvalidArgumentException;

use Ihsaneddin\Ethereum\Support\Utils;
use Ihsaneddin\Ethereum\Eth;
use Ihsaneddin\Ethereum\Contracts\Ethabi;
use Ihsaneddin\Ethereum\Contracts\Types\Address;
use Ihsaneddin\Ethereum\Contracts\Types\Boolean;
use Ihsaneddin\Ethereum\Contracts\Types\Bytes;
use Ihsaneddin\Ethereum\Contracts\Types\Integer;
use Ihsaneddin\Ethereum\Contracts\Types\Str;
use Ihsaneddin\Ethereum\Contracts\Types\Uinteger;
use Ihsaneddin\Ethereum\Validators\AddressValidator;
use Ihsaneddin\Ethereum\Validators\HexValidator;
use Ihsaneddin\Ethereum\Formatters\AddressFormatter;
use Ihsaneddin\Ethereum\Validators\StringValidator;

class Contract{

  use EthereumTrait;

  protected $abi;

  protected $constructor=[];

  protected $functions=[];

  protected $events=[];

  protected $to_address;

  protected $byte_code;

  protected $ethabi;

  public function __construct($abi){

    $this->ethereum();
    $abi = Utils::jsonToArray($abi, 5);

    foreach ($abi as $item) {
        if (isset($item['type'])) {
            if ($item['type'] === 'function') {
                $this->functions[$item['name']] = $item;
            } elseif ($item['type'] === 'constructor') {
                $this->constructor = $item;
            } elseif ($item['type'] === 'event') {
                $this->events[$item['name']] = $item;
            }
        }
    }

    $this->abi = $abi;
    $this->ethabi = new Ethabi([
        'address' => new Address,
        'bool' => new Boolean,
        'bytes' => new Bytes,
        'int' => new Integer,
        'string' => new Str,
        'uint' => new Uinteger,
    ]);

  }

  public function __get($name)
  {
      $method = 'get' . ucfirst($name);

      if (method_exists($this, $method)) {
          return call_user_func_array([$this, $method], []);
      }
      return false;
  }

  public function __set($name, $value)
  {
      $method = 'set' . ucfirst($name);

      if (method_exists($this, $method)) {
          return call_user_func_array([$this, $method], [$value]);
      }
      return false;
  }

  public function getFunctions()
  {
      return $this->functions;
  }

  public function getEvents()
  {
      return $this->events;
  }

  public function getConstructor()
  {
      return $this->constructor;
  }

  public function getAbi()
  {
      return $this->abi;
  }

  public function getEthAbi(){
    return $this->ethabi;
  }

  public function setBytecode($bytecode)
  {
      return $this->bytecode($bytecode);
  }

  public function setToAddress($address)
  {
      return $this->at($address);
  }

  public function at($address)
  {
    if (AddressValidator::validate($address) === false) {
      throw new InvalidArgumentException('Please make sure address is valid.');
    }
    $this->toAddress = AddressFormatter::format($address);

    return $this;
  }

  public function bytecode($bytecode)
  {
      if (HexValidator::validate($bytecode) === false) {
          throw new InvalidArgumentException('Please make sure bytecode is valid.');
      }
      $this->bytecode = Utils::stripZero($bytecode);

      return $this;
  }

  public function abi($abi)
  {
      if (StringValidator::validate($abi) === false) {
          throw new InvalidArgumentException('Please make sure abi is valid.');
      }
      $abi = Utils::jsonToArray($abi, 5);

      foreach ($abi as $item) {
          if (isset($item['type'])) {
              if ($item['type'] === 'function') {
                  $this->functions[$item['name']] = $item;
              } elseif ($item['type'] === 'constructor') {
                  $this->constructor = $item;
              } elseif ($item['type'] === 'event') {
                  $this->events[$item['name']] = $item;
              }
          }
      }
      $this->abi = $abi;

      return $this;
  }

  public function new()
  {
      if (isset($this->constructor)) {
          $constructor = $this->constructor;
          $arguments = func_get_args();
          $callback = array_pop($arguments);

          if (count($arguments) < count($constructor['inputs'])) {
              throw new InvalidArgumentException('Please make sure you have put all constructor params and callback.');
          }
          if (is_callable($callback) !== true) {
              throw new \InvalidArgumentException('The last param must be callback function.');
          }
          if (!isset($this->bytecode)) {
              throw new \InvalidArgumentException('Please call bytecode($bytecode) before new().');
          }
          $params = array_splice($arguments, 0, count($constructor['inputs']));
          $data = $this->ethabi->encodeParameters($constructor, $params);
          $transaction = [];

          if (count($arguments) > 0) {
              $transaction = $arguments[0];
          }
          $transaction['to'] = '';
          $transaction['data'] = '0x' . $this->bytecode . Utils::stripZero($data);

          return $this->ethereum->rpc(function($rpc) use($transaction, $callback) {
            $response = $rpc->eth_sendTransaction($transaction);
            if (is_callable($callback))
              $callback($response);
            else
              return $response;
          });
      }
  }

  public function send()
  {
      if (isset($this->functions)) {
          $arguments = func_get_args();
          $method = array_splice($arguments, 0, 1)[0];
          $callback = array_pop($arguments);

          if (!is_string($method) && !isset($this->functions[$method])) {
              throw new InvalidArgumentException('Please make sure the method is existed.');
          }
          $function = $this->functions[$method];

          if (count($arguments) < count($function['inputs'])) {
              throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
          }
          if (is_callable($callback) !== true) {
              throw new \InvalidArgumentException('The last param must be callback function.');
          }

          $params = array_splice($arguments, 0, count($function['inputs']));
          $data = $this->ethabi->encodeParameters($function, $params);
          $functionName = Utils::jsonMethodToString($function);
          $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
          $transaction = [];

          if (count($arguments) > 0) {
              $transaction = $arguments[0];
          }
          $transaction['to'] = $this->toAddress;
          $transaction['data'] = $functionSignature . Utils::stripZero($data);

          return $this->ethereum->rpc(function($rpc) use($transaction, $callback) {
            $response = $rpc->eth_sendTransaction($transaction);
            if (is_callable($callback))
              return $callback($response);
            else
              return $response;
          });
      }
  }

  public function call()
  {
      if (isset($this->functions)) {
          $arguments = func_get_args();
          $method = array_splice($arguments, 0, 1)[0];
          $callback = array_pop($arguments);
          if (! is_callable($callback)){
            array_push($arguments, $callback);
          }

          if (!is_string($method) && !isset($this->functions[$method])) {
              throw new InvalidArgumentException('Please make sure the method is existed.');
          }
          $function = $this->functions[$method];
          if (count($arguments) < count($function['inputs'])) {
              throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
          }
          if (is_callable($callback) !== true) {
            //throw new \InvalidArgumentException('The last param must be callback function.');
          }
          $params = array_splice($arguments, 0, count($function['inputs']));
          $data = $this->ethabi->encodeParameters($function, $params);
          $functionName = Utils::jsonMethodToString($function);
          $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
          $transaction = [];

          if (count($arguments) > 0) {
              $transaction = $arguments[0];
          }
          $transaction['to'] = $this->toAddress;
          $transaction['data'] = $functionSignature . Utils::stripZero($data);

          return $this->ethereum->rpc(function($rpc) use($transaction, $function, $callback){
            $response= $rpc->eth_call($transaction)->alter_result(function($result) use($function,$transaction) {
              return $this->ethabi->decodeParameters($function, $result);
            });

            if (is_callable($callback)){
             $callback($response);
            }
            return $response;
          })->result();
      }
  }

  public function estimateGas()
  {
    if (isset($this->functions) || isset($this->constructor)) {
        $arguments = func_get_args();
        $callback = array_pop($arguments);

        if (empty($this->toAddress) && !empty($this->bytecode)) {
            $constructor = $this->constructor;

            if (count($arguments) < count($constructor['inputs'])) {
                throw new InvalidArgumentException('Please make sure you have put all constructor params and callback.');
            }
            if (is_callable($callback) !== true) {
                throw new \InvalidArgumentException('The last param must be callback function.');
            }
            if (!isset($this->bytecode)) {
                throw new \InvalidArgumentException('Please call bytecode($bytecode) before estimateGas().');
            }
            $params = array_splice($arguments, 0, count($constructor['inputs']));
            $data = $this->ethabi->encodeParameters($constructor, $params);
            $transaction = [];

            if (count($arguments) > 0) {
                $transaction = $arguments[0];
            }
            $transaction['to'] = '';
            $transaction['data'] = '0x' . $this->bytecode . Utils::stripZero($data);
        } else {
            $method = array_splice($arguments, 0, 1)[0];

            if (!is_string($method) && !isset($this->functions[$method])) {
                throw new InvalidArgumentException('Please make sure the method is existed.');
            }
            $function = $this->functions[$method];

            if (count($arguments) < count($function['inputs'])) {
                throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
            }
            if (is_callable($callback) !== true) {
                throw new \InvalidArgumentException('The last param must be callback function.');
            }
            $params = array_splice($arguments, 0, count($function['inputs']));
            $data = $this->ethabi->encodeParameters($function, $params);
            $functionName = Utils::jsonMethodToString($function);
            $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
            $transaction = [];

            if (count($arguments) > 0) {
                $transaction = $arguments[0];
            }
            $transaction['to'] = $this->toAddress;
            $transaction['data'] = $functionSignature . Utils::stripZero($data);
        }

        return $this->ethereum->rpc(function($rpc) use($transaction, $callback) {
          $response = $rpc->eth_estimateGas($transaction);
          if (is_callable($callback))
            return $callback($response);
          else
            return $response;
        });

        $this->eth->estimateGas($transaction, function ($err, $gas) use ($callback){
            if ($err !== null) {
                return call_user_func($callback, $err, null);
            }
            return call_user_func($callback, null, $gas);
        });
    }
  }

  public function getData()
  {
      if (isset($this->functions) || isset($this->constructor)) {
          $arguments = func_get_args();
          $functionData = '';

          if (empty($this->toAddress) && !empty($this->bytecode)) {
              $constructor = $this->constructor;

              if (count($arguments) < count($constructor['inputs'])) {
                  throw new InvalidArgumentException('Please make sure you have put all constructor params and callback.');
              }
              if (!isset($this->bytecode)) {
                  throw new \InvalidArgumentException('Please call bytecode($bytecode) before getData().');
              }
              $params = array_splice($arguments, 0, count($constructor['inputs']));
              $data = $this->ethabi->encodeParameters($constructor, $params);
              $functionData = $this->bytecode . Utils::stripZero($data);
          } else {
              $method = array_splice($arguments, 0, 1)[0];

              if (!is_string($method) && !isset($this->functions[$method])) {
                  throw new InvalidArgumentException('Please make sure the method is existed.');
              }
              $function = $this->functions[$method];

              if (count($arguments) < count($function['inputs'])) {
                  throw new InvalidArgumentException('Please make sure you have put all function params and callback.');
              }
              $params = array_splice($arguments, 0, count($function['inputs']));
              $data = $this->ethabi->encodeParameters($function, $params);
              $functionName = Utils::jsonMethodToString($function);
              $functionSignature = $this->ethabi->encodeFunctionSignature($functionName);
              $functionData = Utils::stripZero($functionSignature) . Utils::stripZero($data);
          }
          return $functionData;
      }
  }

  public function decodeData(string $input){
    //$input = Utils::stripZero($input);

    # get function id
    $function_signature = mb_substr($input, 0, 10);
    $parameters = mb_substr($input, 10);

    $function_name = null;

    foreach ($this->functions as $functionName => $function) {
      $functionSignature = $this->ethabi->encodeFunctionSignature($function);
      if ($function_signature === $functionSignature){
        $function_name = $functionName;
        break;
      }
    }

    #supported function buyin, transfer, transferFrom
    $supported_functions = array('buyin', 'transfer', 'transferFrom');

    if (!in_array($function_name, $supported_functions)){
      return null;
    }

    $input_str_length = 0;

    $function = $this->functions[$function_name];

    if (isset($function['inputs'])) {
      $inputs = $function['inputs'];
      $count = count($inputs);
      $input_str_length = 64 * $count;

      if (strlen($parameters) != $input_str_length){
        throw new InvalidArgumentException('Invalid paremeters length');
      }

      $firstIndex = 0;
      $lastIndex= 64;

      for ($i=0; $i < $count; $i++) {

        $current_param = mb_substr($parameters, $firstIndex, $lastIndex);
        $firstIndex = $firstIndex + 64;
        $lastIndex = $lastIndex + 64;

        $decoded_offset_params="";
        $current_param_array = str_split($current_param);
        $still_zero = true;

        foreach ($current_param_array as $val) {
          if ($val != "0"){
            $still_zero=false;
          }

          if(!$still_zero){
            $decoded_offset_params = $decoded_offset_params. $val;
          }
        }
        $decoded_offset_params = "0x".$decoded_offset_params;
        $input_type = $inputs[$i]["name"];

        if (!isset($function["params"])){
          $function["params"]=array();
        }

        $function["params"][$input_type] = $decoded_offset_params;

      }

      return $function;

    }else{
      $function["params"] = null;
      return $function;
    }

  }

}