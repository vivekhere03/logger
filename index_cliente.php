<?php
session_start();
include 'dbconnection.php';
error_reporting(0);

if (empty($_SESSION['user_logado'])) {
    unset($_SESSION['user_logado']);
    header("Location: " . BASE_URL . "login");
}

$dados_logado = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM users WHERE username = '".$_SESSION['user_logado']."'"));

$curruser = $dados_logado["username"];



?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta http-equiv="Content-Language" content="en">
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
                        <?php $total_credit = 0; ?>
                       
                        <div class="card-body p-2">
                            <?php if (!empty($_SESSION['acao'])) { echo $_SESSION['acao'].'<hr>'; unset($_SESSION['acao']); } ?>
                            <table width="100%" id="example" class="table table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>GAME</th>
                                        <th>VERSION</th>
                                        <th>DOWNLOAD</th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                    <tr>
                                        <td>Free Fire</td>
                                        <td>3.0</td>
                                        <td><a href="donwloadapk.php">Download File 📁</a></td>
                                      
                                    </tr>
                                    <td
              >FreeFire Max </td>
             
             
          <td>Comming soon
         </td>
            
            <td>Comming Soon
            </td>
              
             
                                </tbody>
                            </table>
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