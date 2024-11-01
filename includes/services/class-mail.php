<?php
/**
 * Mailer class.
 *
 * @link       http://sproutient.com
 * @since      1.0.0
 *
 * @package    zypento
 * @subpackage zypento/includes
 */

namespace Zypento;

/**
 * Mailer class.
 *
 * @since      1.0.0
 * @package    zypento
 * @subpackage zypento/includes
 * @author     Sproutient <dev@sproutient.com>
 */
class Mail {

	/**
	 * Send email.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param String $zyp_email Email.
	 * @param String $zyp_email_subject Subject.
	 * @param String $zyp_email_content Content.
	 */
	public static function send( $zyp_email, $zyp_email_subject, $zyp_email_content ) {

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		wp_mail( $zyp_email, $zyp_email_subject, $zyp_email_content, $headers );

	}

}
