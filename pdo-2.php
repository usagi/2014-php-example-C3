<?php

main();
exit( 0 );

function main()
{
  $database = null;
  
  open_database( $database );
  generate_data( $database, 100 );
  show( $database );
  close_database( $database );
}

function open_database( &$database )
{
  // notice: it is store in the file
  $database = new PDO( 'sqlite:pdo-2.sqlite' );
  
  // what would happen if comment out it.
  $database -> exec( 'drop table t' );
  
  $database -> exec( 'create table t( i integer primary key autoincrement, x real, y real )' );

}

function generate_data( &$database, $number_of_data )
{
  // http://jp2.php.net/manual/ja/pdo.prepare.php
  $prepared_statement =
    $database -> prepare( 'insert into t( x, y ) values( ? , ? )' )
    ;
  
  for( $n = 0; $n < $number_of_data; ++$n )
  {
    $snorm_rng =
      function()
      { return mt_rand() / ( mt_getrandmax() / 2.0 ) - 1.0 ; }
      ;
    
    $x = $snorm_rng();
    $y = $snorm_rng();
    
    // http://jp2.php.net/manual/ja/pdostatement.execute.php
    $prepared_statement -> execute( [ $x, $y ] );
  }
}

function show( &$database )
{
  echo '[ show ]'.PHP_EOL;
  
  echo '';

  $number_of_data =
    $database
      -> query( 'select count(*) from t' )
      -> fetch()[0]
    ;
  
  // http://php.net/manual/ja/pdo.sqlitecreatefunction.php
  $database -> sqliteCreateFunction( "sqrt", "sqrt", 1 );
  
  $number_in_circle =
    $database
      -> query( 'select count(*) from t where sqrt( x * x + y * y ) < 1.0' )
      -> fetch()[0]
    ;
  
  $arris_length      = 2.0;
  $arris_length_half = $arris_length * 0.5;
  $area_of_box       = $arris_length * $arris_length;
  $circle_per_box    = $number_in_circle / $number_of_data;
  $area_of_circle    = $area_of_box * $circle_per_box;
  $pi                = $area_of_circle / ( $arris_length_half * $arris_length_half );
  $pi_error          = abs( 1.0 - $pi / pi() );
  $pi_error_in_ppm   = $pi_error * 1000000;

  echo <<<EOT
number of data             : $number_of_data
number in circle           : $number_in_circle
circle per box             : $circle_per_box
area of circle (estimated) : $area_of_circle
pi (estimated)             : $pi
error[-]                   : $pi_error
error[ppm]                 : $pi_error_in_ppm
EOT;
  
  /*
  while( $row = $result -> fetch( PDO::FETCH_ASSOC ) )
    echo 'i: ' . $row['i']
      . ' ( x: ' . $row['x'] . ', ' . $row['y'] . ' )'
      . PHP_EOL
      ;
  */
  echo PHP_EOL;
}

function close_database( &$databse )
{
  $database = null;
}

?>
