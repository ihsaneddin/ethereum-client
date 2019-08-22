<?php
namespace Ihsaneddin\Ethereum\Traits;

trait SecretStoreModuleTrait {

  public function secretstore_encrypt(string $account_address, string $password, string $data, string $document_data){
    return $this->post(__FUNCTION__, array($account_address, $password, $data, $document_data));
  }

  public function secretstore_decrypt(string $account_address, string $password, string $data, string $encrypted_docunent_data){
    return $this->post(__FUNCTION__, array($account_address, $password, $data, $encrypted_document_data));
  }

  public function secretstore_shadowDecrypt(string $account_address, string $password, string $decrypted_secret, string $common_point, string $decrypt_shadow, $encrypted_docunent_data){
    return $this->post(__FUNCTION__, array($account_address, $password, $decrypted_secret, $common_point, $decrypt_shadow, $encrypted_docunent_data));
  }

  public function secretstore_serversSetHash($node_ids){
    if (!is_array($node_ids)) $node_ids = array($node_ids);
    return $this->post(__FUNCTION__, array($node_ids));
  }

  public function secretstore_signRawHash(string $account_address, string $password, string $to_be_signed_hash){
    return $this->post(__FUNCTION__,array($account_address, $password, $to_be_signed_hash));
  }

}