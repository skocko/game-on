<?php
function go_mail() {
	global $wpdb;
	$dir = plugin_dir_url(__FILE__);
	add_submenu_page( 'game-on-options.php', 'Email', 'Email', 'manage_options', 'go_mail', 'go_mail_menu');
}

function go_mail_menu() {
	global $wpdb;
	if (!current_user_can('manage_options'))  { 
		wp_die( __('You do not have sufficient permissions to access this page.') );
	} 
	else{
		if(isset($_POST['go_mail'])){
			$email = $_POST['go_mail'];
			update_option('go_admin_email', $email);
			} else {
				$email = get_option('go_admin_email','');
				}
		echo 'Recipient Email: <form action="" method="post">
        <textarea name="go_mail">'.$email.'</textarea>
        <input type="submit" value="Submit"/>
        </form> <br />
		Paste this shortcode: <input type="text" value="[go_upload]"/>
		This will display an upload box for files and a text-box for additional comments the user has. It will send an Email with the file as an attachment. The message will be from \"no-replay@go.net\" with the first and last name of the student. The subject will be the page title where this was sent from. The message will contain the addition comments and the student\'s login.';
		}
}

//Shortcode for Email input
add_shortcode('go_upload','go_file_input');
function go_file_input(){
	global $wpdb;
	if(isset($_FILES['go_attachment'])){
	$user_info = get_userdata(get_current_user_id());
    $username = $user_info->user_login;
    $first_name = $user_info->first_name;
    $last_name = $user_info->last_name;
	$user_id = $user_info->ID;
	$to = get_option('go_admin_email','');
	require("mail/class.phpmailer.php");
$mail = new PHPMailer();
$mail->From     = "no-reply@go.net";
$mail->FromName = $first_name.' '.$last_name;
$mail->AddAddress($to);
$mail->Subject  = get_the_title($ID).' - '.$first_name.' '.$last_name;
$mail->Body     = 'User login: '.$username.'
Uploader comments: '.$_POST['go_attachment_com'];
$mail->WordWrap = 50;
 $mail->AddAttachment($_FILES['go_attachment']['tmp_name'],
                         $_FILES['go_attachment']['name']);
if(!$mail->Send()) {
  echo 'Message was not sent.';
  echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
  echo 'Message has been sent.';
}
	}
return('<form action="" method="post" enctype="multipart/form-data">
<input type="file" name="go_attachment"/><br/>
Comments:<br />
<textarea name="go_attachment_com"></textarea><br />
<input type="submit" value="Submit"/>
</form>');
	}
?>