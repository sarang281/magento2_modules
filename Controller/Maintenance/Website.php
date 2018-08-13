<?php

namespace Sarang\StoreMaintenance\Controller\Maintenance;
 
class Website extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
        $currentIp = $this->remoteAddress->getRemoteAddress();
        $allowedIps = $this->scopeConfig->getValue("website/general/allowed_ips", $storeScope);
        $enabled = $this->scopeConfig->getValue("website/general/enable", $storeScope);
        if (!$enabled || strpos($allowedIps, $currentIp) !== false) {
            $this->_redirect('/');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setStatusHeader(503, '1.1', 'Service Temporarily Unavailable');
        $resultPage->setHeader('Status', '503 Service Temporarily Unavailable');
        return $resultPage;
    }
}
