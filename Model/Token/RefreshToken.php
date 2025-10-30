<?php

namespace SelectCo\Sage200Api\Model\Token;

use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\Bootstrap;

class RefreshToken extends Bootstrap
{
    /**
     * @var AccessToken
     */
    private $accessToken;

    public function __construct(Data $data, AccessToken $accessToken)
    {
        parent::__construct($data);
        $this->accessToken = $accessToken;
    }

    /**
     * Get the saved refresh token
     *
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        if ($this->accessToken->getAccessToken()) {
            return $this->accessToken->getAccessToken()->getRefreshToken();
        }
        return null;
    }

    /**
     * Check API refresh token expiry
     *
     * @return array|bool
     */
    public function checkRefreshTokenExpiry()
    {
        (int)$tokenExpiry = $this->helper->getConfigValue(self::OAUTH_REFRESH_TOKEN_EXPIRY);
        if ($this->getRefreshToken() === null || $tokenExpiry < time()) {
            return false;
        }
        return [
            'minutesToExpire' => round(($tokenExpiry - time()) / 60, 2, 2),
            'hoursToExpire' => round(($tokenExpiry - time()) / 60 / 60, 2, 2),
            'daysToExpire' => round(($tokenExpiry - time()) / 60 / 60 / 24, 2, 2),
            'expiryDate' => date('d/m/Y H:i:s', $tokenExpiry)
        ];
    }
}
