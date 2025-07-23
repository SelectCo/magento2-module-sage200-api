<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Controller\Adminhtml\OAuth;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use SelectCo\Core\Helper\Data as ConfigHelper;
use SelectCo\Sage200Api\Model\OAuth\Provider;

class Submit extends Action
{
    const ADMIN_RESOURCE = 'SelectCo_Sage200Api::refresh_token';

    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var ConfigHelper
     */
    private $helper;

    public function __construct(Provider $provider, ResultFactory $resultFactory, ConfigHelper $helper, Context $context)
    {
        parent::__construct($context);
        $this->provider = $provider;
        $this->resultFactory = $resultFactory;
        $this->helper = $helper;
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
