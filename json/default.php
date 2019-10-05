<?
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

class Retorno{
  public $mensagem = '001-ok';
  public $itens = array();
  public $log = '';
}

$retorno = new Retorno();
?>