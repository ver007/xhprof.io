<?php
if (extension_loaded('xhprof')) {
    $xhprof_config			= require __DIR__ . '/../xhprof/includes/config.inc.php';
    if (!empty($xhprof_config['enable']) && $xhprof_config['enable']()) {
        xhprof_enable(XHPROF_FLAGS_MEMORY | XHPROF_FLAGS_CPU);
        register_shutdown_function(function() use ($xhprof_config){
            $xhprof_data	= xhprof_disable();
            if (function_exists('fastcgi_finish_request')) {
                fastcgi_finish_request();
            }
            require_once __DIR__ . '/../xhprof/classes/data.php';
            $xhprof_data_obj	= new \ay\xhprof\Data($xhprof_config['pdo']);
            $xhprof_data_obj->save($xhprof_data);
        });
    }
}
