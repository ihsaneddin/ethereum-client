<?php
namespace Ihsaneddin\Ethereum\Interfaces;

use Illuminate\Support\Collection;

interface HasConfigInterface{

  public function config() : Collection;

}