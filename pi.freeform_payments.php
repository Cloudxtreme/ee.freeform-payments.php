<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
					
/**
 * Freeform_payments Class
 *
 * @package			Freeform Payments
 * @category		Plugin
 * @author			Kevin Thompson
 * @copyright		Copyright (c) 2010, Kevin Thompson
 * @link			http://kevinthompson.info
 */

$plugin_info = array(
	'pi_name'			=> 'Freeform Payments',
	'pi_version'		=> '1.0',
	'pi_author'			=> 'Kevin Thompson',
	'pi_author_url'		=> 'http://kevinthompson.info/',
	'pi_description'	=> 'Allows the addition of payment fields to freeform.',
	'pi_usage'			=> Freeform_payments::usage()
);

class Freeform_payments {

	function Freeform_payments()
	{
		$this->EE =& get_instance(); 
		
		// Load Language File
		$this->EE->lang->loadfile('freeform_payments');
		
		$fields  = '';
		$fields	.= '<div class="form-row"><label for="ffp_name_on_card">'	. $this->EE->lang->line('ffp_name_on_card')		. '*</label><input type="text" name="ffp_name_on_card" class="required" /></div>';
		$fields	.= '<div class="form-row"><label for="ffp_card_number">'	. $this->EE->lang->line('ffp_card_number')		. '*</label><input type="text" name="ffp_card_number" class="required" /></div>';
		$fields	.= '<div class="form-row"><label for="ffp_expiration">'		. $this->EE->lang->line('ffp_expiration')		. '*</label><input type="text" name="ffp_expiration" class="required" /></div>';
		$fields	.= '<div class="form-row"><label for="ffp_security_code">'	. $this->EE->lang->line('ffp_security_code')	. '*</label><input type="text" name="ffp_security_code" class="required" /></div>';
		$fields	.= ( ! $this->EE->TMPL->fetch_param('amount')) ? '' : '<input type="hidden" name="ffp_amount" value="' . $this->EE->TMPL->fetch_param('amount') . '" />';
		$fields	.= '<input type="hidden" name="ffp_active" value="true" />';
		
		$this->return_data = $fields;
	}


	// --------------------------------------------------------------------
  
	/**
	* Usage
	*
	* Plugin Usage
	*
	* @access  public
	* @return  string
	*/

	function usage()
	{
		ob_start(); 
		?>
		
		<form>
			<label for="email">Email:</label>
			<input type="text" name="email" />
			
			{exp:freeform_payments amount="199.99"}
			
			<input type="submit" />
		</form>
		
		<?php
		$buffer = ob_get_contents();

		ob_end_clean(); 

		return $buffer;
	}

}

/* End of file pi.freeform_payments.php */ 
/* Location: ./system/expressionengine/third_party/freeform_payments/pi.freeform_payments.php */