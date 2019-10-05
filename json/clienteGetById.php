<?php
header("Access-Control-Allow-Origin: *");
include("../json/default.php");
include("../php/conexao.php");


class Registro{
 var $id_cliente = '';
 var $nome = '';
 var $fl_ativo = '';
 var $ts_inclusao = '';
 var $url_logo = '';
 var $nivel_critico = '';
 var $nivel_alerta = '';
 var $chamados_abertos_media = '';
 var $tma_geral = '';
}






	/*============================================================
	           LER A SEQUENCIA
	============================================================*/
	
	try {
		
		$sql = " select * 
		from bel_cliente
		where id_cliente = " . $_REQUEST['id'] ;
		
		    
		$retorno->log .= $sql;
		    
	  $result = $conn->query( $sql );
	  $rows   = $result->fetchAll();
	  
	
		foreach( $rows as $r)
		{
			$reg = new Registro();
			
 $reg->id_cliente = $r['id_cliente'];
 $reg->nome = $r['nome'];
 $reg->fl_ativo = $r['fl_ativo'];
 $reg->ts_inclusao = $r['ts_inclusao'];
 $reg->url_logo = $r['url_logo'];
 $reg->nivel_critico = $r['nivel_critico'];
 $reg->nivel_alerta = $r['nivel_alerta'];
 $reg->chamados_abertos_media = $r['chamados_abertos_media'];
 $reg->tma_geral = $r['tma_geral'];
 	 
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