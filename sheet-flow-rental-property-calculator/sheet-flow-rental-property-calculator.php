<?php
/**
 * Plugin Name:       Sheet & Flow Rental Property Calculator
 * Plugin URI:        https://github.com/onyxaholguin-cyber/sheet-flow-rental-property-calculator
 * Description:       Rental property and short-term rental (vacation rental) calculators for any page: cash flow, cap rate, cash-on-cash, DSCR, NOI, break-even occupancy. Shortcodes, no tracking, no external requests.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Sheet & Flow
 * Author URI:        https://onyxaholguin-cyber.github.io/sheet-and-flow/
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sheet-flow-rental-property-calculator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SFRC_VERSION', '1.0.0' );

/**
 * Field definitions: key => array( label, default, step ). Percent fields are entered as percents (5 = 5%).
 *
 * @param string $kind 'ltr' or 'str'.
 * @return array
 */
function sfrc_fields( $kind ) {
	if ( 'str' === $kind ) {
		return array(
			'price'         => array( __( 'Purchase price ($)', 'sheet-flow-rental-property-calculator' ), 350000, '1000' ),
			'downPct'       => array( __( 'Down payment (%)', 'sheet-flow-rental-property-calculator' ), 20, '0.5' ),
			'rate'          => array( __( 'Interest rate (%)', 'sheet-flow-rental-property-calculator' ), 7, '0.125' ),
			'years'         => array( __( 'Loan term (years)', 'sheet-flow-rental-property-calculator' ), 30, '1' ),
			'closing'       => array( __( 'Closing costs ($)', 'sheet-flow-rental-property-calculator' ), 10000, '100' ),
			'furnishing'    => array( __( 'Furnishing ($)', 'sheet-flow-rental-property-calculator' ), 20000, '100' ),
			'adr'           => array( __( 'Average nightly rate ($)', 'sheet-flow-rental-property-calculator' ), 200, '1' ),
			'occupancy'     => array( __( 'Occupancy (%)', 'sheet-flow-rental-property-calculator' ), 60, '1' ),
			'avgStayNights' => array( __( 'Average stay (nights)', 'sheet-flow-rental-property-calculator' ), 3, '0.1' ),
			'blockedNights' => array( __( 'Owner-blocked nights / year', 'sheet-flow-rental-property-calculator' ), 14, '1' ),
			'cleaningFee'   => array( __( 'Cleaning fee charged per stay ($)', 'sheet-flow-rental-property-calculator' ), 120, '1' ),
			'cleaningCost'  => array( __( 'Cleaning cost per stay ($)', 'sheet-flow-rental-property-calculator' ), 90, '1' ),
			'hostFeePct'    => array( __( 'Platform host fee (% of revenue)', 'sheet-flow-rental-property-calculator' ), 3, '0.1' ),
			'mgmtPct'       => array( __( 'Management (% of revenue)', 'sheet-flow-rental-property-calculator' ), 0, '0.5' ),
			'maintCapexPct' => array( __( 'Maintenance + CapEx (% of revenue)', 'sheet-flow-rental-property-calculator' ), 5, '0.5' ),
			'fixedMonthly'  => array( __( 'Fixed costs / month ($): tax, insurance, utilities, internet', 'sheet-flow-rental-property-calculator' ), 900, '10' ),
		);
	}
	return array(
		'price'       => array( __( 'Purchase price ($)', 'sheet-flow-rental-property-calculator' ), 250000, '1000' ),
		'downPct'     => array( __( 'Down payment (%)', 'sheet-flow-rental-property-calculator' ), 25, '0.5' ),
		'rate'        => array( __( 'Interest rate (%)', 'sheet-flow-rental-property-calculator' ), 7, '0.125' ),
		'years'       => array( __( 'Loan term (years)', 'sheet-flow-rental-property-calculator' ), 30, '1' ),
		'closing'     => array( __( 'Closing costs ($)', 'sheet-flow-rental-property-calculator' ), 7500, '100' ),
		'repairs'     => array( __( 'Up-front repairs ($)', 'sheet-flow-rental-property-calculator' ), 0, '100' ),
		'units'       => array( __( 'Units', 'sheet-flow-rental-property-calculator' ), 1, '1' ),
		'rent'        => array( __( 'Rent per unit / month ($)', 'sheet-flow-rental-property-calculator' ), 2100, '10' ),
		'otherIncome' => array( __( 'Other income / month ($)', 'sheet-flow-rental-property-calculator' ), 0, '10' ),
		'vacancyPct'  => array( __( 'Vacancy (% of income)', 'sheet-flow-rental-property-calculator' ), 5, '0.5' ),
		'taxes'       => array( __( 'Property taxes / year ($)', 'sheet-flow-rental-property-calculator' ), 3000, '50' ),
		'insurance'   => array( __( 'Insurance / year ($)', 'sheet-flow-rental-property-calculator' ), 1500, '50' ),
		'hoa'         => array( __( 'HOA / month ($)', 'sheet-flow-rental-property-calculator' ), 0, '5' ),
		'utilities'   => array( __( 'Owner-paid utilities / month ($)', 'sheet-flow-rental-property-calculator' ), 0, '5' ),
		'repairsPct'  => array( __( 'Repairs (% of rent)', 'sheet-flow-rental-property-calculator' ), 5, '0.5' ),
		'capexPct'    => array( __( 'CapEx reserve (% of rent)', 'sheet-flow-rental-property-calculator' ), 5, '0.5' ),
		'mgmtPct'     => array( __( 'Management (% of collected)', 'sheet-flow-rental-property-calculator' ), 8, '0.5' ),
	);
}

/**
 * Register (not enqueue) assets; they load only on pages that use a shortcode.
 */
function sfrc_register_assets() {
	$base = plugin_dir_url( __FILE__ ) . 'assets/';
	wp_register_style( 'sfrc', $base . 'style.css', array(), SFRC_VERSION );
	wp_register_script( 'sfrc-calc', $base . 'calc.js', array(), SFRC_VERSION, true );
	wp_register_script( 'sfrc-ui', $base . 'ui.js', array( 'sfrc-calc' ), SFRC_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'sfrc_register_assets' );

/**
 * Render one calculator.
 *
 * @param string $kind  'ltr' or 'str'.
 * @param array  $atts  Shortcode attributes (lowercased by WordPress).
 * @return string
 */
function sfrc_render( $kind, $atts ) {
	$fields   = sfrc_fields( $kind );
	$defaults = array(
		'title' => 'str' === $kind ? __( 'Short-Term Rental Calculator', 'sheet-flow-rental-property-calculator' ) : __( 'Rental Property Calculator', 'sheet-flow-rental-property-calculator' ),
	);
	foreach ( $fields as $key => $f ) {
		$defaults[ strtolower( $key ) ] = $f[1];
	}
	$atts = shortcode_atts( $defaults, (array) $atts, 'str' === $kind ? 'sheetflow_str_calculator' : 'sheetflow_rental_calculator' );

	if ( ! wp_script_is( 'sfrc-ui', 'registered' ) ) {
		sfrc_register_assets();
	}
	wp_enqueue_style( 'sfrc' );
	wp_enqueue_script( 'sfrc-ui' );

	static $n = 0;
	$n++;
	$id = 'sfrc-' . $n;

	$html  = '<div class="sfrc-calculator" id="' . esc_attr( $id ) . '" data-kind="' . esc_attr( $kind ) . '">';
	$html .= '<h3>' . esc_html( $atts['title'] ) . '</h3><form novalidate><div class="sfrc-grid">';
	foreach ( $fields as $key => $f ) {
		$val   = is_numeric( $atts[ strtolower( $key ) ] ) ? (float) $atts[ strtolower( $key ) ] : (float) $f[1];
		$html .= '<label>' . esc_html( $f[0] ) . '<input type="number" inputmode="decimal" min="0" step="' . esc_attr( $f[2] ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '"></label>';
	}
	$html .= '</div></form>';
	$html .= '<table class="sfrc-results" aria-live="polite"><tbody></tbody></table>';
	$html .= '<p class="sfrc-note">' . esc_html__( 'Year-one estimate for education only, not financial advice. Results update as you type; nothing you enter leaves your browser.', 'sheet-flow-rental-property-calculator' ) . '</p>';
	if ( get_option( 'sfrc_show_credit' ) ) {
		$html .= '<p class="sfrc-note">' . esc_html__( 'Calculator by', 'sheet-flow-rental-property-calculator' ) . ' <a href="' . esc_url( 'https://onyxaholguin-cyber.github.io/sheet-and-flow/' ) . '">Sheet &amp; Flow</a></p>';
	}
	$html .= '</div>';
	return $html;
}

/**
 * [sheetflow_rental_calculator] long-term rental calculator.
 *
 * @param array $atts Attributes.
 * @return string
 */
function sfrc_shortcode_rental( $atts ) {
	return sfrc_render( 'ltr', $atts );
}
add_shortcode( 'sheetflow_rental_calculator', 'sfrc_shortcode_rental' );

/**
 * [sheetflow_str_calculator] short-term (vacation) rental calculator.
 *
 * @param array $atts Attributes.
 * @return string
 */
function sfrc_shortcode_str( $atts ) {
	return sfrc_render( 'str', $atts );
}
add_shortcode( 'sheetflow_str_calculator', 'sfrc_shortcode_str' );

/**
 * Settings: one opt-in checkbox for a credit link. Off by default.
 */
function sfrc_admin_init() {
	register_setting(
		'sfrc',
		'sfrc_show_credit',
		array(
			'type'              => 'boolean',
			'default'           => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);
}
add_action( 'admin_init', 'sfrc_admin_init' );

/**
 * Add the settings page under Settings.
 */
function sfrc_admin_menu() {
	add_options_page(
		__( 'Rental Calculator', 'sheet-flow-rental-property-calculator' ),
		__( 'Rental Calculator', 'sheet-flow-rental-property-calculator' ),
		'manage_options',
		'sfrc',
		'sfrc_settings_page'
	);
}
add_action( 'admin_menu', 'sfrc_admin_menu' );

/**
 * Settings page output.
 */
function sfrc_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Sheet & Flow Rental Property Calculator', 'sheet-flow-rental-property-calculator' ); ?></h1>
		<h2><?php esc_html_e( 'How to use', 'sheet-flow-rental-property-calculator' ); ?></h2>
		<p><?php esc_html_e( 'Add a shortcode to any page or post (Shortcode block in the editor):', 'sheet-flow-rental-property-calculator' ); ?></p>
		<p><code>[sheetflow_rental_calculator]</code> &mdash; <?php esc_html_e( 'long-term rental: cash flow, cap rate, cash-on-cash, DSCR, NOI, GRM, 1% rule, break-even occupancy.', 'sheet-flow-rental-property-calculator' ); ?></p>
		<p><code>[sheetflow_str_calculator]</code> &mdash; <?php esc_html_e( 'short-term / vacation rental: cash flow, RevPAR, break-even occupancy and nightly rate.', 'sheet-flow-rental-property-calculator' ); ?></p>
		<p><?php esc_html_e( 'Set your own starting values with attributes, for example:', 'sheet-flow-rental-property-calculator' ); ?> <code>[sheetflow_rental_calculator price="325000" rent="2400" taxes="4200" title="Run the numbers on this listing"]</code></p>
		<form method="post" action="options.php">
			<?php settings_fields( 'sfrc' ); ?>
			<label><input type="checkbox" name="sfrc_show_credit" value="1" <?php checked( (bool) get_option( 'sfrc_show_credit' ) ); ?>> <?php esc_html_e( 'Show a small "Calculator by Sheet & Flow" link under each calculator (off by default).', 'sheet-flow-rental-property-calculator' ); ?></label>
			<?php submit_button(); ?>
		</form>
		<h2><?php esc_html_e( 'Need more than year one?', 'sheet-flow-rental-property-calculator' ); ?></h2>
		<p><?php esc_html_e( 'Sheet & Flow also sells spreadsheet versions with multi-year projections, IRR and side-by-side deal comparison:', 'sheet-flow-rental-property-calculator' ); ?>
			<a href="<?php echo esc_url( 'https://sheetandflow.gumroad.com/l/rental-calculator-pro' ); ?>" target="_blank" rel="noopener">Rental Property Calculator Pro</a>,
			<a href="<?php echo esc_url( 'https://sheetandflow.gumroad.com/l/short-term-rental-analyzer' ); ?>" target="_blank" rel="noopener">Short-Term Rental Analyzer</a>.
		</p>
	</div>
	<?php
}

/**
 * Settings link on the Plugins screen.
 *
 * @param array $links Links.
 * @return array
 */
function sfrc_action_links( $links ) {
	$links[] = '<a href="' . esc_url( admin_url( 'options-general.php?page=sfrc' ) ) . '">' . esc_html__( 'Settings', 'sheet-flow-rental-property-calculator' ) . '</a>';
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'sfrc_action_links' );
