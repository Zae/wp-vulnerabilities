<?php
/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

use Illuminate\Support\Arr;

/**
 * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
 * keys to arrays rather than overwriting the value in the first array with the duplicate
 * value in the second array, as array_merge does. I.e., with array_merge_recursive,
 * this happens (documented behavior):
 *
 * Arr::merge_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('org value', 'new value'));
 *
 * Arr::merge_distinct does not change the datatypes of the values in the arrays.
 * Matching keys' values in the second array overwrite those in the first array, as is the
 * case with array_merge, i.e.:
 *
 * Arr::merge_distinct(array('key' => 'org value'), array('key' => 'new value'));
 *     => array('key' => array('new value'));
 *
 *
 * @param array $array1
 * @param array $array2
 * @return array
 * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
 * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
 */
Arr::macro('merge_distinct', function(array $array1, array $array2 )
{
	$merged = $array1;

	foreach ( $array2 as $key => &$value )
	{
		if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
		{
			$merged [$key] = Arr::merge_distinct ( $merged [$key], $value );
		}
		else
		{
			$merged [$key] = $value;
		}
	}

	return $merged;
});

/**
 * Combination of array_merge and array_merge_recursive, does merge news keys into an existing array, but
 * overrides values instead of creating arrays from duplicate values.
 *
 * this is the splat version of Arr::merge_distinct which can only handle two input arrays.
 *
 * @return array
 */
Arr::macro('merge', function()
{
	$result = [];
	$args = func_get_args();
	$args_limit = func_num_args();

	for ($i = 0; $i < $args_limit; $i++) {
		$result = Arr::merge_distinct($result, $args[$i]);
	}

	return $result;
});