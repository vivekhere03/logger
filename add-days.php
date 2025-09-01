<?php
session_start();
include 'dbconnection.php';
if (empty($_SESSION['user_logado'])) {
	unset($_SESSION['user_logado']);
	header("Location: " . BASE_URL . "login");
}

$dados_logado = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE username = '".$_SESSION['user_logado']."'"));
if ($dados_logado['type'] != "reseller" && $dados_logado['type'] != "admin" && $dados_logado['type'] != "reseller") {
	header("Location: ".BASE_URL . "login");
	exit();
}

if ($dados_logado['type'] != "reseller" && $dados_logado['type'] != "admin" && $dados_logado['credits'] <= 0) {
	$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">Seus créditos acabaram. Adquira com Error404</div>';
	header("Location: " . BASE_URL . "index");
	exit();
}	

if($dados_logado['credits'] < 1) {
	$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">Seus créditos acabaram. Adquira com Error404</div>';
	header("Location: " . BASE_URL . "index");
	exit();
}				
// for updating user info    
if (isset($_POST['Submit'])) {
	
	$user = isset($_POST['usuario']) ? $_POST['usuario'] : '';
	$dias = isset($_POST['senha']) ? $_POST['senha'] : '';
	
	$userchecked = mysqli_real_escape_string($con, $user);
	$diaschecked = mysqli_real_escape_string($con, $dias);

	

	$check = mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE username = '$userchecked'"));
	if ($check == 0) {
		$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">User dont exists.</div>';
	} else {

	$query = $con->query("SELECT * FROM `users` WHERE `username` = '$userchecked'");
	$res = $query->fetch_assoc();
	$date2 = $res["expired"];
    $mod_date = strtotime($date2."+ $diaschecked days");
    $adicionardias = date("Y/m/d h:i",$mod_date);

		
					$query = mysqli_query($con, "UPDATE users SET `expired` = '$adicionardias' WHERE username = '$userchecked'");
					echo  "UPDATE users SET `expired` = '$adicionardias' WHERE username = '$userchecked'";
					$_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert">Added.</div>';

			      
		
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
    <title>Add User - New MOD</title>
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
					<?php if ($dados_logado['type'] == "reseller") { ?>
					<a href="index_reseller" class="btn btn-dark"><i class="fa fa-arrow-left"></i>  Back</a>
					<?php } else { ?>
						<a href="index" class="btn btn-dark"><i class="fa fa-arrow-left"></i>  Back</a>
					<?php } ?>
				</div>			
				</div>
				<div class="row">
                    <div class="col-md-12">
                        <div class="main-card mb-3 card">
                            <div class="card-header">Add user
                            </div>
                            <div class="card-body">
								<?php if(!empty($_SESSION['acao'])){ echo $_SESSION['acao'].'<hr>'; unset($_SESSION['acao']); }  ?>
								<form action="" method="POST">
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Usuario</label>
        								<input type="text" name="usuario" placeholder="Enter a username" class="form-control" required>
    								</div>
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Dias</label>
        								<input type="number" min="1" max="999" name="senha" placeholder="Enter days" class="form-control" required>
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
	<script>
	document.getElementById("fname").style.visibility = "visible";
		</script>
		<style>
.disclaimer { display: none; }
<style>
</body>
</html>
