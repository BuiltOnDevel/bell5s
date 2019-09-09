<?php 
include("./php/conexao.php");
/*
$id_unidade = $_POST['selUnidade'];
$dt_inicio = $_POST['dtInicio'];
$dt_termino = $_POST['dtTermino'];

echo "-->".$dt_termino;

exit;


$sql = "select 
		id_mensagem as id
		, mensagem
		, to_char( inclusao_ts, 'dd/mm/yyyy hh24:mi:ss' ) as horario_fmt 
		, cliente, unidade, estacao
		, terminal_botao
		, terminal_name
		from bel_mensagem 
		where st_mensagem = 'PENDENTE'
		and inclusao_ts between ".$dt_inicio." and ". $dt_termino ." 
		and unidade_id = ".$id_unidade."
		"; */
	$sql = " select 
		id_mensagem as id
		, mensagem
		, to_char( inclusao_ts, 'dd/mm/yyyy hh24:mi:ss' ) as horario_fmt 
		, cliente, unidade, estacao
		, terminal_botao
		, terminal_name
		, terminal_motivo
		from bel_mensagem 
		where st_mensagem = 'PENDENTE'
		order by 1 desc";

      $result = $conn->query($sql);
      $rows = $result->fetchAll();
    
      $content = "Horario;Cliente;Local;Estacao;Mensagem;Dispositivo;Botao;Motivo \n";
      header("Content-type: text/csv");
	  header("Content-Disposition: attachment; filename=".date('dmY_his')."_export.csv");
      foreach($rows as $row){
        $content .= $row['horario_fmt'].";".$row['cliente'].";".$row['unidade'];
        $content .= $row['estacao'].";".$row['id'].";".$row['terminal_name'].";".$row['terminal_botao'].$row['terminal_motivo']."\n";      
      }
		print $content;      
     
?>