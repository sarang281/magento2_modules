<?php

namespace Sarang\StoreMaintenance\Block;

class Website extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    private $filterProvider;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     * @param \Magento\Cms\Model\Template\FilterProvider
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->filterProvider = $filterProvider;
        parent::__construct($context);
    }

    public function getPageContent()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
        $content = $this->scopeConfig->getValue("website/general/display_text", $storeScope);
        return $this->filterProvider->getPageFilter()->filter($content);
    }
}
