<?php 
include("./php/conexao.php");
/*
$id_unidade = $_POST['selUnidade'];
$dt_inicio = $_POST['dtInicio'];
$dt_termino = $_POST['dtTermino'];

echo "-->".$dt_termino;

exit;
*/
	$sql = " SELECT id_chamado, cliente, terminal
	              , ts_abertura_fmt, ts_atendimento_fmt
				  , intervalo_tm, dt_abertura_fmt
				  , dt_atendimento_fmt, terminal_motivo
	          FROM bel_chamado_export";

      $result = $conn->query($sql);
      $rows = $result->fetchAll();
    
      $content = "Cliente;Estacao;Motivo;Data Chamada/Horario Chamada;Data Atendimento/Horario Atendimento;Intervalor \n";
      header("Content-type: text/csv");
	  header("Content-Disposition: attachment; filename=".date('dmY_his')."_export.csv");
      foreach($rows as $row){
        $content .= $row['cliente'].";".$row['terminal'].";".$row['terminal_motivo'].";".$row['ts_abertura_fmt'].";".$row['ts_atendimento_fmt'].";".$row['intervalo_tm']."\n";;
             
      }
		print $content;      
     
?>