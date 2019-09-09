<?php
if (!interface_exists("Magento\Framework\App\CsrfAwareActionInterface")) {
    require('vendor/connectpos/module-xretail/Controller/Contract/ApiAbstract22.php');
} else {
    require ('vendor/connectpos/module-xretail/Controller/Contract/ApiAbstract23.php');
}
