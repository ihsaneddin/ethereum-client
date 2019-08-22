<?php
namespace Ihsaneddin\Ethereum\Interfaces;

interface SingletonInterface {

  public function initialize(array $config) : SingletonInterface;

}