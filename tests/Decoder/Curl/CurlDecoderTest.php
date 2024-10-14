<?php

namespace Test\Decoder\Curl;

use Debuggertools\Logger;
use Test\ExtendClass\BaseTestCase;

class CurlDecoderTest extends BaseTestCase
{


    private $urlTest = "http://www.example.com/";

    public function setUp(): void
    {
        if (PHP_MAJOR_VERSION >= 8) {
            $this->markTestSkipped('all tests in this file are invactive for this PHP verison!');
        }
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger();
    }

    public function testInit()
    {
        // initialisation de la session
        $ch = curl_init();

        $this->Logger->logger($ch);

        // fermeture des ressources
        curl_close($ch);
        $this->assertMatchesRegularExpression('/ : resource \'curl\' : \[\]/', $this->getContent());
    }

    public function testGetSimpleNotSend()
    {
        // initialisation de la session
        $ch = curl_init();

        // configuration des options
        curl_setopt($ch, CURLOPT_URL, $this->urlTest);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Capture la sortie au lieu de l'afficher

        // exécution de la session
        $this->Logger->logger($ch);

        // fermeture des ressources
        curl_close($ch);
        $this->assertMatchesRegularExpression('/ : resource \'curl\'/', $this->getContent());
        $this->assertMatchesRegularExpression('/ : \{"request":\{"url":"http:\\\\\/\\\\\/www.example.com\\\\\/"\}\}/', $this->getContent());
    }

    public function testGetSimpleSend()
    {
        // initialisation de la session
        $ch = curl_init();

        // configuration des options
        curl_setopt($ch, CURLOPT_URL, $this->urlTest);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Capture la sortie au lieu de l'afficher

        // exécution de la session
        curl_exec($ch);
        $this->Logger->logger($ch);

        // fermeture des ressources
        curl_close($ch);
        $this->assertMatchesRegularExpression('/ : resource \'curl\'/', $this->getContent());
        $this->assertMatchesRegularExpression('/ : \{"request":\{"url":"http:\\\\\/\\\\\/www.example.com\\\\\/"\},"response":\{"httpCode":200,"ContentType":"text\\\\\/html; charset=UTF-8"\}\}/', $this->getContent());
    }

    // public function testPostSimpleSend()
    // {
    //     // initialisation de la session
    //     $ch = curl_init();

    //     // configuration des options
    //     curl_setopt($ch, CURLOPT_URL, $this->urlTest);
    //     curl_setopt($ch, CURLOPT_HEADER, 0);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Capture la sortie au lieu de l'afficher
    //     curl_setopt($ch, CURLOPT_POST, 1);

    //     // exécution de la session
    //     curl_exec($ch);
    //     $this->Logger->logger($ch);

    //     // fermeture des ressources
    //     curl_close($ch);
    //     $this->assertMatchesRegularExpression('/ : resource \'curl\'/', $this->getContent());
    // }
}
