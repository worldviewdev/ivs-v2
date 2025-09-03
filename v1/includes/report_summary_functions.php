<?php
/**
 * Helper functions for report_file_summary table
 * These functions provide easy access to cached financial totals
 */
/**
 * Get cached financial summary for a file
 * @param int $file_id The file ID
 * @return array|false Array of financial data or false if not found
 */
function get_file_summary($file_id) {
    $file_id = intval($file_id);
    $result = db_result("SELECT * FROM mv_file_report_summary WHERE file_id = '$file_id'");
    return $result;
}
/**
 * Get specific financial metric for a file
 * @param int $file_id The file ID
 * @param string $metric The metric name (total_net_services_cost, gross_profit, etc.)
 * @return float The metric value or 0 if not found
 */
function get_file_metric($file_id, $metric) {
    $file_id = intval($file_id);
    $allowed_metrics = [
        'total_net_services_cost',
        'total_services_sub_total',
        'total_paid',
        'remaining_balance',
        'gross_profit',
        'card_fee',
        'taxes',
        'company_net_profit'
    ];
    if (!in_array($metric, $allowed_metrics)) {
        return 0;
    }
    $value = db_scalar("SELECT $metric FROM mv_file_report_summary WHERE file_id = '$file_id'");
    return floatval($value);
}
/**
 * Check if file summary exists and is recent
 * @param int $file_id The file ID
 * @param int $hours_threshold How many hours old is considered stale (default 24)
 * @return bool True if summary exists and is recent
 */
function is_file_summary_current($file_id, $hours_threshold = 24) {
    $file_id = intval($file_id);
    $hours_threshold = intval($hours_threshold);
    $count = db_scalar("SELECT COUNT(*) FROM mv_file_report_summary
                       WHERE file_id = '$file_id'
                       AND updated_at > DATE_SUB(NOW(), INTERVAL $hours_threshold HOUR)");
    return $count > 0;
}
/**
 * Get summary data for multiple files
 * @param array $file_ids Array of file IDs
 * @return array Array of summary data indexed by file_id
 */
function get_multiple_file_summaries($file_ids) {
    if (empty($file_ids) || !is_array($file_ids)) {
        return array();
    }
    $file_ids = array_map('intval', $file_ids);
    $file_ids_str = implode(',', $file_ids);
    $results = array();
    $sql = db_query("SELECT * FROM mv_file_report_summary WHERE file_id IN ($file_ids_str)");
    while ($row = mysqli_fetch_array($sql)) {
        $results[$row['file_id']] = $row;
    }
    return $results;
}
/**
 * Force refresh of file summary by triggering recalculation
 * This is useful when you know the data has changed
 * @param int $file_id The file ID
 * @return bool True if successful
 */
function refresh_file_summary($file_id) {
    $file_id = intval($file_id);
    // Delete existing summary to force recalculation
    db_query("DELETE FROM mv_file_report_summary WHERE file_id = '$file_id'");
    // The summary will be regenerated the next time file_services.php loads for this file
    return true;
}
?>