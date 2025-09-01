<?php
session_start();
include 'dbconnection.php';
if (empty($_SESSION['user_logado'])) {
	unset($_SESSION['user_logado']);
	header("Location: " . BASE_URL . "login");
}

$dados_logado = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE username = '".$_SESSION['user_logado']."'"));
if ($dados_logado['type'] != "master" && $dados_logado['type'] != "admin" && $dados_logado['type'] != "reseller") {
	header("Location: ".BASE_URL . "login");
	exit();
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="pt-BR">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Download Mod - New MOD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="msapplication-tap-highlight" content="no">
	<link href="main.css" rel="stylesheet">
</head>
<body>
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">
        
		<?php include('header.php'); ?>

		<?php include('menu.php'); ?>

		<div class="app-main__outer">
            <div class="app-main__inner">
				<div class="row mb-3">
				<div class="col-md-12">
					<?php if ($dados_logado['type'] == "master") { ?>
					<a href="index_reseller" class="btn btn-dark"><i class="fa fa-arrow-left"></i>  Back</a>
					<?php } else { ?>
					<a href="index" class="btn btn-dark"><i class="fa fa-arrow-left"></i>  Back</a>
					<?php } ?>
				</div>			
				</div>
				<div class="row">
                    <div class="col-md-12">
                        <div class="main-card mb-3 card">
                            <div class="card-body">
								<h2>Versions Disponible</h2>
								<br></br>
								<?php $ffs = scandir("ff");
									if (isset($ffs) && count($ffs) > 0) {
										foreach ($ffs as $key => $value) {
											if ($value != "." && $value != "..") {?>
												<left>
													<a href="<?php echo "ff/".$value; ?>" class="btn btn-info"><?php echo $value; ?></a>
													<hr></hr>
												</left>
								<?php 		}
										}
									}?>
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
<style>
.disclaimer { display: none; }
<style>
</body>
</html>
