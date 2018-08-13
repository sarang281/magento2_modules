<?php

namespace Sarang\StoreMaintenance\Observer;

use Magento\Framework\Event\ObserverInterface;

class Maintenance implements ObserverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlInterface;
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    private $remoteAddress;
    
    /**
     * @param \Psr\Log\LoggerInterface
     * @param \Magento\Framework\UrlInterface
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->logger = $logger;
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        $this->remoteAddress = $remoteAddress;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();
        $request = $observer->getRequest();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
        $moduleStatus = $this->scopeConfig->getValue("website/general/enable", $storeScope);
        if ($moduleStatus && 'maintenance' !== $request->getRouteName()) {
            $allowedIps = $this->scopeConfig->getValue("website/general/allowed_ips", $storeScope);
            if (!$this->allowAccess($allowedIps)) {
                $url = $this->urlInterface->getUrl('maintenance/maintenance/website');
                $observer->getControllerAction()
                        ->getResponse()
                        ->setRedirect($url);
            }
        }
    }
    
    /**
     * @param string
     */
    private function allowAccess($allowedIps)
    {
        $currentIp = $this->remoteAddress->getRemoteAddress();
        if (strpos($allowedIps, $currentIp) !== false) {
            return true;
        }
        return false;
    }
}
