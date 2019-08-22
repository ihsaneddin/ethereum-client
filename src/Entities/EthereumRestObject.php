<?php
namespace Ihsaneddin\Ethereum\Entities;

use Ihsaneddin\Ethereum\Traits\EncodeHexTrait as EncodeHex;
use Ihsaneddin\Ethereum\Traits\EthereumTrait;

abstract class EthereumRestObject {

  use EncodeHex;
  use EthereumTrait;


  protected $__properties = array();
  protected $__data, $__altered_data;


  public function __construct(array $attributes=array()){
    $this->ethereum();
    $this->hydrate($attributes);
  }

  protected function hydrate(array $attributes=array()){
    $this->__data= $attributes;
    $data = $this->__data;

    if (is_array($data))
    {
      foreach($data as $name => $value)
      {
        if (isset($this->__properties[strtolower($name)]))
        {
          $key = "init_" . $this->__properties[strtolower($name)];
          if (method_exists($this, $key))
            if(is_null($this->{strtolower($name)})){
              $this->$key($value);
            }
        }
        $this->__data[strtolower($name)] = $value;
      }
    }
  }

  public function data(){
    return $this->altered_data() ? $this->altered_data() : $this->unaltered_data();
  }

  public function unaltered_data(){
    return $this->__data;
  }

  public function altered_data(){
    return $this->__altered_data;
  }

  public function reset(){
    $this->__altered_data= null;
  }

  public function __set($name, $value)
  {
    if (isset($this->__properties[strtolower($name)]))
    {
      $key = "set_" . $this->__properties[strtolower($name)];
      if(method_exists($this, $key))
        return $this->$key($value);
    }

    $data = $this->data();

    if (isset($data[$name]))
      return $data[$name] = $value;
  }

  /**
   * @internal
   */
  public function __get($name)
  {

    $data = $this->data();

    if (isset($this->__properties[strtolower($name)]))
    {
      $key = "get_" . $this->__properties[strtolower($name)];
      if (method_exists($this, $key))
        return $this->$key();
      if (array_key_exists(strtolower($name), $data))
        return $data[strtolower($name)];
      else
        return null;
    }

    if (array_key_exists($name, $data)){
      return $data[$name];
    }

    $trace = debug_backtrace();
    trigger_error(
        'Undefined property via __get(): ' . $name .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE);
    return null;
  }

  /**
   * @internal
   */
  public function __isset($name)
  {
    if (isset($this->__properties[strtolower($name)]))
    {
      return true;
    }
    $data = $this->data();
    return isset($data[$name]);
  }

  /**
   * @internal
   */
  public function __unset($name)
  {
    if (isset($this->__properties[strtolower($name)]))
    {
      $key = "set_" . $this->__properties[strtolower($name)];
      if (method_exists($this, $key));
        return $this->$key(null);
    }

    $data = $this->_data();
    unset($data[strtolower($name)]);
  }

  public function encode_properties_to_hex(array $encode_hex_keys = array()){
    return $this->altered_properties(function($_properties){
      return $this->encode_hex($_properties, $encode_hex_keys);
    });
  }

  public function decode_properties_from_hex(array $decode_hex_keys= array()){
    return $this->altered_properties(function($_properties){
      return $this->decode_hex($_properties, $decode_hex_keys);
    });
  }

  protected function alter_data(){
    $num_of_arguments = func_num_args();

    if ( ( $num_of_arguments > 2)  || ($num_of_arguments <= 1) ) throw new \InvalidArgumentException("Wrong number of argument");

    if ($num_of_arguments == 1){
      list($new_data) = func_get_args();
    }

    if ($num_of_arguments == 2){
      list($condition, $new_data) = func_get_args();
    }

    if (!isset($condition)) $condition = true;

    if ($condition){
      if (is_callable($new_data)){
        $this->__altered_data = $new_data($this->_data());
      }else{
        $this->__altered_data = $new_data;
      }
    }
    return $this;
  }

  abstract public function as_params() : array;

}