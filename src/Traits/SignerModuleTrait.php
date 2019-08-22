<?php
namespace Ihsaneddin\Ethereum\Traits;

trait SignerModuleTrait {

  public function signer_confirmRequest($request_id,  array $transaction=array(), string $password, $encode_hex=false){
    if ($encode_hex) $request_id = $this->encode_hex($request_id);
    return $this->post(__FUNCTION__, array($request_id, $transaction, $password));
  }

  public function signer_confirmRequestRaw($request_id, string $data, $encode_hex=false){
    if ($encode_hex) $request_id = $this->encode_hex($request_id);
    return $this->post(__FUNCTION__, array($request_id, $data));
  }

  public function signer_confirmRequestWithToken($request_id, array $transaction=array(), string $password_or_token){
    return $this->post(__FUNCTION__, array($request_id, $transaction, $password_or_token));
  }

  public function signer_generateAuthorizationToken(){
    return $this->post(__FUNCTION__);
  }

  public function signer_generateWebProxyAccessToken(string $domain){
    return $this->post(__FUNCTION__, array($domain));
  }

  public function signer_rejectRequest($request_id, $encode_hex=false){
    if ($encode_hex) $request_id = $this->encode_hex($request_id);
    return $this->post(__FUNCTION__, array($request_id));
  }

  public function signer_requestsToConfirm(){
    return $this->post(__FUNCTION__);
  }

  public function signer_subscribePending(){
    return $this->post(__FUNCTION__);
  }

  public function signer_unsubscribePending($subscription_id){
    return $this->post(__FUNCTION__, array($subscription_id));
  }

}