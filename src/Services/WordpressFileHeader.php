<?php namespace Zae\WPVulnerabilities\Services;

/**
 * @author       Ezra Pool <ezra@tsdme.nl>
 * @copyright (c), 2016 Ezra Pool
 */

/**
 * Class WordpressFileHeader
 *
 * @package Zae\WPVulnerabilities\Services
 */
class WordpressFileHeader
{
	/**
	 * @param $file
	 * @param $default_headers
	 *
	 * @return array
	 */
	public function get_file_data($file, $default_headers)
	{
		// We don't need to write to the file, so just open for reading.
		$fp = fopen($file, 'r');

		// Pull only the first 8kiB of the file in.
		$file_data = fread($fp, 8192);

		// PHP will close file handle, but we are good citizens.
		fclose($fp);

		// Make sure we catch CR-only line endings.
		$file_data = str_replace("\r", "\n", $file_data);

		$all_headers = [];

		foreach ($default_headers as $field => $regex) {
			if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, '/') . ':(.*)$/mi', $file_data, $match) && $match[1]) {
				$all_headers[$field] = $this->_cleanup_header_comment($match[1]);
			} else {
				$all_headers[$field] = '';
			}
		}

		return $all_headers;
	}

	/**
	 * @param $str
	 *
	 * @return string
	 */
	private function _cleanup_header_comment($str)
	{
		return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
	}
}