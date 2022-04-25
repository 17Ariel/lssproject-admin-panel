<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'vendor/autoload.php';

use Parse\ParseClient;

// $appId = "wEKHZ41j0I4sh0Pa6cHL8gtOCDLUXV25mxhLhAF4";
// $restKey = "ECtgHNgEK5Mqj9M2463fQoVb177X7Y0BjXSdOnzp";
// $masterKey = "x4rblsVZtKt1yriGLcfUBm3GwjggZMJoiWopG4zb";
// Initializes with the <APPLICATION_ID>, <REST_KEY>, and <MASTER_KEY>

$appId = "A0kOZQDff55haySNoEaiLWfu2DrWutTTPXbsWMse";
$restKey = "0LOv5whoElYmLBnQFq6V0E95h53ctBunLoH5Bs2c";
$masterKey = "GREF7w8sAexmvTUDNbrLplYm5LOD7beM0r4zYN3o";
ParseClient::initialize($appId, $restKey, $masterKey);
ParseClient::setServerURL('https://parseapi.back4app.com', '/');
