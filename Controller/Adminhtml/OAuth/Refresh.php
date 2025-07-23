<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Controller\Adminhtml\OAuth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use SelectCo\Sage200Api\Model\OAuth\SageToken;

class Refresh extends Action
{
    const ADMIN_RESOURCE = 'SelectCo_Sage200Api::refresh_token';

    /**
     * @var SageToken
     */
    private $token;

    public function __construct(SageToken $token, Context $context)
    {
        parent::__construct($context);
        $this->token = $token;
    }

    public function execute()
    {
        $this->token->refreshToken();
        return $this->resultRedirectFactory->create()->setPath('s200/oauth/');
    }
}
