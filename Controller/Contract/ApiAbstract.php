<?php
if (!interface_exists("Magento\Framework\App\CsrfAwareActionInterface")) {
    require('app/code/SM/XRetail/Controller/Contract/ApiAbstract22.php');
} else {
    require ('app/code/SM/XRetail/Controller/Contract/ApiAbstract23.php');
}
