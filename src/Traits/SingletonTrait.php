<?php
namespace Ihsaneddin\Ethereum\Traits;

trait SingletonTrait{

  private static $instance;
  private static $white_listed_methods = ["get_instance", "__invoke", "__sleep", "__clone", "__wakeup"];

  #
  # make constructor method protected
  #
  protected function __construct(){
    if (method_exists($this, 'boot_construct'))
      $this->boot_construct();
  }

  #
  # make them private
  #
  private function __clone(){}

  private function __wakeup(){}
  #
  # end of privatization
  #

  public static function get_instance(){
    if ( !isset(self::$instance) ){
      self::$instance = new self();
    }
    return self::$instance;
  }

  #
  # init instance
  #
  public static function initialize( array $config= array() ){
    return !static::$instance ?  self::get_instance() : static::$instance;
  }

  /*
    Delegate function call to self instance
  */
  public static function __callStatic($method, $arguments=array())
  {

    if ( !in_array($method, self::$white_listed_methods) ){
      if (method_exists(self::get_instance(), $method)){
        return call_user_func_array(array(self::get_instance(), $method, $arguments));
      }
    }else{
      if (method_exists(get_called_class(), $method)){
        return call_user_func_array( array(get_called_class(), $method), $arguments);
      }
    }

    $trace = debug_backtrace();
    trigger_error(
        'Undefined method: ' . $method .
        ' in ' . $trace[0]['file'] .
        ' on line ' . $trace[0]['line'],
        E_USER_NOTICE);
    return null;
  }

}
