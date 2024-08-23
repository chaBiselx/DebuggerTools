<?php

namespace Test\SubElement\Symfony\QueryBuilder;

use Debuggertools\Logger;
use Test\ExtendClass\SymfonyTestCase;

class QueryBuilderTest extends SymfonyTestCase
{

	public function setUp(): void
	{
		parent::setUp();
		$this->purgeLog();
		$this->Logger = new Logger();
	}

	public function testSimpleCase()
	{
		$qb = $this->getQueryBuilder();
		$this->Logger->logger($qb);
	}
}
