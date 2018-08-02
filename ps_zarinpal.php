<?php

class ps_zarinpal extends ps_payment_gateway {

	public $merchant_id;

	public function __construct() {
		self::load_nusoap();
	}

	public function send( $callback, $price, $username, $email, $order_id ) {
		$url                      = 'https://www.zarinpal.com/pg/services/WebGate/wsdl';
		$client                   = new nusoap_client( $url, 'wsdl' );
		$client->soap_defencoding = 'UTF-8';

		$result = $client->call( 'PaymentRequest', array(
			'MerchantID'  => $this->merchant_id,
			'Amount'      => $price,
			'Description' => 'خرید از سایت با استفاده از افزونه فروش پست',
			'CallbackURL' => $callback,
			'Email'       => $email
		) );

		if ( $result['Status'] == 100 ) {
			$this->insert_payment( $username, $price, $order_id, $email );
			echo $this->info_alert( 'در حال اتصال به درگاه ...' );
			$url = 'https://www.zarinpal.com/pg/StartPay/' . $result['Authority'];
			$this->redirect( $url );
		} else {
			echo $this->danger_alert( 'خطا در متصل شدن به درگاه ! :' . $result['Status'] );
		}
	}

	public function verify( $price, $post_id, $order_id, $course_id = 0 ) {
		if ( isset( $_GET['Authority'] ) ) {
			if ( $_GET['Status'] == 'OK' ) {
				$Authority                = $_GET['Authority'];
				$url                      = 'https://www.zarinpal.com/pg/services/WebGate/wsdl';
				$client                   = new nusoap_client( $url, 'wsdl' );
				$client->soap_defencoding = 'UTF-8';
				$result                   = $client->call( 'PaymentVerification', array(
					'MerchantID' => $this->merchant_id,
					'Authority'  => $Authority,
					'Amount'     => $price
				) );

				if ( $result['Status'] == 100 ) {
					$this->success_payment( $result['RefID'], $order_id, $price, $post_id, $course_id );
				} else {
					echo $this->danger_alert( 'خطا در پردازش عملیات پرداخت ، نتیجه پرداخت : ' . $result['Status'] );
				}
			} else {
				echo $this->danger_alert( 'پرداخت ناموفق!' );
			}
			$this->end_payment();
		}
	}
}