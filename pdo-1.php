<?php

main();
exit( 0 );

function main()
{
  $database = null;
  
  open_database( $database );
  generate_data( $database );
  show_halfs( $database );
  show_arithmetic_average( $database );
  show_x_min_top3( $database );
  close_database( $database );
}

function open_database( &$database )
{
  // http://php.net/manual/ja/pdo.connections.php
  // http://php.net/manual/ja/ref.pdo-sqlite.connection.php
  $database = new PDO( 'sqlite::memory:' );
  
  // http://php.net/manual/ja/pdo.exec.php
  $database -> exec( 'create table t( i integer primary key autoincrement, x real, y real )' );

}

function generate_data( &$database )
{
  for( $n = 0; $n < 10; ++$n )
  {
    $snorm_rng =
      function()
      { return mt_rand() / ( mt_getrandmax() / 2.0 ) - 1.0 ; }
      ;
    
    $x = $snorm_rng();
    $y = $snorm_rng();
    
    echo 'generate[' . $n . '] = ( x: ' . $x . ', y:' . $y . ')' . PHP_EOL;
    
    $database -> exec( 'insert into t( x, y ) values( ' . $x . ', ' . $y . ' )' );
  }

  echo PHP_EOL;
}

function show_halfs( &$database )
{
  echo '[ show ( x < 0.5, y < 0.5 ) ]'.PHP_EOL;

  // http://php.net/manual/ja/pdo.query.php
  $result = $database -> query( 'select * from t where x < 0.5 and y < 0.5' );

  // http://php.net/manual/ja/pdostatement.fetch.php
  while( $row = $result -> fetch( PDO::FETCH_ASSOC ) )
    echo 'i: ' . $row['i']
      . ' ( x: ' . $row['x'] . ', ' . $row['y'] . ' )'
      . PHP_EOL
      ;
  
  echo PHP_EOL;
}

function show_arithmetic_average( &$database )
{
  echo '[ show arithmetic average ]'.PHP_EOL;
  
  // is it stylish? has it no problems?
  print_r
    ( $database
      -> query( 'select avg(x), avg(y) from t' )
      -> fetch( PDO::FETCH_ASSOC )
    );
  
  echo PHP_EOL;
}

function show_x_min_top3( &$database )
{
  echo '[ show x min top 3 ]'.PHP_EOL;

  // http://php.net/manual/ja/pdo.query.php
  $result = $database -> query( 'select * from t order by x asc limit 3' );
  
  // http://php.net/manual/ja/pdostatement.fetchall.php
  foreach( $result -> fetchALL() as $row )
    echo 'i: ' . $row['i']
      . ' ( x: ' . $row['x'] . ', ' . $row['y'] . ' )'
      . PHP_EOL
      ;
  
  echo PHP_EOL;
}

function close_database( &$databse )
{
  // http://php.net/manual/ja/pdo.connections.php
  $database = null;
}

// and more SQLite SELECT statement
// http://www.sqlite.org/lang_select.html

?>
