<?php
if(is_post_back()) {
	$sql="select * from tbl_supplier_master where supplier_username='".$_POST['login_id']."' and supplier_status='Active'";
	if(!strstr($_SERVER['PHP_SELF'],'/'.SUPPLIER_DIR.'/')){
			$sql.=" and supplier_type='Admin' ";
	}
	$result = db_query($sql);
	if ($line_raw = mysqli_fetch_assoc($result)) {
		@extract($line_raw);
		if ($supplier_password==$_POST['password']) {
			$_SESSION['sess_supp_id'] = $supplier_id;
			$_SESSION['sess_supp_type'] = $supplier_type;
			if($return_page=='') {
				header("location: index.php");
				exit;
			} else {
				header("location: ".$return_page);
				exit;
			}
		} else {
			$_SESSION['supplier_msg'] = '<li>'.INCORRECT_USERNAME_PASSWORD.'</li>';
		}
	} else {
		$_SESSION['supplier_msg'] = '<li>'.INCORRECT_USERNAME_PASSWORD.'</li>';
	}
	header("location: my-secure-login.php");
	exit;
}
?>