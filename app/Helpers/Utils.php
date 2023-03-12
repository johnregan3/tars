<?php

namespace App\Helpers;

/**
 * A collection of utility functions.
 */
class Utils
{

	public static function humanReadableDate($date, $full = false)
	{
		$now = new \DateTime();
		$ago = new \DateTime($date);
		$diff = $now->diff($ago);
		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;
		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}
		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	/**
	 * Normalize a date.
	 *
	 * Returns a date in the format: "Monday, January 1, at 12:03AM"
	 *
	 * @param string $date The date to normalize.
	 *
	 * @return string The normalized date.
	 */
	public static function normalizedDate( $date )
	{
		$time = strtotime($date);
		return date('l, F j,', $time) . ' at ' . date('h:iA', $time);
	}

	/**
	 * Clarify a prompt by removing unnecessary text.
	 *
	 * This exists to reduce the number of tokens in the prompt.
	 *
	 * Not that if you really want to let one of these words
	 * pass through the filter, you can just add any character
	 * to the beginning or end of the word.
	 *
	 * @param string $prompt The prompt to clarify.
	 *
	 * @return string The clarified prompt.
	 */
	public static function clarifyPrompt($prompt)
	{
		$blackList = [
			'actually',
			'basically',
			'completely',
			'could you',
			'essentially',
			'goodbye',
			'hello',
			'I think',
			'in my opinion',
			'just',
			'kindly',
			'literally',
			'obviously',
			'please',
			'really',
			'simply',
			'so',
			'thank you',
			'totally',
			'very',
			'would you',
		];

		// Wrap each word in a regular expression.
		$blackList = array_map(function ($word) {
			// This regex ignores caps and includes an optional trailing comma.
			return '/\b' . preg_quote($word, '/') . '(?:,\s)?\b/i';
		}, $blackList);

		$prompt = preg_replace($blackList, '', $prompt);
	}
}
