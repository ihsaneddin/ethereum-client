<?php
namespace Ihsaneddin\Ethereum\Traits;

trait TraceModuleTrait{

  public function trace_call(array $call_options=array(), $types_of_trace, $block="latest", $encode_hex=false){
    if ($encode_hex) $block = $this->encode_hex($block);
    return $this->types_of_trace($types_of_trace, function($types) use ($call_options, $block) {
      return $this->post(__FUNCTION__, array($call_options, $types, $block));
    });
  }

  public function trace_rawTransaction(string $data, $types_of_trace){
    return $this->types_of_trace($types_of_trace, function($types) use ($data) {
      return $this->post(__FUNCTION__, array($data, $types));
    });
  }

  public function trace_replayTransaction(string $hash, $types_of_trace){
    return $this->types_of_trace($types_of_trace, function($types) use ($hash) {
      return $this->post(__FUNCTION__, array($hash, $types));
    });
  }

  public function trace_replayBlockTransactions($block="latest", $types_of_trace, $encode_hex=false){
    if ($encode_hex) $block = $this->encode_hex($block);
    return $this->types_of_trace($types_of_trace, function($types) use($block){
      return $this->post(__FUNCTION__, array($block, $types));
    });
  }

  public function trace_block($block="latest", $encode_hex=false){
    if ($encode_hex) $block = $this->encode_hex($block);
    return $this->post(__FUNCTION__, array($block));
  }

  public function trace_filter(array $filter_options=array(), $from_address, string $to_address){
    if (!is_array($from_address)) $from_address = array($from_address);
    return $this->post(__FUNCTION__, array($filter_options, $from_address, $to_address));
  }

  public function trace_get(string $hash, $index, $encode_hex=false){
    if (!is_array($index)) $index= array($index);
    if ($encode_hex){
      $index = collect($index)->transform(function($item, $key){ return $this->encode_hex($item); });
    }
    return $this->post(__FUNCTION__, array($hash, $index));
  }

  public function trace_transaction(string $hash){
    return $this->post(__FUNCTION__, array($hash));
  }

  private function types_of_trace($types, callable $callback=null){
    if (!is_array($types)) $types = array($types);
    if (is_callable($callback))
      return $callback($types);
    else
      return $types;
  }

}