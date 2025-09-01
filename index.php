<?php
session_start();
// For local development, uncomment the line below and comment the line above
// include 'dbconnection_local.php';
include 'dbconnection.php';
if (empty($_SESSION['user_logado'])) {
	unset($_SESSION['user_logado']);
	header("Location: " . BASE_URL . "login");
}

$stmt = mysqli_prepare($con, "SELECT * FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_logado']);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$dados_logado = mysqli_fetch_assoc($result);
if ($dados_logado['type'] != "admin") {
	header("Location: " . BASE_URL . "login");
	exit();
}

if(isset($_GET['id']) && $dados_logado['type'] == "admin") {
	$adminid = trim($_GET['id']);
	$stmt = mysqli_prepare($con, "SELECT * FROM users WHERE username = ?");
	mysqli_stmt_bind_param($stmt, "s", $adminid);
	mysqli_stmt_execute($stmt);
	$result = mysqli_stmt_get_result($stmt);
	$check = mysqli_num_rows($result);
	if ($check >= 1) {
		$stmt = mysqli_prepare($con, "DELETE FROM users WHERE username = ?");
		mysqli_stmt_bind_param($stmt, "s", $adminid);
		$msg = mysqli_stmt_execute($stmt);
		if ($msg) {
			$_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert"> User: ' . htmlspecialchars($adminid) . ' deleted.</div>';	
		}
	}
}

if(isset($_GET['resetall'])) {
	$resetAll = trim($_GET['resetall']);
	if ($dados_logado['type'] == "admin") {
	    $stmt = mysqli_prepare($con, "UPDATE users SET uid = ?");
	    mysqli_stmt_bind_param($stmt, "s", $resetAll);
	    $msg = mysqli_stmt_execute($stmt);
    	if ($msg) {
    		$_SESSION['acao'] = '<div class="alert alert-success fade show" role="alert"> All users reseted.</div>';
    	}
    }
}

if (isset($_GET['resetuserid'])) {
	$resetUID = trim($_GET['resetuserid']);
	$stmt = mysqli_prepare($con, "UPDATE users SET uid = NULL WHERE username = ?");
	mysqli_stmt_bind_param($stmt, "s", $resetUID);
	$msg = mysqli_stmt_execute($stmt);
	if($msg) {
		$_SESSION['acao']='<div class="alert alert-success fade show" role="alert"> User: '.htmlspecialchars($resetUID).' reseted.</div>';
	}
}

if(isset($_GET['adddays']) && $dados_logado['type'] == "admin")
{
	$daysCount = intval($_GET['adddays']);
	$msg = mysqli_query($con,"UPDATE users SET `expired` = DATE_ADD(`expired` , INTERVAL 1 DAY) WHERE `expired` > CURDATE();");
	if ($msg)
	{
		$_SESSION['acao']='<div class="alert alert-success fade show" role="alert"> Added '.htmlspecialchars($daysCount).' day to all users.</div>';
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
		<title>All Users - New MOD</title>
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
						<div class="card-header"><h4><i class="fa fa-angle-right"></i> All Users </h4>
							<div class="btn-actions-pane-right actions-icon-btn">
								<?php if ($dados_logado['type'] == "admin") { ?>
								<a href="index.php?adddays=1" class="btn btn-danger">
									<i class="fa fa-plus-circle mr-1"></i> Add 1 Days
								</a>
								<a href="index.php?resetall=null" class="btn btn-warning">
									<i class="fa fa-asterisk mr-1"></i> Reset All Users
								</a>
								<?php } ?>
						     	<?php if ($dados_logado['type'] == "reseller") { ?>
								<a href="index.php?resetall=null" class="btn btn-warning">
									<i class="fa fa-asterisk mr-1"></i> Reset All Users
								</a>
								<?php } ?>
								<a href="add-users" class="btn btn-success">
									<i class="fa fa-plus-circle mr-1"></i> New User
								</a>
							</div>
                        </div>
                        <?php $cur_user = $_SESSION['user_logado']; $active_count = 0; $expired_count = 0;?>
						<div class="card-body p-2">
							<?php if(!empty($_SESSION['acao'])){ echo $_SESSION['acao'].'<hr>'; unset($_SESSION['acao']); }?>
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
									<?php if ($dados_logado['type'] == "master" || $dados_logado['type'] == "admin") { ?>
									<th>Type</th>
									<th>Credits</th>
									<?php } ?>
									<th>Action</th>
								</tr>
								</thead>
								<tbody>
									<?php 
									
									//$admins=$row['username'];
									$credits = $dados_logado['credits'];
									$query_users = mysqli_query($con,"SELECT * FROM users WHERE `type` != 'Admin' ORDER BY id ASC");
									/*
									$cur_user = $_SESSION['user_logado'];
									$query_users = mysqli_query($con,"SELECT * FROM tokens WHERE 'Vendedor' = $cur_user");
									print_r($query_users);
									*/
									while ($row = mysqli_fetch_assoc($query_users)) {
										if (($dados_logado['type'] == "reseller" && $row['type'] == "member" && strtolower($row['reseller']) == strtolower($_SESSION['user_logado'])) || $dados_logado['type'] == "admin") {
									?>
									<tr>
										<td><?php echo $row['id'];?></td>
										<td><?php echo $row['username'];?></td>
										<td><?php echo $row['password'];?></td>
										<td><?php if ($row['UID'] == NULL) {
    echo "0/1";
 } else {
    echo "1/1";
 }
										?>
										</td>
										<td><?php echo $row['registered'];?></td>
										<td><?php echo $row['expired'];?></td>
										<td>
										<?php if(strtotime(date("Y-m-d h:i")) <= strtotime($row['expired'])) {
										    if ($row['type'] == "cliente") {
										        $active_count++;
										    }
										    echo "Active";
										} else {
										    if ($row['type'] == "cliente") {
										        $expired_count++;
										    }
										    echo "Expired";
										} ?>
										</td>
										<td><?php echo $row['reseller'];?></td>
										<?php if ($dados_logado['type'] == "master" || $dados_logado['type'] == "admin") { ?>
										<td><?php echo $row['type'];?></td>
										<td><?php echo $row['credits']; ?></td>
										<?php } ?>
										<td>
										<?php if ($dados_logado['type'] == "master" || $dados_logado['type'] == "admin") { ?>
										<a href="edit-users.php?id=<?php echo $row['username']; ?>"> 
										    <button class="btn btn-dark btn-xs"><i class="fa fa-key"></i></button>
										</a>
										<a href="index.php?id=<?php echo $row['username']; ?>"> 
										    <button class="btn btn-danger btn-xs" onClick="return confirm('Do you really want to delete?');"><i class="fa fa-trash-alt"></i></button>
										</a>
										<?php } ?>
										<a href="index.php?resetuserid=<?php echo $row['username']; ?>"> 
										    <button class="btn btn-warning btn-xs"><i class="fa fa-asterisk"></i></button>
										</a>
										</td>
									</tr>
									<?php
										}
									}
									if ($dados_logado['type'] == "reseller") {
									?>
									<h2>Credit: <?php echo $credits; ?></h2>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="card-footer">
						    <h6>Total Members : <?php echo ($dados_logado['type'] == "admin") ? mysqli_num_rows(mysqli_query($con,"SELECT * FROM users WHERE `type`='cliente'")) : mysqli_num_rows(mysqli_query($con,"SELECT * FROM users WHERE `type`='cliente' AND `reseller`='$cur_user'"));?></h6>
						    <hr>
						    <h6>Total Active : <?php echo $active_count; ?></h6>
						    <hr>
						    <h6>Total Expired : <?php echo $expired_count; ?></h6>
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