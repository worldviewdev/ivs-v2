<?php
/**
 * Konfigurasi Multi-Domain untuk IVS
 * File ini berisi definisi website dan database yang tersedia
 */
// Array konfigurasi website yang tersedia
$MULTI_DOMAIN_CONFIG = array(
    'italy' => array(
        'name' => 'Italy Vacation Specialists',
        'domain' => 'italyvacationspecialists.com',
        'database' => array(
            'host' => 'localhost',
            'name' => 'italyvacation_ivs',
            'user' => 'italyvacation_ivs_user',
            'pass' => 'RxBax+r2e-]8ZQB-'
        ),
        'logo' => 'images/italy-logo.png',
        'theme_color' => '#1e3a8a',
        'description' => 'Specialized tours and vacation packages in Italy'
    ),
    'france' => array(
        'name' => 'Balivisits',
        'domain' => 'balivisits.com',
        'database' => array(
            'host' => 'localhost',
            'name' => 'brazilgr_balivisits',
            'user' => 'root',
            'pass' => ''
        ),
        'logo' => 'images/france-logo.png',
        'theme_color' => '#dc2626',
        'description' => 'Exclusive travel experiences in Bali'
    ),
);
// Konfigurasi default (jika tidak ada domain yang dipilih)
$DEFAULT_DOMAIN = 'italy';
// Fungsi untuk mendapatkan konfigurasi domain berdasarkan key
function get_domain_config($domain_key = null) {
    global $MULTI_DOMAIN_CONFIG, $DEFAULT_DOMAIN;
    if ($domain_key === null) {
        $domain_key = $DEFAULT_DOMAIN;
    }
    if (isset($MULTI_DOMAIN_CONFIG[$domain_key])) {
        return $MULTI_DOMAIN_CONFIG[$domain_key];
    }
    return $MULTI_DOMAIN_CONFIG[$DEFAULT_DOMAIN];
}
// Fungsi untuk mendapatkan semua domain yang tersedia
function get_available_domains() {
    global $MULTI_DOMAIN_CONFIG;
    return $MULTI_DOMAIN_CONFIG;
}
// Fungsi untuk memvalidasi domain key
function is_valid_domain($domain_key) {
    global $MULTI_DOMAIN_CONFIG;
    return isset($MULTI_DOMAIN_CONFIG[$domain_key]);
}
?>