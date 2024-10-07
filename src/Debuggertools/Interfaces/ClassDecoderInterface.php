<?php

namespace Debuggertools\Interfaces;

use Debuggertools\Interfaces\AppenderLogInterfaces;

interface ClassDecoderInterface
{
    /**
     * Retrieves maximum information on the content of the element
     *
     * @param mixed $obj $element to transfom in array
     * @return array|null
     */
    public function decodeObject($obj): ?array;

    public function getAppender($obj): ?AppenderLogInterfaces;
}
