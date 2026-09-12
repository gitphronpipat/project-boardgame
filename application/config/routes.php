<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';

$route['player'] = 'player/index';
$route['player/lobby/(:any)'] = 'player/lobby/$1';
$route['player/lobby/(:any)/(:any)'] = 'player/lobby/$1/$2';

// XO Game Routes
$route['xo'] = 'xo/index';
$route['xo/room/(:any)'] = 'xo/room/$1';
$route['xo/get_state/(:any)'] = 'xo/get_state/$1';
$route['xo/make_move/(:any)'] = 'xo/make_move/$1';
$route['xo/rematch/(:any)'] = 'xo/rematch/$1';
$route['xo/leave/(:any)'] = 'xo/leave/$1';

// UNO Game Routes
$route['uno'] = 'uno/index';
$route['uno/room/(:any)'] = 'uno/room/$1';
$route['uno/get_state/(:any)'] = 'uno/get_state/$1';
$route['uno/play_card/(:any)'] = 'uno/play_card/$1';
$route['uno/draw_card/(:any)'] = 'uno/draw_card/$1';
$route['uno/pass_turn/(:any)'] = 'uno/pass_turn/$1';
$route['uno/call_uno/(:any)'] = 'uno/call_uno/$1';
$route['uno/catch_uno/(:any)'] = 'uno/catch_uno/$1';
$route['uno/rematch/(:any)'] = 'uno/rematch/$1';
$route['uno/leave/(:any)'] = 'uno/leave/$1';

// Minesweeper Game Routes
$route['minesweeper']                         = 'minesweeper/index';
$route['minesweeper/room/(:any)']             = 'minesweeper/room/$1';
$route['minesweeper/get_state/(:any)']        = 'minesweeper/get_state/$1';
$route['minesweeper/reveal/(:any)']           = 'minesweeper/reveal/$1';
$route['minesweeper/flag/(:any)']             = 'minesweeper/flag/$1';
$route['minesweeper/change_settings/(:any)']  = 'minesweeper/change_settings/$1';
$route['minesweeper/propose_difficulty/(:any)'] = 'minesweeper/propose_difficulty/$1';
$route['minesweeper/respond_difficulty/(:any)'] = 'minesweeper/respond_difficulty/$1';
$route['minesweeper/rematch/(:any)']          = 'minesweeper/rematch/$1';
$route['minesweeper/leave/(:any)']            = 'minesweeper/leave/$1';

$route['404_override']       = '';
$route['translate_uri_dashes'] = FALSE;

