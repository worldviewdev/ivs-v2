<?php
$mem = memory_get_usage(true);
$peak = memory_get_peak_usage(true);
$time = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
$load = sys_getloadavg();
$phpv = PHP_VERSION;
$server = $_SERVER['SERVER_SOFTWARE'];

function fmt($bytes)
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $pow = $bytes > 0 ? floor(log($bytes, 1024)) : 0;
    return round($bytes / (1 << (10 * $pow)), 2) . ' ' . $units[$pow];
}
?>

</div>


<div id="kt_app_footer" class="app-footer">

    <div class="app-container container-fluid d-flex flex-column flex-md-row flex-center flex-md-stack py-3">

        <div class="text-gray-900 order-2 order-md-1">
            <span class="text-muted fw-semibold me-1">2025&copy;</span>
            <a href="https://keenthemes.com" target="_blank" class="text-gray-800 text-hover-primary">Keenthemes</a>
        </div>

        <?php if(LOCAL_MODE): ?>
        <ul class="menu menu-gray-600 menu-hover-primary fw-semibold order-1">
            <li class="menu-item">
                <a href="https://keenthemes.com" target="_blank" class="menu-link px-2"> ⚡ Time: <?= number_format($time, 3) ?>s |
                    💾 Memory: <?= fmt($mem) ?> (Peak: <?= fmt($peak) ?>) |
                    🐘 PHP: <?= $phpv ?> |
                    🌐 Server: <?= $server ?></a>
            </li>
        </ul>
        <?php endif; ?>
    </div>

</div>

</div>
</div>
</div>

</div>
<script>
    var hostUrl = "<?php echo SITE_WS_PATH; ?>/assets/";
</script>


<script src="<?php echo SITE_WS_PATH; ?>/assets/js/scripts.bundle.js"></script>
<script src="<?php echo SITE_WS_PATH; ?>/assets/plugins/custom/datatables/datatables.bundle.js"></script>
<!-- <script src="assets/js/custom/apps/ecommerce/sales/listing.js"></script> -->
<!-- <script src="assets/js/custom/apps/projects/targets/targets.js"></script>
<script src="assets/js/custom/utilities/modals/create-campaign.js"></script>
<script src="assets/js/custom/utilities/modals/new-target.js"></script>
<script src="assets/js/custom/utilities/modals/new-card.js"></script>
<script src="assets/js/custom/utilities/modals/bidding.js"></script>
<script src="assets/js/custom/utilities/modals/top-up-wallet.js"></script> -->
<script src="<?php echo SITE_WS_PATH; ?>/assets/js/widgets.bundle.js"></script>
<script src="<?php echo SITE_WS_PATH; ?>/assets/js/custom/widgets.js"></script>
<!-- <script src="assets/js/custom/widgets.js"></script>
<script src="assets/js/custom/apps/chat/chat.js"></script>
<script src="assets/js/custom/utilities/modals/share-earn.js"></script>
<script src="assets/js/custom/utilities/modals/upgrade-plan.js"></script>
<script src="assets/js/custom/utilities/modals/users-search.js"></script> -->
<script src="<?php echo SITE_WS_PATH; ?>/assets/js/custom.js"></script>
</body>

</html>