<?php

declare(strict_types=1);

namespace Debuggertools\Objects;

use DateTimeInterface;
use ReflectionFunction;
use Debuggertools\Interfaces\ClassDecoderInterface;
use Error;

class ClosureDecoder implements ClassDecoderInterface
{

    public function decodeObject($closure): ?array
    {
        $fakeData = [];
        $r = new ReflectionFunction($closure);

        $Parameters = $r->getParameters();
        if (!empty($Parameters)) {
            $fakeData['parameters']  = [];
            foreach ($Parameters as $Parameter) {
                $fakeData['parameters'][] = $this->decodeParameter($Parameter);
            }
        }

        if ($r->hasReturnType()) {
            $fakeData['returnType'] = $r->getReturnType()->getName();
        }

        return $fakeData;
    }

    /**
     * Decode object to add fake data
     *
     * @param array $fakeData
     * @return array
     */
    protected function decodeParameter($parameter): array
    {
        $ret = ['name' => $parameter->getName()];
        $type = $parameter->getType();
        if ($type) {
            $ret['type'] = $type->getName();
        }
        return $ret;
    }
}
