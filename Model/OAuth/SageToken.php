<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Model\OAuth;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken as SageAccessToken;
use Magento\Framework\Encryption\EncryptorInterface;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\Bootstrap;
use SelectCo\Sage200Api\Model\Token\AccessToken;

class SageToken extends Bootstrap
{
    /**
     * @var Provider
     */
    private $provider;
    /**
     * @var EncryptorInterface
     */
    private $_encryptor;
    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @param Data $data
     * @param Provider $provider
     * @param EncryptorInterface $encryptor
     * @param AccessToken $accessToken
     */
    public function __construct(Data $data, Provider $provider, EncryptorInterface $encryptor, AccessToken $accessToken)
    {
        parent::__construct($data);
        $this->provider = $provider;
        $this->_encryptor = $encryptor;
        $this->accessToken = $accessToken;
    }

    /**
     * Process new API token
     *
     * @param string $code
     * @return void
     */
    public function processToken(string $code): void
    {
        try {
            $accessToken = $this->provider->getProvider()->getAccessToken('authorization_code', [
                'code' => urldecode($code),
            ]);

            $this->saveAccessToken($accessToken);
            if ($accessToken->getRefreshToken() === null) {
                $this->saveRefreshTokenExpiry('REFRESH TOKEN NOT SET');
            } else {
                $this->saveRefreshTokenExpiry($this->helper->getConfigValue(self::OAUTH_REFRESH_TOKEN_LIFETIME));
            }
        } catch (IdentityProviderException $e) {
            //TODO - Add logging
        }
        $this->helper->clearConfigCache();
    }

    /**
     * Refresh access token
     *
     * @return bool
     */
    public function refreshAccessToken(): bool
    {
        if ($this->accessToken->getAccessToken() && $this->accessToken->getAccessToken()->getRefreshToken() !== null) {
            $provider = $this->provider->getProvider();

            try {
                $newAccessToken = $provider->getAccessToken('refresh_token', [
                    'refresh_token' => $this->accessToken->getAccessToken()->getRefreshToken()
                ]);
                $this->saveAccessToken($newAccessToken);
                $this->helper->clearConfigCache();
            } catch (IdentityProviderException $e) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Save access token
     *
     * @param SageAccessToken $token
     * @return void
     */
    public function saveAccessToken(SageAccessToken $token): void
    {
        $this->helper->setConfigValue(
            self::OAUTH_ACCESS_TOKEN,
            $this->_encryptor->encrypt(json_encode($token->jsonSerialize()))
        );
    }

    /**
     * Reset refresh token expiry date
     *
     * @param int|string $expiry
     * @return void
     */
    public function saveRefreshTokenExpiry($expiry): void
    {
        $this->helper->setConfigValue(self::OAUTH_REFRESH_TOKEN_EXPIRY, time() + ((int)$expiry * 24 * 60 * 60));
    }
}
