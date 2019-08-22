<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */

namespace Ihsaneddin\Ethereum\Validators;

use Ihsaneddin\Ethereum\Validators\IValidator;

class BooleanValidator
{
    /**
     * validate
     *
     * @param mixed $value
     * @return bool
     */
    public static function validate($value)
    {
        return is_bool($value);
    }
}