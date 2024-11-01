<?php

/**
 * Convenience class to simplify some common operations.
 * 
 * @since 1.0.3
 * @author Martin Krcho <martin.krcho@devstudio.sk>
 * 
 */
class DS_WP_Reports_Utils {

	/**
	 * Check if date is valid MySQL Data
	 *
	 * @since 1.0.1
	 *
	 * @param bool $value
	 *
	 * @return bool True if value is valid MySQL Date, false if not
	 */
	public static function isValidMySQLDate($value = false) {

		$matches = preg_match('/^\d{4}\-\d{2}\-\d{2}$/', $value);
		return ($matches === 1);

	}

}
