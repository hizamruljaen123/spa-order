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
$route['default_controller'] = 'booking';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/* Frontend Booking */
$route['booking'] = 'booking/index';
$route['booking/form'] = 'booking/form';
$route['booking/submit'] = 'booking/submit';
$route['booking/availability'] = 'booking/availability'; // GET: date=YYYY-MM-DD&therapist_id=ID(optional)
$route['booking/mobile'] = 'booking/mobile';
$route['booking/invoice/(:any)'] = 'booking/invoice/$1';
$route['booking/success/(:any)'] = 'booking/success/$1';

/* Admin Dashboard */
$route['admin'] = 'admin/index';
$route['admin/therapists'] = 'admin/therapists';
$route['admin/therapist/create'] = 'admin/therapist_create';
$route['admin/therapist/edit/(:any)'] = 'admin/therapist_edit/$1';
$route['admin/therapist/delete/(:any)'] = 'admin/therapist_delete/$1';

$route['admin/packages'] = 'admin/packages';
$route['admin/package/create'] = 'admin/package_create';
$route['admin/package/edit/(:any)'] = 'admin/package_edit/$1';
$route['admin/package/delete/(:any)'] = 'admin/package_delete/$1';

$route['admin/schedule'] = 'admin/schedule';
$route['admin/bookings'] = 'admin/bookings';
$route['admin/report'] = 'admin/report';

/* Invoice */
$route['admin/invoice/(:any)'] = 'admin/invoice/$1';
$route['admin/invoice/generate/(:any)'] = 'admin/generate_invoice/$1';

/* Booking status actions */
$route['admin/booking/confirm/(:any)'] = 'admin/booking_confirm/$1';
$route['admin/booking/complete/(:any)'] = 'admin/booking_complete/$1';
$route['admin/booking/cancel/(:any)'] = 'admin/booking_cancel/$1';
$route['admin/booking/update-time'] = 'admin/booking_update_time';
$route['admin/booking/delete'] = 'admin/booking_delete';
$route['admin/booking/set-status'] = 'admin/booking_set_status';

/* API (Telegram) */
$route['api/telegram/send'] = 'api/send_booking_notification';

/* Report Controller (optional direct endpoint) */
$route['report/monthly'] = 'report/monthly';

/* Auth */
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
