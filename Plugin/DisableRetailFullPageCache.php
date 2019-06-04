<?php
/**
 * Created by KhoiLe - mr.vjcspy@gmail.com
 * Date: 7/4/17
 * Time: 3:54 PM
 */

namespace SM\XRetail\Plugin;

use Closure;
use Magento\Framework\App\PageCache\Kernel;
use Magento\Framework\App\Request\Http;

class DisableRetailFullPageCache
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    public function __construct(
        Http $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\App\PageCache\Kernel $subject
     * @param \Closure                                $proceed
     * @param \Magento\Framework\App\Response\Http    $response
     */
    public function aroundProcess(
        Kernel $subject,
        Closure $proceed,
        \Magento\Framework\App\Response\Http $response
    ) {
        $path = $this->request->getPathInfo();
        if (strpos($path, '/xrest/v1/xretail') !== false) {
            return;
        } else {
            $proceed($response);
        }
    }
}
