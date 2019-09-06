<?php
/**
 * Created by Nomad
 * Date: 9/4/19
 * Time: 10:12 AM
 */

namespace SM\XRetail\Controller\Contract;

use SM\XRetail\Controller\Contract\AbstractClass\ApiAbstract as AbstractClass;

if (!interface_exists("Magento\Framework\App\CsrfAwareActionInterface")) {
    class ApiAbstract extends AbstractClass
    {

    }
}
