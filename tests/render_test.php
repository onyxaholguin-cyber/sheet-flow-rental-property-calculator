<?php
// Minimal WordPress stubs so the plugin's shortcode output can be rendered and checked without WordPress.
define( 'ABSPATH', __DIR__ );
$GLOBALS['sc'] = array(); $GLOBALS['opt'] = array();
function __( $s, $d = null ) { return $s; }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' ); }
function esc_html__( $s, $d = null ) { return esc_html( $s ); }
function esc_url( $s ) { return $s; }
function add_action() {} function add_filter() {}
function add_shortcode( $t, $f ) { $GLOBALS['sc'][ $t ] = $f; }
function plugin_dir_url( $f ) { return 'https://example.test/wp-content/plugins/x/'; }
function plugin_basename( $f ) { return 'x/x.php'; }
function wp_register_style() {} function wp_register_script() {}
function wp_script_is() { return true; }
function wp_enqueue_style() {} function wp_enqueue_script() {}
function get_option( $k ) { return isset( $GLOBALS['opt'][ $k ] ) ? $GLOBALS['opt'][ $k ] : false; }
function shortcode_atts( $pairs, $atts, $sc = '' ) {
	$out = array();
	foreach ( $pairs as $k => $v ) { $out[ $k ] = array_key_exists( $k, $atts ) ? $atts[ $k ] : $v; }
	return $out;
}
require __DIR__ . '/../sheet-flow-rental-property-calculator/sheet-flow-rental-property-calculator.php';
function check( $c, $m ) { if ( ! $c ) { fwrite( STDERR, "FAIL: $m\n" ); exit( 1 ); } }
check( isset( $GLOBALS['sc']['sheetflow_rental_calculator'], $GLOBALS['sc']['sheetflow_str_calculator'] ), 'shortcodes registered' );
$h = call_user_func( $GLOBALS['sc']['sheetflow_rental_calculator'], array( 'price' => '325000', 'title' => '<b>X</b>', 'rent' => 'evil"><script>' ) );
check( strpos( $h, 'name="price" value="325000"' ) !== false, 'price attribute applied' );
check( strpos( $h, 'name="rent" value="2100"' ) !== false, 'non-numeric attribute falls back to default' );
check( strpos( $h, '<script' ) === false && strpos( $h, '&lt;b&gt;X&lt;/b&gt;' ) !== false, 'title escaped' );
check( substr_count( $h, '<input' ) === 17, 'ltr has 17 inputs' );
check( strpos( $h, 'Sheet &amp; Flow</a>' ) === false, 'no credit link by default' );
$GLOBALS['opt']['sfrc_show_credit'] = true;
$h2 = call_user_func( $GLOBALS['sc']['sheetflow_str_calculator'], '' );
check( substr_count( $h2, '<input' ) === 16 && strpos( $h2, 'data-kind="str"' ) !== false, 'str has 16 inputs' );
check( strpos( $h2, 'Sheet &amp; Flow</a>' ) !== false, 'credit link when opted in' );
check( strpos( $h2, 'id="sfrc-2"' ) !== false, 'unique ids' );
echo "ok render tests\n";
