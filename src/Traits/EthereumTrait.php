<?php
namespace Ihsaneddin\Ethereum\Traits;

use Ihsaneddin\Ethereum;

trait EthereumTrait{

  protected $ethereum;
  protected $preferred_rpc;

  /**
    * @return Ethereum
  */
  protected function ethereum(){
    if ($this->ethereum) return $this->ethereum;
    return $this->ethereum= Ethereum::get_instance();
  }

  public function preferred_rpc(){
    return $this->preferred_rpc;
  }

  public function invoke($preferred_rpc = null, callable $callback){
    return $this->ethereum()->rpc($preferred_rpc, $callback);
  }

  protected static function ethereum_instance(){
    return Ethereum::get_instance();
  }

}