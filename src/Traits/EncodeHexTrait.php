<?php
namespace Ihsaneddin\Ethereum\Traits;

use Ihsaneddin\Ethereum\Support\Utils;

use Illuminate\Support\Collection;
use \Litipk\BigNumbers\Decimal as Decimal;

trait EncodeHexTrait{

  public function encode_hex($input, array $encoded_keys=array())
  {
    return static::encode_to_hex($input, $encoded_keys);
  }

  public function decode_hex($input, array $can_be_decoded_keys=array())
  {
    return static::decode_from_hex($input, $can_be_decoded_keys);

  }

  public function str_hex(string $string)
  {
    $hexstr = unpack('H*', $string);
    return array_shift($hexstr);
  }


  public static function encode_to_hex($input, array $encoded_keys=array()){

    if (is_null($input)) return $input;

    if (!is_array($input) && !($input instanceOf Collection)){

      if (is_numeric($input))
        return Utils::tohex($input, true);
      else{
        $hexstr = unpack('H*', $input);
        return array_shift($hexstr);
      }
    }

    if (is_array($input))
      $input = collect($input);

    if ($input instanceOf Collection){

      if ($encoded_keys == 'all')
        $encoded_keys = $input->keys();

      if (empty($encoded_keys))
        $encoded_keys = isset(static::$can_be_encoded_keys) ? static::$can_be_encoded_keys : $encoded_keys;

      foreach ($encoded_keys as $key) {
        $initial_value = $input->get($key);
        if ($initial_value){
          if (is_numeric($initial_value))
            $initial_value= Utils::tohex($initial_value, true);
          else{
            $hexstr = unpack('H*', $initial_value);
            $initial_value= array_shift($hexstr);
          }
        }
        $input->put($key, $initial_value);
      }

      /*$input->transform(function($item, $key) use ($encoded_keys) {
        if (in_array($key, $encoded_keys)){
          if (is_numeric($item))
            return '0x' . dechex((int)$item);
          else{
            $hexstr = unpack('H*', $item);
            return array_shift($hexstr);
          }

        }else{
          return $item;
        }
      });*/

      return $input->all();

    }

  }

  public static function decode_from_hex($input, array $can_be_decoded_keys=array()){

    if (is_null($input)) return $input;

    if (!is_array($input) && !($input instanceOf Collection)){
      // if (substr($input, 0, 2) == '0x') {
      //   $input = substr($input, 2);
      // }
      // if (preg_match('/[a-f0-9]+/', $input)) {
      //   return hexdec($input);
      // }
      return Utils::toBn($input)->toString();
    }

    if (is_array($input))
      $input = collect($input);

    if ($input instanceOf Collection){

      if ($can_be_decoded_keys == 'all')
        $can_be_decoded_keys = $input->keys();

      if (empty($can_be_decoded_keys))
        $can_be_decoded_keys = isset(static::$can_be_decoded_keys) ? static::$can_be_decoded_keys : $can_be_decoded_keys;

      $input->transform(function($item, $key) use ($can_be_decoded_keys, $input) {
        if (in_array($key, $can_be_decoded_keys)){
          // if (substr($input, 0, 2) == '0x') {
          //   $input = substr($input, 2);
          // }
          // if (preg_match('/[a-f0-9]+/', $input)) {
          //   return hexdec($input);
          // }
          return Utils::toBn($input)->toString();

        }else{
          return $item;
        }
      });

    }
  }

  public static function add0x($param){
    if (substr($param, 0, 2) != '0x') {
      $param = '0x' . $param;
    }
    return $param;
  }

  public static function strip0x($param){
    if ( (strpos($param, '0x') === 0)){
      $count = 1;
      return str_replace('0x', '', $param, $count);
    }
  }

}
