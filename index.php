<?php
/*
Plugin Name: پرداخت زرین پال - فروش پست ها
Version: 1.0
Description:  درگاه پرداخت واسط زرین پال برای افزونه فروش پست ها post shop
Plugin URI: http://behnam-rasouli.ir/p/post-shop/
Author: بهنام رسولی
Author URI: http://behnam-rasouli.ir/
License: GPL3
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function ps_load_zarinpal_payment() {
	function ps_add_zarinpal_payment( $list ) {
		$list['zarinpal'] = array(
			'name'       => 'زرین پال',
			'class_name' => 'ps_zarinpal',
			'settings'   => array(
				'merchant_id' => array( 'name' => 'کد دروازه پرداخت زرین پال' )
			)
		);

		return $list;
	}

	function ps_load_zarinpal_class() {
		include_once plugin_dir_path( __FILE__ ) . '/ps_zarinpal.php';
	}

	if ( class_exists( 'ps_payment_gateway' ) && ! class_exists('ps_sms_newsms') ) {
		add_filter( 'ps_payment_list', 'ps_add_zarinpal_payment' );
		add_action( 'ps_load_payment_class', 'ps_load_zarinpal_class' );
	}
}

add_action( 'plugins_loaded', 'ps_load_zarinpal_payment', 0 );


add_action( 'admin_notices', 'ps_zarinpal_check_requirement' );

function ps_zarinpal_check_requirement() {
	if ( current_user_can( 'activate_plugins' ) ) {
		if ( ! class_exists( 'ps_payment_gateway' ) ) {
			echo '<div class="notice notice-warning is-dismissible">';
			echo 'برای استفاده از این درگاه پرداخت نیاز به افزونه فروش پست ها است،لطفا این پلاگین رو خریداری کنید و نصب فعال کنید.';
			echo '<br><a href="http://behnam-rasouli.ir/p/post-shop?source=pay_plugin">اطلاعات بیشتر ...</a>';
			echo '</div>';
		} elseif ( version_compare( PS_VERSION, '5.5.0', '<' ) ) {
			echo '<div class="notice notice-warning is-dismissible">';
			echo 'برای استفاده از این پلاگین ورژن افزونه فروش پست ها باید حداقل 5.5 باشد!';
			echo '<br><a href="http://behnam-rasouli.ir/p/post-shop?source=pay_plugin">اطلاعات بیشتر ...</a>';
			echo '</div>';
		}
	}
}