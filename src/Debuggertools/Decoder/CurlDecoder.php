<?php

declare(strict_types=1);

namespace Debuggertools\Decoder;

use ReflectionClass;
use CurlHandle;
use Debuggertools\Interfaces\ClassDecoderInterface;

class CurlDecoder implements ClassDecoderInterface
{
    /**
     * {@inheritDoc}
     */
    public function decodeObject($obj): ?array
    {
        $this->handle = $obj;
        $this->info = curl_getinfo($this->handle);
        $this->fakeData = null;
        if ($this->info) {
            $this->fakeData = [];
            if ($this->info['url']) {
                $this->setRequest();
            }
            if ($this->info['http_code']) {
                $this->setResponse();
            }
        }

        return $this->fakeData;
    }

    private function setRequest()
    {
        $this->fakeData['request'] = [];
        $this->getUrl();
    }

    private function setResponse()
    {
        $this->fakeData['response'] = [];
        $this->getHttpCode();
        $this->getContentType();
    }

    private function getUrl(): void
    {
        try {
            $url = $this->info['url'];
            if ($url) $this->fakeData['request']['url'] = $url;
        } catch (\Throwable $th) {
            //pass
        }
    }

    private function getHttpCode(): void
    {
        try {
            $httpCode = $this->info['http_code'];
            if ($httpCode) $this->fakeData['response']['httpCode'] = $httpCode;
        } catch (\Throwable $th) {
            //pass
        }
    }

    private function getContentType(): void
    {
        try {
            $httpCode = $this->info['content_type'];
            if ($httpCode) $this->fakeData['response']['ContentType'] = $httpCode;
        } catch (\Throwable $th) {
            //pass
        }
    }
}
