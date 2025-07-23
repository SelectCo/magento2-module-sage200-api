<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Controller\Adminhtml\OAuth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use SelectCo\Sage200Api\Model\OAuth\SageToken;

class Callback extends Action
{
    const ADMIN_RESOURCE = 'SelectCo_Sage200Api::refresh_token';

    /**
     * @var Http
     */
    private $request;

    /**
     * @var SageToken
     */
    private $token;

    public function __construct(
            Http $request,
            SageToken $token,
            Context $context
    )
    {
        parent::__construct($context);
        $this->request = $request;
        $this->token = $token;
    }

    public function execute()
    {
        $oauth2state = $this->_getSession()->getOauth2state();
        $requestState = $this->request->getParam('state');

        if (empty($requestState) || empty($oauth2state) || $requestState !== $oauth2state) {
            $this->_getSession()->unsOauth2state();
        } else {
            $this->token->processToken($this->request->getParam('code'));
        }
        return $this->resultRedirectFactory->create()->setPath('s200/oauth/');
    }
}
