<?php
if (!interface_exists("Magento\Framework\App\CsrfAwareActionInterface")) {
    require($_SERVER['DOCUMENT_ROOT'] . '/vendor/connectpos/module-xretail/Controller/Contract/ApiAbstract22.php');
} else {
    require ($_SERVER['DOCUMENT_ROOT'] . '/vendor/connectpos/module-xretail/Controller/Contract/ApiAbstract23.php');
}

