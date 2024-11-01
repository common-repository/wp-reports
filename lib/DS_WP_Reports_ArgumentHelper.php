<?php

/**
 * Class DS_WP_Reports_ArgumentHelper
 *
 * @since 5.5.0
 * @author Jakub Bajzath <bajzath.jakub@gmail.com>
 */
class DS_WP_Reports_ArgumentHelper {

	/**
	 * @param string $value
	 * @return bool
	 *
	 * @since 5.5.0
	 */
	public static function isStringValueTrue($value = '') {
		return in_array($value, array(
			true,
			'true',
			'TRUE',
			1,
			'1',
			'yes',
			'on'), TRUE);
	}

	/**
	 *
	 * @param int|array $arg
	 *
	 * @return array
	 * @since 4.4.0
	 */
	public static function normalizeIntegerArrayArgument($arg) {

		$result = array();
		if (is_array($arg)) {

			foreach ($arg as $dataSource) {
				$dataSourceId = intval($dataSource);
				if ($dataSourceId > 0) {
					array_push($result, $dataSourceId);
				}
			}
		} else {
			$parts = explode(',', $arg);
			foreach ($parts as $dataSource) {
				$dataSourceId = intval($dataSource);
				if ($dataSourceId > 0) {
					array_push($result, $dataSourceId);
				}
			}
		}

		$result = array_unique($result);

		return $result;
	}

}
