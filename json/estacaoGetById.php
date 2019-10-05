<?php
header("Access-Control-Allow-Origin: *");
include("../json/default.php");
include("../php/conexao.php");


class Registro{
 var $id_estacao = 0;
 var $id_cliente = 0;
 var $nome = '';
 var $fl_ativo = '';
 var $ts_inclusao = '';
 var $fl_add_term = '';
 var $id_unidade = 0;
}




	/*============================================================
	           LER A SEQUENCIA
	============================================================*/
	
	try {
		
		$sql = " select * 
		from bel_estacao
		where id_estacao = " . $_REQUEST['id'] ;
		
		    
		$retorno->log .= $sql;
		    
	  $result = $conn->query( $sql );
	  $rows   = $result->fetchAll();
	  
	
		foreach( $rows as $r)
		{
			$reg = new Registro();
			
 $reg->id_estacao = $r['id_estacao'];
 $reg->id_cliente = $r['id_cliente'];
 $reg->nome = $r['nome'];
 $reg->fl_ativo = $r['fl_ativo'];
 $reg->ts_inclusao = $r['ts_inclusao'];
 $reg->fl_add_term = $r['fl_add_term'];
 $reg->id_unidade = $r['id_unidade'];

 	 
			$retorno->itens[] = $reg;
			
		}	
	
	
	}
	catch(PDOException $e) {
	    $retorno->log .= "Error: " . $e->getMessage();
	}
	
	
/*========================================================================
                    RETORNO AO CLIENTE
========================================================================*/

//$retorno->log = '';
print( json_encode( $retorno ) );



?>