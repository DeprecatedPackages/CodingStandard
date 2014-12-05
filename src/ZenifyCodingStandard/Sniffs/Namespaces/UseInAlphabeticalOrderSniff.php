<?php

/**
 * This file is part of Zenify
 * Copyright (c) 2012 Tomas Votruba (http://tomasvotruba.cz)
 */

namespace ZenifyCodingStandard\Sniffs\Namespaces;

use PHP_CodeSniffer_File;
use PHP_CodeSniffer_Sniff;


/**
 * Rules:
 * - Use statements should be in alphabetical order.
 *
 * @author Mikulas Dite <mikulas@dite.pro>
 */
class UseInAlphabeticalOrderSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * @var array
	 */
	protected $processedFiles = [];


	/**
	 * @return int[]
	 */
	public function register()
	{
		return [T_USE];
	}


	/**
	 * @param PHP_CodeSniffer_File $file
	 * @param int $position
	 */
	public function process(PHP_CodeSniffer_File $file, $position)
	{
		if (isset($this->processedFiles[$file->getFilename()])) {
			return;
		}

		$tokens = $file->getTokens();
		$isClosure = $file->findPrevious([T_CLOSURE], ($position - 1), NULL, FALSE, NULL, TRUE);
		if ($isClosure) {
			return;
		}

//		// Only one USE declaration allowed per statement.
//		$next = $file->findNext([T_COMMA, T_SEMICOLON], ($position + 1));
//		if ($tokens[$next]['code'] === T_COMMA) {
//			$error = 'There must be one USE keyword per declaration';
//			$file->addError($error, $position);
//		}

		$uses = [];
		$next = $position;
		while (TRUE) {
			$content = '';
			$end = $file->findNext([T_SEMICOLON, T_OPEN_CURLY_BRACKET], $next);
			$useTokens = array_slice($tokens, $next, $end - $next, TRUE);
			$index = NULL;
			foreach ($useTokens as $index => $token) {
				if ($token['code'] === T_STRING || $token['code'] === T_NS_SEPARATOR) {
					$content .= $token['content'];
				}
			}

			 // Check for class scoping on use. Traits should be ordered independently.
			$scope = 0;
			if ( ! empty($token['conditions'])) {
				$scope = key($token['conditions']);
			}
			$uses[$scope][$content] = $index;

			$next = $file->findNext(T_USE, $end);
			if ( ! $next) {
				break;
			}
		}

		// Prevent multiple uses in the same file from entering
		$this->processedFiles[$file->getFilename()] = TRUE;

		$ordered = TRUE;
		$failedIndex = NULL;
		foreach ($uses as $scope => $used) {
			$defined = $sorted = array_keys($used);

			natcasesort($sorted);
			$sorted = array_values($sorted);
			if ($sorted === $defined) {
				continue;
			}

			foreach ($defined as $i => $name) {
				if ($name !== $sorted[$i]) {
					if ($failedIndex === NULL) {
						$failedIndex = $used[$name];
					}
					$ordered = FALSE;
				}
			}
		}

		if ( ! $ordered) {
			$error = 'Use statements should be in alphabetical order';
			$file->addError($error, $failedIndex);
		}
	}

}
