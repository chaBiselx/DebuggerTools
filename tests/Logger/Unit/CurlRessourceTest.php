<?php

namespace Test\Logger\Unit;

use Debuggertools\Logger;

use Test\ExtendClass\BaseTestCase;

class CurlRessourceTest extends BaseTestCase
{

    private $urlTest = "http://www.example.com/";

    public function setUp(): void
    {
        parent::setUp();
        $this->purgeLog();
        $this->Logger = new Logger();
    }

    public function testBase()
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
    }
}
