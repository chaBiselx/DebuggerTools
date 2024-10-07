<?php

declare(strict_types=1);

namespace Debuggertools\Decoder;

use ReflectionFunction;
use Debuggertools\Interfaces\AppenderLogInterfaces;
use Debuggertools\Interfaces\ClassDecoderInterface;

class ClosureDecoder implements ClassDecoderInterface
{
    /**
     * {@inheritDoc}
     */
    public function decodeObject($obj): ?array
    {
        $fakeData = [];
        $r = new ReflectionFunction($obj);

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

    public function getAppender($obj): ?AppenderLogInterfaces {
        return null;
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
