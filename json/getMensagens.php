<?php
header("Access-Control-Allow-Origin: *");
include("../json/default.php");
include("../php/conexao.php");


class Registro{
 public $horario = 0;
 public $cliente    = 'CLIENTE';
 public $local = 'LOCAL';
 public $estacao = 'ESTACAO';
 public $mensagem = '';
 public $obs = 'OBS';
}


$reg = new Registro();



	/*============================================================
	           LER A SEQUENCIA
	============================================================*/
	
	try {
		
		$sql = " select id_mensagem as id
		, mensagem
		, to_char( inclusao_ts, 'dd/mm/yyyy HH:mi:ss' ) as horario_fmt 
		from bel_mensagem 
		order by inclusao_ts desc";
		
		    
		$retorno->log .= $sql;
		    
	  $result = $conn->query( $sql );
	  $rows   = $result->fetchAll();
	  
	
		foreach( $rows as $r)
		{
			$reg->horario     = $r['horario_fmt'];
			$reg->mensagem    = $r['mensagem'];	
			
			$retorno->itens[] = $reg;
		}	
	
	
	
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}
	
	
/*========================================================================
                    RETORNO AO CLIENTE
========================================================================*/

$retorno->log = '';
print( json_encode( $retorno ) );



?>