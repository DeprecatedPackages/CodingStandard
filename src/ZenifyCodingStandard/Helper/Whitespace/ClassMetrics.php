<?php

declare(strict_types = 1);

/*
 * This file is part of Zenify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace ZenifyCodingStandard\Helper\Whitespace;

use PHP_CodeSniffer_File;


final class ClassMetrics
{

	/**
	 * @var PHP_CodeSniffer_File
	 */
	private $file;

	/**
	 * @var int
	 */
	private $classPosition;

	/**
	 * @var array
	 */
	private $tokens;


	public function __construct(PHP_CodeSniffer_File $file, int $classPosition)
	{
		$this->file = $file;
		$this->classPosition = $classPosition;
		$this->tokens = $file->getTokens();
	}


	/**
	 * @return FALSE|int
	 */
	public function getLineDistanceBetweenClassAndLastUseStatement()
	{
		$lastUseStatementPosition = $this->getLastUseStatementPosition();
		if ( ! $lastUseStatementPosition) {
			return FALSE;
		}

		return (int) $this->tokens[$this->getClassPositionIncludingComment()]['line']
			- $this->tokens[$lastUseStatementPosition]['line']
			- 1;
	}


	/**
	 * @return FALSE|int
	 */
	public function getLastUseStatementPosition()
	{
		return $this->file->findPrevious(T_USE, $this->classPosition);
	}


	/**
	 * @return FALSE|int
	 */
	public function getLineDistanceBetweenNamespaceAndFirstUseStatement()
	{
		$namespacePosition = $this->file->findPrevious(T_NAMESPACE, $this->classPosition);

		$nextUseStatementPosition = $this->file->findNext(T_USE, $namespacePosition);
		if ( ! $nextUseStatementPosition) {
			return FALSE;
		}

		if ($this->tokens[$nextUseStatementPosition]['line'] === 1 || $this->isInsideClass($nextUseStatementPosition)) {
			return FALSE;
		}

		return $this->tokens[$nextUseStatementPosition]['line'] - $this->tokens[$namespacePosition]['line'] - 1;
	}


	/**
	 * @return FALSE|int
	 */
	public function getLineDistanceBetweenClassAndNamespace()
	{
		$namespacePosition = $this->file->findPrevious(T_NAMESPACE, $this->classPosition);

		if ( ! $namespacePosition) {
			return FALSE;
		}

		$classStartPosition = $this->getClassPositionIncludingComment();

		return $this->tokens[$classStartPosition]['line'] - $this->tokens[$namespacePosition]['line'] - 1;
	}


	/**
	 * @return FALSE|int
	 */
	private function getClassPositionIncludingComment()
	{
		$classStartPosition = $this->file->findPrevious(T_DOC_COMMENT_OPEN_TAG, $this->classPosition);
		if ($classStartPosition) {
			return $classStartPosition;
		}

		return $this->classPosition;
	}


	private function isInsideClass(int $position) : bool
	{
		$prevClassPosition = $this->file->findPrevious(T_CLASS, $position, NULL, FALSE);
		if ($prevClassPosition) {
			return TRUE;
		}

		return FALSE;
	}

}
