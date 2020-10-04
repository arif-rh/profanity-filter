<?php

namespace Arifrh\ProfanityFilter;

/**
 * Main class for profanity filter
 *
 * @author DeveloperDino
 * @author Arif Rahman Hakim
 */

class Check
{
	const SEPARATOR_PLACEHOLDER = '{!!}';

	/**
	 * Escaped separator characters
	 *
	 * @var mixed $escapedSeparatorCharacters
	 */
	protected $escapedSeparatorCharacters = [
		'\s',
	];

	/**
	 * Unescaped separator characters.
	 * @var mixed $separatorCharacters
	 */
	protected $separatorCharacters = [
		'@',
		'#',
		'%',
		'&',
		'_',
		';',
		"'",
		'"',
		',',
		'~',
		'`',
		'|',
		'!',
		'$',
		'^',
		'*',
		'(',
		')',
		'-',
		'+',
		'=',
		'{',
		'}',
		'[',
		']',
		':',
		'<',
		'>',
		'?',
		'.',
		'/',
	];

	/**
	 * List of potential character substitutions as a regular expression.
	 *
	 * @var mixed $characterSubstitutions
	 */
	protected $characterSubstitutions = [
		'/a/' => [
			'a',
			'4',
			'@',
			'Á',
			'á',
			'À',
			'Â',
			'à',
			'Â',
			'â',
			'Ä',
			'ä',
			'Ã',
			'ã',
			'Å',
			'å',
			'æ',
			'Æ',
			'α',
			'Δ',
			'Λ',
			'λ',
		],
		'/b/' => ['b', '8', '\\', '3', 'ß', 'Β', 'β'],
		'/c/' => ['c', 'Ç', 'ç', 'ć', 'Ć', 'č', 'Č', '¢', '€', '<', '(', '{', '©'],
		'/d/' => ['d', '\\', ')', 'Þ', 'þ', 'Ð', 'ð'],
		'/e/' => ['e', '3', '€', 'È', 'è', 'É', 'é', 'Ê', 'ê', 'ë', 'Ë', 'ē', 'Ē', 'ė', 'Ė', 'ę', 'Ę', '∑'],
		'/f/' => ['f', 'ƒ'],
		'/g/' => ['g', '6', '9'],
		'/h/' => ['h', 'Η'],
		'/i/' => ['i', '!', '|', ']', '[', '1', '∫', 'Ì', 'Í', 'Î', 'Ï', 'ì', 'í', 'î', 'ï', 'ī', 'Ī', 'į', 'Į'],
		'/j/' => ['j'],
		'/k/' => ['k', 'Κ', 'κ'],
		'/l/' => ['l', '!', '|', ']', '[', '£', '∫', 'Ì', 'Í', 'Î', 'Ï', 'ł', 'Ł'],
		'/m/' => ['m'],
		'/n/' => ['n', 'η', 'Ν', 'Π', 'ñ', 'Ñ', 'ń', 'Ń'],
		'/o/' => [
			'o',
			'0',
			'Ο',
			'ο',
			'Φ',
			'¤',
			'°',
			'ø',
			'ô',
			'Ô',
			'ö',
			'Ö',
			'ò',
			'Ò',
			'ó',
			'Ó',
			'œ',
			'Œ',
			'ø',
			'Ø',
			'ō',
			'Ō',
			'õ',
			'Õ',
		],
		'/p/' => ['p', 'ρ', 'Ρ', '¶', 'þ'],
		'/q/' => ['q'],
		'/r/' => ['r', '®'],
		'/s/' => ['s', '5', '$', '§', 'ß', 'Ś', 'ś', 'Š', 'š'],
		'/t/' => ['t', 'Τ', 'τ'],
		'/u/' => ['u', 'υ', 'µ', 'û', 'ü', 'ù', 'ú', 'ū', 'Û', 'Ü', 'Ù', 'Ú', 'Ū'],
		'/v/' => ['v', 'υ', 'ν'],
		'/w/' => ['w', 'ω', 'ψ', 'Ψ'],
		'/x/' => ['x', 'Χ', 'χ'],
		'/y/' => ['y', '¥', 'γ', 'ÿ', 'ý', 'Ÿ', 'Ý'],
		'/z/' => ['z', 'Ζ', 'ž', 'Ž', 'ź', 'Ź', 'ż', 'Ż'],
	];

	/**
	 * List of profanities to test against.
	 *
	 * @var mixed $profanities
	 */
	protected $profanities = [];

	/**
	 * List of whitelist to exclude against the test
	 *
	 * @var mixed $whitelists
	 */
	protected $whitelists = [];

	protected $separatorExpression;
	protected $characterExpressions;

	/**
	 * Saved bad words found.
	 */
	protected $badWordsFound = '';

	/**
	 * Initital construct
	 *
	 * @param mixed|null $config
	 * @param mixed|null $whitelists
	 */
	public function __construct($config = null, $whitelists = null)
	{
		if ($config === null) {
			$config = __DIR__ . '/../config/profanities.php';
		}

		if (is_array($config)) 
		{
			$this->profanities = $config;
		} 
		else
		{
			$this->profanities = $this->loadFromFile($config);
		}

		if ($whitelists === null) 
		{
			$whitelists = __DIR__ . '/../config/whitelists.php';
		}

		if (is_array($whitelists)) 
		{
			$this->whitelists = $whitelists;
		} 
		else
		{
			$this->whitelists = $this->loadFromFile($whitelists);
		}

		$this->separatorExpression  = $this->generateSeparatorExpression();
		$this->characterExpressions = $this->generateCharacterExpressions();
	}

	/**
	 * Load 'profanities' from config file.
	 *
	 * @param string $config profanity or whitelist config filename with full path
	 *
	 * @return array
	 */
	private function loadFromFile(string $config)
	{
		/** @noinspection PhpIncludeInspection */
		return include($config);
	}

	/**
	 * Generates the separator regular expression.
	 *
	 * @return string
	 */
	private function generateSeparatorExpression()
	{
		return $this->generateEscapedExpression($this->separatorCharacters, $this->escapedSeparatorCharacters);
	}

	/**
	 * Generates the separator regex to test characters in between letters.
	 *
	 * @param array $characters
	 * @param array $escapedCharacters
	 * @param string $quantifier
	 *
	 * @return string
	 */
	private function generateEscapedExpression(
		array $characters = [],
		array $escapedCharacters = [],
		$quantifier = '*?'
	) {
		$regex = $escapedCharacters;
		foreach ($characters as $character) 
		{
			$regex[] = preg_quote($character, '/');
		}

		return '[' . implode('', $regex) . ']' . $quantifier;
	}

	/**
	 * Generates a list of regular expressions for each character substitution.
	 *
	 * @return array
	 */
	protected function generateCharacterExpressions()
	{
		$characterExpressions = [];

		foreach ($this->characterSubstitutions as $character => $substitutions) 
		{
			$characterExpressions[$character] = $this->generateEscapedExpression(
				$substitutions,
				[],
				'+?'
			 ) . self::SEPARATOR_PLACEHOLDER;
		}

		return $characterExpressions;
	}

	/**
	 * Obfuscates string that contains a 'profanity'.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public function obfuscateIfProfane(string $string)
	{
		if ($this->hasProfanity($string)) 
		{
			$string = str_repeat('*', strlen($string));
		}

		return $string;
	}

	/**
	 * Obfuscates only the string that contains a 'profanity'.
	 *
	 * @param string $string
	 * @param string $replacement
	 *
	 * @return string
	 */
	public function cleanWords(string $string, string $replacement = '*')
	{
		if ($this->hasProfanity($string)) 
		{
			$profanity = $this->generateProfanityExpression(
			 $this->badWordsFound,
			 $this->characterExpressions,
			 $this->separatorExpression
			);
			$string = preg_replace($profanity, str_repeat($replacement, mb_strlen($this->badWordsFound, 'UTF-8')), $string);
		}

		return $string;
	}

	/**
	 * Checks string for profanities based on list 'profanities'
	 *
	 * @param string $string
	 *
	 * @return bool
	 */
	public function hasProfanity(string $string)
	{
		$this->badWordsFound = '';

		if (! empty($string)) 
		{
			$profanities    = [];
			$profanityCount = count($this->profanities);

			for ($i = 0; $i < $profanityCount; $i++) 
			{
				$profanities[$i] = $this->generateProfanityExpression(
				 $this->profanities[$i],
				 $this->characterExpressions,
				 $this->separatorExpression
				);
			}

			foreach ($profanities as $i => $profanity) 
			{
				if ($this->stringHasProfanity($string, $profanity)) 
				{
					$this->badWordsFound = $this->profanities[$i];
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Get bad words found
	 */
	public function getBadWordsFound()
	{
		return $this->badWordsFound;
	}

	/**
	 * Generate a regular expression for a particular word
	 *
	 * @param string $word
	 * @param mixed $characterExpressions
	 * @param mixed $separatorExpression
	 *
	 * @return mixed
	 */
	protected function generateProfanityExpression(string $word, $characterExpressions, $separatorExpression)
	{
		$expression = '/' . preg_replace(
			 array_keys($characterExpressions),
			 array_values($characterExpressions),
			 $word
			) . '/i';

		return str_replace(self::SEPARATOR_PLACEHOLDER, $separatorExpression, $expression);
	}

	/**
	 * Checks a string against a profanity.
	 *
	 * @param string $string
	 * @param mixed  $profanity
	 *
	 * @return bool
	 */
	private function stringHasProfanity(string $string, $profanity)
	{
		if (in_array($string, $this->whitelists))
		{
			return false;
		}

		return preg_match($profanity, $string) === 1;
	}
}
