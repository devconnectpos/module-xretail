<?php
/**
 * Created by Nomad
 * Date: 9/4/19
 * Time: 10:12 AM
 */

namespace SM\XRetail\Controller\Contract;

use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use SM\XRetail\Controller\Contract\AbstractClass\ApiAbstract as AbstractClass;

if (interface_exists("Magento\Framework\App\CsrfAwareActionInterface")) {
    class ApiAbstract extends AbstractClass implements \Magento\Framework\App\CsrfAwareActionInterface
    {

        /**
         * Create exception in case CSRF validation failed.
         * Return null if default exception will suffice.
         *
         * @param RequestInterface $request
         *
         * @return InvalidRequestException|null
         */
        public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
        {
            return null;
        }

        /**
         * Perform custom request validation.
         * Return null if default validation is needed.
         *
         * @param RequestInterface $request
         *
         * @return bool|null
         */
        public function validateForCsrf(RequestInterface $request): ?bool
        {
            return true;
        }

    }
}
