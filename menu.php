<div class="app-main">
	<div class="app-sidebar sidebar-shadow bg-dark sidebar-text-light">
		<div class="app-header__logo">
			<div class="logo-src"></div>
			<div class="header__pane ml-auto">
				<div>
					<button type="button" class="hamburger close-sidebar-btn hamburger--elastic" data-class="closed-sidebar">
						<span class="hamburger-box">
							<span class="hamburger-inner"></span>
						</span>
					</button>
				</div>
			</div>
		</div>
		<div class="app-header__mobile-menu">
			<div>
				<button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
					<span class="hamburger-box">
						<span class="hamburger-inner"></span>
					</span>
				</button>
			</div>
		</div>
		<div class="app-header__menu">
			<span>
				<button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
					<span class="btn-icon-wrapper">
						<i class="fa fa-ellipsis-v fa-w-6"></i>
					</span>
				</button>
			</span>
		</div>    
		<div class="scrollbar-sidebar">
			<div class="app-sidebar__inner">
				<ul class="vertical-nav-menu">
					<li class="app-sidebar__heading">Navigation </li> 
					<li>
						<?php if ($dados_logado['type'] == "master") { ?>
						<a href="index_reseller" <?php if(strpos($_SERVER["REQUEST_URI"], 'index_reseller')){echo 'class="mm-active"';} ?>>
							<i class="metismenu-icon pe-7s-users"></i>
							Manage users
						</a>
						<?php }else{ ?>
						<a href="index" <?php if(strpos($_SERVER["REQUEST_URI"], 'index')){echo 'class="mm-active"';} ?>>
							<i class="metismenu-icon pe-7s-users"></i>
							Manage users
						</a>
						<?php } ?>
					</li>
					<li>
						<a href="mudar-senha" <?php if(strpos($_SERVER["REQUEST_URI"], 'mudar-senha')){echo 'class="mm-active"';} ?>>
							<i class="metismenu-icon pe-7s-key"></i>
							Change Password
						</a>
					</li>
					
					<li>
					    					<a href="add-users">
							 <i class="metismenu-icon pe-7s-users"></i>
							Add Users
						</a>
						</li>
						
					<li>
					<a href="download-ff">
							 <i class="metismenu-icon pe-7s-download"></i>
							Download Apk
						</a>
						</li>
						
					<li>
					    			<a href="donwloadapk">
							 <i class="metismenu-icon pe-7s-download"></i>
							Download
						</a>
						</li>
						
					<li>
					<a href="logout">
							 <i class="metismenu-icon pe-7s-back"></i>
							Exit
						</a>
					</li>
					<li class="app-sidebar__heading"><span>Welcome! <?php echo $_SESSION['user_logado']; ?></span></li>
				</ul>
			</div>
		</div>
	</div>