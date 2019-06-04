<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 20/06/2016
 * Time: 10:56
 */

namespace SM\XRetail\Model\Api;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Xml\Parser;

class Configuration extends DataObject
{

    /**
     * @var
     */
    protected $apiRouters;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $mageConfig;
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $reader;
    /**
     * @var \Magento\Framework\Xml\Parser
     */
    protected $parser;

    /**
     * Configuration constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Module\Dir\Reader               $reader
     * @param \Magento\Framework\Xml\Parser                      $parser
     * @param array                                              $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Reader $reader,
        Parser $parser,
        array $data = []
    ) {
        $this->reader      = $reader;
        $this->parser      = $parser;
        $this->mageConfig = $scopeConfig;
        parent::__construct($data);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getApiRouters()
    {
        if (is_null($this->apiRouters)) {
            $this->apiRouters = $this->getRouterData();
        }

        return $this->apiRouters;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getRouterData()
    {
        $filePath    = $this->reader->getModuleDir('etc', 'SM_XRetail') . '/config.xml';
        $parsedArray = $this->parser->load($filePath)->xmlToArray();

        if (isset($parsedArray['config']['_value']['default']['apirouters']['router'])) {
            return $parsedArray['config']['_value']['default']['apirouters']['router'];
        }

        throw new Exception("Can't get routers data");
    }
}
