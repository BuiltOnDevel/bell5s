<?



?>


      <!-- Nav Item - Dashboard -->
      <li class="nav-item active">
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

<? if( $usuario->idCliente == 0){ ?>
            <a class="collapse-item" href="register-client.php">Cliente</a>
<? } ?>            
            <a class="collapse-item" href="register-unit.php">Unidade</a>
            <a class="collapse-item" href="register-station.php">Estação</a>
            <a class="collapse-item" href="register-terminais.php">Terminais</a>
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
