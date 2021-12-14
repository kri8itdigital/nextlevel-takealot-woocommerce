class-nextlevel-takealot-settings.php<?php
/**
 * WooCommerce Account Settings.
 *
 * @package WooCommerce/Admin
 */
/**
 * WC_Settings_Accounts.
 */
class Nextlevel_Takealot_Woocommerce_Settings extends WC_Settings_Page {


	public function __construct() {
		$this->id    = 'nextlevel-takealot-woocommerce';
		$this->label = __( 'NEXTLEVEL Takealot Settings', 'nextlevel-takealot-woocommerce' );
		parent::__construct();
	} 



	public function get_settings($current_section = '') {


		$_SETTINGS = array(

			array(
			'title' => __( 'NEXTLEVEL Takealot Settings', 'nextlevel-takealot-woocommerce' ),
				'type'  => 'title',
				'id'    => 'nextlevel_takealot_woocommerce_settings',
			),
			array(
				'title'         => __( 'Enable Takealot sync', 'nextlevel-takealot-woocommerce' ),
				'id'            => 'nextlevel_takealot_woocommerce_enabled',
				'default'       => 'no',
				'type'          => 'checkbox'
			),
			array(
				'title'         => __( 'Takealot KEY', 'nextlevel-takealot-woocommerce' ),
				'id'            => 'nextlevel_takealot_woocommerce_key',
				'default'       => '',
				'type'          => 'text'
			),
			array(
				'title'         => __( 'Takealot Endpoint', 'nextlevel-takealot-woocommerce' ),
				'id'            => 'nextlevel_takealot_woocommerce_endpoint',
				'default'       => 'https://seller-api.takealot.com/v2/',
				'type'          => 'text'
			),
			array(
				'title'         => __( 'Takealot Lead Days', 'nextlevel-takealot-woocommerce' ),
				'id'            => 'nextlevel_takealot_woocommerce_lead_days',
				'default'       => 5,
				'type'          => 'number',
				'custom_attributes' => array(
						'min' 	=> 5,
						'max' 	=> 7,
						'step' 	=> 1,
				),
			),
			array(
				'title'         => __( 'Takealot Warehouse', 'nextlevel-takealot-woocommerce' ),
				'id'            => 'nextlevel_takealot_woocommerce_warehouse',
				'default'       => '',
				'type'          => 'number'
			),
			array(
					'title' 	=> __('Time Interval', 'nextlevel-takealot-woocommerce'),
					'type' 		=> 'select',
					'default' 	=> '',
					'id' 		=> 'nextlevel_takealot_woocommerce_interval',
					'default' 	=> 'None',
					'options'   => array(
						'hourly'	=> __( 'Hourly', 'nextlevel-takealot-woocommerce' ),
						'twohours'	=> __( 'Every 2 Hours', 'nextlevel-takealot-woocommerce' ),
						'daily'	=> __( 'Once Daily', 'nextlevel-takealot-woocommerce' ),
						'twicedaily' => __( 'Twice Daily', 'nextlevel-takealot-woocommerce' ),
					)
			),

		);



		$_FILTER = apply_filters(
			'woocommerce_' . $this->id . '_settings',
			$_SETTINGS
		);

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $_FILTER );


	}




	public function save(){

		$_SETTINGS = $this->get_settings();

		$_ERRORS = false;

		if($_POST['nextlevel_takealot_woocommerce_key'] == ''):
			$_POST['nextlevel_takealot_woocommerce_enabled'] = 0;
			WC_Admin_Settings::add_error('Takealot KEY is required');
			$_ERRORS = true;
		endif;

		if($_POST['nextlevel_takealot_woocommerce_endpoint'] == ''):
			$_POST['nextlevel_takealot_woocommerce_enabled'] = 0;
			WC_Admin_Settings::add_error('Takealot Endpoint is required');
			$_ERRORS = true;
		endif;

		if($_POST['nextlevel_takealot_woocommerce_lead_days'] == ''):
			$_POST['nextlevel_takealot_woocommerce_enabled'] = 0;
			WC_Admin_Settings::add_error('Takealot Lead Days is required');
			$_ERRORS = true;
		endif;

		if($_POST['nextlevel_takealot_woocommerce_warehouse'] == ''):
			$_POST['nextlevel_takealot_woocommerce_enabled'] = 0;
			WC_Admin_Settings::add_error('Takealot Warehouse is required');
			$_ERRORS = true;
		endif;


		WC_Admin_Settings::save_fields( $_SETTINGS );

	}



}