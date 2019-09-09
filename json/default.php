<?
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);

class Retorno{
  public $mensagem = '999-Falha';
  public $itens = array();
  public $log = '';
}

$retorno = new Retorno();
?>