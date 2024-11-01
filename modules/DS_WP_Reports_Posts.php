<?php

class DS_WP_Reports_Posts implements DS_WP_Reports_ModuleInterface {

	public static function initModule() {

		add_filter('wpr-available-reports', array(__CLASS__, 'addReports'), 10, 1);

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

		$reports['wp-post-count'] = [
			'report_id' => 'wp-post-count',
			'group_id' => 'wordpress',
			'name' => __('Posts', 'ds-wp-reports'),
			'filters' => array(),
			'data_callback' => array(__CLASS__, 'generateDailyPostsCount'),
			'suitable_visualizations' => array('timeline', 'tabular')
		];

		return $reports;

	}

	/**
	 * Generate daily stats for WordPress posts
	 *
	 * @author Jakub Bajzath <bajzath.jakub@gmail.com>
	 * @since 1.0.1
	 *
	 * @param int $reportId
	 * @param array $settings
	 *
	 * @return array|WP_Error Generated data for report, or WP Error if error occurred
	 */
	public static function generateDailyPostsCount($reportId = 0, $settings = array()) {

		$fromDate = $settings['date_from'];
		$toDate = $settings['date_to'] . ' 23:59:59';

		global $wpdb;

		$postsQueryPrepared = "SELECT DATE_FORMAT(post_date,%s) AS post_date2, COUNT(*) AS count"
				. " FROM {$wpdb->posts} "
				. " WHERE post_date BETWEEN %s AND %s "
				. " AND post_status = 'publish' "
				. " AND post_type = 'post' "
				. " GROUP BY post_date2;";

		$postsQuery = $wpdb->prepare($postsQueryPrepared, '%Y-%m-%d', $fromDate, $toDate, '%Y-%m-%d');

		$posts = $wpdb->get_results($postsQuery);

		$values = array();

		$totalPosts = 0;
		foreach ($posts as $post) {
			$values[$post->post_date2] = [
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
					'description' => 'posts'
				]
			],
			'values' => $values
		];

		$result['query'] = $postsQuery;

		return $result;

	}

}
