<?php 

echo '<h1>Wiki-Ema</h1>';

$args = array(
    'post_type' => 'obras'
);

$obras_sync = new WP_Query( $args );

//print_r( $obras_sync );

foreach( $obras_sync as $obra_sync ){
    $autor = get_post_meta(get_the_id());
    print_r( $autor );
}


?>