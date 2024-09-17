<?php

declare(strict_types=1);

namespace Debuggertools\Objects;

use Debuggertools\Interfaces\TraceDecoderInterface;

class TraceDecoder implements TraceDecoderInterface
{

    private $traces = [];
    private $basePath = "";
    private $delimiter = "===========";

    public function __construct()
    {
        $this->traces = debug_backtrace();
        $this->basePath = $this->detectBasePathFromTraces();
    }

    /**
     * Decode a trace decoder
     *
     * @return array array of string
     */
    public function decode(): array
    {
        $arrayText = [];
        $arrayText[] = $this->delimiter . " TRACE START " . $this->delimiter;
        foreach ($this->traces as $trace) {
            //ignore package
            if (preg_match('/chabiselx\/debuggertools/', $trace['file'])) continue;
            if (isset($trace['class']) && $trace['class'] == 'Debuggertools\Trace') continue;
            if (isset($trace['class']) && $trace['class'] == 'Debuggertools\Objects\TraceDecoder') continue;

            $messageFile = $this->getFileMessage($trace);
            if ($messageFile) $arrayText[] = $messageFile;

            $messageFunction = $this->getFunctionMessage($trace);
            if ($messageFunction) $arrayText[] = $messageFunction;
        }
        $arrayText[] =  $this->delimiter . " TRACE END " . $this->delimiter;
        return $arrayText;
    }

    /**
     * Create Text for file
     *
     * @param [type] $trace
     * @return string
     */
    protected function getFileMessage($trace): string
    {
        $messageFile = "";
        if (isset($trace['file'])) {
            $class = '';
            if (isset($trace['class'])) {
                $class = " '" . $trace['class'] . "' ";
            }
            $trace['file'] = preg_replace('/^' . addcslashes($this->basePath, DIRECTORY_SEPARATOR) . '/', "", $trace['file']); // remove base path
            $messageFile = $trace['file'] . ' (line : ' . $trace['line'] . ') ' . $class;
        }
        return $messageFile;
    }

    /**
     * Get function name
     *
     * @param array $trace
     * @return string
     */
    protected function getFunctionMessage($trace): string
    {
        $messageFunction = "";
        if (isset($trace['function'])) {
            $type = $trace['type'] ?? '';
            $function = $trace['function'] ?? '';
            $messageFunction .= "          " . $type . ' ' .  $function;
            $messageFunction .= "(";
            if ($trace['args'] && !empty($trace['args'])) {
                foreach ($trace['args'] as $k => $arg) {
                    if ($k != 0) $messageFunction .= ', ';
                    $messageFunction .= $this->convertArgToString($arg);
                }
            }
            $messageFunction .= ")";
        }
        return $messageFunction;
    }

    /**
     * Detectet common path from all files
     *
     * @return string
     */
    protected function detectBasePathFromTraces(): string
    {
        $paths = [];
        foreach ($this->traces as $trace) {
            if (isset($trace['file'])) {
                //ignore Virtual File Systems
                $paths[] = preg_replace('/^[a-zA-Z]*:\/\//', '', $trace['file']);
            }
        }
        if (empty($paths)) {
            return '';
        }

        // Split each path into an array of directories
        $splitPaths = array_map(function ($path) {
            return explode(DIRECTORY_SEPARATOR, $path);
        }, $paths);

        // Find the minimum length among the split paths
        $minLength = min(array_map('count', $splitPaths));
        $commonPathParts = [];
        for ($i = 0; $i < $minLength; $i++) {
            $currentPart = $splitPaths[0][$i];

            $add = true;
            foreach ($splitPaths as $splitPath) {
                if ($currentPart != $splitPath[$i]) {
                    $add = false;
                }
            }
            if ($add) {
                $commonPathParts[] = $currentPart;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $commonPathParts);
    }

    /**
     * Convert Arg to string for parameters of function
     *
     * @param $arg
     * @return string
     */
    protected function convertArgToString($arg): string
    {
        $text = "";
        //check type
        $type = gettype($arg);
        switch ($type) {
            case 'integer':
            case 'float':
            case 'double':
            case 'string':
                $text = (string) $arg;
                break;
            case 'boolean':
                $text = $type . ' : ' . ($arg ? 'TRUE' : 'FALSE');
                break;
            case 'object':
                $text = "'" . get_class($arg) . "'";
                break;
            case 'array':
            default:
                $text = $type;
                break;
        }
        return $text;
    }
}
