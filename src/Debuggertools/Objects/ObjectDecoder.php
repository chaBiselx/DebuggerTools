<?php

namespace Debuggertools\Objects;

use DateTimeInterface;
use Debuggertools\Interfaces\ObjectDecoderInterface;

class ObjectDecoder implements ObjectDecoderInterface
{

    public function decodeObject($obj): ?array
    {
        $class = get_class($obj); // get classname
        $fakeData = json_decode(json_encode($obj), true); // clone the public data

        // get private var with getter
        foreach (get_class_methods($obj) as $function) {

            if (preg_match('/^get/', $function)) {
                $method = new \ReflectionMethod($class, $function);
                try {
                    if (empty($method->getParameters())) { // not parameters
                        $this->decode($fakeData, $obj, $function);
                    }
                } catch (\Error $e) {
                    $fakeData["->$function"] = ["CUSTOMLOG" => "ERROR LOGGER", "MESSAGE" => $e->getMessage()];
                }
            }
        }
        return $fakeData;
    }

    /**
     * Decode object to add fake data
     *
     * @param array $fakeData
     * @param object $obj
     * @param string $function
     * @return void
     */
    protected function decode(&$fakeData, $obj, $function)
    {
        $res =  $obj->$function();
        if (gettype($res) != 'object') {
            $fakeData["->$function"] = $res;
        } elseif ($res instanceof DateTimeInterface) {
            $fakeData["->$function"] = self::decodeObject($res);
        } else {
            $fakeData["->$function"] = [get_class($res) => $obj];
        }
    }
}
