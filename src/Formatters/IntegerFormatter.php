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

class IntegerFormatter implements IFormatter
{
    /**
     * format
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        $value = (string) $value;
        $arguments = func_get_args();
        $digit = 64;

        if (isset($arguments[1]) && is_numeric($arguments[1])) {
            $digit = intval($arguments[1]);
        }
        $bn = Utils::toBn($value);
        if (is_array($bn)){
            $bn = array_values(array_slice($bn, -2))[0];
        }
        $bnHex = $bn->toHex(true);
        $padded = mb_substr($bnHex, 0, 1);

        if ($padded !== 'f') {
            $padded = '0';
        }
        return implode('', array_fill(0, $digit-mb_strlen($bnHex), $padded)) . $bnHex;
    }
}