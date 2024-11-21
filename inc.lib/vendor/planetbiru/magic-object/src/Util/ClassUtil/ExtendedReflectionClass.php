<?php

namespace MagicObject\Util\ClassUtil;

use MagicObject\Exceptions\InvalidClassException;
use ReflectionClass;

/**
 * The MIT License (MIT)
 *
 * Copyright (c) Ozgur (Ozzy) Giritli <ozgur@zeronights.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @package MagicObject\Util\ClassUtil
 */
class ExtendedReflectionClass extends ReflectionClass {

	/**
     * Array of use statements for the class.
     *
     * @var array
     */
    protected $useStatements = array();

    /**
     * Flag indicating whether use statements have been parsed.
     *
     * @var bool
     */
    protected $useStatementsParsed = false;

    /**
     * Parses the class file and retrieves use statements from the current namespace.
     *
     * This method reads the source of the class file, tokenizes it, and extracts 
     * the use statements. If the use statements have already been parsed, 
     * it returns the cached results.
     *
     * @return array An array of use statements.
     * @throws InvalidClassException If the class is not user-defined.
     */
	protected function parseUseStatements() {

		if ($this->useStatementsParsed) {
			return $this->useStatements;
		}

		if (!$this->isUserDefined()) {
			throw new InvalidClassException('Must parse use statements from user defined classes.');
		}

		$source = $this->readFileSource();
		$this->useStatements = $this->tokenizeSource($source);

		$this->useStatementsParsed = true;
		return $this->useStatements;
	}

	/**
     * Reads the source code of the class file up to the line where the class is defined.
     *
     * @return string The source code of the class file up to the class definition.
     */
	private function readFileSource() {

		$file = fopen($this->getFileName(), 'r');
		$line = 0;
		$source = '';

		while (!feof($file)) {
			++$line;

			if ($line >= $this->getStartLine()) {
				break;
			}

			$source .= fgets($file);
		}

		fclose($file);

		return $source;
	}

	/**
     * Tokenizes the source code and extracts use statements.
     *
     * This method parses the tokens in the source code to identify use statements
     * and their aliases, returning an array of these statements.
     *
     * @param string $source The source code to be tokenized.
     * @return array An array of use statements, including aliases.
     */
	private function tokenizeSource($source) // NOSONAR
	{
		$tokens = token_get_all($source);

		$builtNamespace = '';
		$buildingNamespace = false;
		$matchedNamespace = false;

		$useStatements = array();
		$record = false;
		$currentUse = array(
			'class' => '',
			'as' => ''
		);

		foreach ($tokens as $token) {

			if ($token[0] === T_NAMESPACE) {
				$buildingNamespace = true;

				if ($matchedNamespace) {
					break;
				}
			}

			if ($buildingNamespace) {

				if ($token === ';') {
					$buildingNamespace = false;
					continue;
				}

				switch ($token[0]) {

					case T_STRING:
					case T_NS_SEPARATOR:
						$builtNamespace .= $token[1];
						break;
					default:
						break;
				}

				continue;
			}

			if ($token === ';' || !is_array($token)) {

				if ($record) {
					$useStatements[] = $currentUse;
					$record = false;
					$currentUse = [
						'class' => '',
						'as' => ''
					];
				}

				continue;
			}

			if ($token[0] === T_CLASS) {
				break;
			}

			if (strcasecmp($builtNamespace, $this->getNamespaceName()) === 0) {
				$matchedNamespace = true;
			}

			if ($matchedNamespace) {

				if ($token[0] === T_USE) {
					$record = 'class';
				}

				if ($token[0] === T_AS) {
					$record = 'as';
				}

				if ($record) {
					switch ($token[0]) {

						case T_STRING:
						case T_NS_SEPARATOR:

							if ($record) {
								$currentUse[$record] .= $token[1];
							}

							break;
						default:
							break;
					}
				}
			}

			if ($token[2] >= $this->getStartLine()) {
				break;
			}
		}

		// Make sure the as key has the name of the class even
		// if there is no alias in the use statement.
		foreach ($useStatements as &$useStatement) {

			if (empty($useStatement['as'])) {

				$useStatement['as'] = $this->baseName($useStatement['class']);
			}
		}

		return $useStatements;
	}

	/**
     * Returns the array of use statements for the class.
     *
     * @return array An array of use statements for the class.
     */
	public function getUseStatements() {

		return $this->parseUseStatements();
	}

	/**
     * Extracts the base name of a class from its fully qualified name.
     *
     * @param string $className The fully qualified class name.
     * @return string The base name of the class (i.e., the class name without namespace).
     */
	private function baseName($className)
	{
		$className = str_replace("/", "\\", $className);
		if(stripos($className, "\\") !== false)
		{
			$classArr = explode("\\", $className);
			return end($classArr);
		}
		return $className;
	}

	/**
     * Checks if the class has a specific use statement or alias.
     *
     * @param string $class The name of the class to check.
     * @return bool True if the class or its alias is found in the use statements, false otherwise.
     */
	public function hasUseStatement($class) {

		$useStatements = $this->parseUseStatements();

		return
			array_search($class, array_column($useStatements, 'class')) ||
			array_search($class, array_column($useStatements, 'as'));
	}
}