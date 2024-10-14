<?php

declare(strict_types=1);

namespace Debuggertools\Decoder;

use DateTimeInterface;
use Debuggertools\Interfaces\AppenderLogInterfaces;
use Debuggertools\Interfaces\ClassDecoderInterface;

class EnumDecoder implements ClassDecoderInterface
{

    /**
     * {@inheritDoc}
     */
    public function decodeObject($obj): ?array
    {
        $class = get_class($obj); // get classname
        $fakeData = [];
        $fakeData['name'] = $obj->name;
        $fakeData['functions'] = [];

        // get private var with getter
        foreach (get_class_methods($obj) as $function) {
            try {
                $method = new \ReflectionMethod($class, $function);
                $fakeData['functions'][] = $function;
            } catch (\Error $e) {
            }
        }
        return $fakeData;
    }

    public function getAppender($obj): ?AppenderLogInterfaces
    {
        return null;
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
