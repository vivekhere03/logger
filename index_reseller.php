<?php
session_start();
include 'dbconnection.php';
error_reporting(0);

if (empty($_SESSION['user_logado'])) {
	unset($_SESSION['user_logado']);
	header("Location: " . BASE_URL . "login");
}

$dados_logado = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE username = '".$_SESSION['user_logado']."'"));
$creditos = $dados_logado['credits'];
$curruser = $dados_logado["username"];
if ($dados_logado['type'] != "reseller") {
	header("Location: " . BASE_URL . "login");
	exit();
}

if (isset($_GET['id'])) {
	$adminid = $_GET['id'];
	$msg = mysqli_query($con, "DELETE FROM users WHERE username = '$adminid'");
	if ($msg) {
		$_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert"> Usuário: ' . $adminid . ' Deletado.</div>';	
	}
}

if (isset($_GET['resetuserid'])) {
	$resetUID = $_GET['resetuserid'];
	$msg = mysqli_query($con, "UPDATE users SET uid = NULL WHERE username = '$resetUID'");
	if ($msg) {
		$_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert"> Usuário: ' . $resetUID . ' Resetado.</div>';
	}
}

if (isset($_GET['resetall'])) {
	$resetAll = $_GET['resetall'];
	$msg = mysqli_query($con,"UPDATE users SET uid=$resetAll");
	if ($msg) {
		$_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert"> Todos os Usuários foram resetados.</div>';
	}
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta http-equiv="Content-Language" content="en">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>All Users </title>
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
			<div class="row">
				<div class="col-md-12">
					<div class="main-card mb-3 card">
					    <?php $total_credit = 0; ?>
						<div class="card-header"><h4><i class="fa fa-angle-right"></i> Credit: <?php echo $creditos; ?></h4>
						    <div class="btn-actions-pane-right actions-icon-btn">
						    <?php if ($dados_logado['type'] == "master") { ?>
							    <a href="index_reseller.php?resetall=null" class="btn btn-warning">
									<i class="fa fa-asterisk mr-1"></i> Reset All Users
								</a>
						    <?php } ?>
								<a href="index_reseller.php?resetall=null" class="btn btn-warning">
									<i class="fa fa-asterisk mr-1"></i> Reset All User
								</a>
								<a href="add-users" class="btn btn-success">
									<i class="fa fa-plus-circle mr-1"></i> New User
								</a>
							</div>
                        </div>
						<div class="card-body p-2">
							<?php if (!empty($_SESSION['acao'])) { echo $_SESSION['acao'].'<hr>'; unset($_SESSION['acao']); } ?>
							<table width="100%" id="example" class="table table-hover">
								<thead class="thead-dark">
    								<tr>
    									<th>ID</th>
    									<th>User</th>
    									<th>Password</th>
    									<th>Device</th>
    									<th>Start Date</th>
    									<th>End Date</th>
    									<th>Status</th>
    									<th>Reseller</th>
    									<th>Action</th>
    								</tr>
								</thead>
								<tbody>
									<?php 
									
									//$admins=$row['username'];
									$query_users = mysqli_query($con,"SELECT * FROM users WHERE `type` = 'cliente' AND `reseller` = '$curruser' ORDER BY id ASC");
									while ($row = mysqli_fetch_assoc($query_users)) {
									    
									?>
									<tr>
										<td><?php echo $row['id']; ?></td>
										<td><?php echo $row['username']; ?></td>
										<td><?php echo $row['password']; ?></td>
										<td>
										<?php if ($row['UID'] == NULL){
											echo "0/1"; 
										} else {
											echo "1/1";
										}?>
										</td>
											<td><?php echo $row['registered']; ?></td>		
										<td><?php echo $row['expired']; ?></td>
										<td><?php if(strtotime(date("Y-m-d")) <= strtotime($row['expired'])) {
    										    echo "Active";
    										} else {
    										    echo "Expired";
    										} ?>
										</td>
										
										<td><?php echo $row['reseller']; ?></td>
										<td>
    										<a href="edit-users.php?id=<?php echo $row['username']; ?>">
    										<button class="btn btn-dark btn-xs"><i class="fa fa-key"></i></button></a>
    										<a href="index_reseller.php?resetuserid=<?php echo $row['username']; ?>">
    										    <button class="btn btn-warning btn-xs"><i class="fa fa-asterisk"></i></button>
    										</a>
    										<a href="index.php?id=<?php echo $row['username']; ?>">
    										<?php if ($dados_logado['type'] == "reseller") { ?>
    										<a href="index_reseller.php?id=<?php echo $row['username']; ?>">
    										<?php } else { ?>
    										<a href="index.php?id=<?php echo $row['username']; ?>">
    										<?php } ?>										
			</a>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="card-footer">
						    <h4>Total Usuario: <?php $query_users = mysqli_query($con,"SELECT * FROM users WHERE `type`='cliente' AND `reseller` = '$curruser'"); echo mysqli_num_rows($query_users);?></h4>
						</div>
					</div>
				</div>
			</div>
        </div>
        
		<?php include('footer.php'); ?>
		
	</div>
	<style>
.disclaimer { display: none; }
</style>
</body>
</html>