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
use Ihsaneddin\Ethereum\Validators\TagValidator;
use Ihsaneddin\Ethereum\Formatters\QuantityFormatter;

class OptionalQuantityFormatter implements IFormatter
{
    /**
     * format
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        if (TagValidator::validate($value)) {
            return $value;
        }
        return QuantityFormatter::format($value);
    }
}