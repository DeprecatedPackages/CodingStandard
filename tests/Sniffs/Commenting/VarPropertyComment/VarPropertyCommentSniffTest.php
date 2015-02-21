<?php

namespace Zenify\CodingStandard\Tests\Sniffs\Commenting\VarPropertyComment;

use PHPUnit_Framework_TestCase;
use Zenify\CodingStandard\Tests\CodeSnifferRunner;


class VarPropertyCommentSniffTest extends PHPUnit_Framework_TestCase
{

	/**
	 * @var CodeSnifferRunner
	 */
	private $codeSnifferRunner;


	protected function setUp()
	{
		$this->codeSnifferRunner = new CodeSnifferRunner('ZenifyCodingStandard.Commenting.VarPropertyComment');
	}


	public function testDetection()
	{
		$this->assertSame(1, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/wrong.php'));
		$this->assertSame(0, $this->codeSnifferRunner->getErrorCountInFile(__DIR__ . '/correct.php'));
	}

}
