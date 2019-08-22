<?php
namespace Ihsaneddin\Ethereum\Interfaces;

use Ihsaneddin\Ethereum\Interfaces\RequestInterface;

interface ApiInterface{

  public function connection() : RequestInterface;

}