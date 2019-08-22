<?php
namespace Ihsaneddin\Ethereum\API;

use Ihsaneddin\Ethereum\Traits\EthereumTrait;
use Ihsaneddin\Ethereum\Traits\EncodeHexTrait;
use Ihsaneddin\Ethereum\Traits\Web3ModuleTrait;
use Ihsaneddin\Ethereum\Traits\NetModuleTrait;
use Ihsaneddin\Ethereum\Traits\EthModuleTrait;
use Ihsaneddin\Ethereum\Traits\ShhModuleTrait;
use Ihsaneddin\Ethereum\Traits\SecretStoreModuleTrait;
use Ihsaneddin\Ethereum\Traits\SignerModuleTrait;
use Ihsaneddin\Ethereum\Traits\TraceModuleTrait;
use Ihsaneddin\Ethereum\Traits\ParityAccountsModuleTrait;
use Ihsaneddin\Ethereum\Traits\ParitySetModuleTrait;
use Ihsaneddin\Ethereum\Traits\ParitySubPubModuleTrait;
use Ihsaneddin\Ethereum\Traits\PersonalModuleTrait;
use Ihsaneddin\Ethereum\Traits\EthPubSubModuleTrait;
use Ihsaneddin\Ethereum\Traits\ParityModuleTrait;

use Ihsaneddin\Ethereum\Interfaces\ApiInterface;
use Ihsaneddin\Ethereum\Interfaces\RequestInterface;

use Illuminate\Support\Collection;

abstract class Api implements ApiInterface {

  protected $connection;

  use EthereumTrait, EncodeHexTrait;

  use Web3ModuleTrait, NetModuleTrait, EthModuleTrait, ShhModuleTrait, SecretStoreModuleTrait, SignerModuleTrait, TraceModuleTrait, ParityModuleTrait, ParityAccountsModuleTrait, ParitySetModuleTrait, ParitySubPubModuleTrait, EthPubSubModuleTrait, PersonalModuleTrait;

  protected static $can_be_decoded_keys = ['size', 'gasLimit', 'minGasPrice', 'gasUsed', 'timestamp', 'nonce', 'blockNumber', 'value', 'gas', 'transactionIndex', 'gasPrice', 'cumulativeGasUsed', "startingBlock", "highestBlock", "currentBlock"];

  public function connection() : RequestInterface{
    if (!($this->connection instanceOf RequestInterface)) {
      $connection_class = $this->connection_class();
      $this->connection = new $connection_class;
    }
    return $this->connection;
  }

  protected function post(string $method, $params=array()){
    return $this->connection->post(array('method' => $method, 'params' => $params));
  }

  abstract protected function connection_class();

}