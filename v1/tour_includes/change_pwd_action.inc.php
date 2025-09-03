<?php
if(is_post_back()) {
	$_SESSION['supplier_msg']="";
	$sql="select * from tbl_supplier_master where supplier_id = '".$_SESSION['sess_supp_id']."' and supplier_status='Active' ";
	$result = db_query($sql);
	if ($line = mysqli_fetch_array($result)) {
		if($line['supplier_password'] == $old_password) {
			$sql="update tbl_supplier_master set supplier_password = '$password' where supplier_id = '".$_SESSION['sess_supp_id']."'";
			db_query($sql);
			$_SESSION['supplier_msg'] .= '<li>'.'Password changed successfully'.'</li>';
			$_SESSION['supplier_msg_class']='sucess_msg';
			header("Location: change_pwd.php");
			exit;
		}else {
			$_SESSION['supplier_msg'] .= '<li>'.'Password is incorrect'.'</li>';
		}
	}else{
			$_SESSION['supplier_msg'] .= '<li>'.'Invalid admin session'.'</li>';
	}
}
?>