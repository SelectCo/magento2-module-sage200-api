<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Controller\Adminhtml\OAuth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\OAuth\SageToken;

class Refresh extends Action
{
    const ADMIN_RESOURCE = 'SelectCo_Sage200Api::refresh_token';

    /**
     * @var Data
     */
    private $helper;
    /**
     * @var SageToken
     */
    private $token;

    public function __construct(Data $helper, SageToken $token, Context $context)
    {
        parent::__construct($context);
        $this->helper = $helper;
        $this->token = $token;
    }

    public function execute()
    {
        if ($this->helper->isModuleEnabled()) {
            $this->token->refreshToken();
        }
        return $this->resultRedirectFactory->create()->setPath('s200/oauth/');
    }
}
