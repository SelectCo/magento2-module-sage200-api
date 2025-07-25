<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Controller\Adminhtml\OAuth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\OAuth\Provider;

class Submit extends Action
{
    const ADMIN_RESOURCE = 'SelectCo_Sage200Api::refresh_token';

    /**
     * @var Data
     */
    private $helper;
    /**
     * @var Provider
     */
    private $provider;
    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    public function __construct(Data $helper, Provider $provider, ResultFactory $resultFactory, Context $context)
    {
        parent::__construct($context);
        $this->helper = $helper;
        $this->provider = $provider;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Execute action based on request and return result
     *
     * @throws NotFoundException
     */
    public function execute()
    {
        $provider = $this->provider->getProvider();

        $authorizationUrl = $provider->getAuthorizationUrl(
            [   'audience' => 's200ukipd/sage200',
                'scope' => $this->helper->getConfigValue($this->provider::OAUTH_SCOPE_ACCESS)
            ]
        );

        $this->_getSession()->setOauth2state($provider->getState());

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setUrl($authorizationUrl);
    }
}
