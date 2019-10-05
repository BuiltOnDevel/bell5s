<?php
header("Access-Control-Allow-Origin: *");
include("../json/default.php");
include("../php/conexao.php");


class Registro{
 var $id_unidade = 0;
 var $id_cliente = 0;
 var $nome = '';
 var $fl_ativo = '';
}






	/*============================================================
	           LER A SEQUENCIA
	============================================================*/
	
	try {
		
		$sql = " select * 
		from bel_unidade
		where id_unidade = " . $_REQUEST['id'] ;
		
		    
		$retorno->log .= $sql;
		    
	  $result = $conn->query( $sql );
	  $rows   = $result->fetchAll();
	  
	
		foreach( $rows as $r)
		{
			$reg = new Registro();
			
 $reg->id_unidade = $r['id_unidade'];
 $reg->id_cliente = $r['id_cliente'];
 $reg->nome = $r['nome'];
 $reg->fl_ativo = $r['fl_ativo'];

 	 
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