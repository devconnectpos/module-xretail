<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 20/06/2016
 * Time: 10:44
 */

namespace SM\XRetail\Observer;

use Magento\Framework\App\Request\Http as Request;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Psr\Log\LoggerInterface;

class ApiControllerBefore implements ObserverInterface
{
    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(JsonSerializer $jsonSerializer, Request $request, LoggerInterface $logger)
    {
        $this->jsonSerializer = $jsonSerializer;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Customer login bind process
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \SM\XRetail\Controller\V1\Xretail $apiController */
        $apiController = $observer->getData('apiController');
        $params = $this->request->getContent();

        // get data as json
        try {
            $data = $this->jsonSerializer->unserialize($params);

            if (!is_null($data)) {
                $apiController->getRequest()->setParams($data);
            }
        } catch (\Exception $e) {
            if ($this->request->isPost()) {
                $this->logger->warning("[CPOS] No request body supplied to the POST request: {$e->getMessage()}");
            }
        }

        $apiController->checkPath();

        return $this;
    }
}
