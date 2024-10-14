<?php

declare(strict_types=1);

namespace Debuggertools\Extractor;

use Debuggertools\Decoder\CurlDecoder;
use Debuggertools\Decoder\ClassDecoder;
use Debuggertools\Decoder\ClosureDecoder;
use Debuggertools\Decoder\ArrayIteratorDecoder;
use Debuggertools\Decoder\DoctrineQueryDecoder;
use Debuggertools\Interfaces\ExtracterInterface;
use Debuggertools\Interfaces\AppenderLogInterfaces;
use Debuggertools\Interfaces\ClassDecoderInterface;
use Debuggertools\Decoder\DoctrineQueryBuilderDecoder;
use Debuggertools\ExtendClass\AbstractAdvancedExtracter;

class ClassExtracter extends AbstractAdvancedExtracter implements ExtracterInterface
{

    public function __construct()
    {
        parent::__construct();
        $this->ClassDecoder = new ClassDecoder();
        $this->ClosureDecoder = new ClosureDecoder();
        $this->CurlDecoder = new CurlDecoder();
        $this->ArrayIteratorDecoder = new ArrayIteratorDecoder();
        $this->DoctrineQueryDecoder = new DoctrineQueryDecoder();
        $this->DoctrineQueryBuilderDecoder = new DoctrineQueryBuilderDecoder();
        
    }


    public function extract($obj): ExtracterInterface
    {
        $this->class = get_class($obj); // get classname
        $this->type = 'class'; //type
        $decoder = null;
        if ($this->class === 'Closure') {
            $decoder = $this->ClosureDecoder;
        } elseif ($this->class === 'ArrayIterator') {
            $decoder = $this->ArrayIteratorDecoder;
        }elseif ($this->class === 'CurlHandle') {
            $decoder = $this->CurlDecoder;
        }elseif (class_exists('Doctrine\\ORM\\QueryBuilder') && $this->class === "Doctrine\\ORM\\QueryBuilder") {
            $decoder = $this->DoctrineQueryBuilderDecoder;
        }elseif (class_exists('Doctrine\\ORM\\Query') && $this->class === "Doctrine\\ORM\\Query") {
            $decoder = $this->DoctrineQueryDecoder;
        } else {
            $decoder = $this->ClassDecoder;
        }
        $this->decodeContent($obj, $decoder);

        //check instance for more data
        $this->appendLog = $this->getContentSpecialClass($obj, $decoder);

        return $this;
    }

    private function decodeContent($obj, ClassDecoderInterface $decoder): void
    {
        $this->content = $decoder->decodeObject($obj);
    }

    /**
     * Get more content from spécial class
     *
     * @param object $obj
     * @return array
     */
    private function getContentSpecialClass($obj,ClassDecoderInterface $decoder): array
    {
        $toAppendToLog = [];
 
        $appender =  $decoder->getAppender($obj);
        if ($appender) $toAppendToLog = array_merge($toAppendToLog, $this->extractMoreData($obj, $appender));
        return $toAppendToLog;
    }

    private function extractMoreData($obj, AppenderLogInterfaces $appender): array
    {
        return $appender->extractDataLog($obj);
    }
}
