<?php
/**
 * Routes - all standard Routes are defined here.
 *
 * @author David Carr - dave@daveismyname.com
 * @version 3.0
 */

use Core\Router;
use Helpers\Hooks;


/** Define static routes. */

// Default Routing
Router::any('', 'App\Controllers\Welcome@index');
Router::any('subpage', 'App\Controllers\Welcome@subPage');

// Demo Routes
Router::any('demo/events',   'App\Controllers\Demo@events');
Router::any('demo/database', 'App\Controllers\Demo@database');
Router::any('demo/validate', 'App\Controllers\Demo@validate');

Router::any('admin/(:any)(/(:any)(/(:any)(/(:all))))', array(
    'filters' => 'test',
    'uses'    => 'App\Controllers\Demo@test'
));

// The Framework's Language Changer.
Router::any('language/(:any)', 'App\Controllers\Language@change');
/** End default Routes */

/** Module Routes. */
$hooks = Hooks::get();

$hooks->run('routes');
/** End Module Routes. */

