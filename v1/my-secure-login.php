<?php
require_once('includes/midas.inc.php');

if (is_post_back()) {
    $login_id = $_POST['login_id'];
    $password = $_POST['password'];
        $sql = "SELECT * FROM mv_employee WHERE emp_username='" . $login_id . "' AND emp_status='Active'";
        $result = db_query($sql);

        if ($line_raw = mysqli_fetch_array($result)) {
            @extract($line_raw);
            if ($emp_password == $password || password_verify($password, $emp_password)) {
                //sampe sini sudah login ?
                $_SESSION['sess_agent_id'] = $emp_id;
                if ($emp_type == 'SuperAdmin') {
                    $_SESSION['sess_super_admin'] = $emp_type;
                    $_SESSION['sess_agent_type'] = 'Admin';
                } else {
                    $_SESSION['sess_super_admin'] = '';
                    $_SESSION['sess_agent_type'] = $emp_type;
                }
                $_SESSION['sess_agent_name'] = $emp_first_name;
                $_SESSION['sess_agent_last_name'] = $emp_last_name;
                if ($emp_official_email != '') {
                    $_SESSION['sess_agent_email'] = $emp_official_email;
                } else {
                    $_SESSION['sess_agent_email'] = $emp_personal_email;
                }
                $id_address = $_SERVER['REMOTE_ADDR'];
                if ($password == $emp_password) { // disini cek kalau sudah berhasil login 
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 8]);
                    db_query("UPDATE mv_employee SET emp_password='$hashed_password' WHERE emp_id='$emp_id'");
                }
                db_query("INSERT INTO mv_login_history SET fk_user_id='$emp_id', ip_address='$id_address', login_date_time=NOW()");
                $return_page = $_SESSION['sess_agent_redirect_url'];
                $_SESSION['sess_agent_redirect_url'] = "";
                if ($return_page == '') {
                    header("HTTP/1.1 301 Moved Permanently");
                    header("location: " . SITE_WS_PATH . "/" . AGENT_ADMIN_DIR . "/my-secure-index.php");
                    exit;
                } else {
                    header("HTTP/1.1 301 Moved Permanently");
                    header("location: " . $return_page);
                    exit;
                }
            } else {
                $_SESSION['agent_msg'] = 'Incorrect password.';
            }
        } else {
            $_SESSION['agent_msg'] = 'Incorrect username.';
        }
}

if (isset($_SESSION['sess_agent_id']) && $_SESSION['sess_agent_id'] != "") {
    header("location: " . SITE_WS_PATH . "/" . AGENT_ADMIN_DIR . "/my-secure-index.php");
    exit;
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_id = $_POST['login_id'] ?? '';
    $password = $_POST['password'] ?? '';
} else {
    $login_id = '';
    $password = '';
}

?>
<html lang="en">
<head>
    <base href="<?php echo SITE_WS_PATH; ?>" />
    <title>Login :: <?php echo SITE_NAME; ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <script>
        if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
</head>
<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center bgi-no-repeat">
	<script>
		var defaultThemeMode = "light";
		var themeMode;
		if (document.documentElement) {
			if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
				themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
			} else {
				if (localStorage.getItem("data-bs-theme") !== null) {
					themeMode = localStorage.getItem("data-bs-theme");
				} else {
					themeMode = defaultThemeMode;
				}
			}
			if (themeMode === "system") {
				themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
			}
			document.documentElement.setAttribute("data-bs-theme", themeMode);
		}
	</script>
	<div class="d-flex flex-column flex-root" id="kt_app_root">
		<style>
			body {
				background-image: url('assets/media/auth/bg4.jpg');
			}

			[data-bs-theme="dark"] body {
				background-image: url('assets/media/auth/bg4-dark.jpg');
			}
		</style>
		<div class="d-flex flex-column flex-column-fluid flex-lg-row">
			<div class="d-flex flex-center w-lg-50 pt-15 pt-lg-0 px-10">
				<div class="d-flex flex-center flex-lg-start flex-column">
					<a href="index.html" class="mb-7">
						<img alt="Logo" src="assets/media/logos/logo_w_big.png" class="mh-70px" />
					</a>
					<h2 class="text-white fw-normal m-0">Control panel for better management</h2>
				</div>
			</div>
			<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12 p-lg-20">
				<div class="bg-body d-flex flex-column align-items-stretch flex-center rounded-4 w-md-600px p-20">
					<div class="d-flex flex-center flex-column flex-column-fluid px-lg-10 pb-15 pb-lg-20">
						<form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="/v1" method="post" action="">
							<div class="text-center mb-11">
								<h1 class="text-gray-900 fw-bolder mb-3">Sign In</h1>
								<div class="text-gray-500 fw-semibold fs-6">Your Social Campaigns</div>
							</div>
							<div class="fv-row mb-8">
								<input type="text" placeholder="Username" name="login_id"  class="form-control bg-transparent" />
							</div>
							<div class="fv-row mb-3">
								<input type="password" placeholder="Password" name="password" autocomplete="off" class="form-control bg-transparent" />
							</div>
							<div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
								<div></div>
								<a href="authentication/layouts/creative/reset-password.html" class="link-primary">Forgot Password ?</a>
							</div>
							<div class="d-grid mb-10">
								<button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
									<span class="indicator-label">Sign In</span>
									<span class="indicator-progress">Please wait...
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
								</button>
							</div>
							<div class="text-gray-500 text-center fw-semibold fs-6">Not a Member yet?
								<a href="authentication/layouts/creative/sign-up.html" class="link-primary">Sign up</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script src="assets/plugins/global/plugins.bundle.js"></script>
	<script src="assets/js/scripts.bundle.js"></script>
</body>

</html>