<?php
namespace Ihsaneddin\Ethereum\Traits;

trait ShhModuleTrait{

  public function shh_info(){
    return $this->post(__FUNCTION__);
  }

  public function shh_newKeyPair(){
    return $this->post(__FUNCTION__);
  }

  public function shh_addPrivateKey(string $data){
    return $this->post(__FUNCTION__, array($data));
  }

  public function shh_newSymKey(){
    return $this->post(__FUNCTION__);
  }

  public function shh_getPublicKey(string $data){
    return $this->post(__FUNCTION__, array($data));
  }

  public function shh_getPrivateKey(string $data){
    return $this->post(__FUNCTION__, array($data));
  }

  public function shh_getSymKey(string $data){
    return $this->post(__FUNCTION__, $data);
  }

  public function shh_deleteKey(string $data){
    return $this->post(__FUNCTION__, array($data));
  }

  public function shh_post(array $post_options){
    return $this->post(__FUNCTION__, array($post_options));
  }

  public function shh_newMessageFilter(array $message_data){
    return $this->post(__FUNCTION__, array($message_data));
  }

  public function shh_getFilterMessages(string $data){
    return $this->post(__FUNCTION__, array($data));
  }

  public function shh_deleteMessageFilter(string $data){
    return $this->post(__FUNCTION__, array($data));
  }

  public function shh_subscribe(array $params=array()){
    if (method_exists($this, 'subscribe')){
      $this->subscribe($params);
    }else{
      return $this->post(__FUNCTION__, $params);
    }
  }

  public function shh_unsubscribe(array $params=array()){
    return $this->post(__FUNCTION__, $params);
  }


}