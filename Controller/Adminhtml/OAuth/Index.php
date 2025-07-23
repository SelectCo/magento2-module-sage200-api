<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Controller\Adminhtml\OAuth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'SelectCo_Sage200Api::view_token';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    public function __construct(PageFactory $resultPageFactory, Context $context)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('SelectCo_Sage200Api::view_token');
        $resultPage->getConfig()->getTitle()->prepend(__('Sage200 OAuth Token'));

        return $resultPage;
    }
}
