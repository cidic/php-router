<?php

/**
 * example urls:
 * /admin/foo/test/99/
 * /admin/foo/test/alpha_var/beta_var/99/
 *
 */
include_once(dirname(__FILE__) . '/lib/SplClassLoader.php');

$loader = new SplClassLoader('Controller',  dirname(__FILE__));
$loader->register();

$loader = new SplClassLoader(null,  dirname(__FILE__) . '/lib');
$loader->register();

//Create a new instance of Router (you'd likely use a factory or container to manage the instance)
$router = new Router;

//Get an instance of Dispatcher
$dispatcher = new CleanDispatcher;

//Set up a 'catch all' default route and add it to the Router.
$std_route = new Route('/admin/:class/:method/:id/');
$std_route
          ->setMapNameSpace('Controller')
          ->addDynamicElement(':class', ':class')
          ->addDynamicElement(':method', ':method')
          ->addDynamicElement(':id', ':id');
$router->addRoute( 'std_admin', $std_route );

$std_route2 = new Route('/foo/test/:alpha/:beta/:gama/');
$std_route2
            ->setMapNameSpace('Controller')
            ->setMapClass( 'Foo' )
            ->setMapMethod( 'test' )
            ->addDynamicElement(':alpha', ':alpha')
            ->addDynamicElement(':beta', ':beta')
            ->addDynamicElement(':gama',
            function($var){

                if(ctype_digit($var)){
                    return true;
                }
                return false;
            } );

$router->addRoute( 'std_test', $std_route2 );

//Set up your default route:
$default_route = new Route('/');
$default_route
    ->setMapNameSpace('Controller')
    ->setMapClass('Root')
    ->setMapMethod('default_action');
$router->addRoute( 'default', $default_route );


$url = urldecode($_SERVER['REQUEST_URI']);
try {
    $found_route = $router->findRoute($url);
     echo '<pre>';

    $dispatcher->dispatch( $found_route );



} catch (Exception $e ) {


    // log error
    error_log('Route Exception');
    error_log($e->toString());

    // redirec to 404 page

}

