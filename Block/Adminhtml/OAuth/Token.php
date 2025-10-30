<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Block\Adminhtml\OAuth;

use Magento\Backend\Block\Template;
use Magento\Backend\Model\UrlInterface;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\Connector;
use SelectCo\Sage200Api\Model\Token\AccessToken;
use SelectCo\Sage200Api\Model\Token\RefreshToken;

class Token extends Template
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var AccessToken
     */
    private $accessToken;
    /**
     * @var RefreshToken
     */
    private $refreshToken;
    /**
     * @var UrlInterface
     */
    private $urlInterface;
    /**
     * @var Connector
     */
    private $connector;

    public function __construct(
        Data $helper,
        AccessToken $accessToken,
        RefreshToken $refreshToken,
        UrlInterface $urlInterface,
        Template\Context $context,
        Connector $connector,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->urlInterface = $urlInterface;
        $this->connector = $connector;
    }

    public function isModuleEnabled(): bool
    {
        return $this->helper->isModuleEnabled();
    }

    public function isTokenSet(): bool
    {
        if ($this->accessToken->getAccessToken()) {
            return true;
        }
        return false;
    }

    public function getAccessTokenExpiry(): string
    {
        if (($token = $this->accessToken->checkAccessTokenExpiry())) {
            return "Token expires in {$token['hoursToExpire']} hours at {$token['expiryDate']}";
        }
        return 'Token has already expired!';
    }

    public function getRefreshTokenExpiry(): string
    {
        if ($this->refreshToken->getRefreshToken() === null) {
            return 'Refresh token has not been set';
        }
        if (($token = $this->refreshToken->checkRefreshTokenExpiry())) {
            return "Token expires in {$token['daysToExpire']} days at {$token['expiryDate']}";
        }
        return 'Token has already expired!';
    }

    public function getNewTokenUrl(): string
    {
        return $this->urlInterface->getUrl('s200/oauth/submit/');
    }

    public function getTokenRefreshUrl(): string
    {
        return $this->urlInterface->getUrl('s200/oauth/refresh/');
    }

    public function isTokenExpired(): bool
    {
        return $this->accessToken->getAccessToken()->hasExpired();
    }

    public function getAvailableSites(): array
    {
        $results = json_decode($this->connector->send('sites', 'GET'), true);
        if ($results === null || array_key_exists('statusCode', $results)) {
            return [];
        }

        return $results;
    }
}
