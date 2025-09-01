<?php
session_start();
include 'dbconnection.php';
if (empty($_SESSION['user_logado'])) {
	unset($_SESSION['user_logado']);
	header("Location: " . BASE_URL . "login");
}

$dados_logado = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE username = '".$_SESSION['user_logado']."'"));
if ($dados_logado['type'] != "cliente" && $dados_logado['type'] != "admin" && $dados_logado['type'] != "reseller") {
	header("Location: " . BASE_URL . "login");
	exit();
}

// for updating user info    
if (isset($_POST['Submit'])) {
	
	$oldpass = isset($_POST['oldpass']) ? $_POST['oldpass'] : '';
	$newpass = isset($_POST['newpass']) ? $_POST['newpass'] : '';
	$confirmpass = isset($_POST['confirmpass']) ? $_POST['confirmpass'] : '';
	
	$usuario = $_SESSION['user_logado'];
	
	$oldchecked = mysqli_real_escape_string($con, $oldpass);
	$newchecked = mysqli_real_escape_string($con, $newpass);
	$confirmchecked = mysqli_real_escape_string($con, $confirmpass);
	
	$check = mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE username = '$usuario' AND password = '$oldchecked'"));
	if ($check < 1) {
		$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">Usuário incorreto.</div>';
	} else {
		if ($oldchecked == $newchecked) {
			$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">A nova senha não pode ser igual a antiga.</div>';
		} else if ($newchecked != $confirmchecked) {
			$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">Confirme sua senha corretamente.</div>';
		} else {
			$query = mysqli_query($con, "UPDATE users SET password = '$newchecked' WHERE username='$usuario'");
			if ($query) {
			    $_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert">Senha alterada com sucesso!</div>';
			} else {
			    $_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert">Senha alterada com failed!</div>';
			}
		}
	}
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="pt-BR">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Change Password - New MOD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="msapplication-tap-highlight" content="no">
	<link href="main.css" rel="stylesheet">
	<link href="assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
</head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        
		<?php include('header.php'); ?>
		<?php include('menu.php'); ?>

		<div class="app-main__outer">
            <div class="app-main__inner">
				<div class="row mb-3">
    				<div class="col-md-12">
    					<?php if ($dados_logado['type'] == "master") {?>
    					<a href="index_reseller" class="btn btn-dark"><i class="fa fa-arrow-left"></i>  Back</a>
    					<?php } else { ?>
    					<a href="index" class="btn btn-dark"><i class="fa fa-arrow-left"></i>  Back</a>
    					<?php } ?>
    				</div>
				</div>
				<div class="row">
                    <div class="col-md-12">
                        <div class="main-card mb-3 card">
                            <div class="card-header">Change Password
                            </div>
                            <div class="card-body">
								<?php if(!empty($_SESSION['acao'])) { echo $_SESSION['acao'].'<hr>'; unset($_SESSION['acao']); } ?>
								<form action="" method="POST">
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Old password</label>
        								<input type="text" name="oldpass" placeholder="Enter your old password" class="form-control" required>
    								</div>
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">New password</label>
        								<input type="text" name="newpass" placeholder="Enter new password" class="form-control" required>
    								</div>
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Confirm password</label>
        								<input type="text" name="confirmpass" placeholder="Confirm new password" class="form-control" required>
    								</div>
    								<div class="d-block text-right card-footer">
    								    <button type="submit" name="Submit" class="btn btn-success btn-lg">OK</button>
    								</div>
								</form>
							</div>
                        </div>
                    </div>
                </div>
			</div>
			<?php include('footer.php'); ?> 
        </div>
    </div>
    </div>
<script type="text/javascript" src="assets/scripts/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/assets/scripts/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/scripts/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="assets/scripts/bootstrap-datetimepicker.pt-BR.js"></script>
<script type="text/javascript">
    $('.form_datetime').datetimepicker({
		format: 'yyyy/mm/dd',
		minView: 2,
        language:  'pt-BR',
        todayBtn:  1,
		autoclose: 1
    });
</script>
<style>
.disclaimer { display: none; }
</style>
</body>
</html>
