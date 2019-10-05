<?
include( 'cursor_config.php' );

class Conexao{
	var $conn;

  function Conexao( $idSQLCA = 0 ){
    global $sqlca;
    $this->conn = pg_connect( $sqlca[$idSQLCA] );
  }
	
	function getConexao(){
		return $this->conn;
    }
	// fim do construtor
	
	function executar($aSql){
		pg_exec($this->conn, $aSql);
	}
	// fim de execute


} // fim da classe Conexao

/*###############################################################
 CLASSE CURSOR



Implementação simples( Default ):
------------------------------------------------------

include( './lib/cursor.php' );

$c = new Cursor( 'select * from estado' );
while( ! $c->eof() ){
  $r = $c->fetch();
  print( "<br>" . $r['uf'] );
}


Implementação simples( Default ):
Para comandos insert, update, delete 
e executar procedures
------------------------------------------------------
$con = new Conexao();
$con->executar( "select importar_log( )" );



Implementação com segunda base de dados:
---------------------------------------------------

1) adicionar uma linha na parte de conexao de banco de dados no arquivo cursor_config.php:

$sqlca[] = "host=... ";  // conexao default
$sqlca[] = "host=... ";  // conexao adicional, será acessada como 1 ( um )



2) acesse a nova conexao com o exemplo abaixo:

include( './classe/cursor.php' );

$c = new Cursor( 'select * from estado' );     // <<<<<<<<<<<< conexao default
while( ! $c->eof() ){
  $r = $c->fetch();
  print( "<br>" . $r['uf'] );
}

$c = new Cursor( 'select * from estado', 1 ); // <<<<<<<<<<<<< conexao secundaria
while( ! $c->eof() ){
  $r = $c->fetch();
  print( "<br>" . $r['uf'] );
}



############################################################### */
class Cursor{
	var $sql;
	var $conn;
	var $cursor;
	var $linhas;
	var $linhaAtual;
	var $eof;

	function Cursor( $aSQL, $idSQLCA = 0 ) {
		$this->eof = TRUE;
		$this->sql = $aSQL;

    $conexao = new Conexao( $idSQLCA );

		$this->conn = $conexao->getConexao();
		$this->cursor = pg_query ($this->conn, $aSQL ); 
		$this->linhas = pg_num_rows( $this->cursor );
		if($this->linhas > 0) { $this->eof = FALSE; }
		$this->linhaAtual = 0;
	}

	function fetch() {
		$r = @pg_fetch_array( $this->cursor );
		$this->linhaAtual++;
		if( $this->linhaAtual >= $this->linhas ) $this->eof = TRUE;
		return $r;
	} // fim de fetch

	function eof(){
		return $this->eof;
	} // fim de eof

    function linhas(){
		$this->linhas = pg_numrows( $this->cursor );
		return $this->linhas;
	} // fim linhas

} // fim da class Cursor





function js_alert($texto) {
	echo "<script language='javascript'>
			alert('$texto');
		  </script>";
}

function js_location($destino) {
	echo "<script language='javascript'>
			window.location = '$destino';
	      </script>";
}
?>