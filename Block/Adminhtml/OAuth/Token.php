<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Block\Adminhtml\OAuth;

use Magento\Backend\Block\Template;
use Magento\Backend\Model\UrlInterface;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\Connector;
use SelectCo\Sage200Api\Model\OAuth\SageToken;

class Token extends Template
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var SageToken
     */
    private $sageToken;
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
        SageToken $sageToken,
        UrlInterface $urlInterface,
        Template\Context $context,
        Connector $connector,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->sageToken = $sageToken;
        $this->urlInterface = $urlInterface;
        $this->connector = $connector;
    }

    public function isTokenSet(): bool
    {
        if ($this->sageToken->getAccessToken()) {
            return true;
        }
        return false;
    }

    public function getAccessTokenExpiry(): string
    {
        $token = $this->sageToken->checkAccessTokenExpiry();
        if ($token) {
            return 'Token expires in ' . $token['secondsToExpire'] . ' seconds at ' . $token['expiryDate'];
        }
        return 'Token has already expired!';
    }

    public function getRefreshTokenExpiry(): string
    {
        $token = $this->sageToken->checkRefreshTokenExpiry();
        if ($token) {
            return 'Token expires in ' . $token['secondsToExpire'] . ' seconds at ' . $token['expiryDate'];
        }
        if ($this->sageToken->getRefreshToken() === null) {
            return 'Refresh token has not been set';
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
        return $this->sageToken->getAccessToken()->hasExpired();
    }

    public function getAvailableSites(): array
    {
        return json_decode($this->connector->send('sites', 'GET'), true);
    }
}
