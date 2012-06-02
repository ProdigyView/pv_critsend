<?php

class Critsend extends PVStaticInstance {
	
	protected static $_configuration = array(
		'user' => CRITSEND_API_USER,
		'password' => CRITSEND_API_PASSWORD,
		'fast' => false,
		'internal' => false,
		'method' => 'soap'	
	);
	
	
	/**
	 * Initialize that class and can ovveride the current defeault values.
	 * 
	 * @param array $config Configuration arguments that be ovverided
	 * 			-'user' _string_: The username used to access Critsend, default is the constant CRIT_API_USER
	 * 			-'password' _string_: The password used to access Critsend, default the contstant CRIT_API_PASSWORD
	 * 			-'fast' _boolean_: A Critsend variable, default is false
	 * 			-'internal' _boolean_: A Critsend variable, default is false
	 * 			-'method' _string_: The method used to send emails using credit. Default is 'soap', other option is smtp
	 * 
	 * @return voic
	 * @access public
	 */
	public function init(array $config = array()) {
		
		$defaults = array(
			'user' => CRITSEND_API_USER,
			'password' => CRITSEND_API_PASSWORD,
			'fast' => false,
			'internal' => false,
			'method' => 'soap'	
		);
		
		$config += $defaults;
		
		self::$_configuration = $config;
	}
	
	/**
	 * Wraps Critsends MxmcConneect class and sends an email to a specified address.
	 * 
	 * @param array $args An array of arguements that is used to define how the email will be sent
	 * 			-'subject' _string_: The subject of the email to go in the subject line
	 * 			-'html_message' _string_: The message sent to an email that contains html tags
	 * 			-'text_message' _string_: The message sent to an email that contains no html tags
	 * 			-'sender' _string_: The email address that will be sending the email.
	 * 			-'receiver' _string_: The email address that will be receiving the email
	 * 			-'reply_to' _string_: The designated email that a user will reply too
	 * 			-'tags' _array_: Add tags to the email to be sent ex: array('invitation', 'request', 'july')
	 * 
	 * @return boolean Returns true for success
	 * @access public	
	 */
	public static function sendEmail($args) {
		
		if(self::$_configuration['method'] == 'soap') {
			$result = self::sendSOAP($args);
		} else if(self::$_configuration['method'] == 'smtp') {
			$result = self::sendSMTP($args);
		}
		
		self::_notify('Critsend::sendEmail', $args, $result);
		
		return $result;
	}
	
	/**
	 * Sends an email use MxmConnect SOAP client. Make sure php soap module is installed.
	 * 
	 * @param array $args Same args passe into send mail
	 * 
	 * @return boolean
	 * @access public
	 */
	public static function sendSMTP($args) {
		
		$args = self::_setDefaultArgs($args);
		
		$content = array(
			'subject' => $args['subject'],
			'html' => $args['html_message'],
			'text' => $args['text_message']
		);
		
		$options = array(
			'tag'=> $args['tags'], 
			'mailfrom'=> $args['sender'],
	 		'mailfrom_friendly'=> $args['mailfrom_friendly'], 
	 		'replyto'=>$args['reply_to'], 
	 		'replyto_filtered'=> true
		);
		
		$emails = array(
			array('email' => $args['receiver'])
		);
		
		$connect = new MxmConnect(self::$_configuration['user'], self::$_configuration['password']);
		
		return $connect -> sendEmail($content, $options , $emails);
	}
	
	/**
	 * Sends an email use MxmConnect SOAP client. Make sure php soap module is installed.
	 * 
	 * @param array $args Same args passe into send mail
	 * 
	 * @return boolean
	 * @access protected
	 */
	public static function sendSOAP($args) {
		
		$args = self::_setDefaultArgs($args);
			
		$content = array(
			'subject' => $args['subject'],
			'html' => $args['html_message'],
			'text' => $args['text_message']
		);
		
		$options = array(
			'tag'=> $args['tags'], 
			'mailfrom'=> $args['sender'],
	 		'mailfrom_friendly'=> $args['mailfrom_friendly'], 
	 		'replyto'=>$args['reply_to'], 
	 		'replyto_filtered'=> true
		);
		
		$emails = array(
			array('email' => $args['receiver'])
		);
		
		$connect = new MxmConnect(self::$_configuration['user'], self::$_configuration['password']);
		$result = $connect -> sendCampaign($content, $options , $emails);
	
		return $result;
	}
	
	/**
	 * Sets default values for the arguements associated with an email.
	 * 
	 * @param array $args An array of arguments to be send in an email
	 * 
	 * @return array $args Returns the args with new default values
	 * @access public
	 */
	protected static function _setDefaultArgs($args) {
		$defaults=array(
			'receiver'=>'',
			'carboncopy'=>'',
			'blindcopy'=>'',
			'reply_to'=>'',
			'attachment'=>'',
			'attachment_name'=>'',
			'message'=>'',
			'html_message'=>'',
			'text_message'=>'',
			'errors_to'=>'',
			'return_path'=>'',
			'message_id'=>'',
			'eol'=>"\r\n",
			'tags' => array(self::_generateMessageID()),
			'mailfrom_friendly' => $_SERVER['SERVER_NAME']
		);
		
		$args += $defaults;
		
		return $args;
	}
	
	/**
	 * Generates a unique ID and sends it as a tag. The id generated is used to later track individual emails.
	 * 
	 * @return void
	 * @access protected
	 */
	protected static function _generateMessageID() {
		return 'message-'.uniqid(true);
	}
	
}
