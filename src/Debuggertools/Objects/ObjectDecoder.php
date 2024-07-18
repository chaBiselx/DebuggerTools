<?php

namespace Debuggertools\Objects;



class ObjectDecoder
{


    public static function decode($obj): array
    {
        $class = get_class($obj); // get classname
        $fakeData = json_decode(json_encode($obj), true); // clone the public data

        // get private var with getter
        foreach (get_class_methods($obj) as $function) {

            if (preg_match('/^get/', $function)) {
                $method = new \ReflectionMethod($class, $function);
                try {
                    if (empty($method->getParameters())) { // not parameters
                        $res =  $obj->$function();
                        if (gettype($res) != 'object') {
                            $fakeData["->$function"] = $obj->$function();
                        } else {
                            $fakeData["->$function"] = [get_class($res) => $obj];
                        }
                    }
                } catch (\Error $e) {
                    $fakeData["->$function"] = ["CUSTOMLOG" => "ERROR LOGGER", "MESSAGE" => $e->getMessage()];
                }
            }
        }
        return $fakeData;
    }
}
