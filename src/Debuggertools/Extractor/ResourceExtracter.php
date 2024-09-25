<?php

declare(strict_types=1);

namespace Debuggertools\Extractor;

use Debuggertools\Decoder\CurlDecoder;
use Debuggertools\Exceptions\FunctionalException;
use Debuggertools\Interfaces\ExtracterInterface;
use Debuggertools\Interfaces\ClassDecoderInterface;
use Debuggertools\ExtendClass\AbstractAdvancedExtracter;

class ResourceExtracter extends AbstractAdvancedExtracter implements ExtracterInterface
{

    public function __construct()
    {
        $this->CurlDecoder = new CurlDecoder();
    }

    public function extract($resource): ExtracterInterface
    {
        $resourceType = get_resource_type($resource);
        $this->type = 'resource'; //type
        $extractor = null;
        switch ($resourceType) {
            case 'curl':
                $this->class = $resourceType;
                $extractor = $this->CurlDecoder;
                break;
            case 'ftp':
                $this->class = $resourceType;
                break;
            default:
                break;
        }
        if (!is_null($extractor)) $this->content = $this->extractContent($extractor, $resource);
        return $this;
    }

    private function  extractContent(ClassDecoderInterface $extractor, $resource): ?array
    {
        try {
            return $extractor->decodeObject($resource);
        } catch (\Throwable $th) {
            throw new FunctionalException("Error extracting data from $this->type : " . $th->getMessage(), 1);
        }
    }
}
