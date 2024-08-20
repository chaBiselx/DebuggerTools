<?php

namespace Test\SubElement\Unit;

use Test\ExtendClass\BaseTestCase;
use Debuggertools\Objects\SqlDecoder;

class SqlDecoderTest extends BaseTestCase
{

	public function setUp(): void
	{
		parent::setUp();
		$this->purgeLog();
		$this->SqlDecoder = new SqlDecoder();
	}

	public function testSimpleCase()
	{
		$sql = "SELECT * FROM t.user";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user$/', $this->SqlDecoder->serialize($sql));
	}

	public function testUpperCase()
	{
		$sql = "select * from t.user";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user$/', $this->SqlDecoder->serialize($sql));
	}

	public function testSimpleCaseOverSpace()
	{
		$sql = "            SELECT       *         FROM         t.user           ";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user$/', $this->SqlDecoder->serialize($sql));
	}

	public function testOrderBy()
	{
		$sql = "SELECT * FROM t.user ORDER BY id ASC";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user \nORDER BY id ASC$/', $this->SqlDecoder->serialize($sql));
	}

	public function testDelete()
	{
		$sql = "DELETE FROM t.user WHERE id > 10, name='toto', deptDex = 25 ";
		$this->assertMatchesRegularExpression('/DELETE FROM t.user \nWHERE id > 10,\n\s+name=\'toto\',\n\s+deptDex = 25$/', $this->SqlDecoder->serialize($sql));
	}

	public function testSimpleCaseOverSpecialChar()
	{
		$sql = "\n\nSELECT  \n\n     *      \n\n\n   FROM   \n\n\n      t.user   \r\n\n \t\t        ";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user$/', $this->SqlDecoder->serialize($sql));
	}

	public function testSimpleInnerJoin()
	{
		$sql = "SELECT * FROM t.user as us INNER JOIN t.role as ro ON ro.id = us.role_id ";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user as us \nINNER JOIN t.role as ro ON ro.id = us.role_id$/', $this->SqlDecoder->serialize($sql));
	}

	public function testSimpleOuterJoin()
	{
		$sql = "SELECT * FROM t.user as us OUTER JOIN t.role as ro ON ro.id = us.role_id ";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user as us \nOUTER JOIN t.role as ro ON ro.id = us.role_id$/', $this->SqlDecoder->serialize($sql));
	}

	public function testSimpleLeftJoin()
	{
		$sql = "SELECT * FROM t.user as us LEFT JOIN t.role as ro ON ro.id = us.role_id ";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user as us \nLEFT JOIN t.role as ro ON ro.id = us.role_id$/', $this->SqlDecoder->serialize($sql));
	}

	public function testSimpleRightJoin()
	{
		$sql = "SELECT * FROM t.user as us RIGHT JOIN t.role as ro ON ro.id = us.role_id ";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user as us \nRIGHT JOIN t.role as ro ON ro.id = us.role_id$/', $this->SqlDecoder->serialize($sql));
	}

	public function testMultipleInnerJoin()
	{
		$sql = "SELECT * FROM t.user as us INNER JOIN t.role as ro ON ro.id = us.role_id INNER JOIN t.adress as ad ON us.address_id = ad.id ";
		$this->assertMatchesRegularExpression('/SELECT\n\s+\* \nFROM t.user as us \nINNER JOIN t.role as ro ON ro.id = us.role_id \nINNER JOIN t.adress as ad ON us.address_id = ad.id$/', $this->SqlDecoder->serialize($sql));
	}

	public function testHaving()
	{
		$sql = "SELECT customer, SUM(price) FROM cart GROUP BY customer HAVING SUM(price) > 40";
		$this->assertMatchesRegularExpression('/SELECT\n\s+customer,\n\s+SUM\(price\) \nFROM cart \nGROUP BY customer \nHAVING SUM\(price\) > 40$/', $this->SqlDecoder->serialize($sql));
	}

	public function testSubQueryFrom()
	{
		$sql = "SELECT sub.* FROM (SELECT * FROM tutorial.sf_crime_incidents_2014_01 WHERE day_of_week = 'Friday') sub WHERE sub.resolution = 'NONE'";
		$this->assertMatchesRegularExpression('/SELECT\n\s*sub\.\* \nFROM \(\s*\n\s*SELECT \* \n\s*FROM tutorial\.sf_crime_incidents_2014_01 \n\s*WHERE day_of_week = \'Friday\'\s*\n\) sub \nWHERE sub\.resolution = \'NONE\'$/', $this->SqlDecoder->serialize($sql));
	}
}
