<?php

namespace SelectCo\Sage200Api\Model\Token;

use League\OAuth2\Client\Token\AccessToken as SageAccessToken;
use SelectCo\Sage200Api\Model\Bootstrap;

class AccessToken extends Bootstrap
{
    /**
     * @var SageAccessToken|null
     */
    private $token = null;

    /**
     * Get saved access token
     *
     * @return SageAccessToken|null
     */
    public function getAccessToken(): ?SageAccessToken
    {
        if ($this->token) {
            return $this->token;
        }
        $accessToken = $this->helper->getConfigValue(self::OAUTH_ACCESS_TOKEN);

        if ($accessToken) {
            $this->token = new SageAccessToken(json_decode($accessToken, true));
        }
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        if ($this->getAccessToken()) {
            return $this->getAccessToken()->getToken();
        }
        return null;
    }

    /**
     * Check API token expiry
     *
     * @return array|bool
     */
    public function checkAccessTokenExpiry()
    {
        if (!$this->getAccessToken() || $this->getAccessToken()->hasExpired()) {
            return false;
        }
        $tokenExpires = $this->getAccessToken()->getExpires();
        if (!is_int($tokenExpires)) {
            return false;
        }

        return [
            'minutesToExpire' => round(($tokenExpires - time()) / 60, 2, 2),
            'hoursToExpire' => round(($tokenExpires - time()) / 60 / 60, 2, 2),
            'expiryDate' => date('d/m/Y H:i:s', $tokenExpires)
        ];
    }
}
