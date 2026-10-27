<?php defined('BX_DOL') or die('hack attempt');
require_once(BX_DIRECTORY_PATH_INC . 'design.inc.php');
// Public background reads are authorized by the module for every request;
// native content mutations retain the parent form/ACL/CSRF checks.
BxBaseModTextRequest::processAsAction($GLOBALS['aModule'], $GLOBALS['aRequest']);
