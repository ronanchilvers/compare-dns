<?php
// Add middleware here
// Variables available :
//   - $container
//   - $app

$app->add(new \Ronanchilvers\Sessions\SessionMiddleware(
    $container->get('session')
));
