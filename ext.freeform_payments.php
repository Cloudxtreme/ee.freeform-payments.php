<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Draggable
 *
 * This extension works in conjunction with its accessory to add draggable sorting to additional areas of the control panel.
 *
 * @package   Freeform Payments
 * @author    Kevin Thompson <thompson.kevind@gmail.com>
 * @link      http://kevinthompson.info
 * @copyright Copyright (c) 2010 Kevin Thompson
 * @license   http://creativecommons.org/licenses/by-sa/3.0/   Attribution-Share Alike 3.0 Unported
 */

class Freeform_payments_ext
{
	var $settings        = array();
	var $name            = 'Freeform Payments';
	var $version         = '0.1';
	var $description     = 'Accept payments through Freeform forms';
	var $settings_exist  = 'y';
	var $docs_url		 = '';


	function Freeform_payments_ext($settings='')
	{
	    $this->settings = $settings;
	    $this->EE =& get_instance();
		
		// Load Language File
		$this->EE->lang->loadfile('freeform_payments');
	}
	
	
	// --------------------------------
	//  Settings
	// --------------------------------  

	function settings()
	{	
		$settings = array();
		
		$settings['ffp_gateway']		= array('s', array('authnet' => $this->EE->lang->line('ffp_authnet'), 'paypal' => $this->EE->lang->line('ffp_paypal')), 'paypal');
		$settings['ffp_login_id']		= '';
		$settings['ffp_transaction'] 	= '';
	
		return $settings;
	}
	
	
	// --------------------------------
	//  Validate Payment Fields
	// --------------------------------  
	
	function validate_payment_fields($errors)
	{
		$is_active	= $this->EE->input->post('ffp_active');
		
		if($is_active == 'true')
		{
			$fields['ffp_name_on_card']		= ( ! $this->EE->input->post('ffp_name_on_card'))	? '' : $this->EE->input->post('ffp_name_on_card');
			$fields['ffp_card_number']		= ( ! $this->EE->input->post('ffp_card_number')) 	? '' : $this->EE->input->post('ffp_card_number');
			$fields['ffp_expiration'] 		= ( ! $this->EE->input->post('ffp_expiration')) 	? '' : $this->EE->input->post('ffp_expiration');
			$fields['ffp_security_code'] 	= ( ! $this->EE->input->post('ffp_security_code')) 	? '' : $this->EE->input->post('ffp_security_code');
			$fields['ffp_amount']			= ( ! $this->EE->input->post('ffp_amount')) 		? '' : $this->EE->input->post('ffp_amount');
		
			if($fields['ffp_amount'] == '')
			{
				$errors[]	=	$this->EE->lang->line('ffp_error_amount');
			}
		
			foreach($fields as $field_name => $value):
				if($value == ''):
					$errors[] = 'The following field is required: ' . $this->EE->lang->line($field_name);
				endif;
			endforeach;
		
			if(empty($errors)){
				return $this->_process_payment($fields,$errors);
			}	
		}
		
		return $errors;
	}
	
	
	// --------------------------------
	//  Process Payment
	// --------------------------------  
	
	function _process_payment($fields,$errors)
	{
		require_once PATH_THIRD . 'freeform_payments/includes/anet_php_sdk/AuthorizeNet.php';
		
		$transaction = new AuthorizeNetAIM($this->settings['ffp_login_id'], $this->settings['ffp_transaction']);
		$transaction->amount 	= $fields['ffp_amount'];
		$transaction->card_num 	= $fields['ffp_card_number'];
		$transaction->exp_date 	= $fields['ffp_expiration'];
		$transaction->card_code	= $fields['ffp_security_code'];

		$response = $transaction->authorizeAndCapture();

		if ( !$response->approved )
		{
			$errors[] = $response->response_reason_text;
		}
		
		return $errors;
	}
	
	
	// --------------------------------
	//  Activate Extension
	// --------------------------------  
		
	function activate_extension()
	{
		$this->settings = array(
			'ffp_gateway'		=> 'authnet',
		);
		
		$default = array(
			'extension_id'	=> '',
	        'class'			=> ucfirst(get_class($this)),
	        'settings'		=> serialize($this->settings),
	        'priority'		=> 10,
	        'version'		=> $this->version,
	        'enabled'		=> "y"
		);
		
		$hooks = array(
			array(
		        'method'	=> 'validate_payment_fields',
		        'hook'		=> 'freeform_module_validate_end'
			)
		);

		foreach($hooks as $hook):
			$hook = array_merge($hook,$default);
			$this->EE->db->insert('extensions',$hook);
		endforeach;
	}


	// --------------------------------
	//  Update Extension
	// --------------------------------  

	function update_extension($current='')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	    
		$this->EE->db->query("UPDATE exp_extensions 
	     	SET version = '". $this->EE->db->escape_str($this->version)."'
	     	WHERE class = '".ucfirst(get_class($this))."'");
	}
	
	
	// --------------------------------
	//  Disable Extension
	// --------------------------------  
	
	function disable_extension()
	{	    
		$this->EE->db->query("DELETE FROM exp_extensions WHERE class = '".ucfirst(get_class($this))."'");
	}

}

/* End of file ext.freeform_payments.php */
/* Location: ./system/expressionengine/third_party/freeform_payments/ext.freeform_payments.php */