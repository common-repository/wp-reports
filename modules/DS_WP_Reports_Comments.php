<?php

class DS_WP_Reports_Comments {

	public static function initModule() {

		add_filter('wpr-available-reports', array(__CLASS__, 'addReports'), 10, 1);
		add_filter('wpr-available-groups', array(__CLASS__, 'addGroup'), 10, 1);

	}

	public static function addGroup($groups = array()) {

		if (!array_key_exists('wordpress', $groups)) {

			$groups['wordpress'] = array(
				'id' => 'wordpress',
				'name' => __('WordPress', 'ds-wp-reports')
			);
		}

		return $groups;

	}

	/**
	 * Add default Reports from WordPress
	 *
	 * @author Jakub Bajzath <bajzath.jakub@gmail.com>
	 * @since 1.0.1
	 *
	 * @param array $reports
	 *
	 * @return array Array of reports settings and parameters
	 */
	public static function addReports($reports = array()) {

		$reports['wp-comments-count'] = [
			'report_id' => 'wp-comments-count',
			'group_id' => 'wordpress',
			'name' => __('Comments', 'ds-wp-reports'),
			'filters' => array(),
			'data_callback' => array(__CLASS__, 'generateDailyCommentsCount'),
			'suitable_visualizations' => array('timeline', 'tabular')
		];

		return $reports;

	}

	/**
	 * Generate daily stats for WordPress comments.
	 *
	 * @author Jakub Bajzath <bajzath.jakub@gmail.com>
	 * @since 1.0.1
	 *
	 * @param int $reportId
	 * @param array $settings
	 *
	 * @return array|WP_Error Generated data for report, or WP Error if error occurred
	 */
	public static function generateDailyCommentsCount($reportId = 0, $settings = array()) {

		$fromDate = $settings['date_from'];
		$toDate = $settings['date_to'] . ' 23:59:59';

		global $wpdb;

		$postsQueryPrepared = "SELECT DATE_FORMAT(comment_date,%s) AS comment_date2, COUNT(*) AS count"
				. " FROM {$wpdb->comments} "
				. " WHERE comment_date BETWEEN %s AND %s "
				. " AND comment_type != 'pingback' "
				. " AND comment_approved = 1 "
				. " GROUP BY comment_date2;";

		$postsQuery = $wpdb->prepare($postsQueryPrepared, '%Y-%m-%d', $fromDate, $toDate, '%Y-%m-%d');
		$posts = $wpdb->get_results($postsQuery);

		$values = [];

		$totalPosts = 0;
		foreach ($posts as $post) {
			$values[$post->comment_date2] = [
				'total' => intval($post->count)
			];

			$totalPosts += intval($post->count);
		}

		$result = [
			'labels' => [
				'total' => 'Total'
			],
			'highlights' => [
				[
					'title' => 'Total',
					'value' => intval($totalPosts),
					'description' => 'comments'
				]
			],
			'values' => $values
		];

		$result['query'] = $postsQuery;

		return $result;

	}

}
