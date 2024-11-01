<?php

use League\Csv\Writer;

/**
 * AJAX request handler.
 *
 * @author Martin Krcho <martin.krcho@devstudio.sk>
 * @since 1.0.0
 */
class DS_WP_Reports_AJAX {

	public static function init() {

		add_action('wp_ajax_' . DS_WP_Reports_Core::AJAX_ACTION_REPORT_SETUP, array(__CLASS__, 'handleReportSetupCall'), 10, 0);
		add_action('wp_ajax_' . DS_WP_Reports_Core::AJAX_ACTION_REPORT_DATA, array(__CLASS__, 'handleReportDataCall'), 10, 0);

	}

	public static function handleReportSetupCall() {

		$reportId = array_key_exists('report_id', $_REQUEST) ? filter_var(trim($_REQUEST['report_id']), FILTER_SANITIZE_STRING) : NULL;
		if ($reportId === FALSE || $reportId === NULL) {
			wp_send_json_error(__('Invalid or missing report ID', 'ds-wp-reports'));
		}

		$report = DS_WP_Reports_Core::getReportById($reportId);
		$result = $report;

		if (is_wp_error($result)) {
			wp_send_json_error($result->get_error_message());
		}

		unset($result['data_callback']);
		wp_send_json_success($result);

	}

	public static function handleReportDataCall() {

		$reportId = array_key_exists('report_id', $_REQUEST) ? filter_var(trim($_REQUEST['report_id']), FILTER_SANITIZE_STRING) : NULL;
		if ($reportId === FALSE || $reportId === NULL) {
			wp_send_json_error(__('Invalid or missing report ID', 'ds-wp-reports'));
		}

		$reportData = DS_WP_Reports_Core::executeReportDataHandler($reportId);

		//	@todo check the data format

		if (is_wp_error($reportData)) {
			wp_send_json_error($reportData->get_error_message($reportData->get_error_code));
		}

		$exportFormat = array_key_exists('export', $_REQUEST) ? filter_var(trim($_REQUEST['export']), FILTER_SANITIZE_STRING) : NULL;
		if ($exportFormat === 'csv') {
			self::handleReportDataExport($reportId, $reportData);
		}

		$visualizationType = 'timeline';
		if (array_key_exists('visualization', $_REQUEST)) {
			$_vt = filter_var(trim($_REQUEST['visualization']), FILTER_SANITIZE_STRING);
			if ($_vt !== FALSE) {
				$visualizationType = $_vt;
			}
		}

		$result = array_merge($reportData, array(
			'visualization' => $visualizationType
		));
		wp_send_json_success($result);

	}

	public static function handleReportDataExport($reportId, $reportData) {

		$vendorAutoloadFile = DS_WP_REPORTS_PLUGIN_PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
		if (!file_exists($vendorAutoloadFile)) {
			wp_die('Missing 3rd party library needed to generate an Excel file.');
		}

		require($vendorAutoloadFile);

		$valuesToExport = $reportData['values'];

		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $reportId . '_' . current_time('mysql') . '.csv');

		$writer = Writer::createFromFileObject(new SplTempFileObject()); //the CSV file will be created using a temporary File
		$writer->setEnclosure('"');
		$writer->setDelimiter(";"); //the delimiter will be the tab character
		$writer->setNewline("\r\n"); //use windows line endings for compatibility with some csv libraries
		$writer->setOutputBOM(Writer::BOM_UTF8); //adding the BOM sequence on output

		foreach ($valuesToExport as $dataValues => $value) {

			$exportValues = array();
			array_push($exportValues, $dataValues);

			foreach ($value as $separateValue) {
				array_push($exportValues, $separateValue);
			}

			$writer->insertOne($exportValues);
		}

		$writer->output();
		die(0);

	}

}
