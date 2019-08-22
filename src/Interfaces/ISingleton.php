<?php
namespace Ihsaneddin\Ethereum\Interfaces;

interface ISingleton {

    public static function getInstance(): ISingleton;
}