<?php
session_start();
include 'dbconnection.php';
if (empty($_SESSION['user_logado'])) {
	unset($_SESSION['user_logado']);
	header("Location: " . BASE_URL . "login");
}

$dados_logado = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE username = '".$_SESSION['user_logado']."'"));
if ($dados_logado['type'] != "reseller" && $dados_logado['type'] != "admin") {
	header("Location: " . BASE_URL . "login");
	exit();
}

$ID = trim(isset($_GET['id']) ? $_GET['id'] : '');
$stmt = mysqli_prepare($con, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $ID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$dados_editar = mysqli_fetch_assoc($result);
$check = mysqli_num_rows($result);

if($check < 1){
	header("Location: " . BASE_URL);
}

// for updating user info    
if (isset($_POST['Submit'])) {
	$user = isset($_POST['usuario']) ? $_POST['usuario'] : '';
	$pass = isset($_POST['senha']) ? $_POST['senha'] : '';
	$devices = isset($_POST['devices']) ? $_POST['devices'] : '';
	$endate = isset($_POST['endate']) ? $_POST['endate'] : '';
	$cargo = isset($_POST['cargo']) ? $_POST['cargo'] : '';
	$credit = isset($_POST['credit']) ? $_POST['credit'] : '';
	$userchecked = mysqli_real_escape_string($con, $user);
	$passchecked = mysqli_real_escape_string($con, $pass);
	$deviceschecked = mysqli_real_escape_string($con, $devices);
	$endatechecked = mysqli_real_escape_string($con, $endate);
	$cargochecked = mysqli_real_escape_string($con, $cargo);
	$creditchecked = mysqli_real_escape_string($con, $credit);
	
	if ($deviceschecked > 1) {
		$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">Máximo permitido 1 device!</div>';
	} else {
	  if ($deviceschecked == 1) {
	  	if($endatechecked == "" || $endatechecked == "00-00-00" || $endatechecked == NULL) {
	  		$endatechecked = $dados_editar['expired'];
	  	}
		$query = mysqli_query($con, "UPDATE users SET username = '$userchecked', password = '$passchecked', uid = NULL, expired = '$endatechecked', type='$cargochecked', credits='$creditchecked' WHERE username = '$ID'");
		if ($query) {
		    $_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert">' . $userchecked . ' Atualizado. Válido até : ' . $endatechecked . '</div>';

		} else {
		    //$_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">Failed!</div>';
		    $_SESSION['acao'] = "UPDATE users SET username = '$userchecked', password = '$passchecked', uid = NULL, expired = '$endatechecked', type='$cargochecked', credit='$creditchecked' WHERE username = '$ID'";
		}
	}
  }
}

if (isset($_POST['Reset'])) {
	$user = isset($_POST['usuario']) ? $_POST['usuario'] : '';
	$devices = isset($_POST['devices']) ? $_POST['devices'] : '';
	$userchecked = mysqli_real_escape_string($con, $user);
	$deviceschecked = mysqli_real_escape_string($con, $devices);
	
	if ($deviceschecked == 1) {
		$query = mysqli_query($con, "UPDATE users SET uid = NULL, uid = NULL WHERE username = '$ID'");
		if ($query) {
		    $_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert">' . $userchecked . ' Atualizado, Device Resetado.</div>';
		} else {
		    $_SESSION['acao'] = '<div class="alert alert-danger fade show" role="alert">Failed!</div>';
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
    <title>Editar <?php echo $dados_editar['username']; ?> - New MOD</title>
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
                            <div class="card-header">Edit #<?php echo $dados_editar['id']; ?> - <?php echo $dados_editar['username'];?>
                            </div>
                            <div class="card-body">
								<?php if(!empty($_SESSION['acao'])) { echo $_SESSION['acao'].'<hr>'; unset($_SESSION['acao']); }  ?>
								<form action="" method="POST">
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Username</label>
        								<input type="text" name="usuario" value="<?php echo $dados_editar['username']; ?>" class="form-control" required>
    								</div>
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Password</label>
        								<input type="text" name="senha" value="<?php echo $dados_editar['password']; ?>" class="form-control" required>
    								</div>
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Devices</label>
        								<input type="number" max="1" min="1" name="devices" value="<?php echo "1";  ?>" class="form-control" required>
    								</div>
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Start Date</label>
        								<input size="16" type="text" name="startdate" value="<?php echo $dados_editar['registered']; ?>" readonly class="form-control">
    								</div>
    								
    								<?php if($dados_logado['type'] != "reseller") { echo "<div class=\"position-relative form-group\">
        								<label for=\"exampleEmail\" class=\"\">End Date</label>
        								<input size=\"16\" type=\"date\" name=\"endate\"  class=\"form-control form_datetime\" required>
    								</div>"; } ?>
    								
    							
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Cargo</label>
        								<select name="cargo" class="form-control">
            								<?php if($dados_logado['type'] == "reseller") { ?>
            								<option value="cliente">Member</option>
        								</select>
    								</div>


    								
    								
    								
    								<?php } else if ($dados_logado['type'] == "admin") { ?>
    								<option value="cliente">Member</option>
    								<option value="reseller">Vendedor</option>
    								<option value="admin">Admin</option>
    								</select>
    								</div>
    								
    								<div class="position-relative form-group">
        								<label for="exampleEmail" class="">Credits</label>
        								<input type="number" min="0" name="credit" value=<?php echo ($dados_editar['credits'] == '0') ? '10' : $dados_editar['credits']; ?> class="form-control" required>
    								</div>
    								
    								<?php } else { ?>
    								<option value="cliente">Member</option>
    								</select>
    								</div>
    								
    								<?php } ?>
    								
    								<div class="d-block text-right card-footer">
    								<button type="submit" name="Submit" class="btn btn-success btn-lg">Edit User</button>
    								<button type="submit" name="Reset" class="btn btn-warning btn-lg">Reset Device</button>
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
