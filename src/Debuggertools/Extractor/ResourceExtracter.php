<?php

declare(strict_types=1);

namespace Debuggertools\Extractor;

use Debuggertools\Interfaces\ExtracterInterface;
use Debuggertools\ExtendClass\AbstractAdvancedExtracter;

class ResourceExtracter extends AbstractAdvancedExtracter implements ExtracterInterface
{

    public function extract($resource): ExtracterInterface
    {
        $resourceType = get_resource_type($resource);
        $this->type = 'resource'; //type
        switch ($resourceType) {
            case 'curl':
                $this->class = $resourceType;
                break;
            default:
                break;
        }
        return $this;
    }
}
