<?php
/**
 * Created by PhpStorm.
 * User: billy
 * Date: 11/04/19
 * Time: 10:45
 */

namespace Ihsaneddin\Ethereum\Facades;

use Ihsaneddin\Ethereum\Entities\Transaction;
use Illuminate\Support\Facades\Facade;

class EthereumTransactionFacade extends Facade
{
  protected static function getFacadeAccessor() { return Transaction::class; }
}
