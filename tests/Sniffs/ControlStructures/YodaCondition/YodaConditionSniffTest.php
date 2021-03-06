<?php

declare(strict_types = 1);

namespace Zenify\CodingStandard\Tests\Sniffs\ControlStructures\YodaCondition;

use PHPUnit\Framework\TestCase;
use Zenify\CodingStandard\Tests\CodeSnifferRunner;
use ZenifyCodingStandard\Sniffs\ControlStructures\YodaConditionSniff;


final class YodaConditionSniffTest extends TestCase
{

	public function testDetection()
	{
		$codeSnifferRunner = new CodeSnifferRunner(YodaConditionSniff::NAME);

		$this->assertSame(5, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php'));
		$this->assertSame(0, $codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php'));
	}

}
