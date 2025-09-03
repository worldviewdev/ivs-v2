<?php
require_once 'brevo/vendor/autoload.php';
require_once 'phpmailer/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
// Brevo API
use Brevo\Client\Configuration;
use Brevo\Client\Api\TransactionalEmailsApi;
use Brevo\Client\Model\SendSmtpEmail;
use GuzzleHttp\Client;
function safe_number_format($number, $decimals = 0, $decimal_separator = '.', $thousands_separator = ',')
{
	if ($number === null) {
		$number = 0;
	}
	return number_format($number, $decimals, $decimal_separator, $thousands_separator);
}
function get_remote_file($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	if (LOCAL_MODE) {
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
		curl_setopt($ch, CURLOPT_PROXY, "http://192.168.8.111:4480");
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	$response = curl_exec($ch);
	return $response;
}
function  get_remote_file_with_headers($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, TRUE);
	curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
	curl_setopt($ch, CURLOPT_PROXY, "http://192.168.8.111:4480");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	$response = curl_exec($ch);
	return $response;
}
function db_result($sql, $dbcon1 = null, $dbcon2 = null)
{
	if (empty($dbcon1)) {
		if (!isset($GLOBALS['dbcon'])) {
			connect_db(); // pastikan connect_db() menggunakan mysqli
		}
		$dbcon1 = $GLOBALS['dbcon'];
	}
	// pastikan db_query() menggunakan mysqli_query()
	$result = db_query($sql, $dbcon1);
	// Check if query was successful before calling mysqli_fetch_array
	if ($result === false) {
		// Log error or handle failed query
		error_log("Database query failed: " . $sql);
		return false;
	}
	// gunakan mysqli_fetch_array atau mysqli_fetch_assoc
	$line = mysqli_fetch_array($result, MYSQLI_ASSOC); // atau MYSQLI_BOTH
	return $line;
}
function validate_form()
{
	return ' onsubmit="return validateForm(this,0,0,0,1,8);" ';
}
function protect_page()
{
	if ($_SESSION['sess_agent_id'] == "") {
		header("Location: " . SITE_WS_PATH . "/index.php");
		exit;
	}
}
function status_dropdown($name, $sel_value, $extra = '', $choose_one = '')
{
	$arr = array("Active" => 'Active', 'Inactive' => 'Inactive');
	return array_dropdown($arr, $sel_value, $name, $extra = '', $choose_one = '');
}
function yes_no_dropdown($name, $sel_value, $extra = '', $choose_one = '')
{
	$arr = array("Yes" => 'Yes', 'No' => 'No');
	return array_dropdown($arr, $sel_value, $name, $extra = '', $choose_one = '');
}
function dynamicstate_country_dropdown($combo_name, $sel_value = '', $extra = '', $choose_one = '')
{
	$sql = "select countries_iso_code_2,countries_name from tbl_countries order by countries_order,countries_name";
	return make_dropdown($sql, $combo_name, $extra, $choose_one, $sel_value);
}
function country_dropdown($combo_name, $sel_value = '', $extra = '', $choose_one = '')
{
	$sql = "select countries_name,countries_name from tbl_countries order by countries_order";
	return make_dropdown($sql, $sel_value, $combo_name, $extra, $choose_one);
}
function state_dropdown($country_code, $combo_name, $sel_value = '', $extra = '', $choose_one = '')
{
	$sql = "select state_name, state_name from tbl_states where state_country_code = '$country_code' order by state_name";
	return make_dropdown($sql, $combo_name, $extra, $choose_one, $sel_value);
}
function get_country_code($country_name)
{
	return db_scalar("select countries_iso_code_2 from tbl_countries where countries_name='$country_name'");
}
function display_session_message()
{
	$var = "";
	if ($_SESSION['sess_msg'] != "") {
		$cls = ($_SESSION['sess_msg_class'] == '' ? 'error_msg' : $_SESSION['sess_msg_class']);
		$var = '<table cellspacing="0" cellpadding="0" width="100%" border="0" class="' . $cls . '"><tr><td valign="top" align="center" style="padding-top: 5px;padding-bottom: 5px;" width="60">';
		if ($cls == "error_msg") {
			$img = SITE_WS_PATH . '/images/icons/error.gif';
		} else {
			$img = SITE_WS_PATH . '/images/icons/sucess.gif';
		}
		$var .= '<img src="' . $img . '" border="0" alt="" /></td><td align="left"><ul>' . $_SESSION['sess_msg'] . '</ul></td></tr></table><br />';
		$_SESSION['sess_msg'] = '';
		$_SESSION['sess_msg_class'] = '';
	}
	return $var;
}
function count_records($tbl_name)
{
	return db_scalar("select count(*) from $tbl_name where 1");
}
function send_mail($email_name, $dynamic_values, $to_email, $from_email = '', $from_name = '', $html = false, $nltobr = false, $attachment = '')
{
	$row_mail_val = mysqli_fetch_assoc(db_query("select * from tbl_emails where email_name = '$email_name'"));
	$subject = $row_mail_val['email_subject'];
	if ($from_name == "") {
		$from_name = $row_mail_val['email_from_name'];
	}
	if ($from_email == "") {
		$from_email = $row_mail_val['email_from_emailid'];
	}
	if (is_array($dynamic_values)) {
		$search = array_keys($dynamic_values);
		$replace = array_values($dynamic_values);
	}
	$message = str_replace($search, $replace, $row_mail_val['email_message']);
	if ($nltobr == true) {
		#$message = nl2br($message);
	}
	$mail_message = mail_header();
	$mail_message .= $message;
	$mail_message .= mail_footer();
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug = 0;
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587;
	$mail->Username = "info@italyvacationspecialists.com";
	$mail->Password = "htzl oftv orjf pxwv";
	$mail->From = $from_email;
	$mail->FromName = $from_name;
	$mail->AddAddress($to_email);
	$mail->AddReplyTo($mail->From, $mail->FromName);
	if ($attachment != '') {
		$mail->AddAttachment($attachment);
	}
	$mail->Subject = $subject;
	$mail->isHTML(true);
	$body = $mail_message;
	$mail->MsgHTML($body);
	$return = $mail->Send();
	/*
	$headers = 'MIME-Version: 1.0' . "\r\n";
    if ($html == true) {
        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
    } else {
        $headers .= "Content/type: text/plain; charset=iso-8859-1\r\n";
    }
    $headers .= "From: $from_email\r\n";
    @mail($to_email, $subject, $mail_message, $headers);
	*/
}
function send_mail2($from_name, $from_emailid, $to_emailid, $subject, $msg, $attachment = '')
{
	/*
	$headers="MIME-Version: 1.0 \n";
	$headers.="Content-Type: text/html; charset=iso-8859-1\n";
	$headers .= "From: ".$from_name." <".$from_emailid. "> \n";
	$to_emailid = trim($to_emailid);
	@mail($to_emailid, $subject, $msg, $headers, "-f sender@website.com");
	*/
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug = 0;
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587;
	$mail->Username = "info@italyvacationspecialists.com";
	$mail->Password = "htzl oftv orjf pxwv";
	$mail->From = $from_emailid;
	$mail->FromName = $from_name;
	$mail->AddAddress($to_emailid);
	$mail->AddReplyTo($mail->From, $mail->FromName);
	if ($attachment != '') {
		$mail->AddAttachment($attachment);
	}
	$mail->Subject = $subject;
	$mail->isHTML(true);
	$body = $msg;
	$mail->MsgHTML($body);
	$return = $mail->Send();
	return $return;
}
function send_mail3($from_name, $from_emailid, $to_emailid, $subject, $msg, $cc = '', $bcc = '', $attachment = '')
{
	// $mail = new PHPMailer();
	// $mail->IsSMTP();
	// $mail->SMTPDebug = 0;
	// $mail->SMTPAuth = true;
	// $mail->SMTPSecure = 'tls';
	// //	$mail->Host = "mail.italyvacationspecialists.com";
	// //	$mail->Port = 25;
	// //	$mail->Username = "email_authorization@italyvacationspecialists.com";
	// //	$mail->Password = "Glowb@l23433";
	// $mail->Host = "smtp.gmail.com";
	// $mail->Port = 587;
	// $mail->Username = "info@italyvacationspecialists.com";
	// $mail->Password = "htzl oftv orjf pxwv";
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug = 0;
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	$mail->Host = "smtp.gmail.com";
	$mail->Port = 587;
	$mail->Username = "info@italyvacationspecialists.com";
	$mail->Password = "htzl oftv orjf pxwv";
	$mail->From = $from_emailid;
	$mail->FromName = $from_name;
	$mail->AddAddress($to_emailid);
	$mail->AddReplyTo($mail->From, $mail->FromName);
	if (!empty($cc)) {
		foreach ($cc as $email => $name) {
			$mail->AddCC($email, $name);
		}
	}
	if (!empty($bcc)) {
		foreach ($bcc as $email => $name) {
			$mail->AddBCC($email, $name);
		}
	}
	if ($attachment != '') {
		$mail->AddAttachment($attachment);
	}
	$mail->Subject = $subject;
	$mail->isHTML(true);
	$body = $msg;
	$mail->MsgHTML($body);
	$return = $mail->Send();
}
function mail_header()
{
	$var = '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
	  <tr>
		<td align="left" valign="top" style="padding:15px;border:2px solid #F3F2F2;font-family:Verdana, Geneva, sans-serif;font-size:12px; color:#565656;">
		 <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
		  <tr>
			<td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="50%"><img src="http://www.italyvacationspecialists.com/images/logo.png" border="0" alt="" /></td>
				<td width="50%" align="left" valign="middle" style="line-height:20px; padding-left: 10px;">
				  <strong><span style="color:#000;">Email:</span></strong> <strong><a href="mailto:info@italyvacationspecialists.com" style="color:#cf3610;text-decoration:none;outline:none;">info@italyvacationspecialists.com</a></strong></td>
			  </tr>
			</table></td>
		  </tr>
		  <tr>
			<td height="20" align="left" valign="top" style="border-bottom:solid 1px #000;"><img src="' . SITE_WS_PATH . '/images/spacer.gif" width="1" height="1" alt="" /></td>
		  </tr>
		  <tr>
			<td align="left" valign="top" style="line-height:18px; padding-top:15px;">';
	return $var;
}
function mail_footer()
{
	$var = '</td>
				  </tr>
				</table>
				</td>
			  </tr>
			</table>
			</body>
			</html>';
	return $var;
}
function send_template_mail($subject, $message, $to_email, $from_email, $from_name, $html = true, $nltobr = false, $header = "D", $attachment = '')
{
	// Inisialisasi log status
	$log_data = array(
		'timestamp' => date('Y-m-d H:i:s'),
		'to_email' => $to_email,
		'subject' => $subject,
		'from_email' => $from_email,
		'from_name' => $from_name
	);
	$mail_message = "";
	if ($header == "D") {
		$mail_message = mail_header();
	}
	$mail_message .= $message;
	if ($header == "D") {
		$mail_message .= mail_footer();
	}
	try {
		//require_once('phpmailer/class.phpmailer.php');
		$mail = new PHPMailer();
		$mail->isSMTP();
		// $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Disabled to prevent debug output in production
		$mail->Host = 'smtp.gmail.com';
		$mail->Port = 587;
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$mail->SMTPAuth = true;
		$mail->Username = 'info@italyvacationspecialists.com';
		$mail->Password = 'htzl oftv orjf pxwv';
		$mail->SetFrom($from_email, $from_name);
		$mail->AddAddress($to_email);
		$mail->AddReplyTo($from_email, $from_name);
		if ($attachment != '') {
			$mail->AddAttachment($attachment);
			$log_data['attachment'] = $attachment;
		}
		$mail->Subject = $subject;
		$mail->IsHTML($html);
		$mail->Body = $mail_message;
		$send_status = $mail->Send();
		// Log hasil pengiriman
		$log_data['status'] = $send_status ? 'SUCCESS' : 'FAILED';
		$log_data['error'] = $mail->ErrorInfo;
		// Tulis ke file log
		$log_entry = json_encode($log_data) . "\n";
		$log_file = dirname(__FILE__) . '/logs/email_logs_' . date('Y-m-d') . '.log';
		// Buat direktori logs jika belum ada
		if (!file_exists(dirname($log_file))) {
			mkdir(dirname($log_file), 0777, true);
		}
		file_put_contents($log_file, $log_entry, FILE_APPEND);
		return $send_status;
	} catch (Exception $e) {
		// Log error jika terjadi exception
		$log_data['status'] = 'ERROR';
		$log_data['error'] = $e->getMessage();
		$log_entry = json_encode($log_data) . "\n";
		$log_file = dirname(__FILE__) . '/logs/email_logs_' . date('Y-m-d') . '.log';
		if (!file_exists(dirname($log_file))) {
			mkdir(dirname($log_file), 0777, true);
		}
		file_put_contents($log_file, $log_entry, FILE_APPEND);
		return true;
	}
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->Host = "smtp-relay.brevo.com";
	$mail->Port       = 587;
	$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
	//$mail->Username = "email_authorization@italyvacationspecialists.com";
	//$mail->Password = "Glowb@l23433";
	$mail->Username = "8f0aa4001@smtp-brevo.com";
	$mail->Password = "tBdDFP7WqIvLJVYy";
	$mail->setFrom('yeris@italyvacationspecialists.com', 'Bug Report');
	$mail->addAddress('devteam@italyvacationspecialists.com', 'Yeris Bahtiar');
	$mail->AddReplyTo($mail->From, $mail->FromName);
	if ($attachment != '') {
		$mail->AddAttachment($attachment);
	}
	$mail->Subject = $subject;
	$mail->IsHTML(true);
	$body = $mail_message;
	$mail->MsgHTML($body);
	$return = $mail->Send();
	if ($return) {
		$send_status = "Send";
	} else {
		$send_status = "Not Send";
	}
	$page_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	db_query("insert into tbl_email_log set to_mail='" . addslashes($to_email) . "',from_email='" . addslashes($from_email) . "',from_name='" . addslashes($from_name) . "',subject='" . addslashes($subject) . "',message='" . addslashes($message) . "',send_date=now(),send_status='$send_status',page_url='" . $page_url . "'");
	return $return;
}
function send_brevo_transactional_email(
	string $from_email,
	string $from_name,
	string $to_email,
	string $to_name,
	string $subject,
	string $htmlContent,
	string $reply_to_email = '',
	string $reply_to_name = '',
	array $params = []
) {
	try {
		// Konfigurasi API Key Brevo
		$config = Configuration::getDefaultConfiguration()
			->setApiKey('api-key', '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'); // Ganti API Key disini
		$apiInstance = new TransactionalEmailsApi(new Client(), $config);
		// Siapkan data email
		$sendSmtpEmail = new SendSmtpEmail([
			'subject' => $subject,
			'sender' => ['name' => $from_name, 'email' => $from_email],
			'to' => [['name' => $to_name, 'email' => $to_email]],
			'htmlContent' => $htmlContent,
			'params' => $params
		]);
		if (!empty($reply_to_email)) {
			$sendSmtpEmail->setReplyTo([
				'name' => $reply_to_name ?: $from_name,
				'email' => $reply_to_email
			]);
		}
		// Kirim email
		$result = $apiInstance->sendTransacEmail($sendSmtpEmail);
		return $result; // bisa print_r untuk debug
	} catch (Exception $e) {
		return '❌ Exception: ' . $e->getMessage();
	}
}
function sendEmailViaBrevo(
	string $fromName,
	string $fromEmail,
	string $toName,
	string $toEmail,
	string $subject,
	string $bodyMessage
): void {
	// Setup API key
	$config = Brevo\Client\Configuration::getDefaultConfiguration()
		->setApiKey('api-key', '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');
	// Instance Email API
	$apiInstance = new Brevo\Client\Api\TransactionalEmailsApi(
		new GuzzleHttp\Client(),
		$config
	);
	// Setup Email Content
	$sendSmtpEmail = new \Brevo\Client\Model\SendSmtpEmail([
		'subject' => $subject,
		'sender' => ['name' => $fromName, 'email' => $fromEmail],
		'replyTo' => ['name' => $fromName, 'email' => $fromEmail],
		'to' => [['name' => $toName, 'email' => $toEmail]],
		'htmlContent' => '<html><body><h1>This is a IVSBug Report System: {{params.bodyMessage}}</h1></body></html>',
		'params' => ['bodyMessage' => $bodyMessage]
	]);
	// Send Email
	try {
		$result = $apiInstance->sendTransacEmail($sendSmtpEmail);
		print_r($result);
	} catch (Exception $e) {
		echo 'Error sending email: ', $e->getMessage(), PHP_EOL;
	}
}
function send_newsletter_mail($subject, $message, $to_email, $from_email, $from_name, $html = true, $nltobr = false, $header = "D")
{
	$mail_message = "";
	if ($header == "D") {
		$mail_message = mail_header();
	}
	$mail_message .= $message;
	if ($header == "D") {
		$mail_message .= mail_footer();
	}
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug = 1;
	$mail->SMTPAuth = true;
	$mail->SMTPSecure = "ssl";
	$mail->Host = "mail.medvisits.com";
	$mail->Port = 465;
	$mail->Username = "editor@medvisits.com";
	$mail->Password = "Welcome123";
	$mail->From = $from_email;
	$mail->FromName = $from_name;
	$mail->AddAddress($to_email);
	$mail->AddReplyTo($mail->From, $mail->FromName);
	$mail->Subject = $subject;
	$mail->IsHTML(true);
	$body = $mail_message;
	$mail->MsgHTML($body);
	$return = $mail->Send();
	if ($return) {
		$send_status = "Send";
	} else {
		$send_status = "Not Send";
	}
	$page_url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	db_query("insert into tbl_email_log set to_mail='" . addslashes($to_email) . "',from_email='" . addslashes($from_email) . "',from_name='" . addslashes($from_name) . "',subject='" . addslashes($subject) . "',message='" . addslashes($message) . "',send_date=now(),send_status='$send_status',page_url='" . $page_url . "'");
	return $return;
}
function get_page_href($page_id)
{
	return $_SERVER['PHP_SELF'] . "?page_id=$page_id";
	$page_title = db_scalar("select page_title from tbl_static_pages where page_id = '$page_id'");
	$page_title = strtolower($page_title);
	return preg_replace(array('/ /'), array('_'), $page_title) . ".php";
}
function page_title_to_href($page_title)
{
	return preg_replace("/[^A-Za-z0-9]/", "_", $page_title);
}
function page_title_from_href($page_title)
{
	return preg_replace(array('/_/'), array(' '), $page_title);
}
function get_pages_tree()
{
	global $ARR_PAGES;
	if (is_array($ARR_PAGES)) {
		return $ARR_PAGES;
	}
	$sql = "select *  from tbl_static_pages where page_parent_id=0 order by page_display_order ";
	$result = db_query($sql);
	while ($line_raw = mysqli_fetch_array($result)) {
		$line = ms_display_value($line_raw);
		@extract($line);
		$ARR_PAGES[$page_id] = $page_title;
		$sql2 = "select *  from tbl_static_pages where page_parent_id='$page_id' order by page_display_order ";
		$result2 = db_query($sql2);
		while ($line_raw2 = mysqli_fetch_array($result2)) {
			$line2 = ms_display_value($line_raw2);
			@extract($line2);
			$ARR_PAGES[$page_id] = $page_title;
		}
	}
	return $ARR_PAGES;
}
function get_menu($page_parent_id = 0, $level = 0)
{
	$level++;
	$str = '';
	$sql = "select *  from tbl_static_pages where page_parent_id='$page_parent_id' and page_status='Active' order by page_display_order ";
	$result = db_query($sql);
	if (mysqli_num_rows($result)) {
		$str .= $level == 1 ? '<ul id="qm0" class="qmmc">' : '<ul>';
		while ($line_raw = mysqli_fetch_array($result)) {
			$line = ms_display_value($line_raw);
			@extract($line);
			if ($page_id == 1) {
				$str .= '<li><a href="index.php">' . $page_title . '</a>';
			} else {
				$str .= '<li><a href="' . page_title_to_href($page_title) . '.php">' . $page_title . '</a>';
			}
			$str .= get_menu($page_id, $level);
			$str .= '</li>';
		}
		$str .= $level == 1 ? '	<li class="qmclear">&nbsp;</li></ul><script type="text/javascript">qm_create(0,true,0,500,false,false,false,false);</script><ul id="qm0" class="qmmc">' : '</ul>';
	}
	return $str;
}
function pages_drop_down($name, $sel_value, $skip, $extra = '', $choose_one = '', $page_parent_id = 0, $level = 0)
{
	$level++;
	$sql = "select page_id, page_title from tbl_static_pages where page_parent_id='$page_parent_id' and page_type!='Office' and page_id!='$skip' order by page_display_order ";
	#echo $sql."<br /><br />";
	$result = db_query($sql);
	if (mysqli_num_rows($result)) {
		if ($level == 1) {
			$str_dropdown .= "<select name='$name' class='show_page' id='$name' $extra >";
			if ($choose_one != '') {
				$str_dropdown .= "<option value=\"\">$choose_one</option>";
			}
		}
		while ($line_raw = mysqli_fetch_array($result)) {
			$line = ms_display_value($line_raw);
			@extract($line);
			$str_dropdown .= '<option value="' . $line[0] . '"';
			if ($sel_value == $line[0]) {
				$str_dropdown .= "	selected ";
			}
			$str_dropdown .= ">";
			for ($i = 2; $i <= $level; $i++) {
				if ($i == $level) {
					$str_dropdown .= " &nbsp;&nbsp;&raquo; ";
				} else {
					$str_dropdown .= " &nbsp;&nbsp;&nbsp; ";
				}
			}
			$str_dropdown .= "$line[1]</option>";
			$str_dropdown .= pages_drop_down($name, $sel_value, $skip, $extra, $choose_one, $page_id, $level);
		}
		$str_dropdown .= $level == 1 ? '</select>' : '';
	}
	return $str_dropdown;
}
function parent_page_drop_down($name, $sel_value, $extra = '', $choose_one = '')
{
	$sql = "select page_id, page_title from tbl_static_pages where page_parent_id=0 order by page_display_order ";
	return make_dropdown($sql, $sel_value, $name, $extra, $choose_one);
}
function set_page_display_order_all($page_parent_id = 0, $level = 0)
{
	$level++;
	$i = 1;
	$sql = "select *  from tbl_static_pages where page_parent_id='$page_parent_id' order by page_display_order ";
	$result = db_query($sql);
	if (mysqli_num_rows($result)) {
		while ($line_raw = mysqli_fetch_array($result)) {
			$sql = "update tbl_static_pages set page_display_order='$i' where page_id='$line_raw[page_id]' ";
			db_query($sql);
			set_page_display_order_all($line_raw['page_id'], $level);
			$i++;
		}
	}
	return $str;
}
function get_top_level_parent($page_id, $full = false)
{
	$sql = "select page_parent_id from tbl_static_pages where page_id='$page_id' ";
	$result = db_query($sql);
	$line_raw = mysqli_fetch_array($result);
	$page_parent_id = $line_raw['page_parent_id'];
	if ($page_parent_id == 0) {
		if ($full) {
			return $line_raw;
		} else {
			return $page_id;
		}
	} else {
		return get_top_level_parent($page_parent_id);
	}
}
function page_slug_exists($page_slug, $page_id)
{
	$sql = "select count(*) from tbl_static_pages where page_slug='{$page_slug}'";
	if ($page_id != "") {
		$sql .= " and `page_id` != '$page_id' ";
	}
	$count = db_scalar($sql);
	if ($count > 0) {
		return true;
	} else {
		return false;
	}
}
function make_sql_checkboxes($sql, $checkname, $cols, $missit = '', $checksel = '', $style = '', $tableattr = '')
{
	// Fix: Ensure $cols is a number, not an array, to avoid division error
	if (is_array($cols)) {
		$cols = count($cols) > 0 ? count($cols) : 1;
	}
	if (!is_numeric($cols) || $cols <= 0) {
		$cols = 1;
	}
	$colwidth = 100 / $cols;
	$colwidth = round($colwidth, 2);
	$j = 0;
	$checkstr = ""; // Initialize the variable to avoid undefined variable error
	$check_result = db_query($sql);
	// Fix: Define $javascript as an empty string to avoid undefined variable error
	$javascript = '';
	if (mysqli_num_rows($check_result) > 0) {
		while ($check_rs = mysqli_fetch_array($check_result)) {
			$tochecked = "";
			if (is_array($checksel) && in_array($check_rs[0], $checksel)) {
				$tochecked = "checked";
			}
			if ($check_rs[0] != $missit) {
				if ($check_rs[1] != "") {
					if ($j == 0) {
						$checkstr .= "<table cellspacing=\"0\" cellpadding=\"0\"  border=\"0\" $tableattr>\n";
					} else if (($j % $cols) == 0) {
						$checkstr .= "<tr>";
					}
					$checkstr .= "<td align=\"left\" valign=\"top\"><INPUT TYPE='checkbox' $javascript NAME='$checkname" . '[]' . "' value='$check_rs[0]' $tochecked >$check_rs[1]</td>";
					$j++;
					if (($j % $cols) == 0) {
						$checkstr .= "</tr>";
					}
				}
			}
		}
		if ($j % $cols != 0) {
			$colspan = ($cols - $j);
			$j = 0;
			$checkstr .= '<td colspan="' . $colspan . '">&nbsp;</td></tr>';
		}
		$checkstr .= "</table>";
	}
	return $checkstr;
}
function close_fancybox($closethiswindow, $targetpage)
{
	if ($closethiswindow) {
?>
		<script language="javascript">
			<?php if ($targetpage != "") { ?>
				window.parent.location.href = "<?= $targetpage ?>";
				parent.$.fn.fancybox.close();
			<?php } else { ?>
				parent.jQuery.fancybox.close();
				/*parent.jQuery('#fancybox-overlay').css('display', 'none');
				parent.jQuery('#fancybox-wrap').css('display', 'none');*/
			<?php } ?>
		</script>
	<?php
		exit;
	}
}
function region_list($country_code, $state_id)
{
	return sql_dropdown("select state_id, state_name from tbl_states where state_country_code = '$country_code' order by state_name", 'state_id', $state_id, 'onchange="show_provinces(this.value,\'' . SITE_WS_PATH . '\');"', 'Select Regions/States');
}
function province_list($state_id, $province_id)
{
	return sql_dropdown("select province_id, province_name from tbl_province where pregion_id = '$state_id' and province_status='Active' order by province_name", 'province_id', $province_id, 'onchange="show_comune(this.value,\'' . SITE_WS_PATH . '\');"', 'Select Provinces');
}
function comune_list($province_id, $comune_id, $show_locality = 'No')
{
	if ($show_locality == 'Yes') {
		$loc = 'onchange="show_locality(this.value,\'' . SITE_WS_PATH . '\');"';
	}
	return sql_dropdown("select comune_id, comune_name from tbl_comune where comune_province_id = '$province_id' and comune_status='Active' order by comune_name", 'comune_id', $comune_id, 'onchange="show_locality(this.value,\'' . SITE_WS_PATH . '\');"', 'Select Comunes');
}
function locality_list($comune_id, $locality_id)
{
	return sql_dropdown("select locality_id,locality_name from tbl_locality where comune_id = '$comune_id' and comune_id > 0 order by locality_name", 'locality_id', $locality_id, '', 'Select Locality');
}
function show_last_tag_name($tag)
{
	if (empty($tag)) {
		return '';
	}
	$arr = explode(",", $tag);
	$total_element = count($arr) - 1;
	return $arr[$total_element];
}
function show_first_tag_name($tag)
{
	if (empty($tag)) {
		return '';
	}
	$arr = explode(",", $tag);
	return $arr[0];
}
function show_last_tag_id($tag)
{
	$arr = explode(",", $tag);
	$arr = array_filter($arr);
	$total_element = count($arr);
	if ($total_element > 0) {
		$total_element = $total_element - 1;
		return $arr[$total_element];
	}
	return 0;
}
function get_contract_type($supplier_id, $season_ids = '')
{
	$var = "<select name='contracttype_" . $supplier_id . "' id='contracttype_" . $supplier_id . "'>";
	if ($season_ids != '') {
		$sql = db_query("select * from  mv_supplier s where supplier_status!='Delete' and supplier_id IN (select fk_hotel_id from mv_room_price where fk_season_id IN ($season_ids) and fk_hotel_id='$supplier_id' and fk_supplier_id='0' and price_status='Active')");
		while ($res = mysqli_fetch_array($sql)) {
			$var .= "<option value='" . $res['supplier_id'] . "'>Direct</option>";
		}
		$sql = db_query("select * from mv_hotel_dmc_supplier e inner join mv_supplier s on s.supplier_id=e.fk_dmc_supplier_id where fk_hotel_id='$supplier_id' and dmc_status!='Delete' and supplier_status!='Delete' and supplier_id IN (select fk_supplier_id from mv_room_price where fk_season_id IN ($season_ids) and fk_hotel_id='$supplier_id' and price_status='Active')");
		while ($res = mysqli_fetch_array($sql)) {
			$var .= "<option value='" . $res['supplier_id'] . "'>DMC(" . $res['supplier_company_name'] . ")</option>";
		}
	}
	$sql = db_query("select * from mv_hotel_engines e inner join mv_supplier s on s.supplier_id=e.engine_name where fk_supplier_id='$supplier_id' and engine_status!='Delete' and supplier_status!='Delete'");
	//	while($res=mysqli_fetch_array($sql)){
	//		$var.="<option value='BE_".$res['supplier_id']."'>".$res['supplier_company_name']."</option>";
	//	}
	if (mysqli_num_rows($sql) > 0) {
		while ($res = mysqli_fetch_array($sql)) {
			$var .= "<option value='BE_" . $res['supplier_id'] . "'>" . $res['supplier_company_name'] . "</option>";
		}
	} else {
		$var .= "<option value='BE_594'>Italy Vacation Specialists</option>";
	}
	$var .= "</select>";
	return $var;
}
function get_tour_contract_type($tour_id, $tour_owner)
{
	$var = "<select name='contracttype_" . $tour_id . "' id='contracttype_" . $tour_id . "'>";
	$var .= "<option value='" . $tour_owner . "'>Direct</option>";
	$sql = db_query("select * from mv_tour_contract c inner join mv_supplier s on s.supplier_id=c.fk_supplier_id where fk_tour_id='$tour_id' and contract_status!='Delete' and supplier_status!='Delete'");
	while ($res = mysqli_fetch_array($sql)) {
		$var .= "<option value='" . $res['supplier_id'] . "'>" . $res['supplier_company_name'] . "</option>";
	}
	$var .= "</select>";
	return $var;
}
function AllDate_between($fromDate, $toDate)
{
	$dateMonthYearArr = array();
	$fromDateTS = strtotime($fromDate);
	$toDateTS = strtotime($toDate);
	for ($currentDateTS = $fromDateTS; $currentDateTS <= $toDateTS; $currentDateTS += (60 * 60 * 24)) {
		$currentDateStr = date("Y-m-d", $currentDateTS);
		$dateMonthYearArr[] = $currentDateStr;
	}
	return $dateMonthYearArr;
}
function get_all_dates_between($startDate, $endDate)
{
	// Check if startDate or endDate is null or not a valid date string
	if ($startDate === null || $startDate === '' || $endDate === null || $endDate === '') {
		return array();
	}
	$startTimestamp = strtotime($startDate);
	$endTimestamp = strtotime($endDate);
	// Check if timestamps are valid
	if ($startTimestamp === false || $endTimestamp === false) {
		return array();
	}
	$return = array($startDate);
	$start = $startDate;
	$i = 1;
	if ($startTimestamp < $endTimestamp) {
		while (strtotime($start) < $endTimestamp) {
			$start = date('Y-m-d', strtotime($startDate . '+' . $i . ' days'));
			$return[] = $start;
			$i++;
		}
	}
	return $return;
}
/*function get_all_dates_between($fromDate,$toDate){
	 $dateMonthYearArr = array();
	 $fromDateTS = strtotime($fromDate);
	 $toDateTS = strtotime($toDate);
	 for ($currentDateTS = $fromDateTS; $currentDateTS <= $toDateTS; $currentDateTS += (60 * 60 * 24)) {
	  $currentDateStr = date("Y-m-d",$currentDateTS);
	  $dateMonthYearArr[] = $currentDateStr;
	 }
	return $dateMonthYearArr;
}*/
function calculate_hotel_price($start_date, $end_date, $hotel_id, $room_id, $season_ids, $quantity = '1', $dmc_id = 0)
{
	$arr = get_all_dates_between($start_date, $end_date);
	$price = 0;
	$total_nights = count($arr) - 1;
	if ($season_ids != "") {
		for ($i = 0; $i < $total_nights; $i++) {
			$res = db_result("select p.* from mv_room_price p left join mv_hotel_seasons s on p.fk_season_id=s.season_id where p.fk_hotel_id='$hotel_id' and p.fk_room_id='$room_id' and p.fk_season_id IN ($season_ids) and p.fk_supplier_id='$dmc_id' and ('$arr[$i]' between s.season_start_date and  s.season_end_date) ");
			if (!$res) {
				continue; // Skip if no price found for this date
			}
			$day_of_week = date("D", strtotime($arr[$i]));
			switch ($day_of_week) {
				case 'Sun':
					$price += isset($res['price_sunday']) ? $res['price_sunday'] : 0;
					break;
				case 'Mon':
					$price += isset($res['price_monday']) ? $res['price_monday'] : 0;
					break;
				case 'Tue':
					$price += isset($res['price_tuesday']) ? $res['price_tuesday'] : 0;
					break;
				case 'Wed':
					$price += isset($res['price_wednesday']) ? $res['price_wednesday'] : 0;
					break;
				case 'Thu':
					$price += isset($res['price_thursday']) ? $res['price_thursday'] : 0;
					break;
				case 'Fri':
					$price += isset($res['price_friday']) ? $res['price_friday'] : 0;
					break;
				case 'Sat':
					$price += isset($res['price_saturday']) ? $res['price_saturday'] : 0;
					break;
			}
		}
	}
	return $price;
}
function transfer_list($transfer_country, $fk_region_id, $fk_pickup_id, $fk_dropoff_id, $dmc_transfer_type, $vehicle_id = '', $fk_supplier_id = '', $pax = '')
{
	$sql = "select s.supplier_id,s.supplier_company_name,r.* from mv_supplier s left join mv_supplier_transportation t on s.supplier_id=t.fk_supplier_id left join mv_transportation_route r on t.transportation_id=r.fk_transportation_id  where fk_country_id='" . $transfer_country . "' and fk_region_id='" . $fk_region_id . "' and fk_pickup_id='" . $fk_pickup_id . "' and fk_dropoff_id='" . $fk_dropoff_id . "' and transportation_status='Active' and transportation_type='" . $dmc_transfer_type . "' and route_status!='Delete'";
	$sql .= " order by supplier_company_name asc ";
	$result = db_query($sql);
	$var = '';
	if (mysqli_num_rows($result) > 0 && $fk_pickup_id > 0 && $fk_dropoff_id > 0) {
		$var .= '<table width="100%" border="0" cellspacing="1" cellpadding="0" >
								<tr class="row1bg">
								  <td width="65%">&nbsp;DMC </td>
								  <td width="15%">&nbsp;Type </td>
								  <td width="15%">&nbsp;Price </td>
								  <td style="text-align: center;" width="5%">&nbsp;</td>
								</tr>';
		$ii = 0;
		while ($line_raw = mysqli_fetch_array($result)) {
			$line = ms_display_value($line_raw);
			@extract($line);
			if ($ii % 2 == 0) {
				$cls = "row2bg";
			} else {
				$cls = "row3bg";
			}
			$ii++;
			/*if($fk_supplier_id==$supplier_id){
									$chk='checked';
								}else{
									$chk='';
								}*/
			switch ($pax) {
				case 1:
					$base_price = $line['price_pp'];
					break;
				case 2:
					$base_price = $line['price_pp_2'];
					break;
				case 3:
					$base_price = $line['price_pp_3'];
					break;
				case 4:
					$base_price = $line['price_pp_4'];
					break;
				case 5:
					$base_price = $line['price_pp_5'];
					break;
				case 6:
					$base_price = $line['price_pp_6'];
					break;
				case 7:
					$base_price = $line['price_pp_7'];
					break;
				case 8:
					$base_price = $line['price_pp_8'];
					break;
				case 9:
					$base_price = $line['price_pp_9'];
					break;
				case 10:
					$base_price = $line['price_pp_10'];
					break;
			}
			$var .= '<td>&nbsp;' . safe_number_format($price, 2) . '</td>';
			$var .= '<tr class="' . $cls . '">
								  <td valign="top">&nbsp;';
			$var .= "<a href='javascript:void(0);' class='jt' rel='" . SITE_WS_PATH . "/" . AGENT_ADMIN_DIR . "/dmc/dmc_route_price_info.php?route_id=" . $route_id . "' title='Pricing Info'>";
			$var .= $supplier_company_name . '</a></td>';
			$var .= '<td>&nbsp;' . $transfer_type . '</td>';
			$var .= '<td>&nbsp;' . number_format($price, 2) . '</td>';
			$var .= '<td align="center">
									<input onclick="show_route_pricing_option(\'' . SITE_WS_PATH . '\',\'' . $route_id . '\');" name="route_id" type="radio" value="' . $route_id . '"  />
								  </td>
								</tr>';
		}
		$var .= '</table>
						  <br />
						  <table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
							  <td style="text-align:right;padding:2px">
								<input type="submit" class="button" name="Submit" value="Add Transfer" />
							  </td>
							</tr>
						  </table>';
	}
	return $var;
}
function other_transfer_list($dmc_transfer_type, $dep_transfer_country, $dep_region_id, $fk_pickup_station_id, $arr_transfer_country, $arr_region_id, $fk_dropoff_station_id, $fk_supplier_id = '')
{
	/*if($dmc_transfer_type=="219"){
		$fk_pickup_station_id=$dep_region_id;
		$fk_dropoff_station_id=$arr_region_id;
	}*/
	$sql = "select s.supplier_id,s.supplier_company_name,r.route_id,r.price_pp from mv_supplier s left join mv_supplier_transportation t on s.supplier_id=t.fk_supplier_id left join mv_transportation_route r on t.transportation_id=r.fk_transportation_id  where fk_pickup_station_id='" . $fk_pickup_station_id . "' and fk_dropoff_station_id='" . $fk_dropoff_station_id . "' and transportation_status='Active' and transportation_type='" . $dmc_transfer_type . "'  and route_status!='Delete' order by supplier_company_name asc";
	$result = db_query($sql);
	$var = '';
	if (mysqli_num_rows($result) > 0 && $fk_pickup_station_id > 0 && $fk_dropoff_station_id > 0) {
		$var .= '<table width="100%" border="0" cellspacing="1" cellpadding="0" >
								<tr class="row1bg">
								  <td width="75%">&nbsp;DMC </td>
								  <td width="25%">&nbsp;Price(PP)</td>
								  <td style="text-align: center;" width="5%">&nbsp;</td>
								</tr>';
		$ii = 0;
		while ($line_raw = mysqli_fetch_array($result)) {
			$line = ms_display_value($line_raw);
			@extract($line);
			if ($ii % 2 == 0) {
				$cls = "row2bg";
			} else {
				$cls = "row3bg";
			}
			$ii++;
			if ($fk_supplier_id == $supplier_id) {
				$chk = 'checked';
			} else {
				$chk = '';
			}
			$var .= '<tr class="' . $cls . '"><td valign="top">&nbsp;';
			$var .= $supplier_company_name . '</td>
								  <td valign="top">' . $price_pp . '&nbsp;</td>
								  <td align="center">
									<input name="dmc_other_supplier_id" type="radio" value="' . $supplier_id . '" ' . $chk . ' />
								  </td>
								</tr>';
		}
		$var .= '</table>
						  <br />
						  <table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
							  <td style="text-align:right;padding:2px">
								<input type="submit" class="button" name="Submit" value="Add Transfer" />
							  </td>
							</tr>
						  </table>';
	}
	return $var;
}
function flight_supplier_list($dep_airport_id, $arr_airport_id, $fk_supplier_id = '')
{
	$sql = "select s.supplier_id,s.supplier_company_name,r.route_id,r.price_pp from mv_supplier s left join mv_supplier_transportation t on s.supplier_id=t.fk_supplier_id left join mv_transportation_route r on t.transportation_id=r.fk_transportation_id  where fk_airport_id='" . $dep_airport_id . "' and fk_dropoff_airport_id='" . $arr_airport_id . "' and transportation_status='Active' and transportation_type='220'  and route_status!='Delete' order by supplier_company_name asc";
	$result = db_query($sql);
	$var = '';
	if (mysqli_num_rows($result) > 0 && $dep_airport_id > 0 && $arr_airport_id > 0) {
		$var .= '<table width="100%" border="0" cellspacing="1" cellpadding="0" >
								<tr class="row1bg">
								  <td width="75%">&nbsp;DMC </td>
								  <td width="25%">&nbsp;Price(PP)</td>
								  <td style="text-align: center;" width="5%">&nbsp;</td>
								</tr>';
		$ii = 0;
		while ($line_raw = mysqli_fetch_array($result)) {
			$line = ms_display_value($line_raw);
			@extract($line);
			if ($ii % 2 == 0) {
				$cls = "row2bg";
			} else {
				$cls = "row3bg";
			}
			$ii++;
			if ($fk_supplier_id == $supplier_id) {
				$chk = 'checked';
			} else {
				$chk = '';
			}
			$var .= '<tr class="' . $cls . '"><td valign="top">&nbsp;';
			$var .= $supplier_company_name . '</td>
								  <td valign="top">' . $price_pp . '&nbsp;</td>
								  <td align="center">
									<input name="dmc_other_supplier_id" type="radio" value="' . $supplier_id . '" ' . $chk . ' />
								  </td>
								</tr>';
		}
		$var .= '</table>
						  <br />
						  <table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
							  <td style="text-align:right;padding:2px">
								<input type="submit" class="button" name="Submit" value="Add Transfer" />
							  </td>
							</tr>
						  </table>';
	}
	return $var;
}
function get_transfer_booking_price($route_id, $pax)
{
	$price = 0;
	if ($route_id > 0 && $pax > 0) {
		$res = db_result("select * from mv_transportation_route where route_id='$route_id' ");
		switch ($pax) {
			case 1:
				$base_price = $res['price_pp'];
				break;
			case 2:
				$base_price = $res['price_pp_2'];
				break;
			case 3:
				$base_price = $res['price_pp_3'];
				break;
			case 4:
				$base_price = $res['price_pp_4'];
				break;
			case 5:
				$base_price = $res['price_pp_5'];
				break;
			case 6:
				$base_price = $res['price_pp_6'];
				break;
			case 7:
				$base_price = $res['price_pp_7'];
				break;
			case 8:
				$base_price = $res['price_pp_8'];
				break;
			case 9:
				$base_price = $res['price_pp_9'];
				break;
			case 10:
				$base_price = $res['price_pp_10'];
				break;
		}
		$price = $base_price * $pax;
	}
	return $price;
}
function supplier_list($business_type = '', $keyword = '', $location_tag = '', $pagesize = '', $start = '', $base_url = '')
{
	global $arr_business_type;
	$_REQUEST['keyword'] = $keyword;
	$_REQUEST['location_tag'] = $location_tag;
	$_SERVER['PHP_SELF'] = SITE_WS_PATH . '/' . AGENT_ADMIN_DIR . '/supplier_list.php';
	$sql_add = '';
	if ($_REQUEST['keyword'] != '') {
		$keyword_array = preg_split('/\s+/', trim($_REQUEST['keyword']));
		for ($i = 0; $i < count($keyword_array); $i++) {
			$sql_add .= " and (supplier_company_name like '%" . $keyword_array[$i] . "%' OR supplier_email like '%" . $keyword_array[$i] . "%' OR supplier_code like '%" . $keyword_array[$i] . "%' OR supplier_phone like '%" . $keyword_array[$i] . "%')";
		}
	}
	##################  Location Tags ##############
	$comma_in_first = (substr($_REQUEST['location_tag'], 0, 1) == ',') ? true : false;
	if ($comma_in_first) {
		$location_tag = substr($_REQUEST['location_tag'], 1, strlen($_REQUEST['location_tag']));
	}
	if ($location_tag != '') {
		#$tag_name_string=db_scalar("select group_concat(trim(tag_name)) from mv_tags where tag_id IN (".$location_tag.")");
		$tag_name_string = $location_tag;
		$tag_name_string = (substr($tag_name_string, -1) == ',') ? substr($tag_name_string, 0, -1) : $tag_name_string;
		if ($tag_name_string != '' && strstr($tag_name_string, ',')) {
			$sql_add .= " and (location_tag_ids like '%" . $tag_name_string . "%')";
		} elseif ($tag_name_string != '') {
			$sql_add .= " and FIND_IN_SET('" . $tag_name_string . "',location_tag_ids)";
		}
	}
	#############################################
	if ($business_type != "") {
		//$sql_add.=" and supplier_business_type='".$business_type."' ";
		$sql_add .= " and FIND_IN_SET('" . $business_type . "',supplier_business_type)";
	}
	$start = intval($start);
	$pagesize = intval($pagesize) == 0 ? $pagesize = DEF_PAGE_SIZE : $pagesize;
	$sql = "select *  from mv_supplier where supplier_status!='Delete' $sql_add ";
	$order_by = isset($_REQUEST['order_by']) ? $_REQUEST['order_by'] : 'supplier_company_name';
	$order_by2 = isset($_REQUEST['order_by2']) ? $_REQUEST['order_by2'] : 'asc';
	$sql .= "order by is_prefered desc, $order_by $order_by2 ";
	$pager = new midas_pager_sql($sql, $pagesize, $start);
	$sql .= "limit $start, $pagesize ";
	$result = db_query($sql);
	if (mysqli_num_rows($result) == 0) {
	?>
		<div class="msg"><span class="errorMsg">Sorry, no records found.</span></div>
	<?php } else { ?>
		<br />
		<div style="padding:10px;">
			<?= pagesize_dropdown_new('pagesize', $pagesize, 'supplier_list.php'); ?>
			<span style="float:right">
				<?php if ($pager->total_records) {
					$pager->show_displaying();
				}
				?>
			</span>
		</div>
		<form method="post" name="form1" id="form1" onSubmit="confirm_submit(this)">
			<table width="100%" border="0" cellspacing="1" cellpadding="0">
				<tr class="row1bg">
					<td width="18%">Supplier Name
					</td>
					<td width="10%">Email
					</td>
					<td width="10%">Phone
					</td>
					<td width="15%">Location
					</td>
					<td width="10%">Business Type
					</td>
					<td width="9%">Code
					</td>
					<td width="7%">Status</td>
					<td width="9%">Is Preferred</td>
					<td style="text-align: center;" width="7%">&nbsp;</td>
					<td style="text-align: center;" width="5%">
						<input name="check_all" type="checkbox" id="check_all" value="1" onclick="checkall(this.form)" />
					</td>
				</tr>
				<?php
				$i = 0;
				while ($line_raw = mysqli_fetch_array($result)) {
					$line = ms_display_value($line_raw);
					@extract($line);
					if ($i % 2 == 0) {
						$cls = ($is_prefered ? 'row9bg' : 'row2bg');
						//$cls="row2bg";
					} else {
						$cls = ($is_prefered ? 'row9bg' : 'row3bg');
						//$cls="row3bg";
					}
					$i++;
				?>
					<tr class="<?= $cls ?>">
						<td valign="top">
							<?php
							$supplier_business_type = $business_type;
							if ($supplier_business_type == 1 || $supplier_business_type == 5 || $supplier_business_type == 10 || $supplier_business_type == 7 || $supplier_business_type == 9 || $supplier_business_type == 11 || $supplier_business_type == 12 || $supplier_business_type == 13) {
								$summary_file_path = "hotel/hotel_summary_general.php";
							} elseif ($supplier_business_type == 3 || $supplier_business_type == 12) {
								$summary_file_path = "dmc/dmc_summary_general.php";
							} elseif ($supplier_business_type == 6) {
								$summary_file_path = "cruise/cruise_summary_general.php";
							} elseif ($supplier_business_type == 4) {
								$summary_file_path = "booking_engine/bk_summary_general.php";
							} else {
								$summary_file_path = "hotel/hotel_summary_general.php";
							}
							?>
							<a href="<?= $summary_file_path ?>?id=<?= $supplier_id ?>"><?php echo $supplier_company_name; ?></a>
						</td>
						<td valign="top"> <?php echo "<a href='mailto: " . $supplier_email . "'>" . $supplier_email . "</a>"; ?> </td>
						<td valign="top" nowrap="nowrap"><?php echo phone_number_display($supplier_phone_country_code, $supplier_phone_area_code, $supplier_phone); ?></td>
						<td valign="top"> <?php
											echo str_replace(",", " -> ", $location_tags_name);
											?> </td>
						<td valign="top">
							<?php echo getSupplierType($line['supplier_business_type']); ?>
						</td>
						<td valign="top">
							<?php echo $supplier_code; ?>
						</td>
						<td valign="top">
							<?php echo $supplier_status; ?>
						</td>
						<td valign="top">
							<?php echo ($is_prefered ? 'Yes' : 'No'); ?>
						</td>
						<td align="center">
							<a href="<?= $summary_file_path ?>?id=<?= $supplier_id ?>"><img src="images/icons/edit.png" alt="Edit" width="16" height="16" border="0" /></a>
						</td>
						<td align="center">
							<input name="arr_ids[]" type="checkbox" id="arr_ids[]" value="<?= $supplier_id ?>" />
						</td>
					</tr>
				<?php } ?>
			</table>
			<br />
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="right" style="padding:2px">
						<input type="hidden" name="business_type" value="<?= $business_type ?>" />
						<input type="hidden" name="keyword" value="<?= $keyword ?>" />
						<input type="hidden" name="location_tag" value="<?= $location_tag ?>" />
						<input type="submit" class="button" name="AddPrefered" value="Add Prefered" />
						<input type="submit" class="button" name="RemovePrefered" value="Remove Prefered" />
						<input type="submit" class="button" name="Deactivate" value="Deactivate" />
						<input type="submit" class="button" name="Activate" value="Activate" />
						<input type="submit" class="button" name="Deactivate" value="Deactivate" />
						<input type="submit" class="button" name="Delete" value="Delete" onclick="return confirm('Are you sure you want to delete selected records.?');" />
					</td>
				</tr>
			</table>
		</form>
		<?php $pager->show_pager(); ?>
	<?php }
}
function get_tour_price($tour_id, $supplier_id, $tour_date, $pax, $occupancy, $is_private_tour = 'No', $package_id = '', $tour_pricing_method = '')
{
	$tour_result = db_result("select * from mv_tours t left join mv_tour_pricing_extra e on (t.tour_id=e.fk_tour_id and e.fk_supplier_id='$supplier_id' and e.price_status!='Delete') where t.tour_id = '$tour_id'");
	@extract($tour_result);
	$total = 0;
	if ($tour_classification == "1") {
		$season_res = db_result("select season_id from mv_tour_seasons where season_status='Active' and fk_tour_id='$tour_id' and season_start_date <= '$tour_date' and season_end_date >= '$tour_date' and fk_package_id = '$package_id' ");
		$season_id = $season_res['season_id'];
		$tour_pricing_res = db_result("select * from mv_tour_package_pricing where fk_package_id='$package_id' and fk_season_id='$season_id' and fk_occupancy_id='$occupancy'");
		$additional_markup = db_scalar("select additional_markup from mv_tour_packages where package_id='$package_id'");
		if ($is_private_tour == "Yes") {
			$price_pp = $tour_pricing_res['price_adult'] + $season_res['private_supplement'];
		} else {
			$price_pp = $tour_pricing_res['price_adult'];
		}
	} else {
		$season_id = db_scalar("select season_id from mv_tour_seasons where season_status='Active' and fk_tour_id='$tour_id' and season_start_date <= '$tour_date' and season_end_date >= '$tour_date' and (fk_package_id <= 0 or fk_package_id is NULL) ");
		if ($tour_pricing_method == "" || $tour_pricing_method == "G") {
			$group_id = db_scalar("select group_id from mv_tour_group where fk_tour_id='$tour_id' and group_status!='Delete' and '$pax' >= max_pax order by max_pax desc");
			$tour_pricing_res = db_result("select * from mv_tour_pricing where fk_supplier_id='$supplier_id' and fk_tour_id='$tour_id' and fk_season_id='$season_id' and fk_group_id='$group_id' and pricing_type='G'");
			#################### Private supplement for old data only  ###############
			if ($is_private_tour == "Yes") {
				$price_pp = $tour_pricing_res['price_adult'] + $supplement_for_private;
			} else {
				$price_pp = $tour_pricing_res['price_adult'];
			}
			#########################################################################
		} else {
			$price_pp = db_scalar("select price_adult from mv_tour_pricing where fk_supplier_id='$supplier_id' and fk_tour_id='$tour_id' and fk_season_id='$season_id' and number_of_pax='$pax' and pricing_type='" . $tour_pricing_method . "'");
		}
		/*
			$group_id=db_scalar("select group_id from mv_tour_group where fk_tour_id='$tour_id' and group_status!='Delete' and '$pax' >= max_pax order by max_pax desc");
			$tour_pricing_res=db_result("select * from mv_tour_pricing where fk_supplier_id='$supplier_id' and fk_tour_id='$tour_id' and fk_season_id='$season_id' and fk_group_id='$group_id' and pricing_type='G'");
			if($is_private_tour=="Yes"){
				$price_pp=$tour_pricing_res['price_adult']+$supplement_for_private;
			}else{
				$price_pp=$tour_pricing_res['price_adult'];
			}
			*/
	}
	$total = $total + ($price_pp * $pax);
	$total = $total - (($total * $gross_commission) / 100);
	$total = $total + $additional_markup + $entrance_fee + $gratuity;
	return $total;
}
function file_exchnage_rates($file_id, $curr1, $curr2)
{
	if (!isset($_SESSION['file_curr_' . $file_id]) || $_SESSION['file_curr_' . $file_id] != $curr2) {
		$_SESSION['file_curr_' . $file_id] = $curr2;
		$sql = db_query("select * from mv_file_currency where fk_file_id='$file_id'");
		while ($res = mysqli_fetch_array($sql)) {
			@extract($res);
			$_SESSION["file_" . $file_id][$base_currency] = $exchange_rate;
		}
	}
	return isset($_SESSION["file_" . $file_id][$curr1]) ? $_SESSION["file_" . $file_id][$curr1] : 1;
}
function get_exchange_rates($curr1, $curr2)
{
	if ($curr1 != $curr2) {
		return db_scalar("select currency_value from mv_currency where convert_currency_from='$curr1' and convert_currency_to='$curr2'");
	} else {
		return 1;
	}
}
function update_file_total($id)
{
	$result = db_result("select * from mv_files  where file_id = '$id'");
	@extract($result);
	$total_pax = $file_adults + $file_teens + $file_childrens + $file_infants;
	$total_net = 0;
	$total_gross_sc = 0;
	#######################  Accommodation ############################################################
	$file_sql = db_query("select f.*,s.supplier_company_name,s.location_tags_name,r.room_name,r.room_occupancy_type,meal_plan.tag_name as meal_plan  from mv_file_accommodation f left join mv_supplier s on f.fk_hotel_id=s.supplier_id left join mv_hotel_rooms r on f.fk_room_id=r.room_id left join mv_room_detail d on d.room_detail_id=r.fk_room_detail_id left join mv_tags as meal_plan on meal_plan.tag_id=d.room_detail_meal_plan where file_accommodation_status='Active' and fk_file_id='$id' order by check_in_date");
	while ($trans_res = mysqli_fetch_array($file_sql)) {
		$net_sc = $trans_res['booking_net_price'] * file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
		$total_net = $total_net + $net_sc;
		/*$gross_sc=$net_sc+($net_sc*$file_markup)/100;
				$gross_sc=ceil($gross_sc);
				$total_gross_sc=$total_gross_sc+$gross_sc;
				*/
		$total_gross_sc = $total_gross_sc + $trans_res['booking_gross_price'];
	}
	#######################  Transfer Services ############################################################
	$file_sql = db_query("select t.*,t1.tag_name as booking_type,t2.tag_name as location_name,t3.tag_name as pickup,t4.tag_name as dropoff,t5.tag_name as pickup_station,t6.tag_name as dropoff_station  from mv_file_transfers t left join mv_tags t1 on t1.tag_id=t.file_booking_type left join mv_tags t2 on t2.tag_id=t.fk_region_id left join mv_tags t3 on t3.tag_id=t.fk_pickup_id left join mv_tags t4 on t4.tag_id=t.fk_dropoff_id left join mv_tags t5 on t5.tag_id=t.fk_pickup_station_id left join mv_tags t6 on t6.tag_id=t.fk_dropoff_station_id where file_transfer_status='Active' and fk_file_id='$id' order by check_in_date");
	while ($trans_res = mysqli_fetch_array($file_sql)) {
		$net_sc = $trans_res['booking_net_price'] * file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
		$total_net = $total_net + $net_sc;
		/*$gross_sc=$net_sc+($net_sc*$file_markup)/100;
				$gross_sc=ceil($gross_sc);
				$total_gross_sc=$total_gross_sc+$gross_sc;*/
		$total_gross_sc = $total_gross_sc + $trans_res['booking_gross_price'];
	}
	#######################  Cruise Services #######################################################
	$file_sql = db_query("select *  from mv_file_cruises where file_cruise_status='Active' and fk_file_id='$id' order by check_in_date");
	$cruise_extras = 0;
	while ($trans_res = mysqli_fetch_array($file_sql)) {
		$net_sc = $trans_res['booking_net_price'] * file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
		$total_net = $total_net + $net_sc;
		$total_gross_sc = $total_gross_sc + $trans_res['booking_gross_price'];
		$net_fuel_charge = ($total_pax * $trans_res['fuel_charge']) * file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
		$net_port_charge = ($total_pax * $trans_res['port_charge']) * file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
		$cruise_extras = $cruise_extras + ($net_fuel_charge + $net_port_charge);
	}
	#######################  Activities ############################################################
	$file_sql = db_query("select a.*,t.*,t1.tag_name as type  from mv_file_activity a left join mv_tours t on a.fk_tour_id=t.tour_id left join mv_tags t1 on t1.tag_id=t.tour_type where file_activity_status='Active' and fk_file_id='$id' order by check_in_date");
	while ($trans_res = mysqli_fetch_array($file_sql)) {
		$net_sc = $trans_res['booking_net_price'] * file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
		$total_net = $total_net + $net_sc;
		/*$gross_sc=$net_sc+($net_sc*$file_markup)/100;
				$gross_sc=ceil($gross_sc);
				$total_gross_sc=$total_gross_sc+$gross_sc;*/
		$total_gross_sc = $total_gross_sc + $trans_res['booking_gross_price'];
	}
	#######################  Misc Services ############################################################
	$file_sql = db_query("select s.*  from mv_file_misc_service s  where misc_service_status='Active' and fk_file_id='$id' order by service_title");
	while ($trans_res = mysqli_fetch_array($file_sql)) {
		$net_sc = $trans_res['booking_net_price'] * file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
		$total_net = $total_net + $net_sc;
		/*$gross_sc=$net_sc+($net_sc*$file_markup)/100;
				$gross_sc=ceil($gross_sc);
				$total_gross_sc=$total_gross_sc+$gross_sc;*/
		$total_gross_sc = $total_gross_sc + $trans_res['booking_gross_price'];
	}
	###################################################################################################
	$gross_total_sc_without_tax = $total_gross_sc;
	if ($booking_fee_basis == "1") {
		$booking_fee = $total_pax * $file_booking_charge;
	} else {
		$booking_fee = $file_booking_charge;
	}
	$other_service_fees_commissionable = 0;
	$other_service_fees_non_commissionable = 0;
	$other_service_fee_sql = db_query("select * from mv_file_service_fees where service_fee_status!='Delete' and fk_file_id='$id' order by service_fee_name");
	while ($other_service_res = mysqli_fetch_array($other_service_fee_sql)) {
		$fee_exchange_rate = file_exchnage_rates($id, $other_service_res['service_fee_currency'], $result['file_currency']);
		if ($other_service_res['service_fee_basis'] == 1) {
			if ($other_service_res['is_commissionable'] == "Yes") {
				$other_service_fees_commissionable = $other_service_fees_commissionable + ($fee_exchange_rate * $other_service_res['service_fee'] * $total_pax);
			} else {
				$other_service_fees_non_commissionable = $other_service_fees_non_commissionable + ($fee_exchange_rate * $other_service_res['service_fee'] * $total_pax);
			}
		} else {
			if ($other_service_res['is_commissionable'] == "Yes") {
				$other_service_fees_commissionable = $other_service_fees_commissionable + ($fee_exchange_rate * $other_service_res['service_fee']);
			} else {
				$other_service_fees_non_commissionable = $other_service_fees_non_commissionable + ($fee_exchange_rate * $other_service_res['service_fee']);
			}
		}
	}
	$service_fee = $booking_fee + $other_service_fees_commissionable;
	$tax = (($gross_total_sc_without_tax * $file_taxes) / 100) + $other_service_fees_non_commissionable + $cruise_extras;
	$gross_total_sc = $gross_total_sc_without_tax + $tax + $service_fee;
	$card_fee = (($gross_total_sc * $file_card_fee) / 100);
	$gross_total_sc = $gross_total_sc + $card_fee;
	if ($result['fk_agent_id'] > 0) {
		if ($agent_commission_type == "1") {
			$agent_commission = ($gross_total_sc_without_tax * $file_agent_commission) / 100;
		} else {
			$agent_commission = $file_agent_commission;
		}
		$agent_commission = $agent_commission + $result['additional_agent_comm'];
	} else {
		$agent_commission = 0;
	}
	db_query("update mv_files  set gross_total_sc='$gross_total_sc',net_total_sc='$total_net',gross_tax='" . $tax . "',gross_service_fee='" . $service_fee . "',gross_agent_commission='$agent_commission',gross_card_fee='" . $card_fee . "' where file_id = '$id'");
}
function get_airport_country($location_id)
{
	global $my_arr;
	$my_arr[] = $location_id;
	$location_id = db_scalar("select tag_parent_id from mv_tags where tag_id='$location_id'");
	if ($location_id != '2') {
		return get_airport_country($location_id);
	}
	return end($my_arr);
}
function country_operation($arr_counter, $arr = array())
{
	@extract($arr);
	?>
	<table width="100%" border="0" align="center" style="padding-top:15px;">
		<tr>
			<td class="form_green_rounded">
				<table width="100%" border="0" cellspacing="10" cellpadding="0">
					<tr>
						<td class="form_field_caption" valign="top">Country: *</td>
						<td>
							<?php echo sql_dropdown("select tag_id,tag_name from mv_tags where tag_status!='Delete' and tag_parent_id='2' and tag_id='430' order by tag_name", "operation_country[]",  $operation_country[$arr_counter], '', 'Select', '166');
							?>
						</td>
					</tr>
					<tr>
						<td class="form_field_caption">Contact Name: </td>
						<td>
							<input name="operation_contact_name[]" type="text" class="textfield_edit" value="<?= $operation_contact_name[$arr_counter] ?>" size="40">
						</td>
					</tr>
					<tr>
						<td class="form_field_caption">Contact Email: </td>
						<td>
							<input name="operation_contact_email[]" type="text" class="textfield_edit" value="<?= $operation_contact_email[$arr_counter] ?>" size="40">
						</td>
					</tr>
					<tr>
						<td class="form_field_caption">Contact Phone: </td>
						<td>
							<?php echo phone_number_text_box_array('operation_contact_number', $operation_contact_number_country_code[$arr_counter], $operation_contact_number_area_code[$arr_counter], $operation_contact_number[$arr_counter]); ?>
						</td>
					</tr>
					<tr>
						<td class="form_field_caption">Emergency Phone: </td>
						<td>
							<?php echo phone_number_text_box_array('operation_emergency_number', $operation_emergency_number_country_code[$arr_counter], $operation_emergency_number_area_code[$arr_counter], $operation_emergency_number[$arr_counter]); ?>
						</td>
					</tr>
					<tr>
						<td class="form_field_caption">Fax: </td>
						<td>
							<?php echo phone_number_text_box_array('operation_fax', $operation_fax_country_code[$arr_counter], $operation_fax_area_code[$arr_counter], $operation_fax[$arr_counter]); ?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
<?php }
function create_financial_pdf($id)
{
	global $arr_tour_classification;
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/config/lang/eng.php');
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/tcpdf.php');
	$result = db_result("select f.*,ag.*,concat(a.agent_first_name,' ',a.agent_last_name) as agent_name,concat(c.client_first_name,' ',c.client_last_name) as client_name,concat(e.emp_first_name,' ',e.emp_last_name) as consultant_name from mv_files f left join mv_agent a on f.fk_agent_id=a.agent_id left join mv_agency ag on a.fk_agency_id=ag.agency_id left join mv_client c on f.fk_client_id=c.client_id left join mv_employee e on f.file_primary_staff=e.emp_id where file_id = '$id'");
	class MYPDF extends TCPDF
	{
		//Page header
		public function Header()
		{
			//$this->Cell(790, 1, '', 0, 1, 'C', 0, '', 1);
		}
		// Page footer
		public function Footer()
		{
			$this->SetFont('helvetica', 'B', 8);
			$this->Cell(125, 5, 'Page: ' . $this->PageNo() . '', 0, 0, 'C', 0, '', 1);
			$this->Cell(140, 1, 'Printed: ' . date("j F Y, g:i A") . ' ', 0, 1, 'R', 0, '', 1);
		}
	}
	$margin_left = 25;
	$margin_top = 25;
	$margin_right = 25;
	$margin_bottom = 5;
	$pdf = new MYPDF('L', 'px', 'A4', true);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor(SITE_WS_PATH);
	$pdf->SetTitle('Pricing Summary');
	$pdf->SetSubject("Pricing Summary - File " . $result['file_code']);
	$pdf->SetKeywords("Pricing Summary - File " . $result['file_code']);
	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	// set header and footer fonts
	$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	//set margins
	$pdf->SetMargins($margin_left, $margin_top);
	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetHeaderMargin(25);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, $margin_bottom);
	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	//set some language-dependent strings
	$pdf->setLanguageArray($l);
	//initialize document
	//$pdf->AliasNbPages();
	// set font
	$pdf->SetFont('', '', 10);
	// add a page
	$pdf->AddPage();
	$pdf->Image(SITE_FS_PATH . '/' . AGENT_ADMIN_DIR . '/images/logo.gif', 25, '', 200, '', '', '', '', true);
	$pdf->SetFont('helvetica', 'B', 13);
	$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
	$pdf->Cell(260, 1, 'Italy Vacation Specialists', 0, 1, 'L', 0, '', 1);
	$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
	$pdf->SetFont('', '', 10);
	$pdf->Cell(260, 1, 'Via Degli Aranci 25/b, C.P. 80067, Sorrento (NA) – ITALY', 0, 1, 'L', 0, '', 1);
	$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
	$pdf->Cell(260, 1, 'Tel : +39 392-993-3869     Fax : +1-800-393-810', 0, 1, 'L', 0, '', 1);
	$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
	$pdf->Cell(260, 1, 'Email : jessica@italyvacationspecialists.com', 0, 1, 'L', 0, '', 1);
	$pdf->Line(25, 95, 815, 95);
	$pdf->Ln(30);
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(150, 10, $result['file_code'], 0, 0, 'L');
	if ($result['client_name'] != "") {
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(37, 10, 'Client:', 0, 0, 'L');
		$pdf->SetFont('', '', 10);
		$pdf->Cell(170, 10, $result['client_name'], 0, 0, 'L');
	}
	if ($result['agent_name'] != "") {
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(37, 10, 'Agent:', 0, 0, 'L');
		$pdf->SetFont('', '', 10);
		$pdf->Cell(170, 10, $result['agent_name'], 0, 0, 'L');
	}
	if ($result['consultant_name'] != "") {
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(60, 10, 'Consultant:', 0, 0, 'L');
		$pdf->SetFont('', '', 10);
		$pdf->Cell(170, 10, $result['consultant_name'], 0, 1, 'L');
	}
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(37, 10, 'Adults:', 0, 0, 'L');
	$pdf->SetFont('', '', 10);
	$pdf->Cell(30, 10, $result['file_adults'], 0, 0, 'L');
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(34, 10, 'Teen:', 0, 0, 'L');
	$pdf->SetFont('', '', 10);
	$pdf->Cell(30, 10, $result['file_teens'], 0, 0, 'L');
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(45, 10, 'Children:', 0, 0, 'L');
	$pdf->SetFont('', '', 10);
	$pdf->Cell(30, 10, $result['file_childrens'], 0, 0, 'L');
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(37, 10, 'Infant:', 0, 0, 'L');
	$pdf->SetFont('', '', 10);
	$pdf->Cell(114, 10, $result['file_infants'], 0, 0, 'L');
	if ($result['agency_name'] != "") {
		$pdf->SetFont('', '', 10);
		$agency_detail = "<table width=100%><tr><td><strong>Agency:</strong> " . $result['agency_name'];
		if ($result['agency_address'] != "") {
			$agency_detail .= "<br />" . nl2br($result['agency_address']);
		}
		if ($result['agency_city'] != "" || $result['agency_state'] != "") {
			$agency_detail .= "<br />";
			if ($result['agency_city'] != "") {
				$agency_detail .= $result['agency_city'];
			}
			if ($result['agency_city'] != "" && $result['agency_state'] != "") {
				$agency_detail .= ", ";
			}
			if ($result['agency_state'] != "") {
				$agency_detail .= $result['agency_state'];
			}
		}
		if ($result['agency_country'] != "" || $result['agency_zipcode'] != "") {
			$agency_detail .= "<br />";
			if ($result['agency_zipcode'] != "") {
				$agency_detail .= $result['agency_zipcode'];
			}
			if ($result['agency_country'] != "" && $result['agency_zipcode'] != "") {
				$agency_detail .= ", ";
			}
			if ($result['agency_country'] != "") {
				$agency_detail .= $result['agency_country'];
			}
		}
		$agency_detail .= "</td></tr></table>";
		$pdf->writeHTMLCell('230', $h = 0, $x = '', $y = '', $agency_detail, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	}
	$pdf->Ln(10);
	$file_sql = db_query("select f.*,s.supplier_company_name,s.supplier_code,s.location_tags_name,r.room_name,r.room_occupancy_type,meal_plan.tag_name as meal_plan,s.supplier_business_type   from mv_file_accommodation f left join mv_supplier s on f.fk_hotel_id=s.supplier_id left join mv_hotel_rooms r on f.fk_room_id=r.room_id left join mv_room_detail d on d.room_detail_id=r.fk_room_detail_id left join mv_tags as meal_plan on meal_plan.tag_id=d.room_detail_meal_plan where file_accommodation_status='Active' and fk_file_id='$id' order by check_in_date");
	if (mysqli_num_rows($file_sql) > 0) {
		$pdf->SetFont('', 'B', 13);
		$pdf->Cell(790, 10, 'Accommodation', 'B', 1, 'L');
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 9);
		$pdf->Cell(50, 10, 'Check In', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Check Out', 0, 0, 'L');
		$pdf->Cell(85, 10, 'Name', 0, 0, 'L');
		$pdf->Cell(85, 10, 'Room', 0, 0, 'L');
		$pdf->Cell(35, 10, 'Qty', 0, 0, 'L');
		$pdf->Cell(60, 10, 'Occupancy', 0, 0, 'L');
		$pdf->Cell(85, 10, 'Supplier', 0, 0, 'L');
		$pdf->Cell(70, 10, 'Supplier Ref', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Net', 0, 0, 'L');
		$pdf->Cell(40, 10, 'Ex. Rate', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Net SC', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Gross SC', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Profit', 0, 1, 'L');
		$pdf->Ln(10);
		$grand_total_net_sc = 0;
		$grand_total_gross_sc = 0;
		$grand_total_profit = 0;
		while ($trans_res = mysqli_fetch_array($file_sql)) {
			$exchange_rate = file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
			$net_sc = $trans_res['booking_net_price'] * $exchange_rate;
			/*$gross_sc=$net_sc+($net_sc*$result[file_markup])/100;
			$gross_sc=ceil($gross_sc);*/
			$gross_sc = $trans_res['booking_gross_price'];
			$profit = $gross_sc - $net_sc;
			$total_net_sc = $total_net_sc + $net_sc;
			$total_gross_sc = $total_gross_sc + $gross_sc;
			$total_profit = $total_profit + $profit;
			$hotel_name = $trans_res['supplier_company_name'];
			$room_name = $trans_res['room_name'];
			$booked_with = ucwords(strtolower($trans_res['booked_with_supplier_name']));
			$length_arr['hotel_name'] = strlen($hotel_name);
			$length_arr['room_name'] = strlen($room_name);
			$length_arr['booked_with'] = strlen($booked_with);
			$max_key = array_keys($length_arr, max($length_arr));
			$temp_str = $$max_key[0];
			$temp_str = wordwrap($temp_str, 20, "\n");
			$temp_str_arr = explode("\n", $temp_str);
			$temp_str_row = count($temp_str_arr);
			if ($temp_str_row == 1) {
				$ht = 10;
			} else {
				$ht = $temp_str_row * 10;
			}
			$nights = $trans_res['booking_duration'];
			$pdf->SetFont('', '', 8);
			$pdf->Cell(50, $ht, pdf_date_format($trans_res['check_in_date']), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, pdf_date_format($trans_res['check_out_date']), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->MultiCell(85, '', $hotel_name, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(85, '', $room_name, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(35, $ht, $trans_res['room_quantity'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->MultiCell(60, '', $trans_res['room_occupancy_type'], $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(85, '', $booked_with, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(70, $ht, $trans_res['supplier_code'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $trans_res['booking_price_currency'] . " " . $trans_res['booking_net_price'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(40, $ht, $exchange_rate, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . display_price($net_sc), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . $gross_sc, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . display_price($profit), 0, 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			if (isbtype($trans_res['supplier_business_type'], 5)) {
				$depoist_info = "Deposit: ";
				if ($trans_res['deposit_type'] == "1") {
					$deposit = ($trans_res['deposit_value'] * $trans_res['booking_net_price']) / 100;
					$depoist_info .= $trans_res['booking_price_currency'] . " " . display_price($deposit);
				} else {
					$depoist_info .= "$" . display_price($trans_res['deposit_value']);
				}
				$depoist_info .= "   Deposit Paid: " . $trans_res['deposit_paid'];
				$pdf->SetFont('', 'B', 9);
				$pdf->MultiCell(550, '', $depoist_info, $border = 0, $align = 'L', $fill = false, $ln = 1);
				$pdf->Ln(5);
			}
			$pdf->Ln(2);
		}
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(70, 20, 'Totals', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($total_net_sc), 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . $total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($total_profit), 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$grand_total_net_sc = $grand_total_net_sc + $total_net_sc;
		$grand_total_gross_sc = $grand_total_gross_sc + $total_gross_sc;
		$grand_total_profit = $grand_total_profit + $total_profit;
	}
	$file_sql = db_query("select a.*,t.*,s.supplier_code,t1.tag_name as type  from mv_file_activity a left join mv_tours t on a.fk_tour_id=t.tour_id left join mv_supplier s on a.booked_with_supplier_id=s.supplier_id left join mv_tags t1 on t1.tag_id=t.tour_type where file_activity_status='Active' and fk_file_id='$id' order by check_in_date");
	if (mysqli_num_rows($file_sql) > 0) {
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 13);
		$pdf->Cell(790, 10, 'Activity', 'B', 1, 'L');
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 9);
		$pdf->Cell(50, 10, 'Date', 0, 0, 'L');
		$pdf->Cell(65, 10, 'Classification', 0, 0, 'L');
		$pdf->Cell(145, 10, 'Name', 0, 0, 'L');
		$pdf->Cell(60, 10, 'Grouping', 0, 0, 'L');
		$pdf->Cell(135, 10, 'Supplier', 0, 0, 'L');
		$pdf->Cell(70, 10, 'Supplier Ref', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Net', 0, 0, 'L');
		$pdf->Cell(40, 10, 'Ex. Rate', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Net SC', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Gross SC', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Profit', 0, 1, 'L');
		$pdf->Ln(10);
		$total_net_sc = $total_gross_sc = $total_profit = 0;
		while ($trans_res = mysqli_fetch_array($file_sql)) {
			$exchange_rate = file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
			$net_sc = $trans_res['booking_net_price'] * $exchange_rate;
			/*$gross_sc=$net_sc+($net_sc*$result[file_markup])/100;
			$gross_sc=ceil($gross_sc);*/
			$gross_sc = $trans_res['booking_gross_price'];
			$profit = $gross_sc - $net_sc;
			$total_net_sc = $total_net_sc + $net_sc;
			$total_gross_sc = $total_gross_sc + $gross_sc;
			$total_profit = $total_profit + $profit;
			$tour_name = $trans_res['tour_name'];
			$booked_with = ucwords(strtolower($trans_res['booked_with_supplier_name']));
			$length_arr['tour_name'] = strlen($tour_name);
			$length_arr['booked_with'] = strlen($booked_with);
			$max_key = array_keys($length_arr, max($length_arr));
			$temp_str = $$max_key[0];
			$temp_str = wordwrap($temp_str, 40, "\n");
			$temp_str_arr = explode("\n", $temp_str);
			$temp_str_row = count($temp_str_arr);
			if ($temp_str_row == 1) {
				$ht = 10;
			} else {
				$ht = $temp_str_row * 10;
			}
			$pdf->SetFont('', '', 8);
			$pdf->Cell(50, $ht, pdf_date_format($trans_res['check_in_date']), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(65, $ht, $arr_tour_classification[$trans_res['tour_classification']], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->MultiCell(145, '', $tour_name, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(60, '', $trans_res['type'], $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(135, '', $booked_with, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(70, $ht, $trans_res['supplier_code'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $trans_res['booking_price_currency'] . " " . $trans_res['booking_net_price'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(40, $ht, $exchange_rate, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . display_price($net_sc), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . $gross_sc, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . display_price($profit), 0, 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Ln(2);
		}
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(70, 20, 'Totals', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($total_net_sc), 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . $total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($total_profit), 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$grand_total_net_sc = $grand_total_net_sc + $total_net_sc;
		$grand_total_gross_sc = $grand_total_gross_sc + $total_gross_sc;
		$grand_total_profit = $grand_total_profit + $total_profit;
	}
	$file_sql = db_query("select t.*,s.supplier_code,t7.tag_name as country_name,t1.tag_name as booking_type,t2.tag_name as location_name,t3.tag_name as pickup,t4.tag_name as dropoff,t5.tag_name as pickup_station,t6.tag_name as dropoff_station ,t8.airport_title as departure_airport,t9.airport_title as arrival_airport,t10.tag_name as arrival_country from mv_file_transfers t left join mv_tags t1 on t1.tag_id=t.file_booking_type left join mv_tags t2 on t2.tag_id=t.fk_region_id left join mv_tags t3 on t3.tag_id=t.fk_pickup_id left join mv_tags t4 on t4.tag_id=t.fk_dropoff_id left join mv_tags t5 on t5.tag_id=t.fk_pickup_station_id left join mv_tags t6 on t6.tag_id=t.fk_dropoff_station_id left join mv_tags t7 on t7.tag_id=t.transfer_country left join mv_location_airport t8 on t8.airport_id=t.fk_dep_airport_id left join mv_location_airport t9 on t9.airport_id=t.fk_arrival_airport_id left join mv_supplier s on t.booked_with_supplier_id=s.supplier_id left join mv_tags t10 on t10.tag_id=t.transfer_arrival_country where file_transfer_status='Active' and fk_file_id='$id' order by check_in_date,check_in_time");
	if (mysqli_num_rows($file_sql) > 0) {
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 13);
		$pdf->Cell(790, 10, 'Transportation', 'B', 1, 'L');
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 9);
		$pdf->Cell(50, 10, 'Date', 0, 0, 'L');
		$pdf->Cell(45, 10, 'Type', 0, 0, 'L');
		$pdf->Cell(65, 10, 'Location', 0, 0, 'L');
		$pdf->Cell(105, 10, 'Departure', 0, 0, 'L');
		$pdf->Cell(105, 10, 'Destination', 0, 0, 'L');
		$pdf->Cell(85, 10, 'Supplier', 0, 0, 'L');
		$pdf->Cell(70, 10, 'Supplier Ref', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Net', 0, 0, 'L');
		$pdf->Cell(40, 10, 'Ex. Rate', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Net SC', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Gross SC', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Profit', 0, 1, 'L');
		$pdf->Ln(10);
		$total_net_sc = $total_gross_sc = $total_profit = 0;
		while ($trans_res = mysqli_fetch_array($file_sql)) {
			$exchange_rate = file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
			$net_sc = $trans_res['booking_net_price'] * $exchange_rate;
			/*$gross_sc=$net_sc+($net_sc*$result[file_markup])/100;
		$gross_sc=ceil($gross_sc);*/
			$gross_sc = $trans_res['booking_gross_price'];
			$profit = $gross_sc - $net_sc;
			$total_net_sc = $total_net_sc + $net_sc;
			$total_gross_sc = $total_gross_sc + $gross_sc;
			$total_profit = $total_profit + $profit;
			if ($trans_res['file_booking_type'] == "221") {
				$transfer_location_name = show_last_tag_name($trans_res['pickup_city']);
			} elseif ($trans_res['file_booking_type'] == "219" || $trans_res['file_booking_type'] == "220") {
				$transfer_location_name = $trans_res['country_name'];
			} else {
				$transfer_location_name = $trans_res['location_name'];
			}
			if ($trans_res['file_booking_type'] == "221") {
				$transfer_pickup = show_last_tag_name($trans_res['pickup_city']);
			} else {
				$transfer_pickup = $trans_res['pickup'];
				if ($trans_res['pickup_station'] != "") {
					$transfer_pickup .= " - " . $trans_res['pickup_station'];
				}
				if ($trans_res['departure_airport'] != "") {
					$transfer_pickup .= " - " . $trans_res['departure_airport'];
				}
			}
			if ($trans_res['file_booking_type'] == "221") {
				$transfer_dropoff = show_last_tag_name($trans_res['dropoff_city']);
			} else {
				$transfer_dropoff = "";
				if ($trans_res['file_booking_type'] == "219" && $trans_res['arrival_country'] != $trans_res['country_name'] && $trans_res['arrival_country'] != "") {
					$transfer_dropoff .= $trans_res['arrival_country'] . " - ";
				}
				$transfer_dropoff .= $trans_res['dropoff'];
				if ($trans_res['dropoff_station'] != "") {
					$transfer_dropoff .= " - " . $trans_res['dropoff_station'];
				}
				if ($trans_res['arrival_airport'] != "") {
					$transfer_dropoff .= " - " . $trans_res['arrival_airport'];
				}
			}
			$booked_with = ucwords(strtolower($trans_res['booked_with_supplier_name']));
			$length_arr['transfer_location_name'] = strlen($transfer_location_name);
			$length_arr['transfer_pickup'] = strlen($transfer_pickup);
			$length_arr['transfer_dropoff'] = strlen($transfer_dropoff);
			$length_arr['booked_with'] = strlen($booked_with);
			$max_key = array_keys($length_arr, max($length_arr));
			$temp_str = $$max_key[0];
			$temp_str = wordwrap($temp_str, 25, "\n");
			$temp_str_arr = explode("\n", $temp_str);
			$temp_str_row = count($temp_str_arr);
			if ($temp_str_row == 1) {
				$ht = 10;
			} else {
				$ht = $temp_str_row * 10;
			}
			$pdf->SetFont('', '', 8);
			$pdf->Cell(50, $ht, pdf_date_format($trans_res['check_in_date']), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(45, $ht, $trans_res['booking_type'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->MultiCell(65, '', $transfer_location_name, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(105, '', $transfer_pickup, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(105, '', $transfer_dropoff, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(85, '', $booked_with, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(70, $ht, $trans_res['supplier_code'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $trans_res['booking_price_currency'] . " " . $trans_res['booking_net_price'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(40, $ht, $exchange_rate, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . display_price($net_sc), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . $gross_sc, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . display_price($profit), 0, 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Ln(2);
		}
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(70, 20, 'Totals', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($total_net_sc), 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . $total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($total_profit), 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$grand_total_net_sc = $grand_total_net_sc + $total_net_sc;
		$grand_total_gross_sc = $grand_total_gross_sc + $total_gross_sc;
		$grand_total_profit = $grand_total_profit + $total_profit;
	}
	$file_sql = db_query("select s.*,su.supplier_code  from mv_file_misc_service s left join mv_supplier su on s.booked_with_supplier_id=su.supplier_id  where misc_service_status='Active' and fk_file_id='$id' order by service_title");
	if (mysqli_num_rows($file_sql) > 0) {
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 13);
		$pdf->Cell(790, 10, 'Misc Services', 'B', 1, 'L');
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 9);
		$pdf->Cell(50, 10, 'Check In', 0, 0, 'L');
		$pdf->Cell(50, 10, 'Check Out', 0, 0, 'L');
		$pdf->Cell(170, 10, 'Service Name', 0, 0, 'L');
		$pdf->Cell(80, 10, 'Location', 0, 0, 'L');
		$pdf->Cell(105, 10, 'Supplier', 0, 0, 'L');
		$pdf->Cell(70, 10, 'Supplier Ref', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Net', 0, 0, 'L');
		$pdf->Cell(40, 10, 'Ex. Rate', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Net SC', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Gross SC', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Profit', 0, 1, 'L');
		$pdf->Ln(10);
		$total_net_sc = $total_gross_sc = $total_profit = 0;
		while ($trans_res = mysqli_fetch_array($file_sql)) {
			$exchange_rate = file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
			$net_sc = $trans_res['booking_net_price'] * $exchange_rate;
			/*$gross_sc=$net_sc+($net_sc*$result[file_markup])/100;
			$gross_sc=ceil($gross_sc);*/
			$gross_sc = $trans_res['booking_gross_price'];
			$profit = $gross_sc - $net_sc;
			$total_net_sc = $total_net_sc + $net_sc;
			$total_gross_sc = $total_gross_sc + $gross_sc;
			$total_profit = $total_profit + $profit;
			$service_location = show_last_tag_name($trans_res['service_location']);
			$service_title = $trans_res['service_title'];
			$booked_with = ucwords(strtolower($trans_res['booked_with_supplier_name']));
			$length_arr['service_title'] = strlen($service_title);
			$length_arr['service_location'] = strlen($service_location);
			$length_arr['booked_with'] = strlen($booked_with);
			$max_key = array_keys($length_arr, max($length_arr));
			$temp_str = $$max_key[0];
			$temp_str = wordwrap($temp_str, 50, "\n");
			$temp_str_arr = explode("\n", $temp_str);
			$temp_str_row = count($temp_str_arr);
			if ($temp_str_row == 1) {
				$ht = 10;
			} else {
				$ht = $temp_str_row * 10;
			}
			$pdf->SetFont('', '', 8);
			$pdf->MultiCell(50, '', pdf_date_format($trans_res['check_in_date']), $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(50, '', pdf_date_format($trans_res['check_out_date']), $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(170, '', $service_title, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(80, '', $service_location, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(105, '', $booked_with, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(70, $ht, $trans_res['supplier_code'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $trans_res['booking_price_currency'] . " " . $trans_res['booking_net_price'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(40, $ht, $exchange_rate, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . display_price($net_sc), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . $gross_sc, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . display_price($profit), 0, 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Ln(2);
		}
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(70, 20, 'Totals', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($total_net_sc), 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . $total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($total_profit), 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$grand_total_net_sc = $grand_total_net_sc + $total_net_sc;
		$grand_total_gross_sc = $grand_total_gross_sc + $total_gross_sc;
		$grand_total_profit = $grand_total_profit + $total_profit;
	}
	/*$file_sql=db_query("select c.*,cr.cruise_name,r.room_name,r.cabin_code,su.supplier_code from mv_file_cruises c left join mv_cruise cr on c.fk_cruise_id=cr.cruise_id left join mv_cruise_rooms r on r.room_id=c.fk_room_id left join mv_supplier su on c.booked_with_supplier_id=su.supplier_id where file_cruise_status='Active' and fk_file_id='$id' order by check_in_date");
if(mysqli_num_rows($file_sql)>0){
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 13);
	$pdf->Cell(790, 10, 'Cruise', 'B', 1, 'L');
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 9);
	$pdf->Cell(50, 10, 'Date', 0, 0, 'L');
	$pdf->Cell(130, 10, 'Cruise Name', 0, 0, 'L');
	$pdf->Cell(140, 10, 'Room Name', 0, 0, 'L');
	$pdf->Cell(135, 10, 'Supplier', 0, 0, 'L');
	$pdf->Cell(70, 10, 'Supplier Ref', 0, 0, 'L');
	$pdf->Cell(55, 10, 'Net', 0, 0, 'L');
	$pdf->Cell(40, 10, 'Ex. Rate', 0, 0, 'L');
	$pdf->Cell(55, 10, 'Net SC', 0, 0, 'L');
	$pdf->Cell(55, 10, 'Gross SC', 0, 0, 'L');
	$pdf->Cell(55, 10, 'Profit', 0, 1, 'L');
	$pdf->Ln(10);
	$total_pax=$result[file_adults]+$result[file_teens]+$result[file_childrens]+$result[file_infants];
	$total_net_sc=$total_gross_sc=$total_profit=$cruise_extras=0;
	while($trans_res=mysqli_fetch_array($file_sql)) {
		$exchange_rate=file_exchnage_rates($id,$trans_res['booking_price_currency'],$result['file_currency']);
		$net_sc=$trans_res['booking_net_price']*$exchange_rate;
		#$gross_sc=$net_sc+($net_sc*$result[file_markup])/100;
		#$gross_sc=ceil($gross_sc);
		$gross_sc=$trans_res['booking_gross_price'];
		$profit=$gross_sc-$net_sc;
		$total_net_sc=$total_net_sc+$net_sc;
		$total_gross_sc=$total_gross_sc+$gross_sc;
		$total_profit=$total_profit+$profit;
		#$cruise_extras=$cruise_extras+($total_pax*($trans_res['port_charge']+$trans_res['fuel_charge']));
		$net_fuel_charge=($total_pax*$trans_res['fuel_charge'])*file_exchnage_rates($id,$trans_res['booking_price_currency'],$result['file_currency']);
		$net_port_charge=($total_pax*$trans_res['port_charge'])*file_exchnage_rates($id,$trans_res['booking_price_currency'],$result['file_currency']);
		$cruise_extras=$cruise_extras+($net_fuel_charge+$net_port_charge);
		$booked_with=ucwords(strtolower($trans_res['booked_with_supplier_name']));
		$length_arr['cruise_name']=strlen($trans_res[cruise_name]);
		$length_arr['room_name']=strlen($trans_res[room_name]);
		$length_arr['booked_with']=strlen($booked_with);
		$max_key=array_keys($length_arr, max($length_arr));
		$temp_str=$$max_key[0];
		$temp_str=wordwrap($temp_str,50, "\n");
		$temp_str_arr=explode("\n", $temp_str);
		$temp_str_row=count($temp_str_arr);
		if($temp_str_row==1){$ht=10;}else{$ht=$temp_str_row*10;}
		$pdf->SetFont('', '', 8);
		$pdf->Cell(50, $ht, pdf_date_format($trans_res['check_in_date']), 0, 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');
		$pdf->MultiCell(130, '', $trans_res[cruise_name], $border=0, $align='L', $fill=false, $ln=0);
		$pdf->MultiCell(140, '', $trans_res[cabin_code].' - '.$trans_res[room_name], $border=0, $align='L', $fill=false, $ln=0);
		$pdf->MultiCell(135, '', $booked_with, $border=0, $align='L', $fill=false, $ln=0);
		$pdf->Cell(70, $ht, $trans_res['supplier_code'], 0, 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');
		$pdf->Cell(55, $ht, $trans_res['booking_price_currency']." ".$trans_res['booking_net_price'], 0, 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');
		$pdf->Cell(40, $ht, $exchange_rate, 0, 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');
		$pdf->Cell(55, $ht, $result['file_currency']." ".display_price($net_sc), 0, 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');
		$pdf->Cell(55, $ht, $result['file_currency']." ".$gross_sc, 0, 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');
		$pdf->Cell(55, $ht, $result['file_currency']." ".display_price($profit), 0, 1, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');
		$pdf->Ln(2);
	}
	$pdf->SetFont('', 'B', 8);
	$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');
	$pdf->Cell(70, 20, 'Totals', 'T', 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='T');
	$pdf->Cell(55, 20, $result['file_currency']." ".display_price($total_net_sc), 'T', 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->Cell(55, 20, $result['file_currency']." ".$total_gross_sc, 'T', 0, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$pdf->Cell(55, 20, $result['file_currency']." ".display_price($total_profit), 'T', 1, 'L', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M');
	$grand_total_net_sc=$grand_total_net_sc+$total_net_sc;
	$grand_total_gross_sc=$grand_total_gross_sc+$total_gross_sc;
	$grand_total_profit=$grand_total_profit+$total_profit;
}*/
	//exit;
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 8);
	$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
	$pdf->Cell(70, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
	$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
	$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($grand_total_net_sc), 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
	$pdf->Cell(55, 20, $result['file_currency'] . " " . $grand_total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
	$pdf->Cell(55, 20, $result['file_currency'] . " " . display_price($grand_total_profit), 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
	#######################  Service Fee & Tax Calculation ###############################
	$total_pax = $result['file_adults'] + $result['file_teens'] + $result['file_childrens'] + $result['file_infants'];
	if ($result['booking_fee_basis'] == "1") {
		$booking_fee = $total_pax * $result['file_booking_charge'];
	} else {
		$booking_fee = $file_booking_charge;
	}
	$other_service_fees_commissionable = 0;
	$other_service_fees_non_commissionable = 0;
	$other_service_fee_sql = db_query("select * from mv_file_service_fees where service_fee_status!='Delete' and fk_file_id='$id' order by service_fee_name");
	while ($other_service_res = mysqli_fetch_array($other_service_fee_sql)) {
		$fee_exchange_rate = file_exchnage_rates($id, $other_service_res['service_fee_currency'], $result['file_currency']);
		if ($other_service_res['service_fee_basis'] == 1) {
			if ($other_service_res['is_commissionable'] == "Yes") {
				$other_service_fees_commissionable = $other_service_fees_commissionable + ($fee_exchange_rate * $other_service_res['service_fee'] * $total_pax);
			} else {
				$other_service_fees_non_commissionable = $other_service_fees_non_commissionable + ($fee_exchange_rate * $other_service_res['service_fee'] * $total_pax);
			}
		} else {
			if ($other_service_res['is_commissionable'] == "Yes") {
				$other_service_fees_commissionable = $other_service_fees_commissionable + ($fee_exchange_rate * $other_service_res['service_fee']);
			} else {
				$other_service_fees_non_commissionable = $other_service_fees_non_commissionable + ($fee_exchange_rate * $other_service_res['service_fee']);
			}
		}
	}
	$gross_total_sc_without_tax = $grand_total_gross_sc;
	$service_fee = $booking_fee + $other_service_fees_commissionable;
	$tax = (($gross_total_sc_without_tax * $result['file_taxes']) / 100) + $other_service_fees_non_commissionable + $cruise_extras;
	$gross_total_sc = $gross_total_sc_without_tax + $tax + $service_fee;
	$card_fee = (($gross_total_sc * $result['file_card_fee']) / 100);
	$gross_total_sc = $gross_total_sc + $card_fee;
	if ($tax == 0) {
		$gross_total_sc = ceil($gross_total_sc);
	}
	################################################################################################
	$pdf->SetFont('', 'B', 13);
	$pdf->Cell(790, 1, 'Totals', 'B', 1, 'L');
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 9);
	$service_subtotal_text = "<table width=100%><tr><td><strong>Services Sub Total</strong></td><td>" . $result['file_currency'] . " " . display_price($gross_total_sc_without_tax) . "</td></tr></table>";
	$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
	$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $service_subtotal_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	if ($service_fee > 0) {
		$service_fee_text = "<table width=100%><tr><td><strong>Service Fees</strong></td><td>" . $result['file_currency'] . " " . $service_fee . "</td></tr></table>";
		$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $service_fee_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	}
	if ($tax > 0) {
		$tax_fee_text = "<table width=100%><tr><td><strong>Taxes</strong></td><td>" . $result['file_currency'] . " " . $tax . "</td></tr></table>";
		$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $tax_fee_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	}
	if ($card_fee > 0) {
		$card_fee_text = "<table width=100%><tr><td><strong>Card Fee</strong></td><td>" . $result['file_currency'] . " " . $card_fee . "</td></tr></table>";
		$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $card_fee_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	}
	if ($service_fee > 0 || $tax > 0 || $card_fee > 0) {
		$total_cost_text = "<table width=100%><tr><td><strong>Services Total</strong></td><td>" . $result['file_currency'] . " " . $gross_total_sc . "</td></tr></table>";
		$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $total_cost_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	}
	if ($result['file_depoist_basis'] == "3") {
		$deposit = (ceil($result['gross_total_sc']) * $result['file_depoist_value']) / 100;
		$deposit_type = $result['file_depoist_value'] . " %";
	} elseif ($result['file_depoist_basis'] == "2") {
		$deposit = $result['file_currency'] . " " . $result['file_depoist_value'];
		$deposit_type = $result['file_currency'] . " " . $result['file_depoist_value'];
	} elseif ($result['file_depoist_basis'] == "1") {
		$deposit = $result['file_depoist_value'] * $total_pax;
		$deposit_type = "Per Pax " . $result['file_currency'] . " " . $result['file_depoist_value'];
	}
	$paymaent_made = db_scalar("select count(*) from mv_file_payment where payment_status!='Delete' and fk_file_id='$id' and payment_made='Yes' and payment_type='1'");
	$var = "<table width=100%>";
	$var .= "<tr><td><b>Deposit</b></td><td>" . $deposit_type;
	if ($paymaent_made == 0) {
		$var .= " (Unpaid)";
	} else {
		$var .= " (Paid)";
	}
	$var .= "</td></tr>";
	if ($result['file_depoist_basis'] == "1" || $result['file_depoist_basis'] == "3") {
		$var .= "<tr><td><b>Deposit Total</b></td><td>" . $result['file_currency'] . " " . display_price($deposit) . "</td></tr>";
	}
	$var .= "</table>";
	$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
	$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $var, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	$pdf->SetFont('', 'B', 13);
	$pdf->Cell(790, 1, 'Profit', 'B', 1, 'L');
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 9);
	$total_agent_commission = 0;
	$subtotal_profit = ($gross_total_sc_without_tax + $service_fee) - $grand_total_net_sc;
	$subtotal_profit = round($subtotal_profit, 2);
	$total_cost_text = "<table width=100%><tr><td><strong>Company Gross Profit</strong></td><td>" . $result['file_currency'] . " " . display_price($subtotal_profit) . "</td></tr></table>";
	$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
	$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $total_cost_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	if ($result['fk_agent_id'] > 0) {
		if ($result['agent_commission_type'] == "1") {
			$total_agent_commission = ($gross_total_sc_without_tax * $result['file_agent_commission']) / 100;
		} else {
			$total_agent_commission = $result['file_agent_commission'];
		}
		$total_agent_commission = $total_agent_commission + $result['additional_agent_comm'];
		$total_agent_commission = round($total_agent_commission, 2);
		$total_cost_text = "<table width=100%><tr><td><strong>Agent Commission</strong></td><td>" . $result['file_currency'] . " " . display_price($total_agent_commission) . "</td></tr></table>";
		$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $total_cost_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	}
	$total_profit = $subtotal_profit - $total_agent_commission;
	if ($file_card_fee <= 0) {
		$card_fee = (($gross_total_sc * 5) / 100);
		$total_profit = $total_profit - $card_fee;
		$card_fee_text = "<table width=100%><tr><td><strong>Card Fee (5%)</strong></td><td>" . $result['file_currency'] . " " . display_price($card_fee) . "</td></tr></table>";
		$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $card_fee_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	}
	if ($file_taxes <= 0) {
		$tax = (($total_profit * 12) / 100);
		$total_profit = $total_profit - $tax;
		$tax_fee_text = "<table width=100%><tr><td><strong>Taxes(12%)</strong></td><td>" . $result['file_currency'] . " " . display_price($tax) . "</td></tr></table>";
		$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $tax_fee_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	}
	$total_cost_text = "<table width=100%><tr><td><strong>Company NET Profit</strong></td><td>" . $result['file_currency'] . " " . display_price($total_profit) . "</td></tr></table>";
	$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
	$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $total_cost_text, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 13);
	$pdf->Cell(790, 10, 'Supplier Totals', 'B', 1, 'L');
	$stotal = get_suppliers_total($id);
	$var = "<table width=100%>";
	$var .= $stotal;
	$var .= "</table>";
	$pdf->SetFont('', 'B', 8);
	$pdf->Cell(455, 20, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
	$pdf->writeHTMLCell('330', $h = 0, $x = '', $y = '', $var, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
	//Close and output PDF document
	$my_file_name = UP_FILES_FS_PATH . "/pricing/pricingsummary_" . $result['file_code'] . "_" . date("Y-m-d_H-i", time()) . ".pdf";
	$pdf->Output($my_file_name, 'F');
	return $my_file_name;
}
function show_booked_by($file_id, $paid_by, $paid_by_other, $lead_pax, $supplier_name)
{
	global $ARR_PAID_BY;
	$paid_by = 1;
	if ($paid_by == "1") {
		echo $ARR_PAID_BY[$paid_by];
	} elseif ($paid_by == "4") {
		return $paid_by_other;
	} elseif ($paid_by == "5") {
		return $supplier_name;
	} elseif ($paid_by == "2") {
		return $lead_pax;
	} elseif ($paid_by == "3") {
		return db_scalar("select agency_name from mv_files f inner join mv_agent a on f.fk_agent_id=a.agent_id inner join mv_agency ag on a.fk_agency_id=ag.agency_id where file_id = '$file_id'");
	}
}
function xlsBOF()
{
	echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
	return;
}
function xlsEOF()
{
	echo pack("ss", 0x0a, 0x00);
	return;
}
function xlsWriteNumber($Row, $Col, $Value)
{
	echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
	echo pack("d", $Value);
	return;
}
function xlsWriteLabel($Row, $Col, $Value)
{
	$L = strlen($Value);
	echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
	echo $Value;
	return;
}
function create_invoice_pdf($id)
{
	global $arr_tour_classification;
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/config/lang/eng.php');
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/tcpdf.php');
	$result = db_result("select f.*,a.*,ag.*,concat(a.agent_first_name,' ',a.agent_last_name) as agent_name,concat(c.client_first_name,' ',c.client_last_name) as client_name,concat(e.emp_first_name,' ',e.emp_last_name) as consultant_name from mv_files f left join mv_agent a on f.fk_agent_id=a.agent_id  left join mv_client c on f.fk_client_id=c.client_id left join mv_employee e on f.file_primary_staff=e.emp_id left join mv_agency ag on a.fk_agency_id=ag.agency_id where file_id = '$id'");
	class MYPDF extends TCPDF
	{
		//Page header
		public function Header()
		{
			//$this->Cell(790, 1, '', 0, 1, 'C', 0, '', 1);
		}
		// Page footer
		public function Footer()
		{
			$this->SetFont('helvetica', 'B', 8);
			$this->Cell(125, 5, 'Page: ' . $this->PageNo() . '', 0, 0, 'C', 0, '', 1);
			$this->Cell(140, 1, 'Printed: ' . date("j F Y, g:i A") . ' ', 0, 1, 'R', 0, '', 1);
		}
	}
	$margin_left = 25;
	$margin_top = 25;
	$margin_right = 25;
	$margin_bottom = 5;
	$pdf = new MYPDF('L', 'px', 'A4', true);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor(SITE_WS_PATH);
	$pdf->SetTitle('Invoice');
	$pdf->SetSubject("Invoice - File " . $result['file_code']);
	$pdf->SetKeywords("Invoice - File " . $result['file_code']);
	// set default header data
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	// set header and footer fonts
	$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	//set margins
	$pdf->SetMargins($margin_left, $margin_top);
	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetHeaderMargin(25);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	//set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, $margin_bottom);
	//set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	//set some language-dependent strings
	$pdf->setLanguageArray($l);
	//initialize document
	$pdf->AliasNbPages();
	// set font
	$pdf->SetFont('', '', 10);
	// add a page
	$pdf->AddPage();
	$pdf->Image(SITE_FS_PATH . '/' . AGENT_ADMIN_DIR . '/images/logo.gif', 25, '', 200, '', '', '', '', true);
	$pdf->SetFont('helvetica', 'B', 13);
	if ($result['agent_id'] > 0) {
		$var = "";
		if ($result['agency_name'] != "") {
			$var .= $result['agency_name'];
		}
		if ($result['fk_agency_id'] > 0) {
			if ($result['agency_address'] != "") {
				$var .= "<br />" . nl2br($result['agency_address']);
			}
			if ($result['agency_city'] != "" || $result['agency_state'] != "") {
				$var .= "<br />";
				if ($result['agency_city'] != "") {
					$var .= $result['agency_city'];
				}
				if ($result['agency_city'] != "" && $result['agency_state'] != "") {
					$var .= ", ";
				}
				if ($result['agency_state'] != "") {
					$var .= $result['agency_state'];
				}
			}
			if ($result['agency_country'] != "" || $result['agency_zipcode'] != "") {
				$var .= "<br />";
				if ($result['agency_zipcode'] != "") {
					$var .= $result['agency_zipcode'];
				}
				if ($result['agency_country'] != "" && $result['agency_zipcode'] != "") {
					$var .= ", ";
				}
				if ($result['agency_country'] != "") {
					$var .= $result['agency_country'];
				}
			}
		} else {
			if ($result['agent_address'] != "") {
				$var .= "<br />" . nl2br($result['agent_address']);
			}
			if ($result['agent_city'] != "" || $result['agent_state'] != "") {
				$var .= "<br />";
				if ($result['agent_city'] != "") {
					$var .= $result['agent_city'];
				}
				if ($result['agent_city'] != "" && $result['agent_state'] != "") {
					$var .= ", ";
				}
				if ($result['agent_state'] != "") {
					$var .= $result['agent_state'];
				}
			}
			if ($result['agent_country'] != "" || $result['agent_zipcode'] != "") {
				$var .= "<br />";
				if ($result['agent_zipcode'] != "") {
					$var .= $result['agent_zipcode'];
				}
				if ($result['agent_country'] != "" && $result['agent_zipcode'] != "") {
					$var .= ", ";
				}
				if ($result['agent_country'] != "") {
					$var .= $result['agent_country'];
				}
			}
		}
		$phone_var = "";
		if ($result['agent_phone'] != "") {
			$phone_var .= 'Tel : ' . user_phone_number_display($result['agent_phone'], $result['agent_phone_ext']) . "     ";
		}
		if ($result['agent_fax'] != "") {
			$phone_var .= 'Fax : ' . user_phone_number_display($result['agent_fax']);
		}
		$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
		$pdf->Cell(260, 1, $result['agent_name'], 0, 1, 'L', 0, '', 1);
		$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
		$pdf->SetFont('', '', 10);
		$pdf->writeHTMLCell('260', $h = 0, $x = '', $y = '', $var, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = false);
		$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
		$pdf->Cell(260, 1, $phone_var, 0, 1, 'L', 0, '', 1);
		$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
		$pdf->Cell(260, 1, $result['agent_email'], 0, 1, 'L', 0, '', 1);
	} else {
		$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
		$pdf->Cell(260, 1, 'Italy Vacation Specialists', 0, 1, 'L', 0, '', 1);
		$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
		$pdf->SetFont('', '', 10);
		$pdf->Cell(260, 1, 'Via Degli Aranci 25/b, C.P. 80067, Sorrento (NA) – ITALY ', 0, 1, 'L', 0, '', 1);
		$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
		$pdf->Cell(260, 1, 'Tel : +39 392-993-3869     Fax : +1-800-393-810', 0, 1, 'L', 0, '', 1);
		$pdf->Cell(530, 1, '', 0, 0, 'C', 0, '', 1);
		$pdf->Cell(260, 1, 'Email : jessica@italyvacationspecialists.com', 0, 1, 'L', 0, '', 1);
	}
	$pdf->Cell(790, 10, '', 'B', 1, 'L');
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(150, 10, $result['file_code'], 0, 0, 'L');
	if ($result['agent_name'] != "") {
		$pdf->Cell(37, 10, 'Agent:', 0, 0, 'L');
		$pdf->SetFont('', '', 10);
		$pdf->Cell(170, 10, $result['agent_name'], 0, 0, 'L');
	}
	if ($result['client_name'] != "") {
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(37, 10, 'Client:', 0, 0, 'L');
		$pdf->SetFont('', '', 10);
		$pdf->Cell(170, 10, $result['client_name'], 0, 0, 'L');
	}
	if ($result['consultant_name'] != "") {
		$pdf->SetFont('', 'B', 10);
		$pdf->Cell(60, 10, 'Consultant:', 0, 0, 'L');
		$pdf->SetFont('', '', 10);
		$pdf->Cell(170, 10, $result['consultant_name'], 0, 1, 'L');
	}
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(37, 10, 'Adults:', 0, 0, 'L');
	$pdf->SetFont('', '', 10);
	$pdf->Cell(30, 10, $result['file_adults'], 0, 0, 'L');
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(34, 10, 'Teen:', 0, 0, 'L');
	$pdf->SetFont('', '', 10);
	$pdf->Cell(30, 10, $result['file_teens'], 0, 0, 'L');
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(45, 10, 'Children:', 0, 0, 'L');
	$pdf->SetFont('', '', 10);
	$pdf->Cell(30, 10, $result['file_childrens'], 0, 0, 'L');
	$pdf->SetFont('', 'B', 10);
	$pdf->Cell(37, 10, 'Infant:', 0, 0, 'L');
	$pdf->SetFont('', '', 10);
	$pdf->Cell(30, 10, $result['file_infants'], 0, 1, 'L');
	$pdf->Ln(10);
	$file_sql = db_query("select f.*,s.supplier_company_name,s.supplier_code,s.location_tags_name,r.room_name,r.room_occupancy_type,meal_plan.tag_name as meal_plan  from mv_file_accommodation f left join mv_supplier s on f.fk_hotel_id=s.supplier_id left join mv_hotel_rooms r on f.fk_room_id=r.room_id left join mv_room_detail d on d.room_detail_id=r.fk_room_detail_id left join mv_tags as meal_plan on meal_plan.tag_id=d.room_detail_meal_plan where file_accommodation_status='Active' and fk_file_id='$id' order by check_in_date");
	if (mysqli_num_rows($file_sql) > 0) {
		$pdf->SetFont('', 'B', 13);
		$pdf->Cell(790, 10, 'Accommodation', 'B', 1, 'L');
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 9);
		$pdf->Cell(50, 10, 'Check In', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Check Out', 0, 0, 'L');
		$pdf->Cell(85, 10, 'Name', 0, 0, 'L');
		$pdf->Cell(85, 10, 'Room', 0, 0, 'L');
		$pdf->Cell(35, 10, 'Qty', 0, 0, 'L');
		$pdf->Cell(60, 10, 'Occupancy', 0, 0, 'L');
		$pdf->Cell(85, 10, '', 0, 0, 'L');
		$pdf->Cell(70, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 0, 'L');
		$pdf->Cell(40, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Gross SC', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 1, 'L');
		$pdf->Ln(10);
		$grand_total_net_sc = 0;
		$grand_total_gross_sc = 0;
		$grand_total_profit = 0;
		while ($trans_res = mysqli_fetch_array($file_sql)) {
			$exchange_rate = file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
			$net_sc = $trans_res['booking_net_price'] * $exchange_rate;
			/*$gross_sc=$net_sc+($net_sc*$result[file_markup])/100;
				$gross_sc=ceil($gross_sc);*/
			$gross_sc = $trans_res['booking_gross_price'];
			$profit = $gross_sc - $net_sc;
			$total_net_sc = $total_net_sc + $net_sc;
			$total_gross_sc = $total_gross_sc + $gross_sc;
			$total_profit = $total_profit + $profit;
			$hotel_name = $trans_res['supplier_company_name'];
			$room_name = $trans_res['room_name'];
			$booked_with = ucwords(strtolower($trans_res['booked_with_supplier_name']));
			$length_arr['hotel_name'] = strlen($hotel_name);
			$length_arr['room_name'] = strlen($room_name);
			$length_arr['booked_with'] = strlen($booked_with);
			$max_key = array_keys($length_arr, max($length_arr));
			$temp_str = $$max_key[0];
			$temp_str = wordwrap($temp_str, 20, "\n");
			$temp_str_arr = explode("\n", $temp_str);
			$temp_str_row = count($temp_str_arr);
			if ($temp_str_row == 1) {
				$ht = 10;
			} else {
				$ht = $temp_str_row * 10;
			}
			$nights = $trans_res['booking_duration'];
			$pdf->SetFont('', '', 8);
			$pdf->Cell(50, $ht, pdf_date_format($trans_res['check_in_date']), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, pdf_date_format($trans_res['check_out_date']), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->MultiCell(85, '', $hotel_name, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(85, '', $room_name, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(35, $ht, $trans_res['room_quantity'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->MultiCell(60, '', $trans_res['room_occupancy_type'], $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(85, '', '', $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(70, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(40, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . $gross_sc, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Ln(2);
		}
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(70, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(55, 20, 'Totals', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . $total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, '', 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$grand_total_net_sc = $grand_total_net_sc + $total_net_sc;
		$grand_total_gross_sc = $grand_total_gross_sc + $total_gross_sc;
		$grand_total_profit = $grand_total_profit + $total_profit;
	}
	$file_sql = db_query("select a.*,t.*,s.supplier_code,t1.tag_name as type  from mv_file_activity a left join mv_tours t on a.fk_tour_id=t.tour_id left join mv_supplier s on a.booked_with_supplier_id=s.supplier_id left join mv_tags t1 on t1.tag_id=t.tour_type where file_activity_status='Active' and fk_file_id='$id' order by check_in_date");
	if (mysqli_num_rows($file_sql) > 0) {
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 13);
		$pdf->Cell(790, 10, 'Activity', 'B', 1, 'L');
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 9);
		$pdf->Cell(50, 10, 'Date', 0, 0, 'L');
		$pdf->Cell(65, 10, 'Classification', 0, 0, 'L');
		$pdf->Cell(145, 10, 'Name', 0, 0, 'L');
		$pdf->Cell(60, 10, 'Grouping', 0, 0, 'L');
		$pdf->Cell(135, 10, '', 0, 0, 'L');
		$pdf->Cell(70, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 0, 'L');
		$pdf->Cell(40, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Gross SC', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 1, 'L');
		$pdf->Ln(10);
		$total_net_sc = $total_gross_sc = $total_profit = 0;
		while ($trans_res = mysqli_fetch_array($file_sql)) {
			$exchange_rate = file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
			$net_sc = $trans_res['booking_net_price'] * $exchange_rate;
			/*$gross_sc=$net_sc+($net_sc*$result[file_markup])/100;
				$gross_sc=ceil($gross_sc);*/
			$gross_sc = $trans_res['booking_gross_price'];
			$profit = $gross_sc - $net_sc;
			$total_net_sc = $total_net_sc + $net_sc;
			$total_gross_sc = $total_gross_sc + $gross_sc;
			$total_profit = $total_profit + $profit;
			$tour_name = $trans_res['tour_name'];
			$booked_with = ucwords(strtolower($trans_res['booked_with_supplier_name']));
			$length_arr['tour_name'] = strlen($tour_name);
			$length_arr['booked_with'] = strlen($booked_with);
			$max_key = array_keys($length_arr, max($length_arr));
			$temp_str = $$max_key[0];
			$temp_str = wordwrap($temp_str, 40, "\n");
			$temp_str_arr = explode("\n", $temp_str);
			$temp_str_row = count($temp_str_arr);
			if ($temp_str_row == 1) {
				$ht = 10;
			} else {
				$ht = $temp_str_row * 10;
			}
			$pdf->SetFont('', '', 8);
			$pdf->Cell(50, $ht, pdf_date_format($trans_res['check_in_date']), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(65, $ht, $arr_tour_classification[$trans_res['tour_classification']], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->MultiCell(145, '', $tour_name, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(60, '', $trans_res['type'], $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(135, '', '', $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(70, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(40, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . $gross_sc, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Ln(2);
		}
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(70, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(55, 20, 'Totals', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . $total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, '', 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$grand_total_net_sc = $grand_total_net_sc + $total_net_sc;
		$grand_total_gross_sc = $grand_total_gross_sc + $total_gross_sc;
		$grand_total_profit = $grand_total_profit + $total_profit;
	}
	$file_sql = db_query("select t.*,s.supplier_code,t7.tag_name as country_name,t1.tag_name as booking_type,t2.tag_name as location_name,t3.tag_name as pickup,t4.tag_name as dropoff,t5.tag_name as pickup_station,t6.tag_name as dropoff_station ,t8.airport_title as departure_airport,t9.airport_title as arrival_airport from mv_file_transfers t left join mv_tags t1 on t1.tag_id=t.file_booking_type left join mv_tags t2 on t2.tag_id=t.fk_region_id left join mv_tags t3 on t3.tag_id=t.fk_pickup_id left join mv_tags t4 on t4.tag_id=t.fk_dropoff_id left join mv_tags t5 on t5.tag_id=t.fk_pickup_station_id left join mv_tags t6 on t6.tag_id=t.fk_dropoff_station_id left join mv_tags t7 on t7.tag_id=t.transfer_country left join mv_location_airport t8 on t8.airport_id=t.fk_dep_airport_id left join mv_location_airport t9 on t9.airport_id=t.fk_arrival_airport_id left join mv_supplier s on t.booked_with_supplier_id=s.supplier_id where file_transfer_status='Active' and fk_file_id='$id' order by check_in_date");
	if (mysqli_num_rows($file_sql) > 0) {
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 13);
		$pdf->Cell(790, 10, 'Transportation', 'B', 1, 'L');
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 9);
		$pdf->Cell(50, 10, 'Date', 0, 0, 'L');
		$pdf->Cell(45, 10, 'Type', 0, 0, 'L');
		$pdf->Cell(65, 10, 'Location', 0, 0, 'L');
		$pdf->Cell(105, 10, 'Departure', 0, 0, 'L');
		$pdf->Cell(105, 10, 'Destination', 0, 0, 'L');
		$pdf->Cell(85, 10, '', 0, 0, 'L');
		$pdf->Cell(70, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 0, 'L');
		$pdf->Cell(40, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Gross SC', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 1, 'L');
		$pdf->Ln(10);
		$total_net_sc = $total_gross_sc = $total_profit = 0;
		while ($trans_res = mysqli_fetch_array($file_sql)) {
			$exchange_rate = file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
			$net_sc = $trans_res['booking_net_price'] * $exchange_rate;
			$gross_sc = $trans_res['booking_gross_price'];
			/*$gross_sc=$net_sc+($net_sc*$result[file_markup])/100;
			$gross_sc=ceil($gross_sc);*/
			$profit = $gross_sc - $net_sc;
			$total_net_sc = $total_net_sc + $net_sc;
			$total_gross_sc = $total_gross_sc + $gross_sc;
			$total_profit = $total_profit + $profit;
			if ($trans_res['file_booking_type'] == "221") {
				$transfer_location_name = show_last_tag_name($trans_res['pickup_city']);
			} elseif ($trans_res['file_booking_type'] == "220") {
				$transfer_location_name = $trans_res['country_name'];
			} else {
				$transfer_location_name = $trans_res['location_name'];
			}
			if ($trans_res['file_booking_type'] == "221") {
				$transfer_pickup = show_last_tag_name($trans_res['pickup_city']);
			} else {
				$transfer_pickup = $trans_res['pickup'];
				if ($trans_res['pickup_station'] != "") {
					$transfer_pickup .= " - " . $trans_res['pickup_station'];
				}
				if ($trans_res['departure_airport'] != "") {
					$transfer_pickup .= " - " . $trans_res['departure_airport'];
				}
			}
			if ($trans_res['file_booking_type'] == "221") {
				$transfer_dropoff = show_last_tag_name($trans_res['dropoff_city']);
			} else {
				$transfer_dropoff = $trans_res['dropoff'];
				if ($trans_res['dropoff_station'] != "") {
					$transfer_dropoff .= " - " . $trans_res['dropoff_station'];
				}
				if ($trans_res['arrival_airport'] != "") {
					$transfer_dropoff .= " - " . $trans_res['arrival_airport'];
				}
			}
			$booked_with = ucwords(strtolower($trans_res['booked_with_supplier_name']));
			$length_arr['transfer_location_name'] = strlen($transfer_location_name);
			$length_arr['transfer_pickup'] = strlen($transfer_pickup);
			$length_arr['transfer_dropoff'] = strlen($transfer_dropoff);
			$length_arr['booked_with'] = strlen($booked_with);
			$max_key = array_keys($length_arr, max($length_arr));
			$temp_str = $$max_key[0];
			$temp_str = wordwrap($temp_str, 25, "\n");
			$temp_str_arr = explode("\n", $temp_str);
			$temp_str_row = count($temp_str_arr);
			if ($temp_str_row == 1) {
				$ht = 10;
			} else {
				$ht = $temp_str_row * 10;
			}
			$pdf->SetFont('', '', 8);
			$pdf->Cell(50, $ht, pdf_date_format($trans_res['check_in_date']), 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(45, $ht, $trans_res['booking_type'], 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->MultiCell(65, '', $transfer_location_name, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(105, '', $transfer_pickup, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(105, '', $transfer_dropoff, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(85, '', '', $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(70, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(40, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . $gross_sc, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Ln(2);
		}
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(70, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(55, 20, 'Totals', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . $total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, '', 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$grand_total_net_sc = $grand_total_net_sc + $total_net_sc;
		$grand_total_gross_sc = $grand_total_gross_sc + $total_gross_sc;
		$grand_total_profit = $grand_total_profit + $total_profit;
	}
	$file_sql = db_query("select s.*,su.supplier_code  from mv_file_misc_service s left join mv_supplier su on s.booked_with_supplier_id=su.supplier_id  where misc_service_status='Active' and fk_file_id='$id' order by service_title");
	if (mysqli_num_rows($file_sql) > 0) {
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 13);
		$pdf->Cell(790, 10, 'Misc Services', 'B', 1, 'L');
		$pdf->Ln(10);
		$pdf->SetFont('', 'B', 9);
		$pdf->Cell(250, 10, 'Service Name', 0, 0, 'L');
		$pdf->Cell(100, 10, 'Location', 0, 0, 'L');
		$pdf->Cell(105, 10, '', 0, 0, 'L');
		$pdf->Cell(70, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 0, 'L');
		$pdf->Cell(40, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 0, 'L');
		$pdf->Cell(55, 10, 'Gross SC', 0, 0, 'L');
		$pdf->Cell(55, 10, '', 0, 1, 'L');
		$pdf->Ln(10);
		$total_net_sc = $total_gross_sc = $total_profit = 0;
		while ($trans_res = mysqli_fetch_array($file_sql)) {
			$exchange_rate = file_exchnage_rates($id, $trans_res['booking_price_currency'], $result['file_currency']);
			$net_sc = $trans_res['booking_net_price'] * $exchange_rate;
			/*$gross_sc=$net_sc+($net_sc*$result[file_markup])/100;
		$gross_sc=ceil($gross_sc);*/
			$gross_sc = $trans_res['booking_gross_price'];
			$profit = $gross_sc - $net_sc;
			$total_net_sc = $total_net_sc + $net_sc;
			$total_gross_sc = $total_gross_sc + $gross_sc;
			$total_profit = $total_profit + $profit;
			$service_location = show_last_tag_name($trans_res['service_location']);
			$service_title = $trans_res['service_title'];
			$booked_with = ucwords(strtolower($trans_res['booked_with_supplier_name']));
			$length_arr['service_title'] = strlen($service_title);
			$length_arr['service_location'] = strlen($service_location);
			$length_arr['booked_with'] = strlen($booked_with);
			$max_key = array_keys($length_arr, max($length_arr));
			$temp_str = $$max_key[0];
			$temp_str = wordwrap($temp_str, 50, "\n");
			$temp_str_arr = explode("\n", $temp_str);
			$temp_str_row = count($temp_str_arr);
			if ($temp_str_row == 1) {
				$ht = 10;
			} else {
				$ht = $temp_str_row * 10;
			}
			$pdf->SetFont('', '', 8);
			$pdf->MultiCell(250, '', $service_title, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(100, '', $service_location, $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->MultiCell(105, '', '', $border = 0, $align = 'L', $fill = false, $ln = 0);
			$pdf->Cell(70, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(40, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, $result['file_currency'] . " " . $gross_sc, 0, 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Cell(55, $ht, '', 0, 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
			$pdf->Ln(2);
		}
		$pdf->SetFont('', 'B', 8);
		$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(70, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
		$pdf->Cell(55, 20, 'Totals', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, $result['file_currency'] . " " . $total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$pdf->Cell(55, 20, '', 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
		$grand_total_net_sc = $grand_total_net_sc + $total_net_sc;
		$grand_total_gross_sc = $grand_total_gross_sc + $total_gross_sc;
		$grand_total_profit = $grand_total_profit + $total_profit;
	}
	$pdf->Ln(10);
	$pdf->SetFont('', 'B', 8);
	$pdf->Cell(455, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
	$pdf->Cell(70, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
	$pdf->Cell(95, 20, '', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'T');
	$pdf->Cell(55, 20, 'Sub Total', 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
	$pdf->Cell(55, 20, $result['file_currency'] . " " . $grand_total_gross_sc, 'T', 0, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
	$pdf->Cell(55, 20, '', 'T', 1, 'L', $fill = false, $link = '', $stretch = 0, $ignore_min_height = false, $calign = 'T', $valign = 'M');
	//Close and output PDF document
	$my_file_name = UP_FILES_FS_PATH . "/invoice/invoice_" . $result['file_code'] . "_" . date("Y-m-d_H-i", time()) . ".pdf";
	$pdf->Output($my_file_name, 'F');
	return $my_file_name;
}
function reset_net_price($id, $selected_only = 'No', $accom_reset_list = '', $transfer_reset_list = '', $activity_reset_list = '', $misc_reset_list = '', $cruise_reset_list = '')
{
	$result = db_result("select * from mv_files  where file_id = '$id'");
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_transfer_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_transfers where fk_file_id = '$id'");
	} elseif ($transfer_reset_list != "") {
		$reset_sql = db_query("select file_transfer_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_transfers where file_transfer_id IN ($transfer_reset_list)");
	}
	if ($selected_only == "No" || $transfer_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_transfer_id'];
			$sql_for_gross = "";
			if ($reset_res['gross_price_edited'] == "No") {
				$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
				$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
				$booking_gross_price = ceil($gross_sc);
				$sql_for_gross = " ,booking_gross_price= '$booking_gross_price' ";
			}
			//db_query("update mv_file_transfers SET  booking_net_price= booking_original_net_price,net_price_edited='No' $sql_for_gross where file_transfer_id = '$record_id'");
			// this function to reset (need confirmation from accounting team)
			$net_price = $reset_res['booking_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
			db_query("UPDATE mv_file_transfers SET
			booking_net_price= '$net_price',
			net_price_edited='No'
			$sql_for_gross
			where file_transfer_id = '$record_id'");
		}
	}
	###############################################################################
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_cruise_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_cruises where fk_file_id = '$id'");
	} elseif ($cruise_reset_list != "") {
		$reset_sql = db_query("select file_cruise_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_cruises where file_cruise_id IN ($cruise_reset_list)");
	}
	if ($selected_only == "No" || $cruise_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_cruise_id'];
			$sql_for_gross = "";
			if ($reset_res['gross_price_edited'] == "No") {
				$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
				$total_paying_pax = $result['file_adults'] + $result['file_teens'] + $result['file_childrens'] + $result['file_infants'];
				/*
					$extra_charges=$reset_res['fuel_charge']+$reset_res['port_charge'];
					$extra_charges=$total_paying_pax*$extra_charges;
					$extra_charges_sc=$extra_charges*file_exchnage_rates($id,$reset_res['booking_price_currency'],$result['file_currency']);
					$net_sc_plus_extra=$net_sc+$extra_charges_sc;
					$gross_sc=$net_sc_plus_extra+($net_sc_plus_extra*$result[file_markup])/100;
					*/
				$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
				$booking_gross_price = ceil($gross_sc);
				$sql_for_gross = " ,booking_gross_price= '$booking_gross_price' ";
			}
			db_query("update mv_file_cruises SET  booking_net_price= booking_original_net_price,net_price_edited='No' $sql_for_gross where file_cruise_id = '$record_id'");
		}
	}
	###############################################################################
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_accommodation_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_accommodation where fk_file_id = '$id'");
	} elseif ($accom_reset_list != "") {
		$reset_sql = db_query("select file_accommodation_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_accommodation where file_accommodation_id IN ($accom_reset_list)");
	}
	if ($selected_only == "No" || $accom_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_accommodation_id'];
			$sql_for_gross = "";
			if ($reset_res['gross_price_edited'] == "No") {
				$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
				$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
				$booking_gross_price = ceil($gross_sc);
				$sql_for_gross = " ,booking_gross_price= '$booking_gross_price' ";
			}
			// this function to reset (need confirmation from accounting team)
			// $reset_res['booking_price'] is the original price before any changes
			$net_price = $reset_res['booking_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
			db_query("UPDATE mv_file_accommodation SET
			booking_net_price= '$net_price',
			net_price_edited='No'
			$sql_for_gross
			where file_accommodation_id = '$record_id'");
		}
	}
	################################################################################
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_activity_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_activity where fk_file_id = '$id'");
	} elseif ($activity_reset_list != "") {
		$reset_sql = db_query("select file_activity_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_activity where file_activity_id IN ($activity_reset_list)");
	}
	if ($selected_only == "No" || $activity_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_activity_id'];
			$sql_for_gross = "";
			if ($reset_res['gross_price_edited'] == "No") {
				$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
				$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
				$booking_gross_price = ceil($gross_sc);
				$sql_for_gross = " ,booking_gross_price= '$booking_gross_price' ";
			}
			// this function to reset (need confirmation from accounting team)
			// $reset_res['booking_price'] is the original price before any changes
			$net_price = $reset_res['booking_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
			db_query("UPDATE mv_file_activity SET
					booking_net_price = '$net_price',
					net_price_edited = 'No'
					$sql_for_gross
					WHERE file_activity_id = '$record_id'");
		}
	}
	#######################################################################################
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_misc_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_misc_service where fk_file_id = '$id'");
	} elseif ($misc_reset_list != "") {
		$reset_sql = db_query("select file_misc_id,booking_net_price,booking_price_currency,gross_price_edited from mv_file_misc_service where file_misc_id IN ($misc_reset_list)");
	}
	if ($selected_only == "No" || $misc_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_misc_id'];
			$sql_for_gross = "";
			if ($reset_res['gross_price_edited'] == "No") {
				$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
				$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
				$booking_gross_price = ceil($gross_sc);
				$sql_for_gross = " ,booking_gross_price= '$booking_gross_price' ";
			}
			// db_query("update mv_file_misc_service SET booking_net_price= booking_original_net_price,net_price_edited='No' $sql_for_gross where file_misc_id = '$record_id'");
			// this function to reset (need confirmation from accounting team)
			// $reset_res['booking_price'] is the original price before any changes
			$net_price = $reset_res['booking_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
			db_query("UPDATE mv_file_misc_service SET
					booking_net_price = '$net_price',
					net_price_edited = 'No'
					$sql_for_gross
					WHERE file_misc_id = '$record_id'");
		}
	}
}
function reset_gross_price($id, $reset_all = 'Yes', $selected_only = 'No', $accom_reset_list = '', $transfer_reset_list = '', $activity_reset_list = '', $misc_reset_list = '', $cruise_reset_list = '')
{
	$result = db_result("select * from mv_files  where file_id = '$id'");
	$sql_to_add = '';
	if ($reset_all != 'Yes') {
		$sql_to_add .= " and gross_price_edited='No' ";
	}
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_transfer_id,booking_net_price,booking_price_currency from mv_file_transfers where fk_file_id = '$id' $sql_to_add");
	} elseif ($transfer_reset_list != "") {
		$reset_sql = db_query("select file_transfer_id,booking_net_price,booking_price_currency from mv_file_transfers where file_transfer_id IN ($transfer_reset_list) $sql_to_add");
	}
	if ($selected_only == "No" || $transfer_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_transfer_id'];
			$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
			$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
			$booking_gross_price = ceil($gross_sc);
			db_query("update mv_file_transfers SET  booking_gross_price= '$booking_gross_price',gross_price_edited='No' where file_transfer_id = '$record_id'");
		}
	}
	###############################################################################
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_cruise_id,booking_net_price,booking_price_currency from mv_file_cruises where fk_file_id = '$id' $sql_to_add");
	} elseif ($cruise_reset_list != "") {
		$reset_sql = db_query("select file_cruise_id,booking_net_price,booking_price_currency from mv_file_cruises where file_cruise_id IN ($cruise_reset_list) $sql_to_add");
	}
	if ($selected_only == "No" || $cruise_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_cruise_id'];
			$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
			$total_paying_pax = $result['file_adults'] + $result['file_teens'] + $result['file_childrens'] + $result['file_infants'];
			/*$extra_charges=$reset_res['fuel_charge']+$reset_res['port_charge'];
				$extra_charges=$total_paying_pax*$extra_charges;
				$extra_charges_sc=$extra_charges*file_exchnage_rates($id,$reset_res['booking_price_currency'],$result['file_currency']);
				$net_sc_plus_extra=$net_sc+$extra_charges_sc;
				$gross_sc=$net_sc_plus_extra+($net_sc_plus_extra*$result[file_markup])/100;
				*/
			$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
			$booking_gross_price = ceil($gross_sc);
			db_query("update mv_file_cruises SET  booking_gross_price= '$booking_gross_price',gross_price_edited='No' where file_cruise_id = '$record_id'");
		}
	}
	################################################################################
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_accommodation_id,booking_net_price,booking_price_currency from mv_file_accommodation where fk_file_id = '$id' $sql_to_add");
	} elseif ($accom_reset_list != "") {
		$reset_sql = db_query("select file_accommodation_id,booking_net_price,booking_price_currency from mv_file_accommodation where file_accommodation_id IN ($accom_reset_list) $sql_to_add");
	}
	if ($selected_only == "No" || $accom_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_accommodation_id'];
			$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
			$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
			$booking_gross_price = ceil($gross_sc);
			db_query("update mv_file_accommodation SET  booking_gross_price= '$booking_gross_price',gross_price_edited='No' where file_accommodation_id = '$record_id'");
		}
	}
	#################################################################
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_activity_id,booking_net_price,booking_price_currency from mv_file_activity where fk_file_id = '$id' $sql_to_add");
	} elseif ($activity_reset_list != "") {
		$reset_sql = db_query("select file_activity_id,booking_net_price,booking_price_currency from mv_file_activity where file_activity_id IN ($activity_reset_list) $sql_to_add");
	}
	if ($selected_only == "No" || $activity_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_activity_id'];
			$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
			$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
			$booking_gross_price = ceil($gross_sc);
			db_query("update mv_file_activity SET  booking_gross_price= '$booking_gross_price',gross_price_edited='No' where file_activity_id = '$record_id'");
		}
	}
	##############################################################################
	if ($selected_only == "No") {
		$reset_sql = db_query("select file_misc_id,booking_net_price,booking_price_currency from mv_file_misc_service where fk_file_id = '$id' $sql_to_add");
	} elseif ($misc_reset_list != "") {
		$reset_sql = db_query("select file_misc_id,booking_net_price,booking_price_currency from mv_file_misc_service where file_misc_id IN ($misc_reset_list) $sql_to_add");
	}
	if ($selected_only == "No" || $misc_reset_list != "") {
		while ($reset_res = mysqli_fetch_array($reset_sql)) {
			$record_id = $reset_res['file_misc_id'];
			$net_sc = $reset_res['booking_net_price'] * file_exchnage_rates($id, $reset_res['booking_price_currency'], $result['file_currency']);
			$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
			$booking_gross_price = ceil($gross_sc);
			db_query("update mv_file_misc_service SET  booking_gross_price= '$booking_gross_price',gross_price_edited='No' where file_misc_id = '$record_id'");
		}
	}
}
function get_file_currency_list($file_id)
{
	$transfer_currency_list = db_scalar("select group_concat(booking_price_currency) from mv_file_transfers where fk_file_id = '$file_id' and file_transfer_status='Active'");
	$accom_currency_list = db_scalar("select group_concat(booking_price_currency) from mv_file_accommodation where fk_file_id = '$file_id' and file_accommodation_status='Active'");
	$activity_currency_list = db_scalar("select group_concat(booking_price_currency) from mv_file_activity where fk_file_id = '$file_id' and file_activity_status='Active'");
	$misc_currency_list = db_scalar("select group_concat(booking_price_currency) from mv_file_misc_service where fk_file_id = '$file_id' and misc_service_status='Active'");
	$cruise_currency_list = db_scalar("select group_concat(booking_price_currency) from mv_file_cruises where fk_file_id = '$file_id' and file_cruise_status='Active'");
	$var = $transfer_currency_list . ',' . $accom_currency_list . ',' . $activity_currency_list . ',' . $misc_currency_list . ',' . $cruise_currency_list;
	$arr = explode(",", $var);
	$arr = array_unique($arr);
	return $arr;
}
function all_services_confirmed($file_id)
{
	$not_confirmed_transfer = db_scalar("select count(*) from mv_file_transfers where fk_file_id = '$file_id' and file_transfer_status='Active' and service_status='0'");
	$not_confirmed_cruise = db_scalar("select count(*) from mv_file_cruises where fk_file_id = '$file_id' and file_cruise_status='Active' and service_status='0'");
	$not_confirmed_accom = db_scalar("select count(*) from mv_file_accommodation where fk_file_id = '$file_id' and file_accommodation_status='Active' and service_status='0'");
	$not_confirmed_activity = db_scalar("select count(*) from mv_file_activity where fk_file_id = '$file_id' and file_activity_status='Active' and service_status='0'");
	$not_confirmed_misc = db_scalar("select count(*) from mv_file_misc_service where fk_file_id = '$file_id' and misc_service_status='Active' and service_status='0'");
	if ($not_confirmed_transfer == 0 && $not_confirmed_accom == 0 && $not_confirmed_activity == 0 && $not_confirmed_misc == 0 && $not_confirmed_cruise == 0) {
		return true;
	} else {
		return false;
	}
}
function count_trips($current_status)
{
	return mysqli_num_rows(db_query("select * from mv_files where file_current_status='$current_status' and (file_primary_staff='" . $_SESSION['sess_agent_id'] . "' or file_active_staff='" . $_SESSION['sess_agent_id'] . "')"));
}
function get_suppliers_total($file_id)
{
	$arr_supplier = array();
	$sql = db_query("select booked_with_supplier_id,booking_net_price,booking_price_currency from mv_file_transfers where fk_file_id = '$file_id' and file_transfer_status='Active'");
	$i = 0;
	$amount = 0;
	while ($res = mysqli_fetch_array($sql)) {
		$currency = $res['booking_price_currency'];
		$amount = $res['booking_net_price'];
		$supplier = $res['booked_with_supplier_id'];
		$arr_supplier[$supplier][$currency] = $arr_supplier[$supplier][$currency] + $amount;
		$arr_qty[$supplier][$currency] = $arr_qty[$supplier][$currency] + 1;
	}
	$sql = db_query("select booked_with_supplier_id,booking_net_price,booking_price_currency from mv_file_accommodation where fk_file_id = '$file_id' and file_accommodation_status='Active'");
	while ($res = mysqli_fetch_array($sql)) {
		$currency = $res['booking_price_currency'];
		$amount = $res['booking_net_price'];
		$supplier = $res['booked_with_supplier_id'];
		$arr_supplier[$supplier][$currency] = $arr_supplier[$supplier][$currency] + $amount;
		$arr_qty[$supplier][$currency] = $arr_qty[$supplier][$currency] + 1;
	}
	$sql = db_query("select booked_with_supplier_id,booking_net_price,booking_price_currency from mv_file_activity where fk_file_id = '$file_id' and file_activity_status='Active'");
	while ($res = mysqli_fetch_array($sql)) {
		$currency = $res['booking_price_currency'];
		$amount = $res['booking_net_price'];
		$supplier = $res['booked_with_supplier_id'];
		$arr_supplier[$supplier][$currency] = $arr_supplier[$supplier][$currency] + $amount;
		$arr_qty[$supplier][$currency] = $arr_qty[$supplier][$currency] + 1;
	}
	$sql = db_query("select booked_with_supplier_id,booking_net_price,booking_price_currency from mv_file_cruises where fk_file_id = '$file_id' and file_cruise_status='Active'");
	while ($res = mysqli_fetch_array($sql)) {
		$currency = $res['booking_price_currency'];
		$amount = $res['booking_net_price'];
		$supplier = $res['booked_with_supplier_id'];
		$arr_supplier[$supplier][$currency] = $arr_supplier[$supplier][$currency] + $amount;
		$arr_qty[$supplier][$currency] = $arr_qty[$supplier][$currency] + 1;
	}
	$sql = db_query("select booked_with_supplier_id,booking_net_price,booking_price_currency from mv_file_misc_service where fk_file_id = '$file_id' and misc_service_status='Active'");
	while ($res = mysqli_fetch_array($sql)) {
		$currency = $res['booking_price_currency'];
		$amount = $res['booking_net_price'];
		$supplier = $res['booked_with_supplier_id'];
		$arr_supplier[$supplier][$currency] = $arr_supplier[$supplier][$currency] + $amount;
		$arr_qty[$supplier][$currency] = $arr_qty[$supplier][$currency] + 1;
	}
	$var = '<tr><td colspan="2">&nbsp;</td></tr>';
	foreach ($arr_supplier as $key => $val) {
		foreach ($val as $currency => $value) {
			$var .= "<tr><td>" . db_scalar("select supplier_company_name from mv_supplier where supplier_id='$key'");
			$var .= " (X" . $arr_qty[$key][$currency] . ")";
			$var .= "</td><td>" . $currency . " " . display_price($val[$currency]);
			$var .= "</td></tr>";
		}
	}
	return $var;
}
function all_voucher_sent($file_id)
{
	$not_sent_transfer = db_scalar("select count(*) from mv_file_transfers where fk_file_id = '$file_id' and file_transfer_status='Active' and voucher_sent='N'");
	$not_sent_accom = db_scalar("select count(*) from mv_file_accommodation where fk_file_id = '$file_id' and file_accommodation_status='Active' and voucher_sent='N'");
	$not_sent_cruise = db_scalar("select count(*) from mv_file_cruises where fk_file_id = '$file_id' and file_cruise_status='Active' and voucher_sent='N'");
	$not_sent_activity = db_scalar("select count(*) from mv_file_activity where fk_file_id = '$file_id' and file_activity_status='Active' and voucher_sent='N'");
	$not_sent_misc = db_scalar("select count(*) from mv_file_misc_service where fk_file_id = '$file_id' and misc_service_status='Active' and voucher_sent='N'");
	if ($not_sent_transfer == 0 && $not_sent_accom == 0 && $not_sent_activity == 0 && $not_sent_misc == 0) {
		return true;
	} else {
		return false;
	}
}
function search_array($array, $term)
{
	foreach ($array as $key => $value) {
		if (stristr($value, $term) === FALSE) {
			continue;
		} else {
			return $key;
		}
	}
	return FALSE;
}
function display_map($width, $height, $map_url, $fancy_box = 'Y', $zoom_level = '11', $map_type = '1')
{
	$coordinates = $map_url;
	if ($zoom_level == "" || $zoom_level == 0) {
		$zoom_level = '11';
	}
?>
	<div id="map_canvas" style="width: <?= $width ?>px; height: <?= $height ?>px;"></div>
	<?php if ($fancy_box != "Y") { ?>
		<script src="<?= SITE_WS_PATH ?>/<?= AGENT_ADMIN_DIR ?>/fancy/jquery-1.4.3.min.js"></script>
		<script type="text/javascript" src="<?= SITE_WS_PATH ?>/<?= AGENT_ADMIN_DIR ?>/fancy/fancybox/jquery.fancybox.pack.js"></script>
		<link rel="stylesheet" type="text/css" href="<?= SITE_WS_PATH ?>/<?= AGENT_ADMIN_DIR ?>/fancy/fancybox/jquery.fancybox.css" media="screen" />
	<?php } ?>
	<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyBQAcX0e--dSKE6dg3RobA97qwk07JDO-Y"></script>
	<script type="text/javascript">
		var parliament = new google.maps.LatLng(<?= $coordinates ?>);
		var marker;
		var map;
		function initialize() {
			var map = new google.maps.Map(document.getElementById('map_canvas'), {
				mapTypeId: google.maps.MapTypeId.TERRAIN,
				center: new google.maps.LatLng(<?= $coordinates ?>),
				zoom: <?= $zoom_level ?>
			});
			<?php if ($map_type == 1) { ?>
				marker = new google.maps.Marker({
					map: map,
					icon: '<?= SITE_WS_PATH ?>/images/pin_green.png',
					position: parliament
				});
			<?php } ?>
			google.maps.event.addListener(marker, 'click', function() {
				$.fancybox({
					'width': 720,
					'height': 450,
					'autoScale': false,
					'showCloseButton': false,
					'transitionIn': 'elastic',
					'transitionOut': 'elastic',
					'type': 'iframe',
					'href': '<?= SITE_WS_PATH ?>/map.php?co=<?= $coordinates ?>&zoom_view=<?= $zoom_level ?>'
				});
			});
		}
		initialize();
	</script>
<?php }
function display_multipin_map($width, $height, $tour_id, $fancy_box = 'Y', $zoom_level = '11', $map_type = '1', $package_id = '', $cruise_id = '')
{
	$coordinates = ''; // Initialize empty coordinates since $map_url is undefined
	if ($zoom_level == "" || $zoom_level == 0) {
		$zoom_level = '11';
	}
?>
	<div id="map_canvas" style="width: <?= $width ?>px; height: <?= $height ?>px;"></div>
	<?php if ($fancy_box != "Y") { ?>
		<script src="<?= SITE_WS_PATH ?>/<?= AGENT_ADMIN_DIR ?>/fancy/jquery-1.4.3.min.js"></script>
		<script type="text/javascript" src="<?= SITE_WS_PATH ?>/<?= AGENT_ADMIN_DIR ?>/fancy/fancybox/jquery.fancybox.pack.js"></script>
		<link rel="stylesheet" type="text/css" href="<?= SITE_WS_PATH ?>/<?= AGENT_ADMIN_DIR ?>/fancy/fancybox/jquery.fancybox.css" media="screen" />
	<?php } ?>
	<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyBQAcX0e--dSKE6dg3RobA97qwk07JDO-Y"></script>
	<script type="text/javascript">
		var locations = [<?php
							if ($cruise_id > 0) {
								$map_sql = db_query("select * from mv_cruise_maps where fk_cruise_id='$cruise_id' and map_latitude!='' and map_longitude!=''");
							} else if ($package_id > 0) {
								$map_sql = db_query("select * from mv_tour_maps where fk_package_id='$package_id'  and map_latitude!='' and map_longitude!=''");
							} else {
								$map_sql = db_query("select * from mv_tour_maps where fk_tour_id='$tour_id'  and map_latitude!='' and map_longitude!=''");
							}
							$m = 0;
							$var = "";
							while ($map_result = mysqli_fetch_array($map_sql)) {
								$var .= "['', " . $map_result['map_latitude'] . ", " . $map_result['map_longitude'] . ", " . $zoom_level . "],";
								$m++;
							}
							echo substr($var, 0, -1);
							?>];
		var map = new google.maps.Map(document.getElementById('map_canvas'), {
			zoom: <?= $zoom_level ?>,
			center: new google.maps.LatLng(0, 0),
			mapTypeId: google.maps.MapTypeId.TERRAIN
		});
		var flightPlanCoordinates = [
			<?php
			if ($cruise_id > 0) {
				$map_sql = db_query("select * from mv_cruise_maps where fk_cruise_id='$cruise_id'");
			} else if ($package_id > 0) {
				$map_sql = db_query("select * from mv_tour_maps where fk_package_id='$package_id'");
			} else {
				$map_sql = db_query("select * from mv_tour_maps where fk_tour_id='$tour_id'");
			}
			$m = 0;
			$var = "";
			while ($map_result = mysqli_fetch_array($map_sql)) {
				$var .= "new google.maps.LatLng(" . $map_result['map_latitude'] . ", " . $map_result['map_longitude'] . "),";
				$m++;
			}
			echo substr($var, 0, -1);
			?>
		];
		var flightPath = new google.maps.Polyline({
			path: flightPlanCoordinates,
			strokeColor: "#FF0000",
			strokeOpacity: 1.0,
			strokeWeight: 3
		});
		var infowindow = new google.maps.InfoWindow();
		var marker, i;
		var marker_number;
		var bounds = new google.maps.LatLngBounds();
		for (i = 0; i < locations.length; i++) {
			var beach = locations[i];
			var myLatLng = new google.maps.LatLng(beach[1], beach[2]);
			marker_number = i + 1;
			var map_image = "pin_" + marker_number;
			marker = new google.maps.Marker({
				position: new google.maps.LatLng(locations[i][1], locations[i][2]),
				map: map,
				icon: '<?= SITE_WS_PATH ?>/images/red_dotted.png'
			});
			bounds.extend(myLatLng);
			map.fitBounds(bounds);
			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					$.fancybox({
						'width': 720,
						'height': 450,
						'autoScale': false,
						'showCloseButton': false,
						'transitionIn': 'elastic',
						'transitionOut': 'elastic',
						'type': 'iframe',
						'href': '<?= SITE_WS_PATH ?>/map.php?co=' + locations[i][1] + ',' + locations[i][2] + '&zoom_view=<?= $zoom_level ?>'
					});
				}
			})(marker, i));
		}
		flightPath.setMap(map);
	</script>
<?php }
function copy_file_data_for_package($package_id)
{
	$result = db_result("select * from mv_packages  where package_id = '$package_id'");
	db_query("insert into mv_package_file_accommodation (file_package_id,file_booking_type,fk_hotel_id,fk_room_id,booked_with_supplier_id, booked_with_supplier_name,
	booking_duration,check_in_date,check_out_date,check_in_time,check_out_time,previous_record,booking_price_currency,booking_net_price) (select '$package_id' ,file_booking_type,fk_hotel_id,fk_room_id,booked_with_supplier_id,booked_with_supplier_name,
	booking_duration,check_in_date,check_out_date,check_in_time,check_out_time,file_accommodation_id,booking_price_currency,booking_net_price from mv_file_accommodation where fk_file_id='" . $result['fk_file_id'] . "' and file_accommodation_status='Active')");
	db_query("insert into mv_package_file_transfers (file_package_id,file_booking_type,transfer_country,
	transfer_arrival_country,fk_region_id,fk_pickup_id,fk_dropoff_id,fk_pickup_station_id,fk_dropoff_station_id,
	fk_dep_airport_id,fk_arrival_airport_id,check_in_date,check_out_date,booking_method,booked_with_supplier_id,booked_with_supplier_name,
	pickup_depot,dropoff_depot,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,vehicle_id,
	fk_file_accommodation_id, transfer_type,previous_record,booking_price_currency,booking_net_price) (select '$package_id' ,file_booking_type,transfer_country,
	transfer_arrival_country,fk_region_id,fk_pickup_id,fk_dropoff_id,fk_pickup_station_id,fk_dropoff_station_id,
	fk_dep_airport_id,fk_arrival_airport_id,check_in_date,check_out_date,booking_method,booked_with_supplier_id,booked_with_supplier_name,
	pickup_depot,dropoff_depot,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,vehicle_id,
	fk_file_accommodation_id, transfer_type,file_transfer_id,booking_price_currency,booking_net_price from mv_file_transfers where fk_file_id='" . $result['fk_file_id'] . "' and file_transfer_status='Active')");
	db_query("insert into mv_package_file_activity (file_package_id,fk_tour_id,booked_with_supplier_id,
	booked_with_supplier_name,check_in_date,pickup_time,hotel_pickup,hotel_name,occupancy_type,package_id,
	is_private_tour,previous_record,booking_price_currency,booking_net_price,tour_pricing_method) (select '$package_id' ,fk_tour_id,booked_with_supplier_id,
	booked_with_supplier_name,check_in_date,pickup_time,hotel_pickup,hotel_name,occupancy_type,package_id,
	is_private_tour,file_activity_id,booking_price_currency,booking_net_price,tour_pricing_method from mv_file_activity where fk_file_id='" . $result['fk_file_id'] . "' and file_activity_status='Active')");
	db_query("insert into mv_package_file_misc_service (file_package_id,service_title,
	service_description,booked_with_supplier_id,booked_with_supplier_name,service_location,service_location_ids,previous_record,booking_price_currency,booking_net_price,service_type,check_in_date,check_out_date,check_in_time,check_out_time,pax_no,is_private_tour,hotel_pickup,hotel_name,pickup_time,occupancy_type,transfer_country,fk_region_id,fk_pickup_id,fk_dropoff_id,pickup_depot,dropoff_depot,dep_transfer_country,dep_region_id,arr_transfer_country,arr_region_id,ferry_name,ferry_class,arrival_time,fk_pickup_station_id,fk_dropoff_station_id,train_name,train_number,fk_country_id,fk_airport_id,fk_arrival_country_id,fk_arrival_region_id,fk_arrival_airport_id,airline,flight_number,record_locator,car_type,insurance_type,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,room_name,tour_description) (select '$package_id' ,service_title,
	service_description,booked_with_supplier_id,booked_with_supplier_name,service_location,service_location_ids,file_misc_id,booking_price_currency,booking_net_price,service_type,check_in_date,check_out_date,check_in_time,check_out_time,pax_no,is_private_tour,hotel_pickup,hotel_name,pickup_time,occupancy_type,transfer_country,fk_region_id,fk_pickup_id,fk_dropoff_id,pickup_depot,dropoff_depot,dep_transfer_country,dep_region_id,arr_transfer_country,arr_region_id,ferry_name,ferry_class,arrival_time,fk_pickup_station_id,fk_dropoff_station_id,train_name,train_number,fk_country_id,fk_airport_id,fk_arrival_country_id,fk_arrival_region_id,fk_arrival_airport_id,airline,flight_number,record_locator,car_type,insurance_type,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,room_name,tour_description from mv_file_misc_service where fk_file_id='" . $result['fk_file_id'] . "' and misc_service_status='Active')");
	db_query("insert into mv_package_file_itinerary (file_package_id,itinerary_date,itinerary_title,itinerary_hotel,
	itinerary_description,tag_ids_1,tag_ids_2,tag_ids_3,tag_names_1,tag_names_2,tag_names_3,fk_activity_id,day_id,itinerary_segment,itinerary_meals,fk_tour_id,fk_supplier_id,previous_record) (select '$package_id' ,itinerary_date,itinerary_title,itinerary_hotel,
	itinerary_description,tag_ids_1,tag_ids_2,tag_ids_3,tag_names_1,tag_names_2,tag_names_3,fk_activity_id,day_id,itinerary_segment,itinerary_meals,fk_tour_id,fk_supplier_id,itinerary_id from mv_file_itinerary where fk_file_id='" . $result['fk_file_id'] . "')");
}
function insert_package_services($file_package_id, $file_id, $skip_accommodation = 'No')
{
	$file_result = db_result("select * from mv_files  where file_id = '$file_id'");
	$package_result = db_result("select * from mv_packages  where package_id = '$file_package_id'");
	$total_pax = $file_result['file_adults'] + $file_result['file_teens'] + $file_result['file_childrens'] + $file_result['file_infants'];
	if ($skip_accommodation == "No") {
		db_query("insert into mv_file_accommodation (fk_file_id,file_booking_type,fk_hotel_id,fk_room_id,booked_with_supplier_id, booked_with_supplier_name,booking_duration,check_in_date,check_out_date,check_in_time,check_out_time,room_quantity,booking_price_currency,booking_net_price,pax_no) (select '$file_id',file_booking_type,fk_hotel_id,fk_room_id,booked_with_supplier_id,booked_with_supplier_name,booking_duration,check_in_date,check_out_date,check_in_time,check_out_time,'1',booking_price_currency,booking_net_price,'" . $total_pax . "' from mv_package_file_accommodation where file_package_id='" . $file_package_id . "')");
	}
	db_query("insert into mv_file_transfers (fk_file_id,file_booking_type,transfer_country,
	transfer_arrival_country,fk_region_id,fk_pickup_id,fk_dropoff_id,fk_pickup_station_id,fk_dropoff_station_id,
	fk_dep_airport_id,fk_arrival_airport_id,check_in_date,check_out_date,booking_method,booked_with_supplier_id,booked_with_supplier_name,
	pickup_depot,dropoff_depot,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,vehicle_id,
	fk_file_accommodation_id, transfer_type,no_pax,booking_price_currency,booking_net_price) (select '$file_id',file_booking_type,transfer_country,
	transfer_arrival_country,fk_region_id,fk_pickup_id,fk_dropoff_id,fk_pickup_station_id,fk_dropoff_station_id,
	fk_dep_airport_id,fk_arrival_airport_id,check_in_date,check_out_date,booking_method,booked_with_supplier_id,booked_with_supplier_name,
	pickup_depot,dropoff_depot,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,vehicle_id,
	fk_file_accommodation_id, transfer_type,'" . $total_pax . "',booking_price_currency,booking_net_price from mv_package_file_transfers where file_package_id='" . $file_package_id . "')");
	db_query("insert into mv_file_activity (fk_file_id,fk_tour_id,booked_with_supplier_id,
	booked_with_supplier_name,check_in_date,pickup_time,hotel_pickup,hotel_name,occupancy_type,package_id,
	is_private_tour,pax_no,booking_price_currency,booking_net_price,tour_pricing_method) (select '$file_id' ,fk_tour_id,booked_with_supplier_id,
	booked_with_supplier_name,check_in_date,pickup_time,hotel_pickup,hotel_name,occupancy_type,package_id,
	is_private_tour,'" . $total_pax . "',booking_price_currency,booking_net_price,tour_pricing_method from mv_package_file_activity where file_package_id='" . $file_package_id . "')");
	db_query("insert into mv_file_misc_service (fk_file_id,service_title,
	service_description,booked_with_supplier_id,booked_with_supplier_name,service_location,service_location_ids,booking_price_currency,booking_net_price,service_type,check_in_date,check_out_date,check_in_time,check_out_time,pax_no,is_private_tour,hotel_pickup,hotel_name,pickup_time,occupancy_type,transfer_country,fk_region_id,fk_pickup_id,fk_dropoff_id,pickup_depot,dropoff_depot,dep_transfer_country,dep_region_id,arr_transfer_country,arr_region_id,ferry_name,ferry_class,arrival_time,fk_pickup_station_id,fk_dropoff_station_id,train_name,train_number,fk_country_id,fk_airport_id,fk_arrival_country_id,fk_arrival_region_id,fk_arrival_airport_id,airline,flight_number,record_locator,car_type,insurance_type,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,room_name,no_pax,tour_description) (select '$file_id' ,service_title,
	service_description,booked_with_supplier_id,booked_with_supplier_name,service_location,service_location_ids,booking_price_currency,booking_net_price,service_type,check_in_date,check_out_date,check_in_time,check_out_time,pax_no,is_private_tour,hotel_pickup,hotel_name,pickup_time,occupancy_type,transfer_country,fk_region_id,fk_pickup_id,fk_dropoff_id,pickup_depot,dropoff_depot,dep_transfer_country,dep_region_id,arr_transfer_country,arr_region_id,ferry_name,ferry_class,arrival_time,fk_pickup_station_id,fk_dropoff_station_id,train_name,train_number,fk_country_id,fk_airport_id,fk_arrival_country_id,fk_arrival_region_id,fk_arrival_airport_id,airline,flight_number,record_locator,car_type,insurance_type,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,room_name,'" . $total_pax . "',tour_description from mv_package_file_misc_service where file_package_id='" . $file_package_id . "')");
	db_query("insert into mv_file_itinerary (fk_file_id,itinerary_date,itinerary_title,itinerary_hotel,
	itinerary_description,tag_ids_1,tag_ids_2,tag_ids_3,tag_names_1,tag_names_2,tag_names_3,fk_activity_id,day_id,itinerary_segment,itinerary_meals,fk_tour_id,fk_supplier_id,loc_image_display) (select '$file_id' ,itinerary_date,itinerary_title,itinerary_hotel,
	itinerary_description,tag_ids_1,tag_ids_2,tag_ids_3,tag_names_1,tag_names_2,tag_names_3,fk_activity_id,day_id,itinerary_segment,itinerary_meals,fk_tour_id,fk_supplier_id,loc_image_display from mv_package_file_itinerary where file_package_id='" . $file_package_id . "')");
	$current_date_array = get_all_dates_between($file_result['file_arrival_date'], $file_result['file_return_date']);
	$prev_date_array = get_all_dates_between($package_result['package_file_arrival_date'], $package_result['package_file_return_date']);
	########################### Update Accommodation Price ################################
	if ($skip_accommodation == "No") {
		$sql1 = db_query("select * from mv_file_accommodation where fk_file_id='$file_id'");
		while ($res1 = mysqli_fetch_array($sql1)) {
			$key1 = array_search($res1['check_in_date'], $prev_date_array);
			$key2 = array_search($res1['check_out_date'], $prev_date_array);
			$check_in_date = $current_date_array[$key1];
			$check_out_date = $current_date_array[$key2];
			$booking_price_currency = $res1['booking_price_currency'];
			if ($res1['file_booking_type'] == "BE") {
				$booking_price = $res1['booking_net_price'];
			} else {
				if ($res1['file_booking_type'] == "Direct") {
					$season_supplier_id = 0;
				} else {
					$season_supplier_id = $res1['booked_with_supplier_id'];
				}
				$season_ids = db_scalar("select group_concat(season_id) from mv_hotel_seasons where season_status='Active' and fk_supplier_id='" . $res1['fk_hotel_id'] . "' and season_supplier_id='" . $season_supplier_id . "' and ('$check_in_date' <= season_end_date) and ('$check_out_date' >= season_start_date)");
				$booking_price = calculate_hotel_price($check_in_date, $check_out_date, '1', $res1['fk_hotel_id'], $res1['fk_room_id'], $season_ids, $season_supplier_id);
			}
			$net_sc = $booking_price * file_exchnage_rates($file_id, $booking_price_currency, $file_result['file_currency']);
			$gross_sc = $net_sc + ($net_sc * $file_result['file_markup']) / 100;
			$booking_gross_price = ceil($gross_sc);
			db_query("update mv_file_accommodation set check_in_date='" . $check_in_date . "',check_out_date='" . $check_out_date . "',booking_price_currency='" . $booking_price_currency . "',booking_net_price='" . $booking_price . "',booking_original_net_price='" . $booking_price . "',booking_net_price_sc='" . $net_sc . "',booking_gross_price='" . $booking_gross_price . "' where file_accommodation_id='" . $res1['file_accommodation_id'] . "'");
		}
	}
	########################### Update Transportation Price ################################
	$sql2 = db_query("select * from mv_file_transfers where fk_file_id='$file_id'");
	while ($res1 = mysqli_fetch_array($sql2)) {
		$key1 = array_search($res1['check_in_date'], $prev_date_array);
		$booking_price_currency = $res1['booking_price_currency'];
		###########################  Transfer #############################
		if ($res1['file_booking_type'] == "217") {
			if ($res1['booking_method'] == '1') {
				$booking_price = $res1['booking_net_price'];
			} else {
				if ($res1['fk_file_accommodation_id'] > 0) {
					$transfer_net_price_pp = db_scalar("select transfer_net_price_pp from mv_hotel_transfers where fk_supplier_id='" . $res1['booked_with_supplier_id'] . "'  and transfer_status!='Delete' and fk_country_id='" . $res1['transfer_country'] . "' and fk_region_id='" . $res1['fk_region_id'] . "' and fk_pickup_id='" . $res1['fk_pickup_id'] . "' and fk_dropoff_id='" . $res1['fk_dropoff_id'] . "'");
					$booking_price = $total_pax * $transfer_net_price_pp;
				} else {
					if ($res1['vehicle_id'] > 0) {
						$booking_price = db_scalar("select vehicle_price from mv_transportation_vehicle where route_vehicle_id='" . $res1['vehicle_id'] . "'");
					} else {
						$route_id = db_scalar("select r.route_id from mv_supplier s left join mv_supplier_transportation t on s.supplier_id=t.fk_supplier_id left join mv_transportation_route r on t.transportation_id=r.fk_transportation_id  where fk_country_id='" . $res1['transfer_country'] . "' and fk_region_id='" . $res1['fk_region_id'] . "' and fk_pickup_id='" . $res1['fk_pickup_id'] . "' and fk_dropoff_id='" . $res1['fk_dropoff_id'] . "' and transportation_status='Active' and transportation_type='" . $res1['file_booking_type'] . "' and route_status!='Delete' and r.transfer_type='" . $res1['transfer_type'] . "'");
						$booking_price = get_transfer_booking_price($route_id, $total_pax);
					}
				}
			}
		} elseif ($res1['file_booking_type'] == "218") {
			###########################  Train  #############################
			if ($res1['booking_method'] == '1') {
				$booking_price = $res1['booking_net_price'];
			} else {
				$route_price = db_scalar("select r.price_pp from mv_supplier s left join mv_supplier_transportation t on s.supplier_id=t.fk_supplier_id left join mv_transportation_route r on t.transportation_id=r.fk_transportation_id  where fk_pickup_station_id='" . $res1['fk_pickup_station_id'] . "' and fk_dropoff_station_id='" . $res1['fk_dropoff_station_id'] . "' and transportation_status='Active' and transportation_type='" . $res1['file_booking_type'] . "' and supplier_id='" . $res1['booked_with_supplier_id'] . "' and route_status!='Delete' order by supplier_company_name asc");
				$booking_price = $total_pax * $route_price;
			}
		} elseif ($res1['file_booking_type'] == "219") {
			###########################  Ferry  #############################
			if ($res1['booking_method'] == '1') {
				$booking_price = $res1['booking_net_price'];
			} else {
				$route_price = db_scalar("select r.price_pp from mv_supplier s left join mv_supplier_transportation t on s.supplier_id=t.fk_supplier_id left join mv_transportation_route r on t.transportation_id=r.fk_transportation_id  where fk_pickup_station_id='" . $res1['fk_pickup_id'] . "' and fk_dropoff_station_id='" . $res1['fk_dropoff_id'] . "' and transportation_status='Active' and transportation_type='" . $res1['file_booking_type'] . "' and supplier_id='" . $res1['booked_with_supplier_id'] . "' and route_status!='Delete' order by supplier_company_name asc");
				$booking_price = $total_pax * $route_price;
			}
		} elseif ($res1['file_booking_type'] == "220") {
			###########################  Flight  #############################
			if ($res1['booking_method'] == '1') {
				$booking_price = $res1['booking_net_price'];
			} else {
				$route_price = db_scalar("select r.price_pp from mv_supplier s left join mv_supplier_transportation t on s.supplier_id=t.fk_supplier_id left join mv_transportation_route r on t.transportation_id=r.fk_transportation_id  where fk_airport_id='" . $res1['fk_dep_airport_id'] . "' and fk_dropoff_airport_id='" . $res1['fk_arrival_airport_id'] . "'  and transportation_type='" . $res1['file_booking_type'] . "' and supplier_id='" . $res1['booked_with_supplier_id'] . "' and route_status!='Delete' and transportation_status='Active' order by supplier_company_name asc");
				$booking_price = $total_pax * $route_price;
			}
		} elseif ($res1['file_booking_type'] == "221") {
			###########################  Car Rental  #############################
			$booking_price = $res1['booking_net_price'];
		}
		###########################################################################
		$check_out_date = "0000-00-00";
		if ($res1['check_out_date'] != "" && $res1['check_out_date'] != "0000-00-00") {
			$key2 = array_search($res1['check_out_date'], $prev_date_array);
			$check_out_date = $current_date_array[$key2];
		}
		$net_sc = $booking_price * file_exchnage_rates($file_id, $booking_price_currency, $file_result['file_currency']);
		$gross_sc = $net_sc + ($net_sc * $file_result['file_markup']) / 100;
		$booking_gross_price = ceil($gross_sc);
		db_query("update mv_file_transfers set check_in_date='" . $current_date_array[$key1] . "',check_out_date='" . $check_out_date . "',booking_price_currency='" . $booking_price_currency . "',booking_net_price='" . $booking_price . "',booking_original_net_price='" . $booking_price . "',booking_net_price_sc='" . $net_sc . "',booking_gross_price='" . $booking_gross_price . "' where file_transfer_id='" . $res1['file_transfer_id'] . "'");
	}
	########################### Update Activity Price ################################
	$sql3 = db_query("select * from mv_file_activity where fk_file_id='$file_id'");
	while ($res1 = mysqli_fetch_array($sql3)) {
		$key1 = array_search($res1['check_in_date'], $prev_date_array);
		$check_in_date = $current_date_array[$key1];
		$booking_price_currency = $res1['booking_price_currency'];
		$booking_price = get_tour_price($res1['fk_tour_id'], $res1['booked_with_supplier_id'], $current_date_array[$key1], $total_pax, $res1['occupancy_type'], $res1['is_private_tour'], $res1['package_id'], $res1['tour_pricing_method']);
		$net_sc = $booking_price * file_exchnage_rates($file_id, $booking_price_currency, $file_result['file_currency']);
		$gross_sc = $net_sc + ($net_sc * $file_result['file_markup']) / 100;
		$booking_gross_price = ceil($gross_sc);
		db_query("update mv_file_activity set check_in_date='" . $check_in_date . "',booking_price_currency='" . $booking_price_currency . "',booking_net_price='" . $booking_price . "',booking_original_net_price='" . $booking_price . "',booking_net_price_sc='" . $net_sc . "',booking_gross_price='" . $booking_gross_price . "' where file_activity_id='" . $res1['file_activity_id'] . "'");
	}
	########################### Update Misc Price ################################
	$sql4 = db_query("select * from mv_file_misc_service where fk_file_id='$file_id'");
	while ($res1 = mysqli_fetch_array($sql4)) {
		$check_in_date = "0000-00-00";
		if ($res1['check_in_date'] != "" && $res1['check_in_date'] != "0000-00-00") {
			$key1 = array_search($res1['check_in_date'], $prev_date_array);
			$check_in_date = $current_date_array[$key1];
		}
		$check_out_date = "0000-00-00";
		if ($res1['check_out_date'] != "" && $res1['check_out_date'] != "0000-00-00") {
			$key2 = array_search($res1['check_out_date'], $prev_date_array);
			$check_out_date = $current_date_array[$key2];
		}
		$booking_price_currency = $res1['booking_price_currency'];
		$booking_price = $res1['booking_net_price'];
		$net_sc = $booking_price * file_exchnage_rates($file_id, $booking_price_currency, $file_result['file_currency']);
		$gross_sc = $net_sc + ($net_sc * $file_result['file_markup']) / 100;
		$booking_gross_price = ceil($gross_sc);
		db_query("update mv_file_misc_service set check_in_date='" . $current_date_array[$key1] . "',check_out_date='" . $check_out_date . "',booking_price_currency='" . $booking_price_currency . "',booking_net_price='" . $booking_price . "',booking_original_net_price='" . $booking_price . "',booking_net_price_sc='" . $net_sc . "',booking_gross_price='" . $booking_gross_price . "' where file_misc_id='" . $res1['file_misc_id'] . "'");
	}
	########################### Update File Iteneray ################################
	$sql5 = db_query("select * from mv_file_itinerary where fk_file_id='$file_id'");
	while ($res1 = mysqli_fetch_array($sql5)) {
		if ($res1['itinerary_date'] != "" && $res1['itinerary_date'] != "0000-00-00") {
			$key1 = array_search($res1['itinerary_date'], $prev_date_array);
			db_query("update mv_file_itinerary set itinerary_date='" . $current_date_array[$key1] . "' where itinerary_id='" . $res1['itinerary_id'] . "'");
		}
	}
	#######################################################################################
}
function get_tag_main_parent($tag_id)
{
	global $arr;
	$parent_id = db_scalar("select tag_parent_id from mv_tags where tag_id='$tag_id'");
	if ($parent_id != 0) {
		$arr[] = $tag_id;
		return get_tag_main_parent($parent_id);
	}
	return $arr;
}
function reverse_tag($tag_name)
{
	$tag_arr = explode(",", $tag_name);
	$tag_arr = array_reverse($tag_arr);
	return implode(",", $tag_arr);
}
function first_last_tag($tag_name)
{
	$tag_arr = explode(",", $tag_name);
	$var = $tag_arr[0];
	$arr_length = count($tag_arr) - 1;
	if ($tag_arr[$arr_length] != "") {
		$var .= ", " . $tag_arr[$arr_length];
	}
	return $var;
}
function check_for_location_complete($location_id)
{
	if ($location_id > 0) {
		$res = db_result("select * from mv_tags where tag_id='$location_id' and tag_status!='Delete'");
		@extract($res);
		$data_complete = true;
		if ($location_description == "") {
			$data_complete = false;
		}
		################ Check For Images #####################
		if ($data_complete) {
			$image_exists = db_scalar("select count(*) from mv_location_images where fk_location_id='$location_id' and image_status='Active'");
			if ($image_exists == 0) {
				$data_complete = false;
			}
		}
		########################################################
		if ($data_complete) {
			$location_data_completed = "Yes";
		} else {
			$location_data_completed = "No";
		}
		db_query("update mv_tags set is_show_in_dest='$location_data_completed' where tag_id='$location_id'");
	}
}
function check_for_tour_data_complete($tour_id)
{
	if ($tour_id > 0) {
		$res = db_result("select * from mv_tours where tour_id='$tour_id'");
		@extract($res);
		$data_complete = true;
		//if(($tour_includes=="" && $tour_includes_other=="") || $tour_short_description=="" || $tour_description=="" || $tour_interest=="" || ($tour_city=="" && $tour_classification!="3") || ($tour_return_city=="" && $tour_classification!="3") ){
		if (($tour_includes == "" && $tour_includes_other == "") || $tour_short_description == "" || $tour_description == "") {
			$data_complete = false;
		}
		################ Check For Map #####################
		if ($data_complete) {
			$map_exists = db_scalar("select count(*) from mv_tour_maps where fk_tour_id='$tour_id'");
			if ($map_exists == 0) {
				$data_complete = false;
			}
		}
		################ Check For Images #####################
		if ($data_complete) {
			$image_exists = db_scalar("select count(*) from mv_tour_images where fk_tour_id='$tour_id' and image_status='Active' and (image_type='D' or image_type='B')");
			if ($image_exists == 0) {
				$data_complete = false;
			}
		}
		################ Check For Sight Visited  ######################
		/*if($data_complete && $suppress_location=="No"){
			$sight_visited_exists=db_scalar("select count(*) from mv_tour_sights where fk_tour_id='$tour_id' and sight_status='Active'");
			if($sight_visited_exists==0){
				$data_complete=false;
			}
		}*/
		########################################################
		if ($data_complete) {
			$tour_data_completed = "Yes";
		} else {
			$tour_data_completed = "No";
		}
		db_query("update mv_tours set tour_data_completed='$tour_data_completed' where tour_id='$tour_id'");
	}
}
function check_for_package_data_complete($package_id)
{
	if ($package_id > 0) {
		$res = db_result("select * from mv_packages where package_id='$package_id'");
		@extract($res);
		$data_complete = true;
		if (($package_includes == "" && $package_includes_other == "") || $package_short_description == "" || $package_description == "" || $package_interest == "" || $package_departure == "" || $package_return_city == "") {
			$data_complete = false;
		}
		################ Check For Map #####################
		if ($data_complete) {
			$map_exists = db_scalar("select count(*) from mv_tour_maps where fk_package_id='$package_id'");
			if ($map_exists == 0) {
				$data_complete = false;
			}
		}
		################ Check For Images #####################
		if ($data_complete) {
			$image_exists = db_scalar("select count(*) from mv_tour_images where fk_package_id='$package_id' and image_status='Active' and image_type='D'");
			if ($image_exists == 0) {
				$data_complete = false;
			}
		}
		################ Check For Sight Visited  ######################
		if ($data_complete && $suppress_location == "No") {
			$sight_visited_exists = db_scalar("select count(*) from mv_tour_sights where fk_package_id='$package_id' and sight_status='Active'");
			if ($sight_visited_exists == 0) {
				$data_complete = false;
			}
		}
		########################################################
		if ($data_complete) {
			$package_data_completed = "Yes";
		} else {
			$package_data_completed = "No";
		}
		db_query("update mv_packages set package_data_completed='$package_data_completed' where package_id='$package_id'");
	}
}
function check_for_hotel_data_complete($id)
{
	if ($id > 0) {
		$res = db_result("select * from mv_supplier where supplier_id='$id'");
		@extract($res);
		if (isbtype($res['supplier_business_type'], 1)) {
			$data_complete = true;
			if ($supplier_hotel_type == 0 || $supplier_rating == "" || $location_tags_name == "" || $supplier_short_description == "" || $supplier_description == "" || $hotel_amenities == "" || ($map_latitude == "" && $map_image == "")) {
				$data_complete = false;
			}
			################ Check For Hotel Images #####################
			if ($data_complete) {
				$image_exists = db_scalar("select count(*) from mv_hotel_images where fk_supplier_id='$id' and image_status='Active' and image_type='D'");
				if ($image_exists == 0) {
					$data_complete = false;
				}
			}
			################ Check For Room #####################
			/*if($data_complete){
					$room_exists=db_scalar("select count(*) from mv_hotel_rooms where fk_supplier_id='$id' and room_status='Active' and available_online='Yes'");
					if($room_exists==0){
						$data_complete=false;
					}
				}*/
			################ Check For Room Description #####################
			/*if($data_complete){
					$room_sql=db_query("select * from mv_hotel_rooms r left join mv_room_detail d on d.room_detail_id=r.fk_room_detail_id where room_detail_hotel_id='$id' and room_status='Active' and available_online='Yes' group by room_detail_id");
					while($room_result=mysqli_fetch_array($room_sql)){
							if($room_result['room_detail_description']==""){
								$data_complete=false;
								break;
							}
							if($room_result['room_detail_amenities']==""){
								$data_complete=false;
								break;
							}
							$image_exists=db_scalar("select count(*) from mv_room_images where fk_room_id='".$room_result['room_detail_id']."' and  image_status='Active'  and image_type='D' ");
							if($image_exists==0){
								$data_complete=false;
								break;
							}
					}
				}*/
			########################################################
			if ($data_complete) {
				$supplier_data_completed = "Yes";
			} else {
				$supplier_data_completed = "No";
			}
			db_query("update mv_supplier set supplier_data_completed='$supplier_data_completed' where supplier_id='$id'");
		}
	}
}
function check_for_property_data_complete($property_id)
{
	if ($property_id > 0) {
		$res = db_result("select * from mv_hotel_rooms where room_id='$property_id'");
		@extract($res);
		$data_complete = true;
		//if($property_type=="" || $room_occupancy_type=="" || $room_short_description=="" || $room_description=="" || $room_amenities=="" || ($map_latitude=="" && $map_image=="")){
		if ($property_type == "" || $room_occupancy_type == "" || $room_short_description == "" || $room_description == "" || $room_amenities == "") {
			$data_complete = false;
		}
		################ Check For Property Images #####################
		if ($data_complete) {
			$room_exists = db_scalar("select count(*) from mv_room_images where fk_room_id='$property_id' and  image_status='Active'  and image_type='D'");
			if ($room_exists == 0) {
				$data_complete = false;
			}
		}
		########################################################
		if ($data_complete) {
			$property_data_completed = "Yes";
		} else {
			$property_data_completed = "No";
		}
		db_query("update mv_hotel_rooms set property_data_completed='$property_data_completed' where room_id='$property_id'");
	}
}
function check_for_cruise_data_complete($cruise_id)
{
	if ($cruise_id > 0) {
		$res = db_result("select * from mv_cruise where cruise_id='$cruise_id'");
		@extract($res);
		$data_complete = true;
		if ($fk_ship_id == 0 || $fk_ship_id == "" || $cruise_short_description == "" || $cruise_description == "") {
			$data_complete = false;
		}
		################ Check For Cruise Ship Image #####################
		if ($data_complete) {
			$image_exists = db_scalar("select count(*) from mv_ship_images where fk_ship_id='$fk_ship_id' and  image_status='Active'  and image_type='D'");
			if ($image_exists == 0) {
				$data_complete = false;
			}
		}
		################ Check For Day #####################
		if ($data_complete) {
			$day_exists = db_scalar("select count(*) from mv_cruise_schedule where fk_cruise_id='$cruise_id'");
			if ($day_exists < 2) {
				$data_complete = false;
			}
		}
		########################################################
		if ($data_complete) {
			$cruise_data_completed = "Yes";
		} else {
			$cruise_data_completed = "No";
		}
		db_query("update mv_cruise set cruise_data_completed='$cruise_data_completed' where cruise_id='$cruise_id'");
	}
}
function validate_slug($slug)
{
	if (preg_match("/^[a-zA-Z0-9_\-]*$/", $slug)) {
		return true;
	} else {
		return false;
	}
}
function create_slug($slug_name)
{
	$new_slug = preg_replace('/[^a-zA-Z0-9-]/s', '_', strtolower(trim($slug_name)));
	$new_slug = str_replace("__", "_", $new_slug);
	$new_slug = str_replace("__", "_", $new_slug);
	if (substr($new_slug, strlen($new_slug) - 1, strlen($new_slug)) == "_") {
		$new_slug = substr($new_slug, 0, -1);
	}
	return $new_slug;
}
function get_tour_base_price_info($tour_id, $package_id = '', $currency = 'EUR')
{
	$pax = 1;
	$default_markup = db_scalar("select mark_up from mv_config where config_id='1'");
	$tour_result = db_result("select * from mv_tours where tour_id = '$tour_id'");
	@extract($tour_result);
	$total = 0;
	if ($tour_classification == "1") {
		$tour_pricing_res = db_result("select p.*,ta.tag_name,s.fk_supplier_id from mv_tour_package_pricing p left join mv_tour_seasons s  on p.fk_season_id=s.season_id left join mv_tour_occupancy o on p.fk_occupancy_id=o.occupancy_id left join mv_tags ta on o.fk_occupancy_type=ta.tag_id  where season_status='Active' and p.fk_package_id='$package_id' and s.fk_package_id='$package_id' and price_adult>0 order by price_adult limit 0,1");
		$price_pp = $tour_pricing_res['price_adult'];
		$occupancy = " * Based on " . $tour_pricing_res['tag_name'] . " occupancy";
	} else {
		$tour_pricing_res = db_result("select * from mv_tour_pricing p left join mv_tour_seasons s on p.fk_season_id=s.season_id where season_status='Active' and p.fk_tour_id='$tour_id' and  s.fk_tour_id='$tour_id' and pricing_type='G' and price_adult>0 order by price_adult limit 0,1");
		$price_pp = $tour_pricing_res['price_adult'];
		$occupancy = " * Per Pax";
	}
	$tour_extra_result = db_result("select e.* from mv_tour_pricing_extra e where fk_supplier_id='" . $tour_pricing_res['fk_supplier_id'] . "' and price_status!='Delete' and fk_tour_id = '$tour_id'");
	@extract($tour_extra_result);
	$supplier_activity_currency = db_scalar("select supplier_activity_currency from mv_supplier where supplier_id='" . $tour_pricing_res['fk_supplier_id'] . "'");
	if ($tour_classification == "1") {
		$additional_markup = db_scalar("select additional_markup from mv_tour_packages where package_id='$package_id'");
	}
	$total = $total + ($price_pp * $pax);
	$total = $total - (($total * $gross_commission) / 100);
	#####################  Add Markup in the Net Price
	$total = $total + (($total * $default_markup) / 100);
	$total = $total + $additional_markup + $entrance_fee + $gratuity;
	$total = $total * get_exchange_rates($supplier_activity_currency, $currency);
	if (ceil($total) > 0) {
		$return_string = "<strong>Pricing From : </strong>" . $currency . " " . number_format(ceil($total), 2);
	} else {
		$return_string = "";
	}
	return $return_string;
}
function get_room_price_info($room_id, $currency = 'CAD')
{
	$default_markup = db_scalar("select mark_up from mv_config where config_id='1'");
	$res = db_result("select p.*,r.room_occupancy_type from mv_room_price p left join mv_hotel_seasons s on p.fk_season_id=s.season_id left join mv_hotel_rooms r on p.fk_room_id=r.room_id where p.fk_room_id='$room_id' and s.season_status='Active'");
	$day_of_week = date("D", strtotime($arr[$i]));
	$lowest_price = 9999;
	$lowest_price = ($lowest_price > $res['price_sunday']) ? $res['price_sunday'] : $lowest_price;
	$lowest_price = ($lowest_price > $res['price_monday']) ? $res['price_monday'] : $lowest_price;
	$lowest_price = ($lowest_price > $res['price_tuesday']) ? $res['price_tuesday'] : $lowest_price;
	$lowest_price = ($lowest_price > $res['price_wednesday']) ? $res['price_wednesday'] : $lowest_price;
	$lowest_price = ($lowest_price > $res['price_thursday']) ? $res['price_thursday'] : $lowest_price;
	$lowest_price = ($lowest_price > $res['price_friday']) ? $res['price_friday'] : $lowest_price;
	$lowest_price = ($lowest_price > $res['price_saturday']) ? $res['price_saturday'] : $lowest_price;
	#####################  Add Markup in the Net Price
	$total = $lowest_price + (($lowest_price * $default_markup) / 100);
	$supplier_id = $res['fk_supplier_id'];
	if ($res['fk_supplier_id'] == 0) {
		$supplier_id = $res['fk_hotel_id'];
	}
	$supplier_accommodation_currency = db_scalar("select supplier_accommodation_currency from mv_supplier where supplier_id='$supplier_id'");
	$total = $total * get_exchange_rates($supplier_accommodation_currency, $currency);
	$return_string = "<strong>Pricing From : </strong>" . $currency . " " . number_format(ceil($total), 2);
	if ($total > 0) {
		$return_string .= "<br />*based on " . $res['room_occupancy_type'] . " occupancy";
	}
	if (ceil($total) <= 0) {
		$return_string = "";
	}
	return $return_string;
}
function get_cruise_room_price_info($cruise_id, $room_id, $currency = 'CAD')
{
	$default_markup = db_scalar("select mark_up from mv_config where config_id='1'");
	$lowest_price = db_scalar("select minimum_price from mv_cruise_pricing where fk_cruise_id='" . $cruise_id . "' and fk_room_id='" . $room_id . "' and minimum_price>0 order by minimum_price limit 0,1");
	$total = $lowest_price + (($lowest_price * $default_markup) / 100);
	$supplier_accommodation_currency = db_scalar("select supplier_currency from mv_cruise c left join mv_supplier s on c.fk_supplier_id=s.supplier_id where cruise_id='$cruise_id'");
	$total = $total * get_exchange_rates($supplier_accommodation_currency, $currency);
	if (ceil($total) > 0) {
		$return_string = "<strong>Pricing From : </strong>" . $currency . " " . number_format(ceil($total), 2);
	}
	return $return_string;
}
function get_cruise_price($fk_supplier_id, $fk_cruise_id, $fk_room_id, $check_in_date, $price_method = 'S')
{
	$cruise_result = db_result("select cruise_seasonality from mv_cruise  where cruise_id='$fk_cruise_id'");
	if ($price_method == "S") {
		$price_field_name = "standard_price";
	} else {
		$price_field_name = "discount_price";
	}
	if ($cruise_result['cruise_seasonality'] == "Yes") {
		$season_id = db_scalar("select season_id from mv_cruise_seasons where season_status='Active' and fk_cruise_id='$fk_cruise_id' and season_start_date <= '$check_in_date' and season_end_date >= '$check_in_date' and fk_supplier_id = '$fk_supplier_id' ");
		$price = db_scalar("select $price_field_name from mv_cruise_pricing where fk_cruise_id='" . $fk_cruise_id . "' and fk_room_id='" . $fk_room_id . "' and fk_season_id='$season_id'");
	} else {
		$price = db_scalar("select $price_field_name from mv_cruise_pricing where fk_cruise_id='" . $fk_cruise_id . "' and fk_room_id='" . $fk_room_id . "'");
	}
	if ($cruise_result['cruise_price_mode'] == "Gross") {
		$price = $price - (($price * $cruise_result['cruise_gross_commission']) / 100);
	}
	return $price;
}
function get_package_base_price_info($main_package_id, $current_package_id, $currency = 'CAD')
{
	$pax = 1;
	$default_markup = db_scalar("select mark_up from mv_config where config_id='1'");
	$tour_result = db_result("select * from mv_packages where package_id = '$main_package_id'");
	@extract($tour_result);
	$total = 0;
	$tour_pricing_res = db_result("select p.*,ta.tag_name,s.fk_supplier_id from mv_tour_package_pricing p left join mv_tour_seasons s  on p.fk_season_id=s.season_id left join mv_tour_occupancy o on p.fk_occupancy_id=o.occupancy_id left join mv_tags ta on o.fk_occupancy_type=ta.tag_id  where season_status='Active' and p.fk_package_id='$current_package_id' and s.fk_package_id='$current_package_id' and price_adult>0 order by price_adult limit 0,1");
	$price_pp = $tour_pricing_res['price_adult'];
	$tour_extra_result = db_result("select e.* from mv_tour_pricing_extra e where fk_supplier_id='" . $tour_pricing_res['fk_supplier_id'] . "' and price_status!='Delete' and fk_package_id = '$main_package_id'");
	@extract($tour_extra_result);
	$supplier_activity_currency = db_scalar("select supplier_activity_currency from mv_supplier where supplier_id='" . $tour_pricing_res['fk_supplier_id'] . "'");
	$additional_markup = db_scalar("select additional_markup from mv_tour_packages where package_id='$current_package_id'");
	$total = $total + ($price_pp * $pax);
	$total = $total - (($total * $gross_commission) / 100);
	#####################  Add Markup in the Net Price
	$total = $total + (($total * $default_markup) / 100);
	$total = $total + $additional_markup + $entrance_fee + $gratuity;
	$total = $total * get_exchange_rates($supplier_activity_currency, $currency);
	$return_string = "";
	if (ceil($total) > 0) {
		$return_string = "<strong>Pricing From : </strong>" . $currency . " " . number_format(ceil($total), 2);
	}
	if ($total > 0) {
		#$return_string.="<br /> * Based on ".$tour_pricing_res['tag_name']." occupancy";
	}
	return $return_string;
}
function getDateForSpecificDayBetweenDates($startDate, $endDate, $weekdayNumber)
{
	$startDate = strtotime($startDate);
	$endDate = strtotime($endDate);
	$dateArr = array();
	do {
		if (date("w", $startDate) != $weekdayNumber) {
			$startDate += (24 * 3600); // add 1 day
		}
	} while (date("w", $startDate) != $weekdayNumber);
	while ($startDate <= $endDate) {
		$dateArr[] = date('Y-m-d', $startDate);
		$startDate += (7 * 24 * 3600); // add 7 days
	}
	return ($dateArr);
}
function voucher_supplier_contact_info($supplier_id, $supplier_location)
{
	// Initialize variables with default values
	$supplier_company_name = '';
	$supplier_address = '';
	$supplier_zipcode = '';
	$supplier_location = '';
	$supplier_phone = '';
	$supplier_phone_area_code = '';
	$supplier_phone_country_code = '';
	$supplier_emergency_number = '';
	$supplier_emergency_number_country_code = '';
	$supplier_emergency_number_area_code = '';
	$supplier_emergency_number_secondary = '';
	$supplier_emergency_number_secondary_country_code = '';
	$supplier_emergency_number_secondary_area_code = '';
	// Get supplier data
	$supplier_data = db_result("select * from mv_supplier where supplier_id='" . $supplier_id . "'");
	if (!$supplier_data) {
		return;
	}
	// Check supplier business type
	if (isset($supplier_data['supplier_business_type']) && isbtype($supplier_data['supplier_business_type'], 4)) {
		$supplier_location_arr = explode(",", $supplier_location);
		if (!empty($supplier_location_arr[0])) {
			$supplier_operation_result = db_result("select * from mv_country_operation where fk_supplier_id='$supplier_id' and location_tags_name LIKE '%" . $supplier_location_arr[0] . "%'");
			if (isset($supplier_operation_result['operation_id']) && $supplier_operation_result['operation_id'] > 0) {
				$supplier_company_name = $supplier_data['supplier_company_name'] ?? '';
				$supplier_location = $supplier_operation_result['location_tags_name'] ?? '';
				$supplier_phone = $supplier_operation_result['operation_contact_number'] ?? '';
				$supplier_phone_area_code = $supplier_operation_result['operation_contact_number_area_code'] ?? '';
				$supplier_phone_country_code = $supplier_operation_result['operation_contact_number_country_code'] ?? '';
				$supplier_emergency_number = $supplier_operation_result['operation_emergency_number'] ?? '';
				$supplier_emergency_number_country_code = $supplier_operation_result['operation_emergency_number_country_code'] ?? '';
				$supplier_emergency_number_area_code = $supplier_operation_result['operation_emergency_number_area_code'] ?? '';
			}
		}
	}
	// If no operation data or business type is not 4, use supplier data directly
	if (empty($supplier_company_name)) {
		$supplier_company_name = $supplier_data['supplier_company_name'] ?? '';
		$supplier_address = $supplier_data['supplier_address'] ?? '';
		$supplier_zipcode = $supplier_data['supplier_zipcode'] ?? '';
		$supplier_location = $supplier_data['location_tags_name'] ?? '';
		$supplier_phone = $supplier_data['supplier_phone'] ?? '';
		$supplier_phone_area_code = $supplier_data['supplier_phone_area_code'] ?? '';
		$supplier_phone_country_code = $supplier_data['supplier_phone_country_code'] ?? '';
		$supplier_emergency_number = $supplier_data['supplier_emergency_number'] ?? '';
		$supplier_emergency_number_country_code = $supplier_data['supplier_emergency_number_country_code'] ?? '';
		$supplier_emergency_number_area_code = $supplier_data['supplier_emergency_number_area_code'] ?? '';
		$supplier_emergency_number_secondary = $supplier_data['supplier_emergency_number_secondary'] ?? '';
		$supplier_emergency_number_secondary_country_code = $supplier_data['supplier_emergency_number_secondary_country_code'] ?? '';
		$supplier_emergency_number_secondary_area_code = $supplier_data['supplier_emergency_number_secondary_area_code'] ?? '';
	}
?>
	<tr>
		<td valign="top">
			<h3>Supplier: </h3>
		</td>
		<td>
			<h3><?= $supplier_company_name ?></h3>
		</td>
	</tr>
	<?php if ($supplier_address != "" || $supplier_zipcode != "") { ?>
		<tr>
			<td valign="top">
				<h3>Address: </h3>
			</td>
			<td><?php echo nl2br($supplier_address);
				if ($supplier_zipcode != "") {
					echo "<br />" . $supplier_zipcode;
				}
				echo "<br />" . str_replace(",", " - ", $supplier_location);
				?></td>
		</tr>
	<?php
	}
	if ($supplier_phone != "") {
	?>
		<tr>
			<td valign="top">
				<h3>Phone: </h3>
			</td>
			<td>
				<h3><?php
					echo phone_number_display($supplier_phone_country_code, $supplier_phone_area_code, $supplier_phone);
					?></h3>
			</td>
		</tr>
	<?php } ?>
	<?php if ($supplier_emergency_number != "") { ?>
		<tr>
			<td valign="top">
				<h3>Emergency Contact: </h3>
			</td>
			<td>
				<h3><?php
					echo phone_number_display($supplier_emergency_number_country_code, $supplier_emergency_number_area_code, $supplier_emergency_number);
					?></h3>
			</td>
		</tr>
		<?php if ($supplier_emergency_number_secondary != "") { ?>
			<tr>
				<td valign="top">
					<h3>Secondary Contact: </h3>
				</td>
				<td>
					<h3><?php echo phone_number_display($supplier_emergency_number_secondary_country_code, $supplier_emergency_number_secondary_area_code, $supplier_emergency_number_secondary);
						?></h3>
				</td>
			</tr>
	<?php }
	}
}
function clone_file($file_id, $ftype = "PKG")
{
	############ Copy File Data ####################################
	db_query("insert into mv_files(fk_agent_id,fk_client_id,file_code,file_agent_commission,booking_fee_basis,file_booking_charge,file_taxes,file_markup,file_exchange_rate,file_type,file_destination,file_added_by,file_added_on,file_modified_on,file_old_status,file_current_status,file_received_by,file_primary_staff,file_active_staff,file_request,file_currency,file_departure_location,file_departure_date,file_arrival_date,file_return_date,file_adults,file_infants,file_childrens,file_teens,file_comments,file_next_action_date,file_next_action,file_abandoned_reason,file_depoist_basis,file_depoist_value,file_status,gross_total_sc,gross_tax,agent_commission_type,gross_agent_commission,disp_services_total,file_package_id,file_terms,additional_agent_comm,is_package_file,clone_file_id,file_card_fee,gross_card_fee
	) (select fk_agent_id,fk_client_id,file_code,file_agent_commission,booking_fee_basis,file_booking_charge,file_taxes,file_markup,file_exchange_rate,file_type,file_destination,file_added_by,file_added_on,file_modified_on,file_old_status,file_current_status,file_received_by,file_primary_staff,file_active_staff,file_request,file_currency,file_departure_location,file_departure_date,file_arrival_date,file_return_date,file_adults,file_infants,file_childrens,file_teens,file_comments,file_next_action_date,file_next_action,file_abandoned_reason,file_depoist_basis,file_depoist_value,file_status,gross_total_sc,gross_tax,agent_commission_type,gross_agent_commission,disp_services_total,file_package_id,file_terms,additional_agent_comm,is_package_file,'$file_id',file_card_fee,gross_card_fee from mv_files where  file_id='$file_id')");
	$new_file_id = mysqli_insert_id($GLOBALS['dbcon']);
	if ($ftype == "PKG") {
		$file_code = "PKG-" . str_pad($new_file_id, 5, 0, STR_PAD_LEFT);
		$is_package_file = "Yes";
	} else {
		$file_code = db_scalar("select file_code from mv_files where file_id='$new_file_id' ");
		$file_code = substr($file_code, 0, 3) . "-" . str_pad($new_file_id, 5, 0, STR_PAD_LEFT);
		$is_package_file = "No";
	}
	db_query("update mv_files set file_code='$file_code',file_added_by='" . $_SESSION['sess_agent_id'] . "',file_added_on=now(),is_package_file='$is_package_file' where file_id='$new_file_id'");
	db_query("insert into mv_file_currency  (fk_file_id,target_currency,base_currency,exchange_rate)(select '$file_id',target_currency,base_currency,exchange_rate from mv_file_currency where fk_file_id='$file_id')");
	################################  Add File Accommodation ###########
	db_query("insert into mv_file_accommodation(fk_file_id,file_booking_type,fk_hotel_id,fk_room_id,booked_with_supplier_id,booked_with_supplier_name,room_quantity,booking_duration,check_in_date,check_out_date,check_in_time,check_out_time,booking_price_currency,booking_net_price,booking_gross_price,booking_original_net_price,hotel_extra,hotel_extra_price,booking_engine_ref_number,hotel_transfer_id,file_accommodation_added_by,file_accommodation_added_on,file_accommodation_status,service_status,service_paid_by,service_paid_by_other,service_description,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,pax_no
	)(select '$new_file_id',file_booking_type,fk_hotel_id,fk_room_id,booked_with_supplier_id,booked_with_supplier_name,room_quantity,booking_duration,check_in_date,check_out_date,check_in_time,check_out_time,booking_price_currency,booking_net_price,booking_gross_price,booking_original_net_price,hotel_extra,hotel_extra_price,booking_engine_ref_number,hotel_transfer_id,file_accommodation_added_by,file_accommodation_added_on,file_accommodation_status,service_status,service_paid_by,service_paid_by_other,service_description,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,pax_no from mv_file_accommodation where fk_file_id='$file_id')");
	################################  Add Transfer ###########
	db_query("INSERT INTO mv_file_transfers(fk_file_id,file_booking_type,booking_method,transfer_country,transfer_arrival_country,fk_region_id,fk_pickup_id,fk_dropoff_id,fk_pickup_station_id,fk_dropoff_station_id,fk_dep_airport_id,fk_arrival_airport_id,check_in_date,check_out_date,check_in_time,check_out_time,booked_with_supplier_id,booked_with_supplier_name,booking_price_currency,booking_net_price,booking_engine_ref_number,pickup_depot,dropoff_depot,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,insurance_type,car_type,no_pax,vehicle_id,file_transfer_added_by,file_transfer_added_on,file_transfer_status,booking_description,fk_file_accommodation_id,ferry_name,ferry_class,arrival_time,departure_time,airline,flight_number,record_locator,service_status,voucher_file,transfer_type,service_paid_by,service_paid_by_other,train_name,train_number,booking_gross_price,booking_original_net_price,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,disp_transfer_description_auto
	)(select '$new_file_id',file_booking_type,booking_method,transfer_country,transfer_arrival_country,fk_region_id,fk_pickup_id,fk_dropoff_id,fk_pickup_station_id,fk_dropoff_station_id,fk_dep_airport_id,fk_arrival_airport_id,check_in_date,check_out_date,check_in_time,check_out_time,booked_with_supplier_id,booked_with_supplier_name,booking_price_currency,booking_net_price,booking_engine_ref_number,pickup_depot,dropoff_depot,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,insurance_type,car_type,no_pax,vehicle_id,file_transfer_added_by,file_transfer_added_on,file_transfer_status,booking_description,fk_file_accommodation_id,ferry_name,ferry_class,arrival_time,departure_time,airline,flight_number,record_locator,service_status,voucher_file,transfer_type,service_paid_by,service_paid_by_other,train_name,train_number,booking_gross_price,booking_original_net_price,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,disp_transfer_description_auto from mv_file_transfers where fk_file_id='$file_id')");
	###################### Add File Activity #################################
	db_query("INSERT INTO mv_file_activity(fk_file_id,fk_tour_id,booked_with_supplier_id,booked_with_supplier_name,pax_no,check_in_date,pickup_time,meeting_time,booking_price_currency,booking_net_price,hotel_pickup,hotel_name,meeting_place,occupancy_type,package_id,is_private_tour,notes,booking_engine_ref_number,service_status,file_activity_added_by,file_activity_added_on,file_activity_status,service_paid_by,service_paid_by_other,booking_gross_price,booking_original_net_price,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,check_in_time,tour_pricing_method,suppress_description) (select '$new_file_id',fk_tour_id,booked_with_supplier_id,booked_with_supplier_name,pax_no,check_in_date,pickup_time,meeting_time,booking_price_currency,booking_net_price,hotel_pickup,hotel_name,meeting_place,occupancy_type,package_id,is_private_tour,notes,booking_engine_ref_number,service_status,file_activity_added_by,file_activity_added_on,file_activity_status,service_paid_by,service_paid_by_other,booking_gross_price,booking_original_net_price,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,check_in_time,tour_pricing_method,suppress_description from mv_file_activity where fk_file_id='$file_id')");
	################################ Add Misc #########################################
	db_query("INSERT INTO mv_file_misc_service(fk_file_id,service_type,service_title,service_description,booked_with_supplier_id,booked_with_supplier_name,booking_net_price,booking_price_currency,booking_engine_ref_number,service_location,service_location_ids,service_status,service_added_by,service_added_on,misc_service_status,service_paid_by,service_paid_by_other,booking_gross_price,booking_original_net_price,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,check_in_date,check_out_date,check_in_time,check_out_time,pax_no,is_private_tour,hotel_pickup,hotel_name,pickup_time,occupancy_type,transfer_country,fk_region_id,fk_pickup_id,fk_dropoff_id,pickup_depot,dropoff_depot,dep_transfer_country,dep_region_id,arr_transfer_country,arr_region_id,ferry_name,ferry_class,arrival_time,fk_pickup_station_id,fk_dropoff_station_id,train_name,train_number,fk_country_id,fk_airport_id,fk_arrival_country_id,fk_arrival_region_id,fk_arrival_airport_id,airline,flight_number,record_locator,car_type,insurance_type,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,room_name,no_pax,tour_description) (select '$new_file_id',service_type,service_title,service_description,booked_with_supplier_id,booked_with_supplier_name,booking_net_price,booking_price_currency,booking_engine_ref_number,service_location,service_location_ids,service_status,service_added_by,service_added_on,misc_service_status,service_paid_by,service_paid_by_other,booking_gross_price,booking_original_net_price,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,check_in_date,check_out_date,check_in_time,check_out_time,pax_no,is_private_tour,hotel_pickup,hotel_name,pickup_time,occupancy_type,transfer_country,fk_region_id,fk_pickup_id,fk_dropoff_id,pickup_depot,dropoff_depot,dep_transfer_country,dep_region_id,arr_transfer_country,arr_region_id,ferry_name,ferry_class,arrival_time,fk_pickup_station_id,fk_dropoff_station_id,train_name,train_number,fk_country_id,fk_airport_id,fk_arrival_country_id,fk_arrival_region_id,fk_arrival_airport_id,airline,flight_number,record_locator,car_type,insurance_type,pickup_city,pickup_city_ids,dropoff_city,dropoff_city_ids,room_name,no_pax,tour_description from mv_file_misc_service where fk_file_id='$file_id')");
	##############################  Add File Cruise ######################################
	db_query("INSERT INTO mv_file_cruises ( fk_file_id,check_in_date,fk_cruise_id,fk_room_id,file_cruise_status,booking_price_currency,booking_net_price,service_status,booked_with_supplier_name,booked_with_supplier_id,file_cruise_added_by,file_cruise_added_on,service_paid_by,service_paid_by_other,booking_gross_price,booking_original_net_price,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,booking_engine_ref_number,price_mode,liquid_price,liquid_price_type,liquid_commission,service_description,price_method,commissionable_gross,non_commissionable_gross,fuel_charge,port_charge,excursion_included,excursion_cost
	)(select '$new_file_id',check_in_date,fk_cruise_id,fk_room_id,file_cruise_status,booking_price_currency,booking_net_price,service_status,booked_with_supplier_name,booked_with_supplier_id,file_cruise_added_by,file_cruise_added_on,service_paid_by,service_paid_by_other,booking_gross_price,booking_original_net_price,booking_net_price_sc,net_price_edited,gross_price_edited,voucher_sent,booking_engine_ref_number,price_mode,liquid_price,liquid_price_type,liquid_commission,service_description,price_method,commissionable_gross,non_commissionable_gross,fuel_charge,port_charge,excursion_included,excursion_cost from mv_file_cruises where fk_file_id='$file_id')");
	##############################  Add File Itenerary ######################################
	db_query("insert into mv_file_itinerary(fk_file_id,itinerary_date,itinerary_title,itinerary_hotel,itinerary_description,tag_ids_1,tag_ids_2,tag_ids_3,tag_names_1,tag_names_2,tag_names_3,itinerary_added_by,itinerary_added_on,fk_activity_id,day_id,itinerary_segment,itinerary_meals,fk_tour_id,fk_supplier_id,loc_image_display
	)(select '$new_file_id',itinerary_date,itinerary_title,itinerary_hotel,itinerary_description,tag_ids_1,tag_ids_2,tag_ids_3,tag_names_1,tag_names_2,tag_names_3,itinerary_added_by,itinerary_added_on,fk_activity_id,day_id,itinerary_segment,itinerary_meals,fk_tour_id,fk_supplier_id,loc_image_display from mv_file_itinerary where fk_file_id='$file_id')");
	######################## Add File Service Fees #######################################
	db_query("INSERT INTO mv_file_service_fees(fk_file_id,service_fee_name,service_fee_basis,is_commissionable,service_fee_currency,service_fee,service_fee_added_by,service_fee_added_on,service_fee_status,service_fee_description
	)(select '$new_file_id',service_fee_name,service_fee_basis,is_commissionable,service_fee_currency,service_fee,service_fee_added_by,service_fee_added_on,service_fee_status,service_fee_description from mv_file_service_fees where fk_file_id='$file_id')");
	########################  File Pax #########################
	db_query("insert into mv_file_pax_name (fk_file_id,pax_number,client_name)(select '$new_file_id',pax_number,client_name from mv_file_pax_name where fk_file_id='$file_id')");
	return $new_file_id;
}
function update_meals($id, $check_in_date = '', $check_out_date = '')
{
	/*
	$query = db_query("select f.*,concat_ws(' ',client_salutation,client_first_name,client_last_name) as client_name from mv_files f left join mv_client c on f.fk_client_id=c.client_id  where f.file_id='$id'");
		while($result=mysqli_fetch_array($query)){
			$id=$result['file_id'];
			$date_array=get_all_dates_between($result['file_arrival_date'],$result['file_return_date']);
			$multiday_array=array();
			$multiday_sql=db_query("select t.tour_classification,a.check_in_date,t.tour_days,t.tour_nights from mv_file_activity a left join mv_tours t on a.fk_tour_id=t.tour_id where file_activity_status='Active' and fk_file_id='$id' order by check_in_date");
			while($multiday_res=mysqli_fetch_array($multiday_sql)){
				if($multiday_res[tour_classification]=="1"){
					$end_date = date("Y-m-d",strtotime("+".($multiday_res[tour_nights])." days",strtotime($multiday_res['check_in_date'])));
					$new_array=get_all_dates_between($multiday_res['check_in_date'],$end_date);
					$multiday_array=array_merge($multiday_array,$new_array);
				}
			}
			$cruise_array=array();
			for($i=0;$i<count($date_array);$i++){
						$day_res=db_result("select * from mv_file_itinerary where fk_file_id='$id' and itinerary_date='".$date_array[$i]."'");
						$day=$i+1;
						if(in_array($date_array[$i],$multiday_array)){
							$is_multi_tour=true;
							$is_cruise=false;
							$file_activity_result=db_result("select fk_tour_id,file_activity_id,suppress_description from mv_file_activity  a left join mv_tours t on a.fk_tour_id=t.tour_id where file_activity_status='Active' and fk_file_id='$id' and  check_in_date ='".$date_array[$i]."' order by t.tour_classification ,a.meeting_time,a.pickup_time");
							if($file_activity_result['fk_tour_id']!=""){
								$current_tour_id=$file_activity_result['fk_tour_id'];
								$current_activity_id=$file_activity_result['file_activity_id'];
								$day_id=1;
							}else{
								$day_id=$day_id+1;
							}
						}else{
							$is_multi_tour=false;
							$is_cruise=false;
							$day_id=0;
							$current_tour_id="";
							$current_activity_id="";
						}
						$meal_for_the_day=array();
						if($is_multi_tour){
							$file_activity_res=db_result("select * from mv_file_activity where file_activity_id= '".$current_activity_id."'");
							$itinerary_id=db_scalar("select itinerary_id from mv_file_itinerary where fk_file_id='$id' and fk_activity_id='".$current_activity_id."' and day_id='$day_id' and fk_tour_id='$current_tour_id' and fk_supplier_id='".$file_activity_res[booked_with_supplier_id]."'");
							if($itinerary_id==""){
									$it_res=db_result("select * from mv_itinerary_description i left join mv_tours t on i.fk_tour_id=t.tour_id where  fk_tour_id='$current_tour_id' and day_id='$day_id'");
									db_query("insert into mv_file_itinerary SET fk_supplier_id='".$file_activity_res[booked_with_supplier_id]."',fk_file_id='$id' , fk_activity_id='".$current_activity_id."',itinerary_title='".addslashes($it_res[itinerary_title])."',itinerary_description='".addslashes($it_res[itinerary_description])."',day_id='".$it_res[day_id]."',fk_tour_id='".$it_res[fk_tour_id]."',itinerary_added_by='".$it_res[itinerary_added_by]."',itinerary_added_on=now(),tag_names_1='".$it_res[tag_names_1]."',tag_names_2='".$it_res[tag_names_2]."',tag_names_3='".$it_res[tag_names_3]."',tag_ids_1='".$it_res[tag_ids_1]."',tag_ids_2='".$it_res[tag_ids_2]."',tag_ids_3='".$it_res[tag_ids_3]."',itinerary_hotel='".$it_res[itinerary_hotel]."',itinerary_segment='".$it_res[itinerary_segment]."',loc_image_display='".$it_res[loc_image_display]."'");
									$itinerary_id=mysql_insert_id();
							}
							$day_res=db_result("select * from mv_file_itinerary i left join mv_tours t on i.fk_tour_id=t.tour_id where  itinerary_id='$itinerary_id'");
						}
						if($is_cruise){
							$day_res=db_result("select s.*,c.cruise_name from mv_cruise_schedule s left join mv_cruise c on s.fk_cruise_id=c.cruise_id where  fk_cruise_id='$current_cruise_id' and day_id='$day_id' order by day_id,cruise_day_id");
						}
						$meal_for_the_day=array();
						#$meal_for_the_day=explode(",",$day_res[itinerary_meals]);
						$accommodation_res=db_result("select a.*,s.supplier_company_name from mv_file_accommodation a left join mv_supplier s on a.fk_hotel_id=s.supplier_id where fk_file_id = '$id' and file_accommodation_status='Active' and '".$date_array[$i]."' >= check_in_date and '".$date_array[$i]."' < check_out_date ");
						$checkout_accommodation_res=db_result("select a.*,s.supplier_company_name from mv_file_accommodation a left join mv_supplier s on a.fk_hotel_id=s.supplier_id where fk_file_id = '$id' and file_accommodation_status='Active' and check_out_date='".$date_array[$i]."'");
						if($accommodation_res[fk_room_id]>0){
							$room_detail_res=db_result("select r.*,t.tag_name as room_meal,t2.tag_name as property_meal from mv_hotel_rooms r left join mv_room_detail d on r.fk_room_detail_id=d.room_detail_id left join mv_tags t on d.room_detail_meal_plan=t.tag_id left join mv_tags t2 on r.room_meal_plan=t2.tag_id where room_id='".$accommodation_res[fk_room_id]."'");
							if($room_detail_res['room_meal_plan']>0){
								$meal_for_the_day[]=$room_detail_res['property_meal'];
							}else{
								$meal_for_the_day[]=$room_detail_res['room_meal'];
							}
						}
						#######################  Meal For Check out Accommdation  ###########################
						if($checkout_accommodation_res[fk_room_id]>0){
							$checkout_room_detail_res=db_result("select r.*,t.tag_name as room_meal,t2.tag_name as property_meal from mv_hotel_rooms r left join mv_room_detail d on r.fk_room_detail_id=d.room_detail_id left join mv_tags t on d.room_detail_meal_plan=t.tag_id left join mv_tags t2 on r.room_meal_plan=t2.tag_id where room_id='".$checkout_accommodation_res[fk_room_id]."'");
							if($checkout_room_detail_res['room_meal_plan']>0){
								$checkout_meal_plan=$checkout_room_detail_res['property_meal'];
							}else{
								$checkout_meal_plan=$checkout_room_detail_res['room_meal'];
							}
						}
						if("B&B"==$checkout_meal_plan || "FB"==$checkout_meal_plan || "HB"==$checkout_meal_plan || "Breakfast"==$checkout_meal_plan){
							   $meal_for_the_day[]="B&B";
						}
						###############################################################################
						if($is_multi_tour){
							$tour_meal_sql=db_query("select itinerary_meals from mv_file_activity a left join mv_itinerary_description d on (d.fk_tour_id=a.fk_tour_id and a.booked_with_supplier_id=d.fk_supplier_id)   where file_activity_status = 'Active' and fk_file_id = '$id' and day_id='$day_id'");
						}else{
							$tour_meal_sql=db_query("select itinerary_meals from mv_file_activity a left join mv_itinerary_description d on (d.fk_tour_id=a.fk_tour_id and a.booked_with_supplier_id=d.fk_supplier_id)   where file_activity_status = 'Active' and fk_file_id = '$id' and check_in_date = '".$date_array[$i]."'");
						}
						while($tour_meal_res=mysqli_fetch_array($tour_meal_sql)){
							$meal_array=explode(",",$tour_meal_res[itinerary_meals]);
							$meal_for_the_day=array_merge($meal_for_the_day,$meal_array);
						}
						  $final_meal_array=array_unique($meal_for_the_day);
						  $meal_string="";
						  if($is_cruise && $day_res['cruise_meals']!=""){
							$meal_string.=$day_res['cruise_meals'].",";
						  }
						  if(in_array("B&B",$final_meal_array) || in_array("FB",$final_meal_array) || in_array("HB",$final_meal_array) || in_array("Breakfast",$final_meal_array)){
							   if($accommodation_res['check_in_date']!=$date_array[$i] || $checkout_accommodation_res['check_out_date']==$date_array[$i] || ($is_multi_tour && $result['file_arrival_date']!=$date_array[$i]) || $is_cruise){
									$meal_string.="Breakfast,";
							   }
						  }
						  if(in_array("L",$final_meal_array) || in_array("FB",$final_meal_array) || in_array("Lunch",$final_meal_array)){
							  if($accommodation_res['check_in_date']!=$date_array[$i] && $accommodation_res['check_out_date']!=$date_array[$i]){
								$meal_string.="Lunch,";
							  }
						  }
						  if(in_array("D",$final_meal_array) || in_array("FB",$final_meal_array)  || in_array("HB",$final_meal_array) || in_array("Dinner",$final_meal_array)){
							  if(($accommodation_res['check_out_date']!=$date_array[$i] && $accommodation_res['check_out_date']!="") || ($is_multi_tour && $result['file_return_date']!=$date_array[$i])) {
								$meal_string.="Dinner,";
							  }
						  }
						  $meal_string=substr($meal_string,0,-1);
						  if($meal_string!=""){
								$all_meals_array=explode(",",$meal_string);
								$all_meals_array=array_unique($all_meals_array);
								$meal_string=implode(",",$all_meals_array);
						  }
						  if($day_res[itinerary_id]>0){
								db_query("update mv_file_itinerary set itinerary_meals='$meal_string' where itinerary_id='".$day_res[itinerary_id]."'");
						  }else{
								db_query("insert into mv_file_itinerary set fk_file_id='$id' ,itinerary_date='".$date_array[$i]."',itinerary_meals='$meal_string'");
						  }
			}
		}
	*/
}
function submenu($submid, $mid)
{
	?>
	<div id="megamenu<?= $submid ?>" class="megamenu">
		<?php $submenu_sql = db_query("select * from mv_location_highlight where fk_location_id='" . $mid . "' and highlight_status='Active' order by highlight_id limit 5");
		if (mysqli_num_rows($submenu_sql) > 0) {
		?>
			<div class="column">
				<ul>
					<?php while ($submenu = mysqli_fetch_array($submenu_sql)) {
						if (strstr($submenu['highlight_url'], 'http://')) {
							$link = '<a href="' . $submenu['highlight_url'] . '">';
						} else {
							$link = '<a href="http://' . $submenu['highlight_url'] . '">';
						} ?>
						<li><?php echo $link; ?><?= $submenu['highlight_title'] ?></a></li>
					<?php } ?>
					<li><a href="#">Other Destination</a></li>
				</ul>
			</div>
		<?php }
		$submenu_sql2 = db_query("select * from mv_location_top5 where fk_location_id='" . $mid . "' and top5_status='Active' order by top5_id limit 5");
		if (mysqli_num_rows($submenu_sql2) > 0) {
		?>
			<div class="column" style="padding-left:50px;">
				<ul>
					<?php while ($submenu2 = mysqli_fetch_array($submenu_sql2)) {
						if (strstr($submenu2['top5_url'], 'http://')) {
							$link2 = '<a href="' . $submenu2['top5_url'] . '">';
						} else {
							$link2 = '<a href="http://' . $submenu2['top5_url'] . '">';
						}
					?>
						<li><?= $link2 ?><?= $submenu2['top5_title'] ?></a></li>
					<?php } ?>
				</ul>
			</div>
		<?php } ?>
	</div>
	<?php	}
/*
function location_slug_list($tag_id){
    global $loc_arr;
    $tag_parent = db_scalar("select tag_parent_id  from mv_tags where tag_id='$tag_id' and tag_status='Active' and tag_id!='430'");
 if($tag_parent>0){
		$loc_arr[]=$tag_parent;
  location_slug_list($tag_parent);
 }
	return $loc_arr;
}
*/
function location_slug_list($tag_id)
{
	global $loc_arr;
	$loc_arr = array();
	return location_slug_list1($tag_id);
}
function location_slug_list1($tag_id, $loc_arr = [])
{
	// gunakan db_result yang sudah pakai mysqli
	$tag_result = db_result("SELECT tag_parent_id, location_slug FROM mv_tags WHERE tag_id='$tag_id' AND tag_status='Active' AND tag_id!='2'");
	if ($tag_result) {
		$tag_parent = $tag_result['tag_parent_id'];
		if ($tag_parent > 0) {
			$loc_arr[] = $tag_result['location_slug'];
			$loc_arr = location_slug_list1($tag_parent, $loc_arr);
		}
		if ($tag_parent == '2' && $tag_id != '430') {
			$loc_arr[] = "destinations";
		}
	}
	return array_reverse($loc_arr);
}
/*function location_slug_list2($tag_id){
    $tag_result = db_result("select location_slug  from mv_tags where tag_id='$tag_id' and tag_status='Active'");
	$location_slug=$tag_result[location_slug];
 	return $location_slug;
}*/
function copy_activity($tour_id, $tour_name, $supplier_tour_name, $tour_slug, $supplier_id)
{
	/*
	$tour_data=db_result("select * from mv_tours where tour_id='$tour_id'");
	extract($tour_data);
	db_query("INSERT INTO mv_tours  set fk_supplier_id='$supplier_id',tour_slug='$tour_slug',tour_name='$tour_name',supplier_tour_name='$supplier_tour_name',tour_country_tag_ids='$tour_country_tag_ids',
is_private='$is_private',activity_segment='$activity_segment',tour_available_to='$tour_available_to',tour_country='$tour_country',tour_city_tag_ids='$tour_city_tag_ids',
tour_city='$tour_city',tour_meeting_point='$tour_meeting_point',tour_departure='$tour_departure',tour_return_city='$tour_return_city',tour_return_city_tag_ids='$tour_return_city_tag_ids',tour_type='$tour_type'
,tour_min_pax='$tour_min_pax',tour_max_pax='$tour_max_pax',tour_languages='$tour_languages',tour_hotel_pickup='$tour_hotel_pickup',tour_hotel_dropoff='$tour_hotel_dropoff',
tour_length='$tour_length',tour_days='$tour_days',tour_nights='$tour_nights',tour_activity_type='$tour_activity_type',tour_start_time='$tour_start_time',tour_end_time='$tour_end_time',
no_breakfasts='$no_breakfasts',no_lunches='$no_lunches',no_dinners='$no_dinners',no_wine_tastings='$no_wine_tastings',no_cooking_course='$no_cooking_course',
tour_includes='$tour_includes',tour_includes_other=''$tour_includes_other,tour_excludes='$tour_excludes',tour_excludes_other='$tour_excludes_other',tour_interest='$tour_interest',
tour_interest_other='$tour_interest_other',tour_description='$tour_description',tour_base_currency='$tour_base_currency',tour_status='$tour_status',
tour_added_by='".$_SESSION['sess_agent_id']."',tour_added_on=now(),tour_schedule='$tour_schedule',tour_availability='$tour_availability',tour_availability_start_date='$tour_availability_start_date',
tour_availability_end_date='$tour_availability_end_date',tour_availability_days='$tour_availability_days',tour_classification='$tour_classification',
tour_meeting_maplink='$tour_meeting_maplink',tour_meeting_address='$tour_meeting_address',meeting_time='$meeting_time',pickup_time='$pickup_time',
tour_pickup_hotel='$tour_pickup_hotel',map_latitude='$map_latitude',map_longitude='$map_longitude',map_view='$map_view',tour_short_description='$tour_short_description',
tour_published='No',map_type='$map_type',tour_map_image='$tour_map_image',public_pricing='$public_pricing',private_pricing='$private_pricing',group_pricing='$group_pricing',
tour_price='$tour_price',page_heading_content='$page_heading_content'");
	*/
	db_query("INSERT INTO mv_tours (fk_supplier_id,tour_slug,tour_name,supplier_tour_name,tour_country_tag_ids,is_private,activity_segment,tour_available_to,tour_country,tour_city_tag_ids,tour_city,tour_meeting_point,tour_departure,tour_return_city,tour_return_city_tag_ids,tour_type,tour_min_pax,tour_max_pax,tour_languages,tour_hotel_pickup,tour_hotel_dropoff,tour_length,tour_days,tour_nights,tour_activity_type,tour_start_time,tour_end_time,no_breakfasts,no_lunches,no_dinners,no_wine_tastings,no_cooking_course,tour_includes,tour_includes_other,tour_excludes,tour_excludes_other,tour_interest,tour_interest_other,tour_description,tour_base_currency,tour_status,tour_added_by,tour_added_on,tour_schedule,tour_availability,tour_availability_start_date,tour_availability_end_date,tour_availability_days,tour_classification,tour_meeting_maplink,tour_meeting_address,meeting_time,pickup_time,tour_pickup_hotel,map_latitude,map_longitude,map_view,tour_short_description,tour_published,map_type,tour_map_image,public_pricing,private_pricing,group_pricing,tour_price,page_heading_content,tour_highlights,tour_highlights_other,tour_price_type) (select '$supplier_id','$tour_slug','$tour_name','$supplier_tour_name',tour_country_tag_ids,is_private,activity_segment,tour_available_to,tour_country,tour_city_tag_ids,tour_city,tour_meeting_point,tour_departure,tour_return_city,tour_return_city_tag_ids,tour_type,tour_min_pax,tour_max_pax,tour_languages,tour_hotel_pickup,tour_hotel_dropoff,tour_length,tour_days,tour_nights,tour_activity_type,tour_start_time,tour_end_time,no_breakfasts,no_lunches,no_dinners,no_wine_tastings,no_cooking_course,tour_includes,tour_includes_other,tour_excludes,tour_excludes_other,tour_interest,tour_interest_other,tour_description,tour_base_currency,tour_status,'" . $_SESSION['sess_agent_id'] . "',now(),tour_schedule,tour_availability,tour_availability_start_date,tour_availability_end_date,tour_availability_days,tour_classification,tour_meeting_maplink,tour_meeting_address,meeting_time,pickup_time,tour_pickup_hotel,map_latitude,map_longitude,map_view,tour_short_description,'No',map_type,tour_map_image,public_pricing,private_pricing,group_pricing,tour_price,page_heading_content,tour_highlights,tour_highlights_other,tour_price_type from mv_tours where tour_id='$tour_id')");
	$new_tour_id = mysqli_insert_id($GLOBALS['dbcon']);
	$tour_code = "TUR-" . str_pad($new_tour_id, 5, 0, STR_PAD_LEFT);
	db_query("UPDATE mv_tours SET tour_code ='$tour_code' WHERE tour_id ='$new_tour_id'");
	db_query("INSERT INTO mv_tour_sights (fk_supplier_id,fk_tour_id,locations_name,locations_ids,
	sight_status,sight_added_by,sight_added_on) (select '$supplier_id','$new_tour_id',locations_name,locations_ids,
	sight_status,'" . $_SESSION['sess_agent_id'] . "',now() from mv_tour_sights where fk_supplier_id='$supplier_id' and sight_status='Active' and fk_tour_id='$tour_id')");
	db_query("insert into mv_tour_maps (fk_tour_id,map_latitude,map_longitude,map_label) (select '$new_tour_id',map_latitude,map_longitude,map_label from mv_tour_maps where fk_tour_id='$tour_id' )");
	db_query("insert into mv_itinerary_description (fk_supplier_id,fk_tour_id,day_id,itinerary_title,itinerary_hotel,itinerary_segment,itinerary_description,itinerary_meals,tag_ids_1,tag_ids_2,tag_ids_3,tag_names_1,tag_names_2,tag_names_3,itinerary_added_by,itinerary_added_on)(select '$supplier_id','$new_tour_id',day_id,itinerary_title,itinerary_hotel,itinerary_segment,itinerary_description,itinerary_meals,tag_ids_1,tag_ids_2,tag_ids_3,tag_names_1,tag_names_2,tag_names_3,'" . $_SESSION['sess_agent_id'] . "',now() from mv_itinerary_description where  fk_tour_id='$tour_id')");
	db_query("insert into mv_tour_images (fk_tour_id,fk_package_id,image_title,image_description,image_name,image_type,image_status,image_added_by,image_added_on,image_order) (select '$new_tour_id',fk_package_id,image_title,image_description,image_name,image_type,image_status,'" . $_SESSION['sess_agent_id'] . "',now(),image_order from mv_tour_images where fk_tour_id='$tour_id')");
	db_query("insert into mv_tour_start_time (fk_tour_id,tour_start_time,tour_end_time) (select '$new_tour_id',tour_start_time,tour_end_time from mv_tour_start_time where fk_tour_id='$tour_id')");
	return $new_tour_id;
}
function last_tagname_from_id($loc_ids)
{
	$locarr = explode(",", $loc_ids);
	$locarr = array_filter($locarr);
	$loc_id = array_pop($locarr);
	return db_scalar("select trim(tag_name) from mv_tags where tag_id='" . $loc_id . "'");
}
function generate_auto_invoice($id, $ag_comm, $cnf)
{
	$pdf_del_sql = db_query("select pdf_file_name from mv_invoice_files where fk_file_id='$id' and is_agent_invoice='$ag_comm' order by invoice_id desc limit 1");
	if (mysqli_num_rows($pdf_del_sql) > 0) {
		while ($pdf_del_res = mysqli_fetch_array($pdf_del_sql)) {
			if (file_exists($pdf_del_res['pdf_file_name'])) {
				unlink($pdf_del_res['pdf_file_name']);
			}
		}
	}
	$result = db_result("select * from mv_files where file_id = '$id'");
	$disp_agent_comm = $ag_comm;
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/config/lang/eng.php');
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/tcpdf.php');
	if (!class_exists('MYPDF')) {
		class MYPDF extends TCPDF
		{
			//Page header
			public function Header()
			{
				//$this->Cell(790, 1, '', 0, 1, 'C', 0, '', 1);
			}
			// Page footer
			public function Footer()
			{
				$this->SetFont('helvetica', 'B', 8);
				$this->Cell(125, 5, 'Page: ' . $this->PageNo() . '', 0, 0, 'C', 0, '', 1);
				$this->Cell(140, 1, 'Printed: ' . date("j F Y, g:i A") . ' ', 0, 1, 'R', 0, '', 1);
				//$this->Cell(140, 1, 'Printed: 25 July 2014, 6:53 AM', 0, 1, 'R', 0, '', 1);
			}
		}
	}
	$margin_left = 20;
	$margin_top = 30;
	$margin_right = 20;
	$margin_bottom = 30;
	$pdf = new MYPDF('P', 'px', 'A4', true);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor(SITE_WS_PATH);
	$pdf->SetTitle('Pricing Summary');
	$pdf->SetSubject("Voucher - File " . ($result['file_code'] ?? ''));
	$pdf->SetKeywords("Voucher - File " . ($result['file_code'] ?? ''));
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->SetMargins($margin_left, $margin_top);
	$pdf->SetHeaderMargin(25);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, $margin_bottom);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	// Initialize language array if not defined
	if (!isset($l)) {
		$l = array();
	}
	$pdf->setLanguageArray($l);
	$pdf->AddPage();
	$pdf->SetFont('', '', 6);
	$pdf->SetTextColor('108', '108', '108');
	ob_start();
	$pdf->SetFont('', '', 8);
	include(SITE_FS_PATH . "/" . AGENT_ADMIN_DIR . "/voucher/invoice_pdf.inc.php");
	$content = ob_get_contents();
	ob_clean();
	$pdf->writeHTML($content, true, 0, true, 0);
	$file_code = isset($result['file_code']) ? $result['file_code'] : '';
	$my_file_name = UP_FILES_FS_PATH . "/invoice/invoice_" . $file_code . "_" . date("Y-m-d_H-i-s", time()) . ".pdf";
	$pdf->Output($my_file_name, 'F');
	// Insert invoice file record
	db_query("insert into mv_invoice_files set fk_file_id='$id',pdf_file_name='$my_file_name',file_added_by='" . $_SESSION['sess_agent_id'] . "',file_added_on=now(),is_agent_invoice='$disp_agent_comm', is_detail_pricing='$cnf'");
	// Logging
	$log_title = "Invoice generated for file (" . $result['file_code'] . ")";
	$sql = ""; // Initialize empty SQL string for logging
	db_query("insert into mv_daily_log set
		employee_id='" . $_SESSION['sess_agent_id'] . "',
		log_title='" . addslashes($log_title) . "',
		log_action='Delete',
		log_added_date=now(),
		log_mysql_query='" . addslashes($sql) . "',
		log_url='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "',
		log_user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "'");
	$log_description = "Invoice generated.";
	db_query("insert into mv_file_activity_log set
		fk_file_id='$id',
		fk_agent_id='" . $_SESSION['sess_agent_id'] . "',
		activity_added_on=now(),
		activity_description='" . addslashes($log_description) . "'");
}
function generate_auto_invoice_new($id, $ag_comm, $cnf)
{
	$pdf_del_sql = db_query("select pdf_file_name from mv_invoice_files where fk_file_id='$id' and is_agent_invoice='$ag_comm' order by invoice_id desc limit 1");
	if (mysqli_num_rows($pdf_del_sql) > 0) {
		while ($pdf_del_res = mysqli_fetch_array($pdf_del_sql)) {
			if (!empty($pdf_del_res['pdf_file_name']) && file_exists($pdf_del_res['pdf_file_name'])) {
				unlink($pdf_del_res['pdf_file_name']);
			}
		}
	}
	$result = db_result("select * from mv_files where file_id = '$id'");
	if (!$result) {
		return false;
	}
	$disp_agent_comm = $ag_comm;
	$sql = ""; // Initialize $sql variable
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/config/lang/eng.php');
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/tcpdf.php');
	if (!class_exists('MYPDF')) {
		class MYPDF extends TCPDF
		{
			public function Header() {}
			public function Footer()
			{
				$this->SetFont('helvetica', 'B', 8);
				$this->Cell(125, 5, 'Page: ' . $this->PageNo(), 0, 0, 'C', 0, '', 1);
				$this->Cell(140, 1, 'Printed: ' . date("j F Y, g:i A") . ' ', 0, 1, 'R', 0, '', 1);
			}
		}
	}
	$margin_left = 20;
	$margin_top = 30;
	$margin_right = 20;
	$margin_bottom = 30;
	$pdf = new MYPDF('P', 'px', 'A4', true);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor(SITE_FS_PATH);
	$pdf->SetTitle('Pricing Summary');
	$pdf->SetSubject("Voucher - File " . $result['file_code']);
	$pdf->SetKeywords("Voucher - File " . $result['file_code']);
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->SetMargins($margin_left, $margin_top);
	$pdf->SetHeaderMargin(25);
	$pdf->SetAutoPageBreak(TRUE, $margin_bottom);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$l = array(); // Initialize $l array
	$pdf->setLanguageArray($l);
	$pdf->setLanguageArray($l);
	$pdf->AddPage();
	$pdf->SetFont('', '', 6);
	$pdf->SetTextColor('108', '108', '108');
	ob_start();
	$pdf->SetFont('', '', 8);
	include(SITE_FS_PATH . "/" . AGENT_ADMIN_DIR . "/voucher/invoice_pdf.inc.php");
	$content = ob_get_contents();
	ob_clean();
	$pdf->writeHTML($content, true, 0, true, 0);
	$my_file_name = UP_FILES_FS_PATH . "/invoice/invoice_" . $result['file_code'] . "_" . date("Y-m-d_H-i-s", time()) . ".pdf";
	$pdf->Output($my_file_name, 'F');
	$log_title = "Invoice generated for file (" . $result['file_code'] . ")";
	db_query("insert into mv_daily_log set employee_id='" . $_SESSION['sess_agent_id'] . "',log_title='" . addslashes($log_title) . "',log_action='Delete',log_added_date=now(),log_mysql_query=''," . // Removed addslashes($sql)
		"log_url='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "',log_user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "'");
	$log_title = "Invoice generated for file (" . $result['file_code'] . ")";
	db_query("insert into mv_daily_log set employee_id='" . $_SESSION['sess_agent_id'] . "',log_title='" . addslashes($log_title) . "',log_action='Delete',log_added_date=now(),log_mysql_query='" . addslashes($sql) . "',log_url='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "',log_user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "'");
	$log_description = "Invoice generated.";
	db_query("insert into mv_file_activity_log set fk_file_id='$id',fk_agent_id='" . $_SESSION['sess_agent_id'] . "',activity_added_on=now(),activity_description='" . addslashes($log_description) . "'");
}
function check_detail_pricing($id)
{
	$sql = db_scalar("select is_detail_pricing from mv_invoice_files where fk_file_id='$id' order by invoice_id desc limit 1");
	return $sql;
}
function get_list($parent)
{
	global $men;
	$children = db_query("SELECT * FROM mv_tags WHERE tag_parent_id = '" . $parent . "' and location_type='2' and tag_status!='Delete'");
	$items = array();
	while ($row = mysqli_fetch_array($children)) {
		$men[] = $row['tag_id'];
		get_list($row['tag_id']);
	}
	return $men;
}
function get_location_link($tag_ids)
{
	$loc_data = "";
	$loc_tag = explode(",", $tag_ids);
	$loc_tag = array_filter($loc_tag);
	foreach ($loc_tag as $locval) {
		if ($locval != '430') {
			$loc_info = db_result("select is_show_in_dest,tag_name from mv_tags where tag_id='$locval'");
			$loc_name = $loc_info['tag_name'];
			$slugs = implode("/", location_slug_list($locval));
			if ($loc_info['is_show_in_dest'] == "Yes") {
				$loc_data .= "<a target='_blank' style='text-decoration:underline; color:#600' href='" . SITE_WS_PATH . "/" . $slugs . ".htm'>" . $loc_name . "</a>, ";
			} else {
				$loc_data .= $loc_name . ", ";
			}
		}
	}
	return substr($loc_data, 0, -2);
}
function image_watermark($image, $watermark_type = 'default')
{
	// Debug log
	error_log("Image watermark called with type: $watermark_type for image: $image");
	// Validasi input
	if (!file_exists($image)) {
		error_log("Image watermark failed: Image does not exist - $image");
		return false;
	}
	// Determine watermark file based on type
	$watermark_files = [
		'default' => 'logo_w.png',
		'italy' => 'logo_w.png',
		'greece' => 'logo_grc_w.png',
		'none' => null
	];
	// Debug log watermark file selection
	$selected_file = isset($watermark_files[$watermark_type]) ? $watermark_files[$watermark_type] : 'not found';
	error_log("Watermark file selected: $selected_file for type: $watermark_type");
	// If no watermark requested
	if ($watermark_type === 'none' || !isset($watermark_files[$watermark_type])) {
		return true;
	}
	// Path watermark
	$overlay = SITE_FS_PATH . '/images/' . $watermark_files[$watermark_type];
	if (!file_exists($overlay)) {
		error_log("Image watermark failed: Watermark image does not exist - $overlay");
		return false;
	}
	// Load watermark image
	$watermark = imagecreatefrompng($overlay);
	if (!$watermark) {
		error_log("Image watermark failed: Could not create watermark image");
		return false;
	}
	// Get image extension
	$extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
	// Load source image based on type
	$photo = null;
	switch ($extension) {
		case 'jpg':
		case 'jpeg':
			$photo = imagecreatefromjpeg($image);
			break;
		case 'png':
			$photo = imagecreatefrompng($image);
			break;
		case 'gif':
			$photo = imagecreatefromgif($image);
			break;
		default:
			error_log("Image watermark failed: Unsupported image type - $extension");
			return false;
	}
	if (!$photo) {
		error_log("Image watermark failed: Could not create source image");
		return false;
	}
	// Enable alpha blending
	imagealphablending($photo, true);
	imagesavealpha($photo, true);
	// Calculate watermark position
	$offset = 10;
	$watermark_width = imagesx($watermark);
	$watermark_height = imagesy($watermark);
	$photo_width = imagesx($photo);
	$photo_height = imagesy($photo);
	// Position watermark in bottom right corner
	$dest_x = $photo_width - $watermark_width - $offset;
	$dest_y = $photo_height - $watermark_height - $offset;
	// Apply watermark
	imagecopy($photo, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height);
	// Save watermarked image
	$success = false;
	switch ($extension) {
		case 'jpg':
		case 'jpeg':
			$success = imagejpeg($photo, $image, 90);
			break;
		case 'png':
			$success = imagepng($photo, $image, 9);
			break;
		case 'gif':
			$success = imagegif($photo, $image);
			break;
	}
	// Cleanup
	imagedestroy($photo);
	imagedestroy($watermark);
	if (!$success) {
		error_log("Image watermark failed: Could not save watermarked image");
		return false;
	}
	return true;
}
// Function to get available watermark options
function get_watermark_options()
{
	return [
		'none' => 'No Watermark',
		'default' => 'Default Logo',
		'italy' => 'Italy Logo',
		'greece' => 'Greece Logo'
	];
}
// Function to generate watermark selector HTML
function generate_watermark_selector($selected = 'default', $field_name = 'watermark_type')
{
	$options = get_watermark_options();
	$html = '<select name="' . $field_name . '" id="' . $field_name . '" class="form-control">';
	foreach ($options as $value => $label) {
		$selected_attr = ($value === $selected) ? ' selected="selected"' : '';
		$html .= '<option value="' . $value . '"' . $selected_attr . '>' . $label . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function get_italy_url($slugs)
{
	if (count($slugs) == 1) {
		$loc_slug = $slugs[0];
		$loc_parent = 'italy';
	} else if (count($slugs) == 2) {
		$loc_slug = $slugs[1];
		$loc_parent = $slugs[0];
	} else if (count($slugs) == 3) {
		$loc_slug = $slugs[2];
		$loc_parent = $slugs[1];
	} else if (count($slugs) == 4) {
		$loc_slug = $slugs[3];
		$loc_parent = $slugs[2];
	}
	$url = SITE_WS_PATH . "/$loc_parent/$loc_slug";
	return $url;
}
function get_tour_price_from($tour_id, $price, $currency = 'USD')
{
	//$default_markup=db_scalar("select mark_up from mv_config where config_id='1'");
	$tour_result = db_result("select * from mv_tours where tour_id = '$tour_id'");
	@extract($tour_result);
	$total = $tour_price * get_exchange_rates($tour_base_currency, $currency);
	if (ceil($total) > 0) {
		$return_string = "<strong>Price Per Person : </strong>" . $currency . " " . number_format(ceil($total), 2);
	} else {
		$return_string = "";
	}
	return $return_string;
}
function get_currency_rate($price, $currency1 = 'EUR', $currency2 = 'EUR')
{
	$total = $price * get_exchange_rates($currency1, $currency2);
	if (ceil($total) > 0) {
		$return_string = $currency2 . " " . ceil($total);
	} else {
		$return_string = "";
	}
	return $return_string;
}
function create_file($post_vars)
{
	@extract($post_vars);
	db_query("insert into mv_client SET client_first_name ='" . $first_name . "' ,client_last_name ='" . $last_name . "' ,client_email ='" . $email_address . "',client_last_contacted =now(),client_added_date =now() ,client_added_by ='1',client_phone='" . $contact_number . "'");
	$client_id = mysqli_insert_id($GLOBALS['dbcon']);
	$client_code = "CL-" . str_pad($client_id, 5, 0, STR_PAD_LEFT);
	db_query("UPDATE mv_client SET client_code ='$client_code' WHERE client_id ='$client_id'");
	######################################################
	$agent_id = 0;
	$file_current_status = "3";
	$file_request = "1";
	$file_type = "4";
	$file_received_by = "2";
	$qry = "";
	if (count($_SESSION['sess_cart']) > 0) {
		$qry .= "select concat(c.tour_date,' ',c.tour_time) as start_date,t.tour_country_tag_ids  as location,t.tour_days,c.adult,c.child,c.infant,tour_net_price as cost,tour_gross_price as price from tbl_tours_cart c left join mv_tours t on c.tour_id=t.tour_id where cart_id in (" . implode(",", $_SESSION['sess_cart']) . ")";
	}
	if (count($_SESSION['sess_cart']) > 0 && count($_SESSION['sess_transfer_cart']) > 0) {
		$qry .= " union ";
	}
	if (count($_SESSION['sess_transfer_cart']) > 0) {
		$qry .= "select concat(pickup_date,' ',pickup_time) as start_date,'430' as location,0 as tour_days,no_pax as adult,'0' as child,'0' as infant,transfer_net_price as cost,transfer_gross_price as price from tbl_transfer_cart where cart_id in (" . implode(",", $_SESSION['sess_transfer_cart']) . ")";
	}
	$qry .= " order by start_date ";
	$cart_items = db_query($qry);
	$file_start_date = "";
	$file_return_date = "";
	$file_destination_list = "";
	$total_cost = 0;
	$total_price = 0;
	while ($cart_items_row = mysqli_fetch_array($cart_items)) {
		if ($file_start_date == "") {
			$file_start_date = $file_return_date = $cart_items_row['start_date'];
			$file_adults = $cart_items_row['adult'];
			$file_childrens = $cart_items_row['child'];
			$file_infants = $cart_items_row['infant'];
		}
		if ($file_destination_list == "") {
			$file_destination_list = $cart_items_row['location'];
		}
		if ($cart_items_row['tour_days'] > 1) {
			$days_to_add = $cart_items_row['tour_days'];
			$new_return_date = date('Y-m-d', strtotime("+$days_to_add days", strtotime($cart_items_row['start_date'])));
		} else {
			$new_return_date = $cart_items_row['start_date'];
		}
		if ($file_return_date < $new_return_date) {
			$file_return_date = $new_return_date;
		}
		$total_cost = $total_cost + $cart_items_row['cost'];
		$total_price = $total_price + $cart_items_row['price'];
	}
	$location = explode(",", $file_destination_list);
	$file_destination = $location[0];
	$file_departure_date = $file_arrival_date = $file_start_date;
	$file_currency = "EUR";
	$file_comments = addslashes($more_detail);
	$file_agent_commission = 0;
	$config_res = db_result("select * from mv_config where config_id='1'");
	$sql = "Insert into mv_files set fk_agent_id = '" . $agent_id . "',fk_client_id = '$client_id' ,file_type = '$file_type',file_destination = '$file_destination' ,file_added_by = '1' ,file_added_on = now() ,file_modified_on = now() ,file_current_status = '$file_current_status' ,file_received_by = '$file_received_by' ,file_primary_staff = '1' ,file_active_staff = '1' ,file_request = '$file_request' ,file_currency = '$file_currency' ,file_departure_date = '$file_departure_date' ,file_arrival_date = '$file_arrival_date' ,file_return_date = '$file_return_date' ,file_adults = '$file_adults' ,file_infants = '$file_infants' ,file_childrens = '$file_childrens' ,file_teens = '$file_teens' ,file_comments = '$file_comments',file_agent_commission='$file_agent_commission',file_booking_charge='" . $config_res['booking_fee'] . "',file_taxes='" . $config_res['service_tax'] . "',file_markup='0',file_exchange_rate='" . currency_ratio("EUR", $file_currency) . "',is_online='Yes'";
	db_query($sql);
	$file_id = mysqli_insert_id($GLOBALS['dbcon']);
	global $ARR_CURRENCY;
	foreach ($ARR_CURRENCY as $key => $val) {
		db_query("insert into mv_file_currency set fk_file_id='$file_id',target_currency='" . $file_currency . "',base_currency='" . $key . "',exchange_rate='" . currency_ratio($key, $file_currency) . "'");
	}
	###################### Country Code ##########################
	$mv_country_res = db_result("select * from mv_tags where tag_id ='" . $file_destination . "' and tag_id!=''");
	if ($mv_country_res['tag_code'] != '') {
		$mv_country_code = $mv_country_res['tag_code'];
	} elseif ($mv_country_res['tag_name'] != "") {
		$mv_country_code = strtoupper(substr($mv_country_res['tag_name'], 0, 3));
	} else {
		$mv_country_code = "TOU";
	}
	$file_code = $mv_country_code . "-" . str_pad($file_id, 5, 0, STR_PAD_LEFT);
	db_query("UPDATE mv_files SET file_code ='$file_code' WHERE file_id ='$file_id'");
	###############################  For Logging ###########################
	$log_title = "New File (" . $file_code . ") has added to file list.";
	db_query("insert into mv_daily_log set employee_id='1',log_title='" . addslashes($log_title) . "',log_action='Insert',log_added_date=now(),log_mysql_query='" . addslashes($sql) . "',log_url='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "',log_user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "',log_previous_arr='',log_updated_arr=''");
	#########################################################################
	if ($agent_id > 0) {
		$agent_name = db_scalar("select concat(agent_first_name,' ',agent_last_name) from mv_agent where agent_id='$agent_id'");
		$log_description = "Created File for Agent: " . $agent_name;
	} else {
		$client_name = db_scalar("select concat(client_first_name,' ',client_last_name) from mv_client where client_id='$client_id'");
		$log_description = "Created File for Client: " . $client_name;
	}
	db_query("insert into mv_file_activity_log set fk_file_id='$file_id',fk_agent_id='1',activity_added_on=now(),activity_description='" . addslashes($log_description) . "'");
	############################  Add Services To File #################
	if (count($_SESSION['sess_cart']) > 0) {
		foreach ($_SESSION['sess_cart'] as $cart_id) {
			$tour_res = db_result("select c.*,t.tour_name,t.tour_slug,t.fk_supplier_id,s.supplier_company_name,t.tour_price_type from tbl_tours_cart c left join mv_tours t on c.tour_id=t.tour_id left join mv_supplier s on t.fk_supplier_id=s.supplier_id where cart_id='$cart_id'");
			if ($tour_res['tour_price_type'] == "Fixed") {
				$tour_pricing_method = "P";
				$is_private_tour = "No";
			} else {
				$tour_pricing_method = "PR";
				$is_private_tour = "Yes";
			}
			$booking_with_supplier = $tour_res['fk_supplier_id'];
			$season_supplier_id = 0;
			$booking_with = $tour_res['supplier_company_name'];
			$booking_price_currency = "EUR";
			$sql = "insert into mv_file_activity set fk_file_id = '" . $file_id . "' ,fk_tour_id = '" . $tour_res['tour_id'] . "',booked_with_supplier_id = '$booking_with_supplier' ,booked_with_supplier_name = '" . addslashes($booking_with) . "' ,pax_no = '" . $tour_res['guest'] . "' ,check_in_date = '" . $tour_res['tour_date'] . "' ,booking_net_price = '" . $tour_res['tour_net_price'] . "',booking_net_price_sc='" . $tour_res['tour_net_price'] . "' ,file_activity_added_by = '1',file_activity_added_on = now(),booking_price_currency='$booking_price_currency',service_status='1',booking_original_net_price='" . $tour_res['tour_net_price'] . "',booking_gross_price='" . $tour_res['tour_gross_price'] . "',check_in_time='" . $tour_res['tour_time'] . "',tour_pricing_method='" . $tour_pricing_method . "',service_paid_by=1,is_private_tour='$is_private_tour',gross_price_edited='Yes',is_paid='Yes',voucher_sent='Yes'";
			db_query($sql);
			$file_activity_id = mysqli_insert_id();
			#############################  Activity Log ###########
			$log_title = "New Activity (" . $tour_res['tour_name'] . ") has been added in file (" . $file_code . ")";
			db_query("insert into mv_daily_log set employee_id='1',log_title='" . addslashes($log_title) . "',log_action='Insert',log_added_date=now(),log_mysql_query='" . addslashes($sql) . "',log_url='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "',log_user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "',log_previous_arr='" . addslashes($log_previous_arr) . "',log_updated_arr='" . addslashes($log_updated_arr) . "'");
			$log_description = "Added " . $tour_res['tour_name'] . " to Services";
			db_query("insert into mv_file_activity_log set fk_file_id='$file_id',fk_agent_id='1',activity_added_on=now(),activity_description='" . addslashes($log_description) . "'");
		}
	}
	if (count($_SESSION['sess_transfer_cart']) > 0) {
		foreach ($_SESSION['sess_transfer_cart'] as $cart_id) {
			$transfer_res = db_result("select * from tbl_transfer_cart where cart_id='$cart_id'");
			$booking_with_res = db_result("select supplier_company_name,supplier_currency,supplier_transfer_currency from mv_supplier  where supplier_id = '594'  and supplier_status!='Delete'");
			$sql = "insert into mv_file_transfers set fk_file_id = '$file_id' ,file_booking_type = '217' ,transfer_country = '36' ,fk_region_id = '" . $transfer_res['region_id'] . "' ,booked_with_supplier_id = '594' ,booked_with_supplier_name = '" . addslashes($booking_with_res['supplier_company_name']) . "' ,fk_pickup_id = '" . $transfer_res['pickup_id'] . "' ,fk_dropoff_id = '" . $transfer_res['dropoff_id'] . "' ,check_in_date = '" . $transfer_res['pickup_date'] . "' ,booking_net_price = '" . $transfer_res['transfer_net_price'] . "'  ,booking_net_price_sc='" . $transfer_res['transfer_net_price'] . "',file_transfer_added_by = '1',file_transfer_added_on = now(),booking_method='1',vehicle_id='" . $transfer_res['vehicle_id'] . "',no_pax='" . $transfer_res['no_pax'] . "',booking_price_currency='EUR',check_in_time='" . $transfer_res['pickup_time'] . "',booking_original_net_price='" . $transfer_res['transfer_net_price'] . "',booking_gross_price='" . $transfer_res['transfer_gross_price'] . "',disp_transfer_description_auto='Yes',pickup_depot='" . addslashes($transfer_res['pickup_location']) . "',dropoff_depot='" . addslashes($transfer_res['dropoff_location']) . "',display_all_clients='Yes',display_lead_pax='Yes',service_status='1',gross_price_edited='Yes',is_paid='Yes',voucher_sent='Yes'";
			db_query($sql);
			###############################  For Logging ################
			if (mysqli_affected_rows()) {
				$log_title = "New Transfer has been added in file (" . $file_code . ")";
				db_query("insert into mv_daily_log set employee_id='1',log_title='" . addslashes($log_title) . "',log_action='Insert',log_added_date=now(),log_mysql_query='" . addslashes($sql) . "',log_url='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "',log_user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "',log_previous_arr='" . addslashes($log_previous_arr) . "',log_updated_arr='" . addslashes($log_updated_arr) . "'");
				$log_description = "Added Transfer to Services";
				db_query("insert into mv_file_activity_log set fk_file_id='$file_id',fk_agent_id='1',activity_added_on=now(),activity_description='" . addslashes($log_description) . "'");
			}
		}
	}
	update_file_total($file_id);
	generate_auto_invoice($file_id, "Yes", "");
	sleep(2);
	generate_auto_invoice($file_id, "No", "");
	return $file_id;
}
/*
function _create_file($id,$type='',$act=''){
	$res=db_result("select b.*,t.tour_country_tag_ids,t.tour_days,t.fk_supplier_id,t.tour_name,t.activity_segment,t.tour_base_currency from mv_tour_booking b left join mv_tours t on b.fk_tour_id=t.tour_id where booking_id='$id'");
	$total_pax=$res[adult]+$res[child]+$res[infant];
if($act=="new_file"){
		###################  Add Client ######################
		$sql="Insert into mv_client SET client_salutation ='".$res[client_salutation]."' ,client_first_name ='".$res[client_name]."' ,client_last_name ='".$res[client_surname]."' ,client_email ='".$res[client_email]."',client_last_contacted =now(),client_added_date =now() ,client_added_by ='1',client_agent_id='".$res[fk_agent_id]."',client_phone='".$res[client_phone]."'";
		db_query($sql);
		$client_id=mysql_insert_id();
		$client_code="CL-".str_pad($client_id,5,0,STR_PAD_LEFT);
		db_query("UPDATE mv_client SET client_code ='$client_code' WHERE client_id ='$client_id'");
		######################################################
		$file_current_status="1";
		$file_request="1";
		$file_type="4";
		$file_received_by="2";
		$tour_location=explode(",",$res['tour_country_tag_ids']);
		$file_destination=$tour_location[0];
		$agent_id=$res['fk_agent_id'];
		$file_currency=$res['tour_base_currency'];
		$file_departure_date=$res['tour_date'];
		$file_arrival_date=$res['tour_date'];
		if($res['tour_days']>1){
			$days_to_add=$res['tour_days'];
			$file_return_date=date('Y-m-d', strtotime("+$days_to_add days",strtotime($file_arrival_date)));
		}else{
			$file_return_date=$res['tour_date'];
		}
		$file_adults=$res['adult'];
		$file_infants=$res['infant'];
		$file_childrens=$res['child'];
		$file_teens=$res['teens'];
		$file_comments=addslashes($res['detail_note']);
		if($res['discount']==""){
			$booked_price=$res['tour_price'];
			$file_agent_commission = db_scalar("select agent_commission from mv_agent where agent_id='$res[fk_agent_id]'");
		}else{
			$booked_price=$res['tour_price']-$res['discount'];
			$file_agent_commission=0;
		}
		$config_res = db_result("select * from mv_config where config_id='1'");
		$sql="Insert into mv_files set fk_agent_id = '$res[fk_agent_id]',fk_client_id = '$client_id' ,file_type = '$file_type',file_destination = '$file_destination' ,file_added_by = '1' ,file_added_on = now() ,file_modified_on = now() ,file_current_status = '$file_current_status' ,file_received_by = '$file_received_by' ,file_primary_staff = '1' ,file_active_staff = '1' ,file_request = '$file_request' ,file_currency = '$file_currency' ,file_departure_date = '$file_departure_date' ,file_arrival_date = '$file_arrival_date' ,file_return_date = '$file_return_date' ,file_adults = '$file_adults' ,file_infants = '$file_infants' ,file_childrens = '$file_childrens' ,file_teens = '$file_teens' ,file_comments = '$file_comments',file_agent_commission='$file_agent_commission',file_booking_charge='".$config_res['booking_fee']."',file_taxes='".$config_res['service_tax']."',file_markup='0',file_exchange_rate='".currency_ratio("EUR",$file_currency)."',is_online='Yes'";
		db_query($sql);
		$file_id=mysql_insert_id();
		global $ARR_CURRENCY;
		foreach($ARR_CURRENCY as $key => $val){
			db_query("insert into mv_file_currency set fk_file_id='$file_id',target_currency='".$file_currency."',base_currency='".$key."',exchange_rate='".currency_ratio($key,$file_currency)."'");
		}
		###################### Country Code ##########################
		$mv_country_res=db_result("select * from mv_tags where tag_id ='".$file_destination."' and tag_id!=''");
		if($mv_country_res['tag_code']!=''){
			$mv_country_code=$mv_country_res['tag_code'];
		}elseif($mv_country_res['tag_name']!=""){
			$mv_country_code=strtoupper(substr($mv_country_res['tag_name'],0,3));
		}else{
			$mv_country_code="TOU";
		}
		$file_code=$mv_country_code."-".str_pad($file_id,5,0,STR_PAD_LEFT);
		db_query("UPDATE mv_files SET file_code ='$file_code' WHERE file_id ='$file_id'");
		//$_SESSION['agent_msg'] = '<li>New file added successfully.</li>';
		###############################  For Logging ###########################
		$log_title="New File (".$file_code.") has added to file list.";
		db_query("insert into mv_daily_log set employee_id='1',log_title='".addslashes($log_title)."',log_action='Insert',log_added_date=now(),log_mysql_query='".addslashes($sql)."',log_url='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."',log_user_agent='".$_SERVER['HTTP_USER_AGENT']."',log_previous_arr='',log_updated_arr=''");
		#########################################################################
		if($agent_id>0){
			$agent_name=db_scalar("select concat(agent_first_name,' ',agent_last_name) from mv_agent where agent_id='$agent_id'");
			$log_description="Created File for Agent: ".$agent_name;
		}else{
			$client_name=db_scalar("select concat(client_first_name,' ',client_last_name) from mv_client where client_id='$client_id'");
			$log_description="Created File for Client: ".$client_name;
		}
		db_query("insert into mv_file_activity_log set fk_file_id='$file_id',fk_agent_id='1',activity_added_on=now(),activity_description='".addslashes($log_description)."'");
	############################  Add Services To File ##################################
	$file_result = db_result("select * from mv_files  where file_code = '".$file_code."'");
	$booking_type="Direct";
	$booking_with_supplier=$res['fk_supplier_id'];
	$season_supplier_id=0;
	$booking_with_res=db_result("select supplier_company_name,supplier_activity_currency from mv_supplier  where supplier_id = '$booking_with_supplier' and supplier_status!='Delete'");
	$booking_with=$booking_with_res['supplier_company_name'];
	$booking_price_currency=$booking_with_res['supplier_activity_currency'];
	//$booking_price=get_tour_price($res[tour_id],$booking_with_supplier,$res[tour_date],$total_pax,$res[occupancy_type],$is_private_tour,$res[package_id],$res[tour_pricing_method]);
	$booking_price=$booked_price;
	####################  Calculate Gross Price #####################################									//$net_sc=$booking_price*file_exchnage_rates($file_id,$booking_price_currency,$file_result['file_currency']);
	//$gross_sc=$net_sc+($net_sc*$file_result[file_markup])/100;
	$net_sc=$booking_price;
	$gross_sc=$booking_price;
	$booking_gross_price=ceil($gross_sc);
	###############################################################################
	$sql="insert into mv_file_activity set fk_file_id = '".$file_result[file_id]."' ,fk_tour_id = '".$res[fk_tour_id]."',booked_with_supplier_id = '$booking_with_supplier' ,booked_with_supplier_name = '".addslashes($booking_with)."' ,pax_no = '$total_pax' ,check_in_date = '".$res['tour_date']."' ,booking_net_price = '$booking_price' ,file_activity_added_by = '1',file_activity_added_on = now(),occupancy_type='".$res[occupancy_type]."',booking_price_currency='$booking_price_currency',package_id='".$res[package_id]."',service_status='1',booking_original_net_price='$booking_price',booking_gross_price='$booking_gross_price',check_in_time='".$res[tour_time]."',tour_pricing_method='".$res[tour_pricing_method]."',service_paid_by=1";
	//mysql_query($sql) or die(mysql_error());
	db_query($sql);
	$file_activity_id=mysql_insert_id();
	#############################  Activity Log #################################################
	$log_title="New Activity (".$res['tour_name'].") has been added in file (".$file_result[file_code].")";
	db_query("insert into mv_daily_log set employee_id='1',log_title='".addslashes($log_title)."',log_action='Insert',log_added_date=now(),log_mysql_query='".addslashes($sql)."',log_url='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."',log_user_agent='".$_SERVER['HTTP_USER_AGENT']."',log_previous_arr='".addslashes($log_previous_arr)."',log_updated_arr='".addslashes($log_updated_arr)."'");
	$log_description="Added ".$res['tour_name']." to Services";
	db_query("insert into mv_file_activity_log set fk_file_id='$file_id',fk_agent_id='1',activity_added_on=now(),activity_description='".addslashes($log_description)."'");
	$hotel_array=explode(",",$res['hotel_list']);
	for($m=0;$m<count($hotel_array);$m++){
		if($hotel_array[$m]>0){
			$segment_id=$m+1;
			db_scalar("insert into  mv_file_segment_hotel set fk_segment_id='$segment_id' , fk_file_activity_id='$file_activity_id' , fk_hotel_id='".$hotel_array[$m]."'");
		}
	}
	update_file_total($file_result['file_id']);
	db_query("update mv_tour_booking set file_created='Yes',fk_file_id='$file_id' where booking_id='".$id."'");
	generate_auto_invoice($file_result['file_id'],"Yes","");
	sleep(2);
	generate_auto_invoice($file_result['file_id'],"No","");
	return $file_id.'~'.$file_activity_id;
	}else{
		return 0;
	}
}
*/
function get_thumb_image($pid, $type = '', $width = '', $hight = '')
{
	if ($type == 'tour') {
		$photo_sql = db_query("select * from mv_tour_images where fk_tour_id='$pid' and image_type='D' and image_status='Active' and show_in_thumb='Yes' order by image_order");
		if (mysqli_num_rows($photo_sql) == 0) {
			$photo_sql = db_query("select * from mv_tour_images where fk_tour_id='$pid' and image_type='D' and image_status='Active' order by image_order");
		}
		if (mysqli_num_rows($photo_sql) == 0) {
			$photo_sql = db_query("select * from mv_tour_images where fk_tour_id='$pid' and image_type='B' and image_status='Active' order by image_order");
		}
		if (mysqli_num_rows($photo_sql) > 0) {
			$row = mysqli_fetch_array($photo_sql);
			if (file_exists(UP_FILES_FS_PATH . '/tour/' . $row['image_name'])) {
				$file = show_thumb(UP_FILES_FS_PATH . '/tour/' . $row['image_name'], $width, $hight, 'distort');
				$image = $file;
			} else {
				$image = "images/no_image.gif";
			}
			$image = "images/no_image.gif";
		}
	} elseif ($type == 'hotel') {
		$photo_sql = db_query("select * from mv_hotel_images where fk_supplier_id='$pid' and image_type='D' and image_status='Active' order by image_order");
		if (mysqli_num_rows($photo_sql) > 0) {
			$row = mysqli_fetch_array($photo_sql);
			if (file_exists(UP_FILES_FS_PATH . '/supplier/' . $row['image_name'])) {
				$file = show_thumb(UP_FILES_FS_PATH . '/supplier/' . $row['image_name'], $width, $hight, 'distort');
				$image = $file;
			} else {
				$image = "images/no_image.gif";
			}
		} else {
			$image = "images/no_image.gif";
		}
	} elseif ($type == 'unit_rental') {
		$photo_sql = db_query("select * from mv_room_images where fk_room_id='$pid' and image_type='D' and image_status='Active' order by image_order");
		if (mysqli_num_rows($photo_sql) > 0) {
			$row = mysqli_fetch_array($photo_sql);
			if (file_exists(UP_FILES_FS_PATH . '/supplier/room/' . $row['image_name'])) {
				$file = show_thumb(UP_FILES_FS_PATH . '/supplier/room/' . $row['image_name'], $width, $hight, 'distort');
				$image = $file;
			} else {
				$image = "images/no_image.gif";
			}
		} else {
			$image = "images/no_image.gif";
		}
	} elseif ($type == 'location') {
		$photo_sql = db_query("select * from mv_location_images where fk_location_id='$pid' and image_status='Active' and image_type='D' order by image_order");
		if (mysqli_num_rows($photo_sql) > 0) {
			$row = mysqli_fetch_array($photo_sql);
			if (file_exists(UP_FILES_FS_PATH . '/location/' . $row['image_name'])) {
				$file = show_thumb(UP_FILES_FS_PATH . '/location/' . $row['image_name'], $width, $hight, 'distort');
				$image = $file;
			} else {
				$image = "images/no_image.gif";
			}
		} else {
			$image = "images/no_image.gif";
		}
	}
	return $image;
}
function get_cat_tour($lid, $cat, $count)
{
	if ($lid != "") {
		$sub_sql = " and (find_in_set('$lid',tour_city_tag_ids) or FIND_IN_SET('$lid',ts.locations_ids) or FIND_IN_SET('$lid',td.departure_city_tag))";
	}
	$cat_sql = " and find_in_set('$cat',tour_category)";
	$sql = "";
	$sql .= "select t.* from mv_tours t INNER JOIN mv_tour_sights ts ON ts.fk_tour_id=t.tour_id left join mv_tour_departure td on td.tour_id=t.tour_id where tour_status='Active' $sub_sql $cat_sql and t.tour_published='Yes' and t.tour_data_completed='Yes'";
	$final_sql = $sql . " group by tour_name order by tour_name";
	$trans_sql = db_query($final_sql);
	if ($count == 'No') {
		return $trans_sql;
	} else {
		return mysqli_num_rows($trans_sql);
	}
}
function pdf_invoice_date($date)
{
	if (strlen($date) >= 10) {
		if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
			return '';
		}
		$mktime = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
		return date("M. j, Y", $mktime);
		//return date("d-M-y", $mktime);
	} else {
		return $s;
	}
}
function get_private_tour_price($tour_id, $pax = 0, $defualt = FALSE)
{
	$tour_price = db_scalar("select tour_gross_price from igs_private_tour_price where fk_tour_id='$tour_id' and tour_pax='$pax' order by tour_pax");
	if ($defualt)
		$tour_price;
	else
		$tour_price = $tour_price * $pax;
	return display_price($tour_price);
}
function get_variable_tour_net_price($tour_id, $pax = 0)
{
	$tour_price = db_scalar("select tour_price from igs_private_tour_price where fk_tour_id='$tour_id' and tour_pax='$pax' order by tour_pax");
	$tour_price = $tour_price * $pax;
	return display_price($tour_price);
}
function get_locations_image($loc_id, $width = "400", $height = "300")
{
	if (!empty($loc_id)) {
		foreach ($loc_id as $val) {
			$locimg_sql = db_query("select * from mv_location_images where fk_location_id='$val' and image_status='Active' and image_type!='B' order by image_order limit 1");
			if (mysqli_num_rows($locimg_sql) > 0) {
				$photo_res = mysqli_fetch_array($locimg_sql);
				$file_name = $photo_res['image_name'];
				if (file_exists(UP_FILES_FS_PATH . '/location/' . $file_name)) {
					$file = show_thumb(UP_FILES_FS_PATH . '/location/' . $file_name, $width, $height, 'distort');
					$img_title = $photo_res['image_title'];
					echo '<img src="' . $file . '" title="' . $img_title . '" class="img-responsive"/>&nbsp;&nbsp;';
				}
			}
		}
	}
}
function get_locations_image2($loc_id, $width = "400", $height = "300")
{
	if (!empty($loc_id)) {
		echo '<table cellpadding="2"><tr>';
		foreach ($loc_id as $val) {
			$locimg_sql = db_query("select * from mv_location_images where fk_location_id='$val' and image_status='Active' and image_type!='B' order by image_order limit 1");
			if (mysqli_num_rows($locimg_sql) > 0) {
				$photo_res = mysqli_fetch_array($locimg_sql);
				$file_name = $photo_res['image_name'];
				if (file_exists(UP_FILES_FS_PATH . '/location/' . $file_name)) {
					$file = show_thumb(UP_FILES_FS_PATH . '/location/' . $file_name, $width, $height, 'distort');
					$img_title = $photo_res['image_title'];
					echo '<td><img src="' . $file . '" title="' . $img_title . '" class="img-responsive"/></td>';
				}
			}
		}
		echo '</tr></table>';
	}
}
function process_payment_by_pp($invnum, $chargetotal, $currencycode, $cardno, $cardtype, $cardfirstname, $exp_month, $cardexpirationyear, $card_cvv, $cardlastname = '', $street_address1 = '', $street_address2 = '', $city = '', $state = '', $zipcode = '', $country = '')
{
	$paymentType = urlencode('Sale');
	$ipaddress = urlencode($_SERVER['REMOTE_ADDR']);
	$cinvnum = urlencode($invnum);
	$firstName = urlencode($cardfirstname);
	$lastName = urlencode($cardlastname);
	$address1 = urlencode($street_address1);
	$address2 = urlencode($street_address2);
	$city = urlencode($city);
	$state = urlencode($state);
	$zip = urlencode($zipcode);
	$country = urlencode($country);
	$creditCardType = urlencode($cardtype);
	$creditCardNumber = urlencode($cardno);
	$expDateMonth = urlencode($exp_month);
	$expDateYear = urlencode($cardexpirationyear);
	$cvv2Number = urlencode($card_cvv);
	$amount = urlencode($chargetotal);
	$currencyID = urlencode($currencycode);
	$nvpStr = "&PAYMENTACTION=$paymentType&AMT=$amount&CREDITCARDTYPE=$creditCardType&ACCT=$creditCardNumber&EXPDATE=$expDateMonth$expDateYear&CVV2=$cvv2Number&FIRSTNAME=$firstName&LASTNAME=$lastName&STREET=$street_address1&STREET2=$street_address2&CITY=$city&STATE=$state&ZIP=$zip&COUNTRYCODE=$country&CURRENCYCODE=$currencyID&IPADDRESS=$ipaddress&INVNUM=$cinvnum";
	$httpParsedResponseAr = array();
	$httpParsedResponseAr = Paypal_PPHttpPost('DoDirectPayment', $nvpStr);
	$message = "";
	if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
		$message = "00";
		$_SESSION['paypal_response'] = serialize($httpParsedResponseAr);
	} else {
		$message = urldecode($httpParsedResponseAr['L_LONGMESSAGE0']);
		$_SESSION['paypal_response'] = "";
	}
	return $message;
}
function Paypal_PPHttpPost($methodName_, $nvpStr_)
{
	/*
	$environment="sandbox";
	$API_UserName = urlencode('rajume_1296101374_biz_api1.gmail.com');
    $API_Password = urlencode('1296101387');
    $API_Signature = urlencode('AC2KFOa7K8Zdjp-F5sgj1nE6EoQqAo0JeyrfgbyRqjcNdX7SG0wn10cs');
	*/
	$API_UserName = urlencode('nadia_api1.italygroupspecialists.com');
	$API_Password = urlencode('XA645N54XR2SK5Q6');
	$API_Signature = urlencode('Akc-lAA1-O9PdXCuWSu2mZ8cjuvvA7iAaqw5rUBA-OOkAOmFALoSGHfl');
	$API_Endpoint = "https://api-3t.paypal.com/nvp";
	if ("sandbox" === $environment || "beta-sandbox" === $environment) {
		$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
	}
	$version = urlencode('51.0');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
	$httpResponse = curl_exec($ch);
	$rarray = array();
	if (!$httpResponse) {
		print "no http response";
		exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
	}
	$httpResponseAr = explode("&", $httpResponse);
	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if (sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}
	if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}
	return $httpParsedResponseAr;
}
function get_loc_accom($lid, $type, $count)
{
	if ($type == 'Hotel') {
		if ($lid != "") {
			$sub_sql = " and (find_in_set('$lid', location_tag_ids)) ";
		}
		$sql = "";
		$sql .= "select t.*,t2.tag_name as type from mv_supplier t left join mv_tags t2 on t2.tag_id=property_type where supplier_status!='Delete' and t.hotel_published='Yes' and FIND_IN_SET('1',supplier_business_type) and t.supplier_short_description!='' and property_type=1916 and supplier_data_completed='Yes' $sub_sql";
		$final_sql = $sql . " order by supplier_company_name";
	} elseif ($type == 'Villa') {
		if ($lid != "") {
			$sub_sql = " and (find_in_set('$lid', t.location_tag_ids)) ";
		}
		$type_qry = " and t.property_type='167'";
		$sql = "";
		$sql .= "select room_id as sup_id, room_name as supplier_name, room_short_description as description,room_occupancy_type as occupancy,room_slug as accom_slug,t3.tag_name as type,t.location_tags_name as location from  mv_hotel_rooms t left join mv_supplier t2 on t.fk_supplier_id=t2.supplier_id left join mv_tags t3 on t3.tag_id=t.property_type where t2.supplier_status!='Delete' and t.room_status!='Delete' and t.property_published='Yes' and FIND_IN_SET('5',t2.supplier_business_type) and t.room_short_description!='' and property_data_completed='Yes' $type_qry $sub_sql";
		$final_sql = $sql . " order by supplier_name";
	} elseif ($type == 'Apartment') {
		if ($lid != "") {
			$sub_sql = " and (find_in_set('$lid', t.location_tag_ids)) ";
		}
		$type_qry = " and (t.property_type='1274' or t.property_type='168')";
		$sql = "";
		$sql .= "select room_id as sup_id, room_name as supplier_name, room_short_description as description,room_occupancy_type as occupancy,room_slug as accom_slug,t3.tag_name as type,t.location_tags_name as location from  mv_hotel_rooms t left join mv_supplier t2 on t.fk_supplier_id=t2.supplier_id left join mv_tags t3 on t3.tag_id=t.property_type where t2.supplier_status!='Delete' and t.room_status!='Delete' and t.property_published='Yes' and FIND_IN_SET('5',t2.supplier_business_type) and t.room_short_description!='' and property_data_completed='Yes' $type_qry $sub_sql";
		$final_sql = $sql . " order by supplier_name";
	} elseif ($type == 'Winery') {
		if ($lid != "") {
			$sub_sql = " and (find_in_set('$lid', location_tag_ids)) ";
		}
		$sql = "";
		$sql .= "select t.*,t2.tag_name as type from mv_supplier t left join mv_tags t2 on t2.tag_id=property_type where supplier_status!='Delete' and t.hotel_published='Yes' and t.slug!='' and show_in_frontend='Yes' and FIND_IN_SET('8',supplier_business_type) and t.supplier_short_description!='' $sub_sql";
		$final_sql = $sql . " order by supplier_company_name";
	} elseif ($type == 'Restaurant') {
		if ($lid != "") {
			$sub_sql = " and (find_in_set('$lid', location_tag_ids)) ";
		}
		$sql = "";
		$sql .= "select t.*,t2.tag_name as type from mv_supplier t left join mv_tags t2 on t2.tag_id=property_type where supplier_status!='Delete' and t.hotel_published='Yes' and t.slug!='' and show_in_frontend='Yes' and FIND_IN_SET('10',supplier_business_type) and t.supplier_short_description!='' $sub_sql";
		$final_sql = $sql . " order by supplier_company_name";
	}
	$trans_sql = db_query($final_sql);
	if ($count == 'No') {
		return $trans_sql;
	} else {
		return mysqli_num_rows($trans_sql);
	}
}
function show_online_transfer_price($fk_pickup_id, $fk_dropoff_id)
{
	$var = '';
	if ($fk_pickup_id > 0 && $fk_dropoff_id > 0) {
		$description_id = db_scalar("select description_id from mv_transfers_description_list where fk_pickup_id='" . $fk_pickup_id . "' and fk_dropoff_id='" . $fk_dropoff_id . "' and description_status='Active'");
		$veh_sql = db_query("select p.*,vehicle_name,max_passengers,vehicle_id from ivs_transfer_price p left join tbl_ivs_vehicles v on p.fk_vehicle_id=v.vehicle_id where fk_transfer_id='$description_id' and price_status='Active' and gross_price>0");
		if (mysqli_num_rows($veh_sql) > 0) {
			$var .= '<div class="row"><div class="col-sm-12" style=""><div class="form-group"><label for="cc_holder">Vehicles Available</label>';
			$c = 0;
			while ($veh_res = mysqli_fetch_array($veh_sql)) {
				$chk = "";
				if ($veh_res['vehicle_id'] == $_SESSION['sess_vehicle_id']) {
					$chk = "checked";
				} elseif ($_SESSION['sess_vehicle_id'] <= 0 && $c == 0) {
					$chk = "checked";
				}
				$c++;
				$var .= '<p><input type="radio" name="vehicle_id" value="' . $veh_res['vehicle_id'] . '" ' . $chk . '>  ' . $veh_res['vehicle_name'] . ' (<i class="fa fa-user"></i> Max. ' . $veh_res['max_passengers'] . ') - <font color="red">&euro;' . $veh_res['gross_price'] . '</font></p>';
			}
			$var .= '</div></div></div>';
		} else {
			$var = 'No';
		}
	}
	return trim($var);
}
function get_hotel_tour($lid, $cat, $count)
{
	if ($lid != "") {
		$sub_sql = " and (find_in_set('$lid',tour_city) or FIND_IN_SET('$lid',ts.locations_name) or FIND_IN_SET('$lid',td.departure_city))";
	}
	$cat_sql = " and find_in_set('$cat',tour_category)";
	$sql = "";
	$sql .= "select t.* from mv_tours t INNER JOIN mv_tour_sights ts ON ts.fk_tour_id=t.tour_id left join mv_tour_departure td on td.tour_id=t.tour_id where tour_status='Active' $sub_sql $cat_sql and t.tour_published='Yes' and t.tour_data_completed='Yes'";
	$final_sql = $sql . " group by tour_name order by tour_name";
	$trans_sql = db_query($final_sql);
	if ($count == 'No') {
		return $trans_sql;
	} else {
		return mysqli_num_rows($trans_sql);
	}
}
function copy_supplier($id, $supplier_company_name, $suplier_slug, $supplier_business_type, $supplier_code, $hotel_published)
{
	//echo $supplier_business_type.'==='.$supplier_code;
	db_query("INSERT INTO mv_supplier (supplier_hotel_type, supplier_accommodation_type, property_type, supplier_code, supplier_username, supplier_password, supplier_company_name, slug, supplier_rating, supplier_address, supplier_city, supplier_state, supplier_district, supplier_zipcode, supplier_country, supplier_phone_country_code, supplier_phone_area_code, supplier_phone, supplier_fax_country_code, supplier_fax_area_code, supplier_fax, supplier_contact_name, supplier_contact_number_country_code, supplier_contact_number_area_code, supplier_contact_number, supplier_contact_email, supplier_email, supplier_secondary_email, supplier_reservation_contact, supplier_reservation_email, supplier_emergency_number_secondary_area_code, supplier_emergency_number_secondary_country_code, supplier_emergency_number_secondary, supplier_emergency_number_country_code, supplier_emergency_number_area_code, supplier_emergency_number, supplier_website, supplier_mv_url, supplier_booking_engine, supplier_map_link, supplier_bank_information, supplier_comments, supplier_short_description, supplier_description, supplier_directions, supplier_min_no_nights, supplier_currency, supplier_credit_cards, supplier_closed_from, supplier_closed_to, supplier_contract_file, supplier_attachment_file, supplier_images, supplier_business_type, location_tag_ids, location_tags_name, check_in_time, check_out_time, supplier_status, supplier_added_date, supplier_added_by, service_currency_same_for_all, supplier_accommodation_currency, supplier_transfer_currency, supplier_ferry_currency, supplier_train_currency, supplier_flight_currency, supplier_activity_currency, supplier_misc_currency, map_latitude, map_longitude, map_view, hotel_amenities, hotel_published, supplier_map_image, map_type, supplier_data_completed, show_in_frontend, supplier_meta_title, supplier_meta_description, supplier_meta_keywords) (select supplier_hotel_type, supplier_accommodation_type, property_type, supplier_code, supplier_username, supplier_password, '$supplier_company_name', '$suplier_slug', supplier_rating, supplier_address, supplier_city, supplier_state, supplier_district, supplier_zipcode, supplier_country, supplier_phone_country_code, supplier_phone_area_code, supplier_phone, supplier_fax_country_code, supplier_fax_area_code, supplier_fax, supplier_contact_name, supplier_contact_number_country_code, supplier_contact_number_area_code, supplier_contact_number, supplier_contact_email, supplier_email, supplier_secondary_email, supplier_reservation_contact, supplier_reservation_email, supplier_emergency_number_secondary_area_code, supplier_emergency_number_secondary_country_code, supplier_emergency_number_secondary, supplier_emergency_number_country_code, supplier_emergency_number_area_code, supplier_emergency_number, supplier_website, supplier_mv_url, supplier_booking_engine, supplier_map_link, supplier_bank_information, supplier_comments, supplier_short_description, supplier_description, supplier_directions, supplier_min_no_nights, supplier_currency, supplier_credit_cards, supplier_closed_from, supplier_closed_to, supplier_contract_file, supplier_attachment_file, supplier_images, supplier_business_type, location_tag_ids, location_tags_name, check_in_time, check_out_time, supplier_status, now(), '" . $_SESSION['sess_agent_id'] . "', service_currency_same_for_all, supplier_accommodation_currency, supplier_transfer_currency, supplier_ferry_currency, supplier_train_currency, supplier_flight_currency, supplier_activity_currency, supplier_misc_currency, map_latitude, map_longitude, map_view, hotel_amenities, '" . $hotel_published . "', supplier_map_image, map_type, supplier_data_completed, show_in_frontend, supplier_meta_title, supplier_meta_description, supplier_meta_keywords from mv_supplier where supplier_id='$id')");
	$supplier_id = mysqli_insert_id();
	if (isbtype($supplier_business_type, 1) && $supplier_id > 0) {
		$bk_sql = "insert into mv_hotel_engines set engine_name='594',fk_supplier_id='$supplier_id',
					engine_added_by='" . $_SESSION['sess_agent_id'] . "',engine_added_on=now()";
		db_query($bk_sql);
		db_query("insert into mv_room_detail set room_detail_hotel_id='$supplier_id',
					room_detail_name = 'Classic', room_detail_meal_plan = '160'
					 ,room_detail_description = '',room_pricing_from=''");
		$room_detail_id = mysqli_insert_id();
		$sql = "insert into mv_hotel_rooms SET fk_supplier_id = '$supplier_id',room_name='Classic',room_occupancy_type = 'Single',room_added_by = '" . $_SESSION['sess_agent_id'] . "' ,room_added_on = now(),fk_room_detail_id='$room_detail_id'";  //,available_online='$available_online'
		db_query($sql);
		$sql = "insert into mv_hotel_rooms SET fk_supplier_id = '$supplier_id',room_name='Classic',room_occupancy_type = 'Double',room_added_by = '" . $_SESSION['sess_agent_id'] . "' ,room_added_on = now(),fk_room_detail_id='$room_detail_id'";  //,available_online='$available_online'
		db_query($sql);
		$sql = "insert into mv_hotel_rooms SET fk_supplier_id = '$supplier_id',room_name='Classic',room_occupancy_type = 'Triple',room_added_by = '" . $_SESSION['sess_agent_id'] . "' ,room_added_on = now(),fk_room_detail_id='$room_detail_id'";  //,available_online='$available_online'
		db_query($sql);
		$sql = "insert into mv_hotel_rooms SET fk_supplier_id = '$supplier_id',room_name='Classic',room_occupancy_type = 'Quad',room_added_by = '" . $_SESSION['sess_agent_id'] . "' ,room_added_on = now(),fk_room_detail_id='$room_detail_id'";  //,available_online='$available_online'
		db_query($sql);
	}
	//$mv_country_code=db_scalar("select mv_country_code from tbl_countries where countries_iso_code_2 ='$supplier_country'");
	$scodeArr = explode('-', $supplier_code);
	$supplier_code = $scodeArr[0] . "-" . $scodeArr[1] . "-" . str_pad($supplier_id, 5, 0, STR_PAD_LEFT);
	db_query("UPDATE mv_supplier SET supplier_code ='$supplier_code' WHERE supplier_id ='$supplier_id'");
	db_query("insert into mv_hotel_images (fk_supplier_id,image_title,image_description,image_name,image_type,image_status,image_added_by,image_added_on,image_order) (select '$supplier_id',image_title,image_description,image_name,image_type,image_status,'" . $_SESSION['sess_agent_id'] . "',now(),image_order from mv_hotel_images where fk_supplier_id='$id')");
	db_query("insert into mv_supplier_services (service_type_id, fk_supplier_id) (select service_type_id, fk_supplier_id from mv_supplier_services where fk_supplier_id='$id')");
	return $supplier_id;
}
function getSupplierType($business_type)
{
	global $arr_business_type;
	$btArr = explode(',', $business_type);
	$btArr = array_filter($btArr);
	$btnameArr = array();
	if (!empty($btArr)) {
		foreach ($btArr as $val) {
			$btnameArr[] = $arr_business_type[$val];
		}
		return implode(', ', $btnameArr);
		//print_r($btnameArr);
	}
}
function isbtype($string, $needle)
{
	if ($string === null) {
		return false;
	}
	if (in_array($needle, explode(',', $string))) {
		return true;
	}
	return false;
}
if (!function_exists("stritr")) {
	function stritr($string, $one = NULL, $two = NULL)
	{
		/*
stritr - case insensitive version of strtr
Author: Alexander Peev
Posted in PHP.NET
*/
		if (is_string($one)) {
			$two = strval($two);
			$one = substr($one, 0, min(strlen($one), strlen($two)));
			$two = substr($two, 0, min(strlen($one), strlen($two)));
			$product = strtr($string, (strtoupper($one) . strtolower($one)), ($two . $two));
			return $product;
		} else if (is_array($one)) {
			$pos1 = 0;
			$product = $string;
			while (count($one) > 0) {
				$positions = array();
				foreach ($one as $from => $to) {
					if (($pos2 = stripos($product, $from, $pos1)) === FALSE) {
						unset($one[$from]);
					} else {
						$prev_position = $pos2 - 1;
						$prev_string = substr($product, $prev_position, 1);
						if ($prev_string != "/" && $prev_string != "_" && $prev_string != "-") {
							$positions[$from] = $pos2;
						}
					}
				}
				if (count($one) <= 0) break;
				$winner = min($positions);
				$key = array_search($winner, $positions);
				$product = (substr($product, 0, $winner) . $one[$key] . substr($product, ($winner + strlen($key))));
				$pos1 = ($winner + strlen($one[$key]));
			}
			return $product;
		} else {
			return $string;
		}
	}/* endfunction stritr */
}/* endfunction exists stritr */
function genShortUrl($longUrl)
{
	//This is the URL you want to shorten
	//$longUrl = $_REQUEST['url'];
	$apiKey  = 'AIzaSyArU7PMUoVaczVSr1cQsCXWsw0HPH95XEw';
	//Get API key from : http://code.google.com/apis/console/
	$postData = array('longUrl' => $longUrl, 'key' => $apiKey);
	$jsonData = json_encode($postData);
	$curlObj = curl_init();
	curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url?key=' . $apiKey);
	curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curlObj, CURLOPT_HEADER, 0);
	curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
	curl_setopt($curlObj, CURLOPT_POST, 1);
	curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);
	$response = curl_exec($curlObj);
	//convert reponse to a json object
	$json = json_decode($response);
	curl_close($curlObj);
	//echo result
	if (!$json) {
		return "Error: Could not decode response";
	} else if (isset($json->error)) {
		return $json->error->message;
	} else if (isset($json->id)) {
		return $json->id;
	} else {
		return "Error: URL shortening failed";
	}
}
function create_file_by_qc($qcId)
{
	$result = db_result("select * from ivs_quick_contact where id='$qcId'");
	@extract($result);
	$dates_for_travel = date('Y-m-d', strtotime($dates_for_travel));
	if ($name != "") {
		$nameArr = explode(' ', $name);
		$first_name = $nameArr[0];
		if (isset($nameArr[1])) {
			$last_name = $nameArr[1];
		} else if (isset($nameArr[1]) && isset($nameArr[2])) {
			$last_name = $nameArr[1] . ' ' . $nameArr[2];
		}
	}
	db_query("insert into mv_client SET client_first_name ='" . ms_addslashes($first_name) . "' ,client_last_name ='" . ms_addslashes($last_name) . "' ,client_email ='" . $email . "',client_last_contacted=now(),client_added_date=now() ,client_added_by='1',client_phone='" . $phone . "'");
	$client_id = mysqli_insert_id($GLOBALS['dbcon']);
	$client_code = "CL-" . str_pad($client_id, 5, 0, STR_PAD_LEFT);
	db_query("UPDATE mv_client SET client_code ='$client_code' WHERE client_id ='$client_id'");
	######################################################
	$agent_id = 0;
	$file_current_status = "11";
	$file_request = "1";
	$file_type = "4";
	$file_received_by = "2";
	if ($dates_for_travel == '1969-12-31') {
		$file_start_date = "";
	} else {
		$file_start_date = $dates_for_travel;
	}
	$file_return_date = '0000-00-00';
	$file_destination_list = "";
	$total_cost = 0;
	$total_price = 0;
	$file_adults = $adults;
	$file_childrens = $children;
	$file_teens = 0;
	$file_infants = 0;
	//$location=explode(",",$file_destination_list);
	$file_destination = ''; //=$location[0];
	$file_departure_date = $file_arrival_date = $file_start_date;
	$file_currency = "EUR";
	$file_comments = addslashes($message);
	$file_agent_commission = 0;
	$config_res = db_result("select * from mv_config where config_id='1'");
	$sql = "Insert into mv_files set fk_agent_id = '" . $agent_id . "',fk_client_id = '$client_id' ,file_type = '$file_type',file_destination = '$file_destination' ,file_added_by = '1' ,file_added_on = now() ,file_modified_on = now() ,file_current_status = '$file_current_status' ,file_received_by = '$file_received_by' , file_request = '$file_request' ,file_currency = '$file_currency' ,file_departure_date = '$file_departure_date' ,file_arrival_date = '$file_arrival_date' ,file_return_date = '$file_return_date' ,file_adults = '$file_adults' ,file_infants = '$file_infants' ,file_childrens = '$file_childrens' ,file_teens = '$file_teens' ,file_comments = '$file_comments',file_agent_commission='$file_agent_commission',file_booking_charge='" . $config_res['booking_fee'] . "',file_taxes='" . $config_res['service_tax'] . "',file_markup='0',file_exchange_rate='" . currency_ratio("EUR", $file_currency) . "',is_online='Yes'";
	//file_primary_staff = '1' ,file_active_staff = '1' ,
	db_query($sql);
	$file_id = mysqli_insert_id($GLOBALS['dbcon']);
	global $ARR_CURRENCY;
	foreach ($ARR_CURRENCY as $key => $val) {
		db_query("insert into mv_file_currency set fk_file_id='$file_id',target_currency='" . $file_currency . "',base_currency='" . $key . "',exchange_rate='" . currency_ratio($key, $file_currency) . "'");
	}
	###################### Country Code ##########################
	//		$mv_country_res=db_result("select * from mv_tags where tag_id ='".$file_destination."' and tag_id!=''");
	//		if($mv_country_res['tag_code']!=''){
	//			$mv_country_code=$mv_country_res['tag_code'];
	//		}elseif($mv_country_res['tag_name']!=""){
	//			$mv_country_code=strtoupper(substr($mv_country_res['tag_name'],0,3));
	//		}else{
	//			$mv_country_code="QCF";
	//		}
	$mv_country_code = "QCF";
	$file_code = $mv_country_code . "-" . str_pad($file_id, 5, 0, STR_PAD_LEFT);
	db_query("UPDATE mv_files SET file_code ='$file_code' WHERE file_id ='$file_id'");
	db_query("UPDATE ivs_quick_contact SET fk_file_id ='$file_id' WHERE id ='$qcId'");
	###############################  For Logging ###########################
	$log_title = "New File (" . $file_code . ") has added to file list.";
	db_query("insert into mv_daily_log set employee_id='1',log_title='" . addslashes($log_title) . "',log_action='Insert',log_added_date=now(),log_mysql_query='" . addslashes($sql) . "',log_url='http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "',log_user_agent='" . $_SERVER['HTTP_USER_AGENT'] . "',log_previous_arr='',log_updated_arr=''");
	#########################################################################
	if ($agent_id > 0) {
		$agent_name = db_scalar("select concat(agent_first_name,' ',agent_last_name) from mv_agent where agent_id='$agent_id'");
		$log_description = "Created File for Agent: " . $agent_name;
	} else {
		$client_name = db_scalar("select concat(client_first_name,' ',client_last_name) from mv_client where client_id='$client_id'");
		$log_description = "Created File for Client: " . $client_name;
	}
	db_query("insert into mv_file_activity_log set fk_file_id='$file_id',fk_agent_id='1',activity_added_on=now(),activity_description='" . addslashes($log_description) . "'");
	############################  Add Services To File #################
	//	update_file_total($file_id);
	//	generate_auto_invoice($file_id,"Yes","");
	//	sleep(2);
	//	generate_auto_invoice($file_id,"No","");
	return $file_id;
}
function locNametoLink($string, $locArr)
{
	if (!empty($locArr)) {
		//return strtr($string, $locArr);
		foreach ($locArr as $key => $val) {
			if (is_string($string) && preg_match("~\b$key\b~", $string)) {
				if (strpos($string, 'Amalfi Coast') !== false) {
					if ($key == 'Amalfi') {
						$val = $locArr['Amalfi Coast'];
						$key = 'Amalfi Coast';
					}
				}
				$string = str_replace($key, $val, $string);
			}
		}
		return $string;
	} else {
		return $string;
	}
}
function updateServiceDateYear($id)
{
	$sql = db_query("SELECT a.*,f.file_arrival_date  FROM `mv_file_misc_service` a left join mv_files f on a.fk_file_id=f.file_id WHERE `check_in_date` LIKE '%0000-%' and  `check_in_date` NOT LIKE '0000-00-00%' and file_arrival_date NOT LIKE '%0000%' and fk_file_id='$id'");
	while ($res = mysqli_fetch_array($sql)) {
		$arrival_date_year = date("Y", strtotime($res['file_arrival_date']));
		db_query("update `mv_file_misc_service` set check_in_date=replace(check_in_date,'0000-','" . $arrival_date_year . "-') where file_misc_id='" . $res['file_misc_id'] . "'");
	}
	$sql = db_query("SELECT a.*,f.file_arrival_date  FROM `mv_file_misc_service` a left join mv_files f on a.fk_file_id=f.file_id WHERE `check_out_date` LIKE '%0000-%' and  `check_out_date` NOT LIKE '0000-00-00%' and file_arrival_date NOT LIKE '%0000%' and fk_file_id='$id'");
	while ($res = mysqli_fetch_array($sql)) {
		$arrival_date_year = date("Y", strtotime($res['file_arrival_date']));
		db_query("update `mv_file_misc_service` set check_out_date=replace(check_out_date,'0000-','" . $arrival_date_year . "-') where file_misc_id='" . $res['file_misc_id'] . "'");
	}
	$sql = db_query("SELECT a.*,f.file_arrival_date  FROM `mv_file_transfers` a left join mv_files f on a.fk_file_id=f.file_id WHERE `check_in_date` LIKE '%0000-%' and  `check_in_date` NOT LIKE '0000-00-00%' and file_arrival_date NOT LIKE '%0000%'  and fk_file_id='$id'");
	while ($res = mysqli_fetch_array($sql)) {
		$arrival_date_year = date("Y", strtotime($res['file_arrival_date']));
		db_query("update `mv_file_transfers` set check_in_date=replace(check_in_date,'0000-','" . $arrival_date_year . "-') where file_transfer_id='" . $res['file_transfer_id'] . "'");
	}
	$sql = db_query("SELECT a.*,f.file_arrival_date  FROM `mv_file_transfers` a left join mv_files f on a.fk_file_id=f.file_id WHERE `check_out_date` LIKE '%0000-%' and  `check_out_date` NOT LIKE '0000-00-00%' and file_arrival_date NOT LIKE '%0000%'  and fk_file_id='$id'");
	while ($res = mysqli_fetch_array($sql)) {
		$arrival_date_year = date("Y", strtotime($res['file_arrival_date']));
		db_query("update `mv_file_transfers` set check_out_date=replace(`check_out_date`,'0000-','" . $arrival_date_year . "-') where file_transfer_id='" . $res['file_transfer_id'] . "'");
	}
	$sql = db_query("SELECT a.*,f.file_arrival_date  FROM `mv_file_activity` a left join mv_files f on a.fk_file_id=f.file_id WHERE `check_in_date` LIKE '%0000-%' and  `check_in_date` NOT LIKE '0000-00-00%' and file_arrival_date NOT LIKE '%0000%'   and fk_file_id='$id'");
	while ($res = mysqli_fetch_array($sql)) {
		$arrival_date_year = date("Y", strtotime($res['file_arrival_date']));
		db_query("update `mv_file_activity` set check_in_date=replace(check_in_date,'0000-','" . $arrival_date_year . "-') where file_activity_id='" . $res['file_activity_id'] . "'");
	}
	$sql = db_query("SELECT a.*,f.file_arrival_date  FROM `mv_file_accommodation` a left join mv_files f on a.fk_file_id=f.file_id WHERE `check_in_date` LIKE '%0000-%' and  `check_in_date` NOT LIKE '0000-00-00%' and file_arrival_date NOT LIKE '%0000%' and fk_file_id='$id'");
	while ($res = mysqli_fetch_array($sql)) {
		$arrival_date_year = date("Y", strtotime($res['file_arrival_date']));
		db_query("update `mv_file_accommodation` set check_in_date=replace(check_in_date,'0000-','" . $arrival_date_year . "-'),check_out_date=replace(check_out_date,'0000-','" . $arrival_date_year . "-') where file_accommodation_id='" . $res['file_accommodation_id'] . "'");
	}
}
function _updateServiceDateYear($id)
{
	$sql = "SELECT t.file_transfer_id AS sid, t.check_in_date, t.check_out_date, 'transfer' AS type, YEAR(f.file_departure_date) as fyear  FROM mv_files f LEFT JOIN mv_file_transfers t ON f.file_id = t.fk_file_id WHERE fk_file_id = '$id' AND (YEAR(f.file_departure_date)<>YEAR(t.check_in_date) OR YEAR(f.file_departure_date)<>YEAR(t.check_out_date))
    UNION
    SELECT t.file_accommodation_id AS sid, t.check_in_date, t.check_out_date, 'accomm' AS type, YEAR(f.file_departure_date) as fyear  FROM mv_files f LEFT JOIN mv_file_accommodation t ON f.file_id = t.fk_file_id WHERE fk_file_id = '$id' AND (YEAR(f.file_departure_date)<>YEAR(t.check_in_date) OR YEAR(f.file_departure_date)<>YEAR(t.check_out_date))
    UNION
    SELECT t.file_activity_id AS sid, t.check_in_date, '' AS check_out_date, 'activity' AS type, YEAR(f.file_departure_date) as fyear  FROM mv_files f LEFT JOIN mv_file_activity t ON f.file_id = t.fk_file_id WHERE fk_file_id = '$id' AND (YEAR(f.file_departure_date)<>YEAR(t.check_in_date))
    UNION
    SELECT t.file_misc_id AS sid, t.check_in_date, t.check_out_date, 'misc' AS type, YEAR(f.file_departure_date) as fyear  FROM mv_files f LEFT JOIN mv_file_misc_service t ON f.file_id = t.fk_file_id WHERE fk_file_id = '$id' AND (YEAR(f.file_departure_date)<>YEAR(t.check_in_date) OR YEAR(f.file_departure_date)<>YEAR(t.check_out_date))";
	$sqlRes = db_query($sql);
	if (mysqli_num_rows($sqlRes) > 0) {
		while ($resData = mysqli_fetch_array($sqlRes)) {
			//print_r($resData); exit;
			if ($resData['type'] == 'transfer') {
				$upq = '';
				if ($resData['check_in_date'] != '' && $resData['check_in_date'] != '0000-00-00') {
					$dateArr = explode('-', $resData['check_in_date']);
					$neDate = $resData['fyear'] . '-' . $dateArr[1] . '-' . $dateArr[2];
					$upq .= " set check_in_date = '$neDate' ";
				}
				if ($resData['check_out_date'] != '' && $resData['check_out_date'] != '0000-00-00') {
					$dateArr = explode('-', $resData['check_out_date']);
					$neDate = $resData['fyear'] . '-' . $dateArr[1] . '-' . $dateArr[2];
					$upq .= ", check_out_date = '$neDate' ";
				}
				if ($upq != '') {
					$upId = $resData['sid'];
					db_query("update mv_file_transfers $upq where file_transfer_id = '$upId'");
				}
			} else if ($resData['type'] == 'accomm') {
				$upq = '';
				if ($resData['check_in_date'] != '' && $resData['check_in_date'] != '0000-00-00') {
					$dateArr = explode('-', $resData['check_in_date']);
					$neDate = $resData['fyear'] . '-' . $dateArr[1] . '-' . $dateArr[2];
					$upq .= " set check_in_date = '$neDate' ";
				}
				if ($resData['check_out_date'] != '' && $resData['check_out_date'] != '0000-00-00') {
					$dateArr = explode('-', $resData['check_out_date']);
					$neDate = $resData['fyear'] . '-' . $dateArr[1] . '-' . $dateArr[2];
					$upq .= ", check_out_date = '$neDate' ";
				}
				if ($upq != '') {
					$upId = $resData['sid'];
					//echo "update mv_file_accommodation $upq where file_accommodation_id = '$upId'";
					db_query("update mv_file_accommodation $upq where file_accommodation_id = '$upId'");
				}
			} else if ($resData['type'] == 'activity') {
				$upq = '';
				if ($resData['check_in_date'] != '' && $resData['check_in_date'] != '0000-00-00') {
					$dateArr = explode('-', $resData['check_in_date']);
					$neDate = $resData['fyear'] . '-' . $dateArr[1] . '-' . $dateArr[2];
					$upq .= " set check_in_date = '$neDate' ";
				}
				if ($upq != '') {
					$upId = $resData['sid'];
					db_query("update mv_file_activity $upq where file_activity_id = '$upId'");
				}
			} else if ($resData['type'] == 'misc') {
				$upq = '';
				if ($resData['check_in_date'] != '' && $resData['check_in_date'] != '0000-00-00') {
					$dateArr = explode('-', $resData['check_in_date']);
					$neDate = $resData['fyear'] . '-' . $dateArr[1] . '-' . $dateArr[2];
					$upq .= " set check_in_date = '$neDate' ";
				}
				if ($resData['check_out_date'] != '' && $resData['check_out_date'] != '0000-00-00') {
					$dateArr = explode('-', $resData['check_out_date']);
					$neDate = $resData['fyear'] . '-' . $dateArr[1] . '-' . $dateArr[2];
					$upq .= ", check_out_date = '$neDate' ";
				}
				if ($upq != '') {
					$upId = $resData['sid'];
					db_query("update mv_file_misc_service $upq where file_misc_id = '$upId'");
				}
			}
		}
	}
}
function updateServiceStatus($id)
{
	$sql = "SELECT t.file_transfer_id AS sid, t.service_status, 'transfer' AS type  FROM mv_files f LEFT JOIN mv_file_transfers t ON f.file_id = t.fk_file_id WHERE fk_file_id = '$id' AND t.service_status = '0' and file_transfer_status='Active'
    UNION
    SELECT t.file_accommodation_id AS sid, t.service_status, 'accomm' AS type  FROM mv_files f LEFT JOIN mv_file_accommodation t ON f.file_id = t.fk_file_id WHERE fk_file_id = '$id' AND t.service_status = '0' and file_accommodation_status='Active'
    UNION
    SELECT t.file_activity_id AS sid, t.service_status, 'activity' AS type  FROM mv_files f LEFT JOIN mv_file_activity t ON f.file_id = t.fk_file_id WHERE fk_file_id = '$id' AND t.service_status = '0' and file_activity_status='Active'
    UNION
    SELECT t.file_misc_id AS sid, t.service_status, 'misc' AS type FROM mv_files f LEFT JOIN mv_file_misc_service t ON f.file_id = t.fk_file_id WHERE fk_file_id = '$id' AND t.service_status = '0' and misc_service_status='Active'";
	$sqlRes = db_query($sql);
	if (mysqli_num_rows($sqlRes) > 0) {
		while ($resData = mysqli_fetch_array($sqlRes)) {
			//echo "<pre>"; print_r($resData); exit;
			$upId = $resData['sid'];
			if ($resData['type'] == 'transfer') {
				db_query("update mv_file_transfers set service_status = '1' where file_transfer_id = '$upId'");
			} else if ($resData['type'] == 'accomm') {
				db_query("update mv_file_accommodation set service_status = '1' where file_accommodation_id = '$upId'");
			} else if ($resData['type'] == 'activity') {
				db_query("update mv_file_activity set service_status = '1' where file_activity_id = '$upId'");
			} else if ($resData['type'] == 'misc') {
				db_query("update mv_file_misc_service set service_status = '1' where file_misc_id = '$upId'");
			}
		}
	}
}
function get_file_exchnage_rates($file_id, $curr1, $curr2)
{
	$exchange_rate = db_scalar("select exchange_rate from mv_file_currency where fk_file_id='$file_id' and base_currency='$curr1' and target_currency='$curr2'");
	if ($exchange_rate == '') {
		$exchange_rate = db_scalar("select currency_value from mv_currency where convert_currency_from='$curr1' and convert_currency_to='$curr2'");
	}
	return $exchange_rate;
}
function export_tour($tour_id)
{
	$db1 = mysqli_connect("localhost", "brazilgr_ivs", "F1KDTKCq9Z2g"); // db of IVS
	$db2 = mysqli_connect("localhost", "brazilgr_vatican", "8hvepA3efrzC", true); // db of VV
	mysqli_select_db($db1, 'brazilgr_ivs');
	mysqli_select_db($db2, 'brazilgr_vaticanvisits');
	$tour_data = db_result("select * from mv_tours where tour_id='$tour_id'", $db1);
	$supplier1 = db_result("select * from mv_supplier where supplier_id='" . $tour_data['fk_supplier_id'] . "'", $db1);
	$supplier2 = db_result("select supplier_id from mv_supplier where supplier_company_name='" . $supplier1['supplier_company_name'] . "'", $db2);
	//print_r($supplier2); exit;
	if ($supplier2['supplier_id'] > 0) {
		$new_supplier_id = $supplier2['supplier_id'];
	} else {
		extract(ms_addslashes($supplier1));
		$sql = "insert into mv_supplier SET "
			. "supplier_hotel_type ='$supplier_hotel_type' ,"
			. "supplier_username ='$supplier_username' ,"
			. "supplier_password ='$supplier_password' ,"
			. "supplier_company_name ='" . $supplier_company_name . "' , "
			. "slug='$slug', supplier_code = '$supplier_code', "
			. "supplier_address ='" . $supplier_address . "' ,"
			. "supplier_city ='" . $supplier_city . "' ,"
			. "supplier_state ='" . $supplier_state . "' ,"
			. "supplier_district ='" . $supplier_district . "' ,"
			. "supplier_zipcode ='$supplier_zipcode' ,"
			. "supplier_country ='$supplier_country' ,"
			. "supplier_phone ='$supplier_phone' ,"
			. "supplier_fax ='$supplier_fax' ,"
			. "supplier_contact_name ='$supplier_contact_name' ,"
			. "supplier_contact_number ='$supplier_contact_number' ,"
			. "supplier_contact_email ='$supplier_contact_email' ,"
			. "supplier_email ='$supplier_email' ,"
			. "supplier_reservation_email ='$supplier_reservation_email' ,"
			. "supplier_emergency_number ='$supplier_emergency_number' ,"
			. "supplier_website ='$supplier_website' ,"
			. "supplier_mv_url ='$supplier_mv_url' ,"
			. "supplier_booking_engine ='$supplier_booking_engine' ,"
			. "supplier_map_link ='$supplier_map_link' ,"
			. "supplier_bank_information ='$supplier_bank_information' ,"
			. "supplier_comments ='$supplier_comments' ,"
			. "supplier_description ='$supplier_description' ,"
			. "supplier_directions ='$supplier_directions' ,"
			. "supplier_min_no_nights ='$supplier_min_no_nights' ,"
			. "supplier_currency ='$supplier_currency' ,"
			. "supplier_credit_cards ='$supplier_credit_cards' ,"
			. "supplier_closed_from ='$supplier_closed_from' ,"
			. "supplier_closed_to ='$supplier_closed_to' ,"
			. "supplier_contract_file ='$supplier_contract_file' ,"
			. "supplier_attachment_file ='$supplier_attachment_file' ,"
			. "supplier_images ='$supplier_images' ,"
			. "supplier_business_type ='$supplier_business_type' ,"
			. "supplier_status ='Active' ,"
			. "supplier_added_date =now() ,"
			. "supplier_added_by ='" . $_SESSION['sess_agent_id'] . "',"
			. "supplier_emergency_number_secondary='$supplier_emergency_number_secondary',"
			. "supplier_reservation_contact='$supplier_reservation_contact',"
			. "supplier_accommodation_type='$supplier_accommodation_type',"
			. "supplier_phone_country_code='$supplier_phone_country_code',"
			. "supplier_phone_area_code='$supplier_phone_area_code',"
			. "supplier_contact_number_country_code='$supplier_contact_number_country_code',"
			. "supplier_contact_number_area_code='$supplier_contact_number_area_code',"
			. "supplier_emergency_number_secondary_country_code='$supplier_emergency_number_secondary_country_code',"
			. "supplier_emergency_number_secondary_area_code='$supplier_emergency_number_secondary_area_code',"
			. "supplier_fax_country_code='$supplier_fax_country_code',"
			. "supplier_fax_area_code='$supplier_fax_area_code',"
			. "location_tag_ids='$location_tag_ids',"
			. "location_tags_name='$location_tags_name',"
			. "supplier_rating='$supplier_rating',"
			. "supplier_emergency_number_country_code='$supplier_emergency_number_country_code',"
			. "supplier_emergency_number_area_code='$supplier_emergency_number_area_code',"
			. "supplier_accommodation_currency = '$supplier_accommodation_currency' ,"
			. "supplier_transfer_currency = '$supplier_transfer_currency' ,"
			. "supplier_ferry_currency = '$supplier_ferry_currency' ,"
			. "supplier_train_currency = '$supplier_train_currency' ,"
			. "supplier_flight_currency = '$supplier_flight_currency' ,"
			. "supplier_activity_currency = '$supplier_activity_currency' ,"
			. "supplier_misc_currency = '$supplier_misc_currency',"
			. "service_currency_same_for_all='Yes',"
			. "map_latitude='$map_latitude',"
			. "map_longitude='$map_longitude',"
			. "map_view='$map_view' ";
		//echo $sql; exit;
		db_query($sql, $db2);
		$new_supplier_id = mysqli_insert_id();
		$supplier_code_arr = explode('-', $supplier_code);
		$supplier_code = $supplier_code_arr[0] . "-" . $supplier_code_arr[1] . "-" . str_pad($new_supplier_id, 5, 0, STR_PAD_LEFT);
		db_query("UPDATE mv_supplier SET supplier_code ='$supplier_code' WHERE supplier_id ='$new_supplier_id'", $db2);
	}
	extract(ms_addslashes($tour_data));
	$sql2 = "INSERT INTO mv_tours set "
		. "fk_supplier_id='$new_supplier_id',"
		. "tour_slug='$tour_slug',"
		. "tour_name='$tour_name',"
		. "supplier_tour_name='$supplier_tour_name',"
		. "tour_country_tag_ids='$tour_country_tag_ids',"
		. "is_private='$is_private',"
		. "activity_segment='$activity_segment',"
		. "tour_available_to='$tour_available_to',"
		. "tour_country='$tour_country',"
		. "tour_city_tag_ids='$tour_city_tag_ids',"
		. "tour_city='$tour_city',"
		. "tour_meeting_point='$tour_meeting_point',"
		. "tour_departure='$tour_departure',"
		. "tour_return_city='$tour_return_city',"
		. "tour_return_city_tag_ids='$tour_return_city_tag_ids',"
		. "tour_type='$tour_type',"
		. "tour_min_pax='$tour_min_pax',"
		. "tour_max_pax='$tour_max_pax',"
		. "tour_languages='$tour_languages',"
		. "tour_hotel_pickup='$tour_hotel_pickup',"
		. "tour_hotel_dropoff='$tour_hotel_dropoff',"
		. "tour_length='$tour_length',"
		. "tour_days='$tour_days',"
		. "tour_nights='$tour_nights',"
		. "tour_activity_type='$tour_activity_type',"
		. "tour_start_time='$tour_start_time',"
		. "tour_end_time='$tour_end_time',"
		. "no_breakfasts='$no_breakfasts',"
		. "no_lunches='$no_lunches',"
		. "no_dinners='$no_dinners',"
		. "no_wine_tastings='$no_wine_tastings',"
		. "no_cooking_course='$no_cooking_course',"
		. "no_picnics='$no_picnics',"
		. "tour_includes='$tour_includes',"
		. "tour_includes_other='$tour_includes_other',"
		. "tour_excludes='$tour_excludes',"
		. "tour_excludes_other='$tour_excludes_other',"
		. "tour_interest='$tour_interest',"
		. "tour_interest_other='$tour_interest_other',"
		. "tour_description='$tour_description',"
		. "tour_base_currency='$tour_base_currency',"
		. "tour_status='$tour_status',"
		. "tour_added_by='" . $_SESSION['sess_agent_id'] . "',"
		. "tour_added_on=now(),"
		. "tour_schedule='$tour_schedule',"
		. "tour_availability='$tour_availability',"
		. "tour_availability_start_date='$tour_availability_start_date',"
		. "tour_availability_end_date='$tour_availability_end_date',"
		. "tour_availability_days='$tour_availability_days',"
		. "tour_classification='$tour_classification',"
		. "tour_meeting_maplink='$tour_meeting_maplink',"
		. "tour_meeting_address='$tour_meeting_address',"
		. "meeting_time='$meeting_time',"
		. "pickup_time='$pickup_time',"
		. "tour_pickup_hotel='$tour_pickup_hotel',"
		. "map_latitude='$map_latitude',"
		. "map_longitude='$map_longitude',"
		. "map_view='$map_view',"
		. "tour_short_description='" . $tour_short_description . "',"
		. "tour_published='No',"
		. "map_type='$map_type',"
		. "tour_map_image='$tour_map_image',"
		. "public_pricing='$public_pricing',"
		. "private_pricing='$private_pricing',"
		. "group_pricing='$group_pricing',"
		. "mv_meta_title='$mv_meta_title',"
		. "mv_meta_description='$mv_meta_description',"
		. "mv_meta_keywords='$mv_meta_keywords',"
		. "page_heading_content='$page_heading_content',"
		. "tour_category='$tour_category',"
		. "tour_price='$tour_price',"
		. "display_price='$display_price',"
		. "is_top_selling_tour='$is_top_selling_tour',"
		. "tour_highlights='$tour_highlights',"
		. "tour_highlights_other='$tour_highlights_other',"
		. "show_on_landingpage='$show_on_landingpage',"
		. "price_only_USD='$price_only_USD',"
		. "tour_data_completed='$tour_data_completed',"
		. "child_price='$child_price',"
		. "tour_net_cost='$tour_net_cost',"
		. "tour_price_type='$tour_price_type'";
	//echo $sql2; exit;
	db_query($sql2, $db2);
	$new_tour_id = mysqli_insert_id();
	$tour_code = "TUR-" . str_pad($new_tour_id, 5, 0, STR_PAD_LEFT);
	db_query("UPDATE mv_tours SET tour_code ='$tour_code' WHERE tour_id ='$new_tour_id'", $db2);
	db_query("UPDATE mv_tours SET import_to_vv ='$new_tour_id' WHERE tour_id ='$tour_id'", $db1);
	$sql_sight = db_query("select * from mv_tour_sights where fk_supplier_id='$fk_supplier_id' and sight_status='Active' and fk_tour_id='$tour_id'", $db1);
	if (mysqli_num_rows($sql_sight) > 0) {
		while ($tour_sights_data = mysqli_fetch_array($sql_sight)) {
			$tour_sights_data = ms_addslashes($tour_sights_data);
			db_query("INSERT INTO mv_tour_sights set fk_supplier_id = '$new_supplier_id',
            fk_tour_id = '$new_tour_id', locations_name = '" . ms_addslashes($tour_sights_data['locations_name']) . "',
            locations_ids = '" . $tour_sights_data['locations_ids'] . "', sight_status = '" . $tour_sights_data['sight_status'] . "'", $db2);
		}
	}
	$sqlmap = db_query("select * from mv_tour_maps where fk_tour_id='$tour_id'", $db1);
	if (mysqli_num_rows($sqlmap) > 0) {
		while ($tour_map_data = mysqli_fetch_array($sqlmap)) {
			$tour_map_data = ms_addslashes($tour_map_data);
			db_query("insert into mv_tour_maps set fk_tour_id = '$new_tour_id', map_latitude = '" . $tour_map_data['map_latitude'] . "',"
				. " map_longitude = '" . $tour_map_data['map_longitude'] . "', map_label = '" . $tour_map_data['map_label'] . "'", $db2);
		}
	}
	$sql3 = db_query("select * from mv_itinerary_description where fk_tour_id='$tour_id'", $db1);
	if (mysqli_num_rows($sql3) > 0) {
		while ($tour_itinerary = mysqli_fetch_array($sql3)) {
			$tour_itinerary = ms_addslashes($tour_itinerary);
			db_query("insert into mv_itinerary_description set fk_supplier_id = '$new_supplier_id', fk_tour_id = '$new_tour_id',"
				. "day_id = '" . $tour_itinerary['day_id'] . "', itinerary_title = '" . $tour_itinerary['itinerary_title'] . "', "
				. "itinerary_hotel = '" . $tour_itinerary['itinerary_hotel'] . "', "
				. "itinerary_segment = '" . $tour_itinerary['itinerary_segment'] . "', "
				. "itinerary_description = '" . $tour_itinerary['itinerary_description'] . "',"
				. "itinerary_meals = '" . $tour_itinerary['itinerary_meals'] . "', tag_ids_1 = '" . $tour_itinerary['tag_ids_1'] . "',"
				. "tag_ids_2 = '" . $tour_itinerary['tag_ids_2'] . "', tag_ids_3 = '" . $tour_itinerary['tag_ids_3'] . "',"
				. "tag_names_1 = '" . $tour_itinerary['tag_names_1'] . "', tag_names_2 = '" . $tour_itinerary['tag_names_2'] . "',"
				. "tag_names_3 = '" . $tour_itinerary['tag_names_3'] . "'", $db2);
		}
	}
	$sql4 = db_query("select * from mv_tour_images where fk_tour_id='$tour_id' and image_status = 'Active' ", $db1);
	if (mysqli_num_rows($sql4) > 0) {
		while ($tour_images = mysqli_fetch_array($sql4)) {
			$tour_images = ms_addslashes($tour_images);
			db_query("insert into mv_tour_images set fk_tour_id = '$new_tour_id', image_title = '" . $tour_images['image_title'] . "', "
				. "image_description = '" . $tour_images['image_title'] . "', image_name = '" . $tour_images['image_name'] . "', image_type = '" . $tour_images['image_type'] . "',"
				. "image_status = '" . $tour_images['image_status'] . "', image_order = '" . $tour_images['image_order'] . "'", $db2);
			$destPath = '/home/brazilgr/public_html/vaticanvisits.com/uploaded_files/tour';
			if (file_exists(UP_FILES_FS_PATH . '/tour/' . $tour_images['image_name'])) {
				//echo "=================".$tour_images['image_name']."<br>"; exit;
				copy(UP_FILES_FS_PATH . '/tour/' . $tour_images['image_name'], $destPath . '/' . $tour_images['image_name']);
			}
		}
	}
	$sql5 = db_query("select * from mv_tour_start_time where fk_tour_id='$tour_id'", $db1);
	if (mysqli_num_rows($sql5) > 0) {
		while ($tour_start_time = mysqli_fetch_array($sql5)) {
			$tour_start_time = ms_addslashes($tour_start_time);
			db_query("insert into mv_tour_start_time set fk_tour_id = '$new_tour_id', tour_start_time = '" . $tour_start_time['tour_start_time'] . "', tour_end_time = '" . $tour_start_time['tour_end_time'] . "'", $db2);
		}
	}
	$files_sql = db_query("select * from mv_tour_files where fk_tour_id='$tour_id' and file_status='Active' and file_type='Contract'", $db1);
	if (mysqli_num_rows($files_sql) > 0) {
		while ($tour_files = mysqli_fetch_array($files_sql)) {
			$tour_files = ms_addslashes($tour_files);
			db_query("insert into mv_tour_files set fk_tour_id = '$new_tour_id', file_title = '" . $tour_files['file_title'] . "',"
				. " file_name = '" . $tour_files['file_name'] . "', file_status = '" . $tour_files['file_status'] . "', file_type = '" . $tour_files['file_type'] . "'", $db2);
			$destPath = '/home/brazilgr/public_html/vaticanvisits.com/uploaded_files/tour/tour_files';
			if (file_exists(UP_FILES_FS_PATH . '/tour/tour_files/' . $tour_files['file_name'])) {
				//echo "=================".$tour_files[file_name]."<br>";
				copy(UP_FILES_FS_PATH . '/tour/tour_files/' . $tour_files['file_name'], $destPath . '/' . $tour_files['file_name']);
			}
		}
	}
	$sql6 = db_query("select * from igs_private_tour_price where fk_tour_id='$tour_id'", $db1);
	if (mysqli_num_rows($sql6) > 0) {
		while ($tour_price = mysqli_fetch_array($sql6)) {
			$tour_price = ms_addslashes($tour_price);
			db_query("insert into igs_private_tour_price set fk_tour_id = '$new_tour_id', tour_pax = '" . $tour_price['tour_pax'] . "', tour_price = '" . $tour_price['tour_price'] . "', tour_gross_price = '" . $tour_price['tour_gross_price'] . "', price_status = '" . $tour_price['price_status'] . "' ", $db2);
		}
	}
	//echo "Done"; exit;
	return $new_tour_id;
}
function getSignatureTourIds()
{
	$tourIdArr = array();
	/*$tourIds = db_scalar("SELECT GROUP_CONCAT(DISTINCT(tour_id)) as tour_ids FROM ivs_page_tour where page_id = 45");
    if(!empty($tourIds)){
        $tourIdArr = explode(',', $tourIds);
    }*/
	return $tourIdArr;
}
function discount_price($price, $percent = 5)
{
	$disPrice = 0;
	if ($percent > 0) {
		$disPrice = $price - ($price * $percent / 100);
	} else {
		$disPrice = $price;
	}
	return $disPrice;
}
function create_password()
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789#@$";
	$password = substr(str_shuffle($chars), 0, 8);
	return $password;
}
function changeIframeWidth($html)
{
	return preg_replace('/width=[\"\'][0-9]+[\"\']/i', 'width="100%"', $html ?? '');
}
function google_captcha_validation($secret_key, $captcha_response)
{
	$post_data = "secret=" . $secret_key . "&response=" .
		$captcha_response . "&remoteip=" . $_SERVER['REMOTE_ADDR'];
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt(
		$ch,
		CURLOPT_HTTPHEADER,
		array(
			'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
			'Content-Length: ' . strlen($post_data)
		)
	);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	$googresp = curl_exec($ch);
	$decgoogresp = json_decode($googresp);
	curl_close($ch);
	return $decgoogresp;
}
function update_services($fileID)
{
	$result = db_result("select * from mv_files  where file_id = '$fileID'");
	$sql = "
        SELECT fa.fk_file_id, fa.`file_activity_id` AS service_id, fa.`booking_net_price`, 'Activity' AS stype, booking_price_currency FROM `mv_file_activity` fa
        WHERE fa.file_activity_status = 'Active' AND (fa.`booking_net_price` > 0 AND fa.`booking_gross_price` = 0) AND fa.`fk_file_id` = '$fileID'
        UNION
        SELECT fac.fk_file_id, fac.file_accommodation_id AS service_id, fac.`booking_net_price`, 'Accom' AS stype, booking_price_currency FROM `mv_file_accommodation` fac
        WHERE fac.file_accommodation_status = 'Active' AND (fac.`booking_net_price` > 0 AND fac.`booking_gross_price` = 0) AND fac.`fk_file_id` = '$fileID'
        UNION
        SELECT fm.fk_file_id, fm.`file_misc_id` AS service_id, fm.`booking_net_price`, 'Misc' AS stype, booking_price_currency FROM `mv_file_misc_service` fm
        WHERE fm.`misc_service_status` = 'Active' AND (fm.`booking_net_price` > 0 AND fm.`booking_gross_price` = 0) AND fm.`fk_file_id` = '$fileID'
        UNION
        SELECT ft.fk_file_id, ft.`file_transfer_id` AS service_id, ft.`booking_net_price`, 'Transfer' AS stype, booking_price_currency FROM  mv_file_transfers ft
        WHERE ft.`file_transfer_status` = 'Active' AND (ft.`booking_net_price` > 0 AND ft.`booking_gross_price` = 0) AND ft.`fk_file_id` = '$fileID'
        ";
	$sqlRes = db_query($sql);
	if (mysqli_num_rows($sqlRes) > 0) {
		while ($res = mysqli_fetch_array($sqlRes)) {
			echo "<pre>";
			print_r($res);
			$booking_price_currency = ($res['booking_price_currency'] == '' ? $result['file_currency'] : $res['booking_price_currency']);
			$booking_currency_sql = ($res['booking_price_currency'] == '' ? ", booking_price_currency = '$booking_price_currency'" : '');
			$net_sc = $res['booking_net_price'] * file_exchnage_rates($fileID, $booking_price_currency, $result['file_currency']);
			$gross_sc = $net_sc + ($net_sc * $result['file_markup']) / 100;
			$booking_gross_price = ceil($gross_sc);
			if ($res['stype'] == 'Activity') {
				db_query("update mv_file_activity set booking_gross_price = '$booking_gross_price' $booking_currency_sql where file_activity_id = '" . $res['service_id'] . "'");
			} else if ($res['stype'] == 'Accom') {
				db_query("update mv_file_accommodation set booking_gross_price = '$booking_gross_price' $booking_currency_sql where file_accommodation_id = '" . $res['service_id'] . "'");
			} else if ($res['stype'] == 'Misc') {
				db_query("update mv_file_misc_service set booking_gross_price = '$booking_gross_price' $booking_currency_sql where file_misc_id = '" . $res['service_id'] . "'");
			} else if ($res['stype'] == 'Transfer') {
				db_query("update mv_file_transfers set booking_gross_price = '$booking_gross_price' $booking_currency_sql where file_transfer_id = '" . $res['service_id'] . "'");
			}
		}
		return true;
	}
	return false;
}
function generate_wise_invoice($id, $ag_comm, $cnf)
{
	$result = db_result("select * from mv_files where file_id = '$id'");
	$disp_agent_comm = $ag_comm;
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/config/lang/eng.php');
	require_once(SITE_FS_PATH . '/pdf_creator/tcpdf/tcpdf.php');
	if (!class_exists('MYPDF')) {
		class MYPDF extends TCPDF
		{
			//Page header
			public function Header()
			{
				//$this->Cell(790, 1, 'dddddddddddd', 0, 1, 'C', 0, '', 1);
			}
			protected $last_page_flag = false;
			public function Close()
			{
				$this->last_page_flag = true;
				parent::Close();
			}
			// Page footer
			public function Footer()
			{
				$image_file = SITE_FS_PATH . "/" . AGENT_ADMIN_DIR . '/images/invoice-footer.jpg';
				//if ($this->last_page_flag){
				$logoX = 0;
				$logoFileName = $image_file;
				$logoWidth = 596;
				$logoY = 662;
				$logo = $this->Image($logoFileName, $logoX, $logoY, $logoWidth);
				$this->SetX($this->w - 18 - $logoWidth); // Using 18 as the document right margin
				$this->Cell(10, 10, $logo, 0, 0, 'C');
				//}
			}
		}
	}
	$margin_left = 0;
	$margin_top = 15;
	$margin_right = 0;
	$margin_bottom = 180;
	$pdf = new MYPDF('P', 'px', 'A4', true);
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor(SITE_WS_PATH);
	$pdf->SetTitle('Invoice');
	$pdf->SetSubject("Voucher - File " . $result['file_code']);
	$pdf->SetKeywords("Voucher - File " . $result['file_code']);
	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
	$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
	$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
	$pdf->SetMargins($margin_left, $margin_top);
	$pdf->SetHeaderMargin(25);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, $margin_bottom);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->setLanguageArray($l);
	//echo $pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('', '', 6);
	$pdf->SetTextColor('108', '108', '108');
	ob_start();
	$pdf->SetFont('', '', 8);
	include(SITE_FS_PATH . "/" . AGENT_ADMIN_DIR . "/voucher/invoice_wise_pdf.inc.php");
	$content = ob_get_contents();
	ob_clean();
	$pdf->writeHTML($content, true, 0, true, 0);
	$my_file_name = UP_FILES_FS_PATH . "/invoice/invoice_" . $result['file_code'] . "_" . date("Y-m-d_H-i-s", time()) . ".pdf";
	$pdf->Output($my_file_name, 'I');
	//    db_query("insert into mv_invoice_files set fk_file_id='$id',pdf_file_name='$my_file_name',file_added_by='".$_SESSION['sess_agent_id']."',file_added_on=now(),is_agent_invoice='$disp_agent_comm', is_detail_pricing='$cnf'");
	//    ###############################  For Logging ###########################
	//    $log_title="Invoice generated for file (".$result['file_code'].")";
	//    db_query("insert into mv_daily_log set employee_id='".$_SESSION['sess_agent_id']."',log_title='".addslashes($log_title)."',log_action='Delete',log_added_date=now(),log_mysql_query='".addslashes($sql)."',log_url='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."',log_user_agent='".$_SERVER['HTTP_USER_AGENT']."'");
	//    #########################################################################
	//    $log_description="Invoice generated.";
	//    db_query("insert into mv_file_activity_log set fk_file_id='$id',fk_agent_id='".$_SESSION['sess_agent_id']."',activity_added_on=now(),activity_description='".addslashes($log_description)."'");
}
function save_transfer_wise($id, $data = array())
{
	if (!empty($data)) {
		//print_r($data); exit;
		db_query(
			"insert into ivs_transferwise_invoice set fk_file_id='$id', "
				. "service_date='" . $data['date'] . "', "
				. "service_time='" . $data['time'] . "', "
				. "service_name='" . $data['service'] . "', "
				. "no_of_pax='" . $data['pax_no'] . "', "
				. "price_of_pax='" . $data['price'] . "', "
				. "added_by='" . $_SESSION['sess_agent_id'] . "', "
				. "added_on=now() "
		);
	}
}
function service_star($id, $starArr, $serviceId, $serviceStatus, $type)
{
	if ($serviceStatus == 1) {
		$newStarFlag = false;
		$flagKey = $serviceId . '-' . $type;
		if (isset($starArr[$flagKey])) {
			$newStarFlag = true;
			$flagData = $starArr[$flagKey];
			if ($flagData['star1'] == 11) {
				$star1Img = 'golden_star.png';
				$tmpStar1 = 10;
			} else {
				$star1Img = 'grey_star.png';
				$tmpStar1 = 11;
			}
			if ($flagData['star2'] == 21) {
				$star2Img = 'golden_star.png';
				$tmpStar2 = 20;
			} else {
				$star2Img = 'grey_star.png';
				$tmpStar2 = 21;
			}
		}
		if ($newStarFlag) {
	?>
			<a href="file/file_services.php?id=<?= $id ?>&star1=<?= $tmpStar1 ?>&service_status=<?= $serviceStatus ?>&act=update_service_status&record_id=<?= $serviceId ?>&service_type=<?= $type ?>" onclick="return confirm('Are you sure you want to continue?');exit;">
				<?php echo '<img src="' . SITE_WS_PATH . '/images/' . $star1Img . '">'; ?>
			</a>
			<a href="file/file_services.php?id=<?= $id ?>&star2=<?= $tmpStar2 ?>&service_status=<?= $serviceStatus ?>&act=update_service_status&record_id=<?= $serviceId ?>&service_type=<?= $type ?>" onclick="return confirm('Are you sure you want to continue?');exit;">
				<?php echo '<img src="' . SITE_WS_PATH . '/images/' . $star2Img . '">'; ?>
			</a>
		<?php } else { ?>
			<a href="file/file_services.php?id=<?= $id ?>&star1=10&service_status=<?= $serviceStatus ?>&act=update_service_status&record_id=<?= $serviceId ?>&service_type=<?= $type ?>" onclick="return confirm('Are you sure you want to continue?');exit;">
				<?php echo '<img src="' . SITE_WS_PATH . '/images/golden_star.png">'; ?>
			</a>
			<a href="file/file_services.php?id=<?= $id ?>&star2=20&service_status=<?= $serviceStatus ?>&act=update_service_status&record_id=<?= $serviceId ?>&service_type=<?= $type ?>" onclick="return confirm('Are you sure you want to continue?');exit;">
				<?php echo '<img src="' . SITE_WS_PATH . '/images/golden_star.png">'; ?>
			</a>
		<?php } ?>
	<?php } else { ?>
		<a href="file/file_services.php?id=<?= $id ?>&star1=11&service_status=<?= $serviceStatus ?>&act=update_service_status&record_id=<?= $serviceId ?>&service_type=<?= $type ?>" onclick="return confirm('Are you sure you want to continue?');exit;">
			<?php echo '<img src="' . SITE_WS_PATH . '/images/grey_star.png" height="32">'; ?>
		</a>
		<a href="file/file_services.php?id=<?= $id ?>&star2=21&service_status=<?= $serviceStatus ?>&act=update_service_status&record_id=<?= $serviceId ?>&service_type=<?= $type ?>" onclick="return confirm('Are you sure you want to continue?');exit;">
			<?php echo '<img src="' . SITE_WS_PATH . '/images/grey_star.png" height="32">'; ?>
		</a>
<?php
	}
}
function copy_images($folder, $image)
{
	// $srcImgFile = UP_FILES_FS_PATH . '/' . $folder . '/' . $image;
	// $destImgFile = UP_FILES_NEWFS_PATH . '/' . $folder . '/' . $image;
	// if (file_exists($srcImgFile)) {
	// 	if (!file_exists($destImgFile)) {
	// 		copy($srcImgFile, $destImgFile);
	// 	}
	// }
}
function get_agent_bank_details($agent_id, $file_code, $file_current_status)
{
	// TODO search agent bank details in ivs_agent_commissions
	// check if coloumn bank_info, iban, swift have data
	// if not have data then send email to agent to get bank details
	// if have data then return bank details
	$agent_bank_details = db_result("SELECT bank_info, iban, swift FROM ivs_agent_commissions WHERE agent_id='$agent_id' AND bank_info != '' AND iban != '' AND swift != ''");
	// if (!$agent_bank_details) {
	// 	send_email_agent_payment_detail($agent_id);
	// 	error_log("Agent $agent_id does not have bank details. Email sent to agent to provide bank details.");
	// }
	// TODO check on ivs_agent_commissions if there is a file_number associated with the agent
	$file_number = db_scalar("SELECT file_number FROM ivs_agent_commissions WHERE agent_id = '$agent_id' AND CONCAT(' ', file_number, ' ') LIKE '% $file_code %' LIMIT 1");
	if (!$file_number && $file_current_status == 3) {
		// TODO if no file_number found, create a new commission record
		error_log("No commission record found for agent $agent_id with file code $file_code. Creating new commission record.");
		$file_data = db_row("
						SELECT
							f.file_code,
							f.gross_agent_commission,
							f.file_currency,
							CONCAT(a.agent_first_name, ' ', a.agent_last_name) AS agent_name,
							CONCAT(c.client_first_name, ' ', c.client_last_name) AS client_name
						FROM mv_files f
						LEFT JOIN mv_agent a ON f.fk_agent_id = a.agent_id
						LEFT JOIN mv_client c ON f.fk_client_id = c.client_id
						WHERE f.file_code = '$file_code'
						AND f.fk_agent_id = '$agent_id'
						AND f.file_status = 'Active'
						LIMIT 1
		");
		error_log("File data for agent $agent_id with file code $file_code: " . print_r($file_data, true));
		if ($file_data) {
			error_log("Creating new commission record for agent $agent_id with file code $file_code.");
			$sql = "INSERT INTO ivs_agent_commissions (
				file_number, agent_id, agent_name, client_name, payment_amount,
				added_on, added_by, paid_status, paid_on, paid_by,
				bank_info, staff_comments, payment_comments, payment_status,
				modified_on, modified_by, payment_due_date, payment_currency,
				iban, swift, file_name
			) VALUES (
				'$file_code',
				'$agent_id',
				'" . $file_data['agent_name'] . "',
				'" . $file_data['client_name'] . "',
				'" . $file_data['gross_agent_commission'] . "',
				NOW(),
				0,
				'Unpaid',
				'0000-00-00',
				0,
				'" . (!empty($agent_bank_details['bank_info']) ? $agent_bank_details['bank_info'] : '') . "',
				'',
				'',
				'Active',
				NULL,
				0,
				'0000-00-00',
				'" . (!empty($file_data['file_currency']) ? $file_data['file_currency'] : '') . "',
				'" . (!empty($agent_bank_details['iban']) ? $agent_bank_details['iban'] : '') . "',
				'" . (!empty($agent_bank_details['swift']) ? $agent_bank_details['swift'] : '') . "',
				'" . (!empty($file_data['file_name']) ? $file_data['file_name'] : '') . "'
			)";
			error_log("Inserted new agent commission for agent $agent_id with file code $file_code. with query: $sql");
			db_query($sql);
			error_log("Inserting agent commission for agent $agent_id with file code $file_code.");
		}
	}
}
function send_email_agent_payment_detail($agent_id)
{
	// TODO send email to agent with bank details
	// use mv_agent table to get agent email
	$agent_email = db_scalar("SELECT agent_email FROM mv_agent WHERE agent_id='$agent_id'");
	$from_name = "Accounting Italy Vacation Specialists";
	$from_email = "accounting@italyvacationspecialists.com";
	$email_subject = "Request for Bank Details - Italy Vacation Specialists";
	error_log("Sending email to agent $agent_email to get bank details.");
	$email_template = "
		<p>Dear Agent,</p>
		<p>We noticed that you have not provided your bank details for payments. Please provide your bank information, including IBAN and SWIFT codes, to ensure timely payments.</p>
		<p>You can send your bank details to us at your earliest convenience.</p>
		<p>Please send to our Accounting email adderess at accounting@italyvacationspecialists.com</p>
		<br>
		<br>
		<p>Thank you for your cooperation.</p>
		<p>Best regards,<br>
		<b>Italy Vacation Specialists</b></p>
		<br>
		<br>
		<p style='font-size: 8px;'>this email is sent automatically by the system, please do not reply to this email.</p>
	";
	send_mail2($from_name, $from_email, $agent_email, $email_subject, $email_template);
}
function agent_email_payment_template()
{
	// TODO create email template for agent payment details
	$email_template = "
		<p>Dear Agent,</p>
		<p>We noticed that you have not provided your bank details for payments. Please provide your bank information, including IBAN and SWIFT codes, to ensure timely payments.</p>
		<p>You can send your bank details to us at your earliest convenience.</p>
		<p>Please send to our Accounting email adderess at accounting@italyvacationspecialists.com</p>
		<br>
		<br>
		<p>Thank you for your cooperation.</p>
		<p>Best regards,<br>Italy Vacation Specialists</p>
	";
	return $email_template;
}
/**
 * Function to find and validate PDF attachment for invoice
 * @param array $invoice_result - Result from mv_invoice_files query
 * @param int $file_id - File ID for fallback search
 * @return string|false - Returns valid PDF path or false if not found
 */
function get_invoice_pdf_attachment($invoice_result, $file_id = null)
{
	$attachment_path = '';
	// If invoice result has PDF file name, try to find it
	if ($invoice_result && isset($invoice_result['pdf_file_name']) && $invoice_result['pdf_file_name'] != '') {
		$pdf_filename = $invoice_result['pdf_file_name'];
		// First, check if the stored path exists directly
		if (file_exists($pdf_filename)) {
			$attachment_path = $pdf_filename;
		} else {
			// If direct path doesn't work, try to construct alternative paths
			$filename_only = basename($pdf_filename);
			// Check common paths for PDF files
			$possible_paths = [
				$pdf_filename, // Original path from database
				UP_FILES_FS_PATH . '/invoice/' . $filename_only,
				SITE_FS_PATH . '/uploaded_files/invoice/' . $filename_only,
				dirname(__FILE__) . '/../../uploaded_files/invoice/' . $filename_only,
				$_SERVER['DOCUMENT_ROOT'] . '/uploaded_files/invoice/' . $filename_only,
				$_SERVER['DOCUMENT_ROOT'] . '/ivsportal/uploaded_files/invoice/' . $filename_only,
				realpath(dirname(__FILE__) . '/../../') . '/uploaded_files/invoice/' . $filename_only,
			];
			foreach ($possible_paths as $path) {
				if ($path && file_exists($path)) {
					$attachment_path = $path;
					break;
				}
			}
		}
		// If current invoice PDF not found and we have file_id, try to find alternative working PDF
		if (!$attachment_path && $file_id) {
			error_log("Primary PDF not found for invoice. Searching for alternative PDFs for file_id: $file_id");
			// Get all active invoice files for this file_id, ordered by most recent
			$alternative_query = "SELECT * FROM mv_invoice_files WHERE fk_file_id='$file_id' AND file_status='Active' ORDER BY invoice_id DESC LIMIT 10";
			$alternative_sql = db_query($alternative_query);
			while ($alt_result = mysqli_fetch_array($alternative_sql)) {
				$alt_pdf_filename = $alt_result['pdf_file_name'];
				if ($alt_pdf_filename) {
					$alt_filename_only = basename($alt_pdf_filename);
					$alt_possible_paths = [
						$alt_pdf_filename,
						UP_FILES_FS_PATH . '/invoice/' . $alt_filename_only,
						SITE_FS_PATH . '/uploaded_files/invoice/' . $alt_filename_only,
						dirname(__FILE__) . '/../../uploaded_files/invoice/' . $alt_filename_only,
						realpath(dirname(__FILE__) . '/../../') . '/uploaded_files/invoice/' . $alt_filename_only,
					];
					foreach ($alt_possible_paths as $alt_path) {
						if ($alt_path && file_exists($alt_path)) {
							$attachment_path = $alt_path;
							error_log("Alternative PDF found: $alt_path (Invoice ID: " . $alt_result['invoice_id'] . ")");
							break 2; // Break out of both loops
						}
					}
				}
			}
		}
	}
	// Log result
	if ($attachment_path && file_exists($attachment_path)) {
		error_log("PDF attachment resolved: $attachment_path");
		return $attachment_path;
	} else {
		error_log("PDF attachment not found for file_id: $file_id");
		return false;
	}
}
function calculate_invoice_total($file_id, $currency, $total_pax)
{
	$grand_total_gross_sc = 0;
	$file_taxes = 0;
	$file_card_fee = 0;
	$file_booking_charge = 0;
	$booking_fee_basis = 1;
	$cruise_extras = 0;
	$result = db_result("SELECT * FROM mv_files WHERE file_id = '$file_id'");
	extract($result);
	// Hitung ulang grand_total_gross_sc dari semua komponen
	$sections = ['mv_file_accommodation', 'mv_file_misc_service', 'mv_file_transfers', 'mv_file_activity'];
	$section_status_columns = [
		'mv_file_accommodation' => 'file_accommodation_status',
		'mv_file_misc_service' => 'misc_service_status',
		'mv_file_transfers' => 'file_transfer_status',
		'mv_file_activity' => 'file_activity_status'
	];
	foreach ($sections as $table) {
		$status_column = $section_status_columns[$table];
		$q = db_query("SELECT booking_gross_price FROM $table WHERE fk_file_id = '$file_id' AND $status_column = 'Active'");
		while ($row = mysqli_fetch_array($q)) {
			$grand_total_gross_sc += $row['booking_gross_price'];
		}
	}
	$gross_total_sc_without_tax = $grand_total_gross_sc;
	// Hitung service fee
	$booking_fee = ($booking_fee_basis == "1") ? $total_pax * $file_booking_charge : $file_booking_charge;
	$other_service_fees_commissionable = 0;
	$other_service_fees_non_commissionable = 0;
	$other_service_fee_sql = db_query("SELECT * FROM mv_file_service_fees WHERE service_fee_status!='Delete' AND fk_file_id='$file_id'");
	while ($row = mysqli_fetch_array($other_service_fee_sql)) {
		$rate = file_exchnage_rates($file_id, $row['service_fee_currency'], $currency);
		$amount = ($row['service_fee_basis'] == 1) ? $rate * $row['service_fee'] * $total_pax : $rate * $row['service_fee'];
		if ($row['is_commissionable'] == "Yes") {
			$other_service_fees_commissionable += $amount;
		} else {
			$other_service_fees_non_commissionable += $amount;
		}
	}
	$service_fee = $booking_fee + $other_service_fees_commissionable;
	$tax = (($gross_total_sc_without_tax * $file_taxes) / 100) + $other_service_fees_non_commissionable + $cruise_extras;
	$gross_total_sc = $gross_total_sc_without_tax + $tax + $service_fee;
	$card_fee = ($gross_total_sc * $file_card_fee) / 100;
	$gross_total_sc += $card_fee;
	return [
		'grand_total_gross_sc' => ceil($grand_total_gross_sc),
		'gross_total_sc_without_tax' => ceil($gross_total_sc_without_tax),
		'tax' => ceil($tax),
		'service_fee' => ceil($service_fee),
		'card_fee' => ceil($card_fee),
		'gross_total_sc' => ceil($gross_total_sc)
	];
}
function resizeImageToMaxWidth($source_path, $destination_path, $max_width = 1024)
{
	// Get image info
	$image_info = getimagesize($source_path);
	if ($image_info === false) {
		return false;
	}
	$original_width = $image_info[0];
	$original_height = $image_info[1];
	$image_type = $image_info[2];
	// If image is already smaller than max width, just copy it
	if ($original_width <= $max_width) {
		return copy($source_path, $destination_path);
	}
	// Calculate new dimensions maintaining aspect ratio
	$ratio = $original_width / $original_height;
	$new_width = $max_width;
	$new_height = round($max_width / $ratio);
	// Create image resource based on file type
	switch ($image_type) {
		case IMAGETYPE_JPEG:
			$source_image = imagecreatefromjpeg($source_path);
			break;
		case IMAGETYPE_PNG:
			$source_image = imagecreatefrompng($source_path);
			break;
		case IMAGETYPE_GIF:
			$source_image = imagecreatefromgif($source_path);
			break;
		default:
			return false;
	}
	if ($source_image === false) {
		return false;
	}
	// Create new image
	$new_image = imagecreatetruecolor($new_width, $new_height);
	// Preserve transparency for PNG and GIF
	if ($image_type == IMAGETYPE_PNG || $image_type == IMAGETYPE_GIF) {
		imagealphablending($new_image, false);
		imagesavealpha($new_image, true);
		$transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
		imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
	}
	// Resize image
	imagecopyresampled($new_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
	// Save resized image
	$result = false;
	switch ($image_type) {
		case IMAGETYPE_JPEG:
			$result = imagejpeg($new_image, $destination_path, 90);
			break;
		case IMAGETYPE_PNG:
			$result = imagepng($new_image, $destination_path, 9);
			break;
		case IMAGETYPE_GIF:
			$result = imagegif($new_image, $destination_path);
			break;
	}
	// Clean up
	imagedestroy($source_image);
	imagedestroy($new_image);
	return $result;
}
?>