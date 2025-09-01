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
	$date = date("Y/m/d h:i");
	$user = isset($_POST['usuario']) ? $_POST['usuario'] : '';
	$pass = isset($_POST['senha']) ? $_POST['senha'] : '';
	$devices = isset($_POST['devices']) ? $_POST['devices'] : '';
	$endate = isset($_POST['endate']) ? $_POST['endate'] : '';
	$cargo = isset($_POST['cargo']) ? $_POST['cargo'] : '';
	$vendedor = $_SESSION['user_logado'];
	
		$credit = isset($_POST['credit']) ? $_POST['credit'] : '';
		$creditchecked = mysqli_real_escape_string($con, $credit);
	
	$userchecked = mysqli_real_escape_string($con, $user);
	$passchecked = mysqli_real_escape_string($con, $pass);
	$deviceschecked = mysqli_real_escape_string($con, $devices);
	$endatechecked = mysqli_real_escape_string($con, $endate);
	$cargochecked = mysqli_real_escape_string($con, $cargo);
	$vendedorchecked = mysqli_real_escape_string($con, $vendedor);

	$check = mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE username = '$userchecked'"));
	if ($check > 0) {
		$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">User in use, try another.</div>';
	} else {
		if($deviceschecked > 1) {
			 $_SESSION['acao']= '<div class="alert alert-danger fade show" role="alert">Maximum allowed 1 device!</div>';
		} else if ($deviceschecked < 1) {
			 $_SESSION['acao']= '<div class="alert alert-danger fade show" role="alert">Minimum allowed 1 device!</div>';
		} else {
			if ($deviceschecked == 1) {
				if ($dados_logado['type'] == "reseller") {
					$query = mysqli_query($con, "INSERT INTO `users` (`username`,`password`,`registered`,`expired`,`UID`,`reseller`,`type`,`credits`) VALUES ('$userchecked','$passchecked','$date','$endatechecked','$vendedorchecked','cliente','0')");
					$credits = $dados_logado['credits'] - 1;
					if ($query) {
						$msg = mysqli_query($con, "UPDATE users SET credits = $credits WHERE username = '" . $_SESSION['user_logado'] . "'");
						if ($msg) {
							$_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert">' . $userchecked . ' Added. validity: ' . $endatechecked . '</div>';
						}
					}
					//echo "INSERT INTO `users` (`username`,`password`,`registered`,`expired`,`reseller`,`type`,`credits`) VALUES ('$userchecked','$passchecked','$date','$endatechecked','$vendedorchecked','cliente','0')";
					header("Location: ".BASE_URL . "index_reseller");
					exit();
				} else if ($dados_logado['type'] == "admin") {
				    $query = mysqli_query($con, "INSERT INTO `users` (`username`,`password`,`registered`,`expired`,`UID`,`reseller`,`type`, `credits`) VALUES ('$userchecked','$passchecked','$date','$endatechecked','NULL','$vendedorchecked','$cargochecked', '$creditchecked')");
					$credits = $dados_logado['credits'] - 0;
					if ($query) {
						$msg = mysqli_query($con, "UPDATE users SET credit = $credits WHERE username = '" . $_SESSION['user_logado'] . "'");
						if ($msg) {
							$_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert">' . $userchecked . ' Added. validity: ' . $endatechecked . '</div>';
						}
					}
					header("Location: " . BASE_URL . "index");
					exit();
				}  else {$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">You are not admin or reseller!</div>';
					header("Location: " . BASE_URL . "index");
					exit();
				}
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
    <title>Add User - New MOD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="msapplication-tap-highlight" content="no">
	<link href="main.css" rel="stylesheet">
	<link href="assets/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
		
</head>
<body>

  <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">       
		<div class="app-main__outer">
            <div class="app-main__inner">
				<div class="row mb-3">
				<div class="col-md-12">
					<?php if ($dados_logado['type'] == "reseller") { ?>
					<a href="index_reseller" class="btn btn-dark"><i class="fa fa-arrow-left"></i></a>
					<?php } else { ?>
						<a href="index" class="btn btn-dark"><i class="fa fa-arrow-left"></i></a>
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
        								<label for="exampleEmail" class="">Username</label>
        								<input type="text" name="usuario" placeholder="Enter a username" class="form-control" required>
    								</div>
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Password</label>
        								<input type="text" name="senha" placeholder="Enter a password" class="form-control" required>
    								</div>
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Device</label>
        								<input type="number" max="1" min="1" name="devices" value="1" class="form-control" required>
    								</div>
    								
    								<!--
    								<div style="display:none;">
    								<div class="position-relative form-group">
    								<label for="exampleEmail" class="">Data de início</label>
    								<input size="16" type="text" name="startdate" value="<?php echo date('Y-m-d'); ?>" readonly class="form-control">
    								</div>
    								
    								<div class="position-relative form-group">
    								<label for="exampleEmail" class="">Data de vencimento</label>
    								<input size="16" type="date" name="endate" value="<?php echo date('Y-m-d'); ?>" class="form-control form_datetime" required>
    								</div>
    								</div>
    								-->
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Start Date</label>
        								<input size="16" type="text" name="startdate" value="<?php echo date("Y-m-d"); ?>" readonly class="form-control">
    								</div>
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Select Days</label>
        								<select class="form-control" name="endate">
    								    <?php if ($dados_logado['type'] == "reseller") { ?>
    								        <option value="<?php echo Date('Y-m-d', strtotime('+30 days')); 
    								        ?>" selected>30 days</option>
								        <?php } else { ?>
							                <option value="<?php echo Date('Y-m-d', strtotime('+7 day')); ?>" >7 day</option>
            								<option value="<?php echo Date('Y-m-d', strtotime('+30 days')); ?>"
            	selected>30 days</option>
            							<?php } ?>
        								</select>
    								</div>
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Vendedor</label>
        								<input type="text" value="<?php echo $_SESSION['user_logado']; ?>" readonly class="form-control" required>
    								</div>
    								
    									<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Type</label>
        								<select name="cargo" class="form-control">
            								<?php if ($dados_logado['type'] == "reseller") { ?>
            								<option value="cliente">Member</option>
        								</select>
    								</div>
    								
    								
    								
    								<?php } else if ($dados_logado['type'] == "admin") { ?>
        								<option value="cliente">Member</option>
        								<option value="reseller">Vendedor</option>
        								<option value="admin">Admin</option>
    								</select>
    								</div>
    								
    								<?php } else { ?>
    								<option value="member">Member</option>
    								</select>
    								</div>
    								<?php } ?>
	<?php if ($dados_logado['type'] == "admin") { ?>


    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Credit</label>
        								<input type="number" min="0" max="999999" name="credit" value="10" class="form-control" required>
    								</div>
    								<?php } ?>
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
</style>
</body>
</html>
