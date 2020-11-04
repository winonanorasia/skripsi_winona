<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
|	https://codeigniter.com/user_guide/general/routing.html
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
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['login'] = 'auth';
$route['register'] = 'auth/register';
$route['logout'] = 'auth/logout';

// Route For Data Transaksi
$route['gelombang'] = 'gelombang';
$route['gelombang/create'] = 'gelombang/create';
$route['gelombang/doupload'] = 'gelombang/doupload';
$route['gelombang/update/(:any)'] = 'gelombang/update/$1';
$route['gelombang/delete/(:any)'] = 'gelombang/delete/$1';

// Route For Prediksi
$route['prediksi'] = 'prediksi';
$route['prediksi/create'] = 'prediksi/create';
$route['prediksi/createbulanan'] = 'prediksi/createbulanan';
$route['prediksi_mendatang/create_mendatang'] = 'prediksi_mendatang/create_mendatang';
$route['prediksi_mendatang/createbulanan'] = 'prediksi_mendatang/createbulanan';
$route['prediksi/predik'] = 'prediksi/predik';
$route['prediksi_mendatang/predik'] = 'prediksi_mendatang/predik';
$route['prediksi/run'] = 'prediksi/run';
$route['prediksi/auto'] = 'prediksi/auto';
$route['prediksi/delete/(:any)'] = 'prediksi/delete/$1';

// Route For Pengujian with K-fold
$route['pengujian'] = 'pengujian';
$route['pengujian/create'] = 'pengujian/create';

$route['pengujian/uji'] = 'pengujian/uji';
$route['pengujian/(:any)'] = 'pengujian/detail/$1';
$route['pengujian/delete/(:any)'] = 'pengujian/delete/$1';
$route['pengujian/getGraph/(:any)'] = 'pengujian/getGraph/$1';

// Route For Pengujian HW
$route['pengujianhw'] = 'pengujianHW';
$route['pengujianhw/create'] = 'pengujianHW/create';
$route['pengujianhw/predik'] = 'pengujianHW/predik';
$route['pengujianhw/(:any)'] = 'pengujianHW/detail/$1';
$route['pengujianhw/delete/(:any)'] = 'pengujianHW/delete/$1';
$route['pengujianhw/getGraph/(:any)'] = 'pengujianHW/getGraph/$1';
