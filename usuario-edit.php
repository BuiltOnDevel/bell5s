<!DOCTYPE html>
<html lang="en">

<?php
header("Access-Control-Allow-Origin: *");
include("./json/default.php");
include("./php/conexao.php");
include("./php/classes.php");
include("validate-login.php");

$idp  = $_REQUEST['id']; // id usuario
$unp = $_REQUEST['un']; // id da unidade
$cmd = $_REQUEST['cmd']; // acao a ser tomada.


class Registro{
	var $id = 0;
	var $nome = '';
	var $login = '';
	var $senha = '';
	var $email = '';
	var $idCliente = 0;
	var $flAtivo = 'S';
	var $flUsu = 'S';
}

$reg = new Registro();
$reg->id = $idp;


/*============================================================================
==========================================================================================*/

if( $cmd == 'newusuario')
{
	try {
		
 $usuario_nome = $_POST['usuarioNome'];
 $login_nome = $_POST['usuarioLogin'];
 $email_nome = $_POST['usuarioEmail'];
 $senha_nome = $_POST['usuarioSenha'];
 $cliente_id = $_POST['selCliente'];
 $reg->flAtivo = $_POST['usuarioAtivo'];
 $reg->flUsu   = $_POST['usuarioUsu'];

		$sql = "SELECT nextval('bel_usuario_seq') as id";
//print( $sql ); exit;

    $result = $conn->query( $sql );
    $row   = $result->fetchAll();
    $td_cor = "";
    foreach($row as $r){
    	
    	$reg->id   = $r['id'];


    }

//------------------------------------------


 	
  $sql = "insert into bel_usuario
         (id_usuario, id_cliente
         , nome, login
         , email, senha) 
         values(
         " . $reg->id . ", $cliente_id
         ,'$usuario_nome','$login_nome'
         ,'$email_nome','$senha_nome')";
         
	
			    $result = $conn->query( $sql );
	
	}
		catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
    }
    
}

/*============================================================================
==========================================================================================*/

if( $cmd == 'updusuario')
{
	$reg->id = $_POST['usuarioid'];

 $usuario_nome = $_POST['usuarioNome'];
 $login_nome = $_POST['usuarioLogin'];
 $email_nome = $_POST['usuarioEmail'];
 $senha_nome = $_POST['usuarioSenha'];
 $cliente_id = $_POST['selCliente'];	
 $reg->flAtivo = $_POST['usuarioAtivo'];
 $reg->flUsu   = $_POST['usuarioUsu'];
	
	
	try {
	
			$sql = "update bel_usuario set
			    nome   = '$usuario_nome'
			  , login  = '$login_nome'
			  , email  = '$email_nome'
			  , fl_ativo = '" . $reg->flAtivo . "'
			  , fl_usu   = '" . $reg->flUsu . "'
			  , id_cliente = $cliente_id
			where id_usuario = " . $reg->id;
			
	
    $result = $conn->query( $sql );
	
	}
	catch(PDOException $e) {
    $retorno->log .= "Error: " . $e->getMessage();
  }

}


/*============================================================================
==========================================================================================*/

if( $cmd == 'newunidade')
{
	try {
	
			$sql = "insert into bel_usuario_unidade(
	  id_usuario_unidade, id_cliente, id_usuario, id_unidade )
	select nextval( 'bel_usuario_unidade_seq')
	, id_cliente, id_usuario, ".  $unp . "
	from bel_usuario
	where id_usuario = " . $idp;
	
			    $result = $conn->query( $sql );
	
	}
	catch(PDOException $e) {
    $retorno->log .= "Error: " . $e->getMessage();
  }
    
}

/*============================================================================
==========================================================================================*/



if( $cmd == 'delunidade')
{
	try {
	
			$sql = "delete from bel_usuario_unidade
	  where id_unidade = ".  $unp . " and  id_usuario = " . $idp;
	
			    $result = $conn->query( $sql );
	
	}
		catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
    }
    
}



/*============================================================================
==========================================================================================*/








	try {

		$sql = "SELECT u.nome
                , u.email
                , u.login
                , u.senha
                , u.id_cliente
                , u.id_usuario
                , u.fl_ativo
                , u.fl_usu
            FROM bel_usuario u 
            WHERE u.id_usuario = " . $reg->id . "
            ORDER BY u.nome DESC";
//print( $sql ); exit;

		    $result = $conn->query( $sql );
        $row   = $result->fetchAll();
        $td_cor = "";
        foreach($row as $r){
        	
        	$reg->id   = $r['id_usuario'];
        	$reg->nome = $r['nome'];
        	$reg->login = $r['login'];
        	$reg->email = $r['email'];
        	$reg->senha = $r['senha'];
        	$reg->idCliente = $r['id_cliente'];
        	$reg->flAtivo = $r['fl_ativo'];
        	$reg->flUsu = $r['fl_usu'];

        }



	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
    }

/*
LISTA DE CLIENTES
==============================================================*/

try{
	$sql = "select id_cliente, nome from bel_cliente where fl_ativo = 'S' order by nome";
  $result = $conn->query($sql);
  $row = $result->fetchAll();

  $option = "";

  foreach($row as $r){
  	if( $r['id_cliente'] == $reg->idCliente )
      $option .= "<option value=".$r['id_cliente']." selected>".$r['nome']."</option>";

  	if( $r['id_cliente'] != $reg->idCliente )
      $option .= "<option value=".$r['id_cliente'].">".$r['nome']."</option>";

  }	
}
catch(PDOException $e){
    $retorno->log .= "Error: " . $e->getMessage();
}





/*
LISTA DE UNIDADES DISPONIVEIS
==============================================================*/

try{
	$sql = "select nome, id_unidade
from bel_unidade u
where id_cliente = " . $reg->idCliente . " and fl_ativo = 'S' 
and not exists ( select 1 from bel_usuario_unidade d 
       where d.id_unidade = u.id_unidade
         and d.id_usuario = " . $reg->id . " )
order by nome";

// print( $sql ); exit; 
	
  $result = $conn->query($sql);
  $row = $result->fetchAll();

  $lista1 = "";

  foreach($row as $r){
  	$lista1 .= "<p><a href='?cmd=newunidade&id=".$reg->id."&un=" . $r['id_unidade'] . "'>" . $r['nome'] . "</a>";
  }	
}
catch(PDOException $e){
    $retorno->log .= "Error: " . $e->getMessage();
}



/*
LISTA DE UNIDADES SELECIONADAS
==============================================================*/

try{
	$sql = "select nome, id_unidade
from bel_unidade u
where id_cliente = $reg->idCliente and fl_ativo = 'S' 
and  exists ( select 1 from bel_usuario_unidade d 
       where d.id_unidade = u.id_unidade
         and d.id_usuario = $reg->id )
order by nome";
	
  $result = $conn->query($sql);
  $row = $result->fetchAll();

  $lista2 = "";

  foreach($row as $r){
  	$lista2 .= "<p><a href='?cmd=delunidade&id=".$reg->id."&un=" . $r['id_unidade'] . "'>" . $r['nome'] . "</a>";
  }	
}
catch(PDOException $e){
    $retorno->log .= "Error: " . $e->getMessage();
}





/*========================================================================
                    RETORNO AO CLIENTE
========================================================================*/

$viewOptionAtivo   = "<option value = 'S' " . ( $reg->flAtivo == 'S' ? 'selected' : '') .  " >SIM</option>";
$viewOptionAtivo  .= "<option value = 'N' " . ( $reg->flAtivo == 'N' ? 'selected' : '') .  " >NAO</option>";

$viewOptionUsu   = "<option value = 'S' " . ( $reg->flUsu == 'S' ? 'selected' : '') .  " >SIM</option>";
$viewOptionUsu  .= "<option value = 'N' " . ( $reg->flUsu == 'N' ? 'selected' : '') .  " >NAO</option>";


#$retorno->log = '';
//print( json_encode( $retorno ) );

?>

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>SB Admin 2 - Tables</title>

  <!-- Custom fonts for this template -->
  <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template -->
  <link href="css/sb-admin-2.min.css" rel="stylesheet">

  <!-- Custom styles for this page -->
  <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
        <div class="sidebar-brand-text mx-3">
            <img src="img/logo.png" alter="logo" height="55" width="75">
        </div>
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="index.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
      <div class="sidebar-heading">
        Interface
      </div>

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-fw fa-cog"></i>
          <span>Cadastros</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="register-terminais.php">Terminais</a>
            <!--<a class="collapse-item" href="register-color.php">Cor do Terminal</a> -->
            <a class="collapse-item" href="register-client.php">Cliente</a>
            <a class="collapse-item" href="register-unit.php">Unidade</a>
            <a class="collapse-item" href="register-station.php">Estação</a>
            <a class="collapse-item" href="register-user.php">Usuário</a>
          </div>
        </div>
      </li>

      <!-- Nav Item - Utilities Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
          <i class="fas fa-fw fa-wrench"></i>
          <span>Monitoração</span>
        </a>
        <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="cards.php">Painel</a>
            <a class="collapse-item" href="tables.php">Tabela</a>
            <a class="collapse-item" href="monitor2.php">Monitoramento</a>
            <a class="collapse-item" href="export-tables.php">Exporta Tabela</a>
          </div>
        </div>
      </li>
       <!-- Divider -->
       <hr class="sidebar-divider">

      <!-- Heading -->
      <!--
      <div class="sidebar-heading">
        Addons
      </div>
      -->
      <!-- Nav Item - Pages Collapse Menu -->
      <!--
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
          <i class="fas fa-fw fa-folder"></i>
          <span>Pages</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Login Screens:</h6>
            <a class="collapse-item" href="login.html">Login</a>
            <a class="collapse-item" href="register.html">Register</a>
            <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
            <div class="collapse-divider"></div>
            <h6 class="collapse-header">Other Pages:</h6>
            <a class="collapse-item" href="404.html">404 Page</a>
            <a class="collapse-item" href="blank.html">Blank Page</a>
          </div>
        </div>
      </li>
-->
      <!-- Nav Item - Charts -->
      <!--
      <li class="nav-item">
        <a class="nav-link" href="charts.html">
          <i class="fas fa-fw fa-chart-area"></i>
          <span>Charts</span></a>
      </li>
-->
      <!-- Nav Item - Tables -->
      <!--
      <li class="nav-item active">
        <a class="nav-link" href="tables.html">
          <i class="fas fa-fw fa-table"></i>
          <span>Tables</span></a>
      </li>
       -->
      <!-- Divider
      <hr class="sidebar-divider d-none d-md-block">
         -->
      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>

          <!-- Topbar Search -->
          <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
            <div class="input-group">
              <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
              <div class="input-group-append">
                <button class="btn btn-primary" type="button">
                  <i class="fas fa-search fa-sm"></i>
                </button>
              </div>
            </div>
          </form>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">

            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            <li class="nav-item dropdown no-arrow d-sm-none">
              <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                  <div class="input-group">
                    <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button">
                        <i class="fas fa-search fa-sm"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>

            <!-- Nav Item - Alerts -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <!-- Counter - Alerts -->
                <span class="badge badge-danger badge-counter">3+</span>
              </a>
              <!-- Dropdown - Alerts -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                  Alerts Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-primary">
                      <i class="fas fa-file-alt text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 12, 2019</div>
                    <span class="font-weight-bold">A new monthly report is ready to download!</span>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-success">
                      <i class="fas fa-donate text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 7, 2019</div>
                    $290.29 has been deposited into your account!
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-warning">
                      <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 2, 2019</div>
                    Spending Alert: We've noticed unusually high spending for your account.
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
              </div>
            </li>

            <!-- Nav Item - Messages -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-envelope fa-fw"></i>
                <!-- Counter - Messages -->
                <span class="badge badge-danger badge-counter">7</span>
              </a>
              <!-- Dropdown - Messages -->
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                <h6 class="dropdown-header">
                  Message Center
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/fn_BT9fwg_E/60x60" alt="">
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div class="font-weight-bold">
                    <div class="text-truncate">Hi there! I am wondering if you can help me with a problem I've been having.</div>
                    <div class="small text-gray-500">Emily Fowler · 58m</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/AU4VPcFN4LE/60x60" alt="">
                    <div class="status-indicator"></div>
                  </div>
                  <div>
                    <div class="text-truncate">I have the photos that you ordered last month, how would you like them sent to you?</div>
                    <div class="small text-gray-500">Jae Chun · 1d</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/CS2uCrpNzJY/60x60" alt="">
                    <div class="status-indicator bg-warning"></div>
                  </div>
                  <div>
                    <div class="text-truncate">Last month's report looks great, I am very happy with the progress so far, keep up the good work!</div>
                    <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="dropdown-list-image mr-3">
                    <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="">
                    <div class="status-indicator bg-success"></div>
                  </div>
                  <div>
                    <div class="text-truncate">Am I a good boy? The reason I ask is because someone told me that people say this to all dogs, even if they aren't good...</div>
                    <div class="small text-gray-500">Chicken the Dog · 2w</div>
                  </div>
                </a>
                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
              </div>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Valerie Luna</span>
                <img class="img-profile rounded-circle" src="https://source.unsplash.com/QAB-WJcbgJk/60x60">
              </a>
              <!-- Dropdown - User Information -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">
                  <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                  Profile
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                  Settings
                </a>
                <a class="dropdown-item" href="#">
                  <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                  Activity Log
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Logout
                </a>
              </div>
            </li>
          </ul>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800 ">Cadastro de Usuário</h1>
            <div class="col-lg-4">

              <div class="card shadow">
                <div class="card-body ">
                  <!-- Nested Row within Card Body -->
                  <form class="" id="form1" name="form1" action="usuario-edit.php" method="post">
                  	<input type='hidden' name='usuarioid' value='<?=$reg->id;?>'>
                      <div class="">
                        <div class="col-lg-12">
                          <label>Nome</label>
                          <input type="text" class="form-control mb-2 mr-sm-2" value='<?=$reg->nome; ?>' id="usuarioNome" name="usuarioNome" placeholder="Nome do Usuário">
                        </div>
                        <div class="col-lg-12">
                          <label>E-Mail</label>
                          <input type="email" class="form-control mb-2 mr-sm-2"  value='<?=$reg->email; ?>' id="usuarioEmail" name="usuarioEmail" placeholder="Email do usuário">
                        </div>
                        <div class="col-lg-12">
                          <label>Login</label>
                          <input type="text" class="form-control mb-2 mr-sm-2"  value='<?=$reg->login; ?>' id="loginUsuario" name="usuarioLogin" placeholder="Login do usuário">
                        </div>
                        <div class="col-lg-12">
                          <label>Senha</label>
                          <input type="text" class="form-control mb-2 mr-sm-2"  value='<?=$reg->senha; ?>' id="senhaUsuario" name="usuarioSenha" placeholder="Senha do usuário">
                        </div>

                        <div class="col-lg-12">
                        <label>Usuario</label>
                          <select class="custom-select " id="usuarioUsu" name="usuarioUsu" >
                          	<?=$viewOptionUsu; ?>
                          </select>
                        </div>

                        <div class="col-lg-12">
                        <label>Ativo</label>
                          <select class="custom-select " id="usuarioAtivo" name="usuarioAtivo" >
                          	<?=$viewOptionAtivo; ?>
                          </select>
                        </div>
<!--==============================================================================
SELECIONAR CLIENTE SE USUARIO LOGADO FOR MASTER 
ID_CLIENTE == 0 , USUARIO MASTER
ID_CLIENTE > 0, USUARIO ADMIN
==============================================================================-->
 <? if( $usuario->idCliente == 0 ) { ?>                       
                        <div class="col-lg-12">
                        <label>Cliente</label>
                          <select class="custom-select " id="selCliente" name="selCliente" >
                            <option selected>Selecione o Cliente</option>
                            <?=$option;?>
                          </select>
                        </div>
<? } ?>                        
<? if( $usuario->idCliente > 0 ) { ?>    
  <input type='hidden' name='selCliente' id='selCliente' value='<?=$usuario->idCliente;?>'>                   
<? } ?>                        
                         <div class="col-lg-12">
                        	
                        	<? if( $reg->id == 0 ) { ?>
                          <input class="btn btn-success btn-lg btn-block" type="button" name="incluir" value="Incluir" id="incluir" />
                          <input type='hidden' value='newusuario' name='cmd'>
                          <? } ?>
                          
                        	<? if( $reg->id > 0 ) { ?>
                          <input class="btn btn-success btn-lg btn-block" type="button" name="btnAtualizar" value="Atualizar" id="btnAtualizar" />
                          <input type='hidden' value='updusuario' name='cmd'>
                          <? } ?>
                        </div>
                      </div>
                  </form>
                </div>
              </div>
            </div>
        </div>

<? if( $reg->id > 0 ) {  ?>        
          <!-- sidebar-divider Content -->
        <hr class="sidebar-divider">
        <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"></h1>
          <p class="mb-4"></p>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Unidades</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                	
                  <thead>
                    <tr>
                      <th>Selecionar</th>
                      <th>Selecionada</th>
                    </tr>
                  </thead>
                  <!-- DATA -->
                  <tbody>
                  	<tr>
                  		<td><?=$lista1;?></td>
                  		<td><?=$lista2;?></td>
                  	</tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
<? } ?>        
      </div>
      <!-- End of Main Content -->

      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; Your Website 2019</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Você quer mesmo fazer Logout?</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
          <a class="btn btn-primary" href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>


  <!-- Bootstrap core JavaScript-->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <!--<script src="vendor/jquery-easing/jquery.easing.min.js"></script>-->


  <!-- Custom scripts for all pages-->
  <script src="js/sb-admin-2.min.js"></script>

  <!-- Page level plugins -->
  <script src="vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

  <!-- Page level custom scripts -->
  <!-- script src="js/demo/datatables-demo.js"></script -->

<script type="text/javascript" language="javascript">
$(document).ready(function() {
  /// Quando usuário clicar em salvar será feito todos os passo abaixo
  $('#incluir').click(function() {
$('#form1').submit();
return false;
      var dados = $('#form1').serialize();
      $.ajax({
          type: 'POST',
          dataType: 'json',
          url: 'usuario-edit.php',
          async: true,
          data: dados,
          success: function(data) {
            alert('Dados enviados com sucesso!');
              //location.reload();
              location.href = 'usuario-edit.php';
          },
          error: function(data) {
              alert('Dados não enviados!');
              location.href = 'usuario-edit.php';
          }
      });

      return false;
  });
  /// Quando usuário clicar em salvar será feito todos os passo abaixo
  $('#btnAtualizar').click(function() {
  	
  	
$('#form1').submit();
return false;

      var dados = $('#form1').serialize();
      $.ajax({
          type: 'POST',
          dataType: 'json',
          url: 'usuario-edit.php',
          async: true,
          data: dados,
          success: function(data) {
            alert('Dados enviados com sucesso!');
              //location.reload();
              location.href = 'usuario-edit.php';
          },
          error: function(data) {
              alert('Dados não enviados!');
              location.href = 'usuario-edit.php';
          }
      });

      return false;
  });
});
</script>
</body>

</html>
