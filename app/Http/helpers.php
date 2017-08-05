<?php

if (!function_exists('color_rand')) {

	/**
	 * Returns a random color hex code.
	 *
	 * @return string Random color, as hex code
	 */
	function color_rand()
	{
		return sprintf('#%06x', mt_rand(0, 0xffffff));
	}

}

if (!function_exists('human_filesize')) {

	/**
	 * Formats a given size (in bytes) and returns a string with the
	 *   appropriate suffix.
	 *
	 * @param $input int Size to format, in kilobytes
	 * @param $precision int Number of decimal places
	 * @param $space string Separator between value and byte suffix
	 * @return string Formatted size with suffix
	 */
	function human_filesize($input, $precision = null, $space = '')
	{
		$suffix = ['Y', 'Z', 'E', 'P', 'T', 'G', 'M', 'k', ''];
		$total = count($suffix);

		while ($total -- and $input > 1024) {
			$input /= 1024;
		}

		$decimals = $precision ?
			$precision :
			($input < 10 ? 2 : ($input < 100 ? 1 : 0));

		return round($input, $decimals).e($space).$suffix[$total];
	}

}

if (!function_exists('type_of')) {

	/**
	 * Returns a short, unqualified class name, given a long,
	 * fully-qualified one.
	 *
	 * @return string Unqualified, short class name
	 */
	function type_of($object)
	{
		if (is_object($object)) {
			return (new \ReflectionClass($object))->getShortName();
		}

		$class = explode('\\', $object);
		return array_pop($class);
	}

}

if (!function_exists('view_for_class')) {

	/**
	 * Returns a short, unqualified class name, given a long,
	 * fully-qualified one.
	 *
	 * @return string Unqualified, short class name
	 */
	function view_for_class($object)
	{
		if (is_object($object)) {
			$type = (new \ReflectionClass($object))->getShortName();
		} else {
 			$class = explode('\\', $object);
			$type = array_pop($class);
		}

		$type = str_plural(strtolower($type));

		switch ($type) {
			case 'documents':
				return 'docs';
			default:
				return $type;
		}
	}

}
