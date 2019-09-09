<?php
header("Access-Control-Allow-Origin: *");
include("../json/default.php");
include("../php/conexao.php");


$retorno->log .= $_SERVER['QUERY_STRING']; // obter parametros

$estacao_p = $_REQUEST['estacao'];

class Registro{
 public $id_mensagem = 0;
 public $mensagem    = '';
 public $inclusao_ts = 'current_timestamp';
 public $inclusao_dt = 'current_date';
 public $cliente = 'CLIENTE';
 public $unidade = 'UNIDADE';
 public $estacao = 'ESTACAO';
 public $terminal_name = '000';
 public $terminal_id = 0;
 public $terminal_ts = '2019-08-07 23:59:00';
 public $terminal_botao = '';

}


$reg = new Registro();

 $reg->id_mensagem    = 0;
 $reg->mensagem       = 'xxxx';
 $reg->cliente        = $_REQUEST['cli'];
 $reg->unidade        = $_REQUEST['site'];
 $reg->estacao          = $estacao_p;
 $reg->terminal_name       = $_REQUEST['terminal'];
 $reg->terminal_id       = 0;
 $reg->terminal_ts       = $_REQUEST['ts'];
 $reg->terminal_botao          = $_REQUEST['botao'];			
// $reg->inclusao_ts    = '';
// $reg->inclusao_dt    = '';

//$retorno->log .= "mensagem = " + $reg->mensagem;

$retorno->itens[] = $reg;

	/*============================================================
	           LER A SEQUENCIA
	============================================================*/
	
	try {
		
		$sql = " select 
		    nextval('bel_mensagem_seq') as id
		    , current_timestamp as ts
		    , current_date as dt ";
		    
		$retorno->log .= $sql;

	  $result = $conn->query( $sql );
	  $rows   = $result->fetchAll();
	  
	
		foreach( $rows as $r)
		{
			$reg->id_mensagem         = $r['id'];	
			$reg->inclusao_ts = $r['ts'];
			$reg->inclusao_dt = $r['dt'];
		}	
	
	
	
	}
	catch(PDOException $e) {
				$retorno->mensagem = "999-falha";
	    $retorno->log .= "Error: " . $e->getMessage();
	}

//
// INCLUIR 
//=============================================================
if( $reg->id == 0 )
{
	try {
		
	
	
		$sql = "
INSERT INTO bel_mensagem
(id_mensagem, mensagem
, inclusao_ts, inclusao_dt
, cliente, unidade, estacao
, terminal_name, terminal_id, terminal_ts, terminal_botao )
VALUES(
 $reg->id_mensagem, '$reg->mensagem'
 , current_timestamp, current_date
 , '$reg->cliente', '$reg->unidade', '". $reg->estacao . "'
 , '$reg->terminal_name', $reg->terminal_id, '$reg->terminal_ts', '$reg->terminal_botao' )";
	
	$retorno->log .= $sql;
	
	
	
		$rs = $conn->prepare($sql);
		$rs->execute();
	
	  
		$retorno->mensagem = "001-sucesso";
		
	
	}
	catch(PDOException $e) {
		$retorno->mensagem = "999-falha";
    $retorno->log .= " Exception: " . $e->getMessage();
	}
	
	
	

} // fim de inclusao



	/*============================================================
	           LER A SEQUENCIA
	============================================================*/
	
	try {
		
		$sql = " select id_mensagem as id, mensagem
		, inclusao_ts as ts , inclusao_dt as dt
		, cliente, unidade, estacao
		from bel_mensagem
		where id_mensagem =  " . $reg->id_mensagem;
		    
		$retorno->log .= $sql;
		    
	  $result = $conn->query( $sql );
	  $rows   = $result->fetchAll();
	  
	
		foreach( $rows as $r)
		{
			$reg->id_mensagem = $r['id'];
			$reg->mensagem = $r['mensagem'];	
			$reg->inclusao_ts = $r['ts'];
			$reg->inclusao_dt = $r['dt'];
			$reg->cliente = $r['cliente'];
			$reg->unidade = $r['unidade'];
			$reg->estacao = $r['estacao'];
			
			$retorno->itens[] = $reg;
		}	
	
	
	
	}
	catch(PDOException $e) {
				$retorno->mensagem = "999-falha";
	    $retorno->log .= "Error: " . $e->getMessage();
	}
	
	
/*========================================================================
                    RETORNO AO CLIENTE
========================================================================*/

//$retorno->log = '';
print( json_encode( $retorno ) );



?>