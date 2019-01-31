<?php
include_once '../../include/db.php';
include_once '../../include/general.php';
include_once '../../include/authenticate.php';
include_once '../../include/config_functions.php';

// Set success to false if there is an expected error
// You can provide feedback to the user by adding a message
$response['success'] = true;
$response['message'] = '';


$autosave_option_name  = getvalescaped('autosave_option_name', '');
$autosave_option_value = getvalescaped('autosave_option_value', '');


if(getval('autosave', '') === 'true' && !set_config_option($userref, $autosave_option_name, $autosave_option_value))
    {
    $response['success'] = false;
    }

echo json_encode($response);
exit();