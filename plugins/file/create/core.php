<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File Create.
 * Uploads a file to Ortro.
 *
 * PHP version 5
 *
 * LICENSE: This file is part of Ortro.
 * Ortro is published under the terms of the GNU GPL License v2 
 * Please see LICENSE and COPYRIGHT files for details.
 *
 * @category Plugins
 * @package  Ortro
 * @author   Luca Corbo <lucor@ortro.net>
 * @license  GNU/GPL v2
 * @link     http://www.ortro.net
 */

//###### Required core code ######

require_once realpath(dirname($argv[0])) . '/../../init.inc.php';
require_once 'cronUtil.php';

$plugin_name  = basename(dirname($argv[0]), DIRECTORY_SEPARATOR);
$id_job       = $argv[1];// Get the job id
$request_type = $argv[2];// Get the type of request
 
$cronUtil   = new CronUtil($request_type);
$job_infos  = $cronUtil->startJobEvent($plugin_name, $id_job);
$parameters = $job_infos['parameters'];
set_error_handler("errorHandler");

//###### End required core code ######

try {
    //---- Start plugin code -----
    
    $loggerPlugin = new LogUtil($plugin_name, ORTRO_LOG_PLUGINS . $plugin_name);
    $loggerPlugin->trace('INFO', 'Executing job ' . $plugin_name . 
                         ' with id=' . $id_job);
    
    $result = 0;
    
    include_once 'ioUtil.php';
    
    //Get the params required by plugin from argv
        
    $filedata = $parameters['file_create_body'];
    $filename = $parameters['file_create_name'];
        
    createFile($filedata, ORTRO_FILES, $filename);    
    
    $result   = 1;
    $msg_exec = $result;
    //---- End plugin code -----

} catch (Exception $e) {
    $cronUtil->traceError($plugin_name, $e);
    $msg_exec = "Plugin exception occourred: " . $e->getMessage() . "\n" .
                "Please contact system administrator";
    
}

//###### Required core code ######
restore_error_handler();
$cronUtil->endJobEvent($plugin_name, $id_job, $result, $msg_exec, $attachments);
//###### End required core code ######

?>
