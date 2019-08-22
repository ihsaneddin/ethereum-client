<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Ihsaneddin\Ethereum\Formatters;

use InvalidArgumentException;
use Ihsaneddin\Ethereum\Support\Utils;
use Ihsaneddin\Ethereum\Formatters\IFormatter;

class NumberFormatter implements IFormatter
{
    /**
     * format
     *
     * @param mixed $value
     * @return int
     */
    public static function format($value)
    {
        $value = Utils::toString($value);
        $bn = Utils::toBn($value);
        $int = (int) $bn->toString();

        return $int;
    }
}