<?php
class midas_validator
{
	// Define class properties
	protected $form_values = array();
	protected $ERRORS = array();
	protected $validations = array();
	protected $has_errors = false;
	function midas_validator($form_method = 'post')
	{
		$this->form_values = $form_method === 'post' ? $_POST : $_GET;
		$this->ERRORS = array();
		$this->validations = array();
		$this->has_errors = false;
	}
	function add($field, $rule, $message)
	{
		$this->validations[] = array('field' => $field, 'rule' => $rule, 'message' => $message);
	}
	function process()
	{
		foreach ($this->validations as $array) {
			$field = $array['field'];
			if (!isset($this->ERRORS[$field]) || !is_array($this->ERRORS[$field])) {
				$this->ERRORS[$field] = array();
			}
			$value = isset($this->form_values[$field]) ? $this->form_values[$field] : null;
			if ($this->is_invalid($field, $value, $array['rule'])) {
				$this->error($field, $array['message']);
			}
		}
		return $this->ok();
	}
	function is_invalid($field, $value, $rule)
	{
		// Handle null value
		if ($value === null) {
			$value = '';
		}
		$value = trim($value);
		//echo("<br>$field, $value, $rule, $message");
		$tokens = explode('|', $rule);
		$count_tokens = count($tokens);
		$last_token = strtolower($tokens[$count_tokens - 1]);
		if ($last_token == 'opt' || $last_token == 'optional') {
			if ($value == '') {
				return false;
			}
			$check_optional = true;
		}
		switch ($tokens[0]) {
			case 'blank':
				return !$this->check_blank($value);
				break;
			case 'length':
				if (isset($tokens[1]) && isset($tokens[2])) {
					$min_len = $tokens[1];
					$max_len = $tokens[2];
					return !$this->check_length($value, $min_len, $max_len);
				}
				return true;
				break;
			case 'compare_field':
				$token = $tokens[1];
				$compare_field = $tokens[2];
				$value_to_compare = isset($this->form_values[$compare_field]) ? $this->form_values[$compare_field] : '';
				if ($token == '=') {
					if ($value == $value_to_compare) {
						return false;
					}
				} else if ($token == '>') {
					if ($value > $value_to_compare) {
						return false;
					}
				} else if ($token == '<') {
					if ($value < $value_to_compare) {
						return false;
					}
				}
				return true;
				break;
			case 'compare':
				$token = $tokens[1];
				$value_to_compare = $tokens[2];
				if ($token == '=') {
					if ($value == $value_to_compare) {
						return false;
					}
				} else if ($token == '>') {
					if ($value > $value_to_compare) {
						return false;
					}
				} else if ($token == '<') {
					if ($value < $value_to_compare) {
						return false;
					}
				}
				return true;
				break;
			case 'combine':
				$tmp = $tokens[1];
				//$tmp2 = preg_replace('/{.*}/','');
				$new_value = $this->parse_combine($tmp);
				//echo("<br>tmp2: $tmp2");
				$new_rule = implode('|', array_slice($tokens, 2));
				return $this->is_invalid($field, $new_value, $new_rule);
				break;
			case 'group_validate':
				break;
			case 'username':
				return !$this->check_username($value);
				break;
			case 'password':
				return !$this->check_password($value);
				break;
			case 'email':
				return !$this->check_email($value);
				break;
			case 'us_phone':
				return !$this->check_us_phone($value);
				break;
			case 'us_zip':
				return !$this->check_us_zip($value);
				break;
			case 'ip':
				return !$this->check_ip($value);
				break;
			case 'alpha':
				return !$this->check_alpha($value);
				break;
			case 'alpha_numeric':
				return !$this->check_alpha_numeric($value);
				break;
			case 'numeric':
				return !$this->check_numeric($value);
				break;
			case 'float':
				return !$this->check_float($value);
				break;
			case 'url':
				break;
			case 'hex_color':
				return !$this->check_hex_color($value);
				break;
			case 'mysql_date':
				return !$this->check_mysql_date($value);
				break;
			case 'us_date':
				return !$this->check_us_date($value);
				break;
			case 'image':
				break;
			case 'video':
				break;
			case 'document':
				break;
			default:
				//die("Invalid validation rule");
				break;
		}
	}
	function error($field, $error)
	{
		$this->has_errors = true;
		$this->ERRORS[$field][] = $error;
	}
	function ok()
	{
		return !$this->has_errors;
	}
	function get_errors()
	{
		return $this->ERRORS;
	}
	function print_errors()
	{
		foreach ($this->ERRORS as $field => $errors) {
			foreach ($errors as $error) {
				echo '<div class="error_msg">' . $error . '</div>' . "\n";
			}
		}
	}
	function display_errors()
	{
		$var = "";
		foreach ($this->ERRORS as $field => $errors) {
			foreach ($errors as $error) {
				$var .= '<li>' . $error . '</li>' . "\n";
			}
		}
		return $var;
	}
	//---------------------------------
	static function check_blank($value)
	{
		if (strlen(trim($value)) > 0) {
			return true;
		}
	}
	static function check_length($value, $min_len, $max_len)
	{
		$strlen = strlen(trim($value));
		if ($strlen < $min_len) {
			return false;
		} else if ($max_len != '' && $strlen > $max_len) {
			return false;
		}
		return true;
	}
	static function check_username($value)
	{
		if (preg_match('/^[a-z\d_]{4,28}$/i', $value)) {
			//if (preg_match('/^.{4,28}$/i', $value)) {
			return true;
		}
	}
	static function check_password($value)
	{
		//if (preg_match('/^.{4,28}$/i', $value))
		if (preg_match('/^[a-zA-Z0-9]{6,28}$/i', $value)) {
			return true;
		}
	}
	static function check_email($value)
	{
		if (preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $value)) {
			return true;
		}
	}
	static function check_us_phone($value)
	{
		//  032 555 5555
		//echo("<br>value: $value");
		if (preg_match('/^(\(?[2-9]{1}[0-9]{2}\)?|[0-9]{3,3}[-. ]?)[ ][0-9]{3,3}[-. ]?[0-9]{4,4}$/', $value)) {
			//echo("<br>xxxxvalue: $value");
			return true;
		}
	}
	static function check_us_zip($value)
	{
		if (preg_match('/^[0-9]{5,5}([- ]?[0-9]{4,4})?$/', $value)) {
			return true;
		}
	}
	static function check_ip($value)
	{
		if (preg_match('^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$', $value)) {
			return true;
		}
	}
	static function check_us_date($value)
	{
		if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
			return true;
		}
	}
	static function check_mysql_date($value)
	{
		if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
			return true;
		}
	}
	static function check_uk_date($value)
	{
		if (preg_match('', $value)) {
			return true;
		}
	}
	static function check_hex_color($value)
	{
		if (preg_match('/^#(?:(?:[a-f\d]{3}){1,2})$/i', $value)) {
			return true;
		}
	}
	static function check_url($value)
	{
		if (preg_match('', $value)) {
			return true;
		}
	}
	static function check_alpha($value)
	{
		if (preg_match('/^[a-z]+$/i', $value)) {
			return true;
		}
	}
	static function check_alpha_numeric($value)
	{
		if (preg_match('/^[a-z\d]+$/i', $value)) {
			return true;
		}
	}
	static function check_numeric($value)
	{
		if (preg_match('/^[\d]+$/i', $value)) {
			return true;
		}
	}
	static function check_float($value)
	{
		if (preg_match('/^[-+]?[0-9]+(\.[0-9]+)?$/', $value)) {
			return true;
		}
	}
	/*
zip
	'\b[0-9]{5}(?:-[0-9]{4})?\b'
if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
  echo "Valid email address.";
}
*/
	function parse_combine($subject)
	{
		$pattern = '/\{(.*?)\}/';
		preg_match_all($pattern, $subject, $matches);
		//print_r($this->form_values);
		foreach ($matches[1] as $var) {
			//echo("<br>var: $var");
			$subject = str_replace('{' . $var . '}', $this->form_values[$var], $subject);
		}
		return $subject;
	}
}