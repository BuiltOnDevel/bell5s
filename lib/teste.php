
<?
include( '../lib/cursor.php' );

$c = new Cursor( 'select * from glb_usuario' );
while( ! $c->eof() ){
  $r = $c->fetch();
  print( "<br>" . $r['uf'] );
  foreach( $i=0;$r->length; $i++ )
  {
    print( ", coluna " . $i );
  }
}
?>
