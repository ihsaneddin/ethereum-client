<?php
namespace Ihsaneddin\Ethereum\Interfaces;

use Illuminate\Support\Collection;

interface ResponseInterface{

  public function result();

  public function raise_error_if_any();

  public function is_success();

  public function raw() : array;

}