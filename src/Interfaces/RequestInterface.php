<?php
namespace Ihsaneddin\Ethereum\Interfaces;

use Illuminate\Support\Collection;

interface RequestInterface{

  public function send(array $options=array()) : ResponseInterface;

  public function post(array $options=array()) : ResponseInterface;

  public function get(array $options= array()) : ResponseInterface;

  public function response() : ResponseInterface;

  public function raw_response() : Collection;

}