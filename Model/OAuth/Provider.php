<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Model\OAuth;

use League\OAuth2\Client\Provider\GenericProvider;
use Magento\Backend\Model\UrlInterface;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\Bootstrap;

class Provider extends Bootstrap
{
    /**
     * @var UrlInterface
     */
    private $urlInterface;

    public function __construct(Data $data, UrlInterface $urlInterface)
    {
        parent::__construct($data);
        $this->urlInterface = $urlInterface;
    }

    /**
     * @return GenericProvider
     */
    public function getProvider(): GenericProvider
    {
        return new GenericProvider([
            'clientId'                => $this->helper->getConfigValue(self::OAUTH_CLIENT_ID),
            'clientSecret'            => $this->helper->getConfigValue(self::OAUTH_CLIENT_SECRET),
            'redirectUri'             => rtrim($this->urlInterface->getUrl(self::CLIENT_REDIRECT_URI), "/"),
            'urlAuthorize'            => $this->helper->getConfigValue(self::AUTHORIZATION_SERVER_AUTHORIZE_URL),
            'urlAccessToken'          => $this->helper->getConfigValue(self::AUTHORIZATION_SERVER_ACCESS_TOKEN_URL),
            'urlResourceOwnerDetails' => $this->helper->getConfigValue(self::AUTHORIZATION_SERVER_RESOURCE_OWNER_URL),
        ]);
    }
}
