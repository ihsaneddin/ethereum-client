<?php
namespace Ihsaneddin\Ethereum\Traits;

use Ihsaneddin\Ethereum\Entities\TransactionReceipt;

trait TransactionReceiptTrait{

  public function find_receipt(string $hash){
    return TransactionReceipt::find($hash);
  }

}