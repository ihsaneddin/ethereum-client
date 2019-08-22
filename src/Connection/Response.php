<?php
namespace Ihsaneddin\Ethereum\Connection;

use Ihsaneddin\Ethereum\Exceptions\ResponseError;
use Ihsaneddin\Ethereum\Interfaces\ResponseInterface;

use Illuminate\Support\Collection;

class Response implements ResponseInterface {

  public $raw,$result,$error,$altered_result;

  public function __construct( array $response=array(), $raise_error_if_any = true ){
    $this->raw = $response;
    //print_r($response);
    $this->result = isset($response['result']) ? $response['result'] : $response;

    if (is_array($this->result)){
      $this->result = collect($this->result);
    }

    if (array_key_exists('error', $response)){
      $this->error = collect($response['error']);
    }

    if ($raise_error_if_any)
      $this->raise_error_if_any();

  }

  public function raise_error_if_any(){
    if (!is_null($this->error)){
      //print_r("error");
      throw new ResponseError($this->error->get('message'), $this->error->get('code'));
    }
  }

  public function is_success(){
    return !$this->error;
  }

  public function successful(){ return $this->is_success(); }

  public function result(string $key = null){
    $result= $this->altered_result ? $this->altered_result() : $this->unaltered_result();
    if ($key && ($result instanceOf Collection))
      return $result->get($key);
    return $result;
  }

  public function getResult(string $key = null){
    return $this->result($key);
  }

  public function unaltered_result(){
    if (is_array($this->result)){
      $this->result = collect($this->result);
    }
    return $this->result;
  }

  public function altered_result(){
    if (is_array($this->altered_result)){
      $this->altered_result = collect($this->altered_result);
    }
    return $this->altered_result;
  }

  public function raw() : array { return $this->raw; }

  public function alter_result(){

    $num_of_arguments = func_num_args();

    if ( ( $num_of_arguments > 2)  || ($num_of_arguments < 1) ) throw new \InvalidArgumentException("Wrong number of argument");

    if ($num_of_arguments == 1){
      list($new_result) = func_get_args();
    }

    if ($num_of_arguments == 2){
      list($condition, $new_result) = func_get_args();
    }

    if (!isset($condition)) $condition = true;

    if ($this->successful() && $condition){
      if (is_callable($new_result)){
        $this->altered_result = $new_result($this->unaltered_result());
      }else{
        $this->altered_result = $new_result;
      }
    }
    return $this;
  }

}
