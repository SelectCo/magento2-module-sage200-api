<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Model\OAuth;

use InvalidArgumentException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Magento\Framework\Encryption\EncryptorInterface;
use SelectCo\Core\Helper\Data as ConfigHelper;

class SageToken extends Bootstrap
{
    /**
     * @var Provider
     */
    private $provider;

    /**
     * @var AccessToken|null
     */
    private $token = null;
    /**
     * @var EncryptorInterface
     */
    private $_encryptor;

    /**
     * @param ConfigHelper $data
     * @param Provider $provider
     * @param EncryptorInterface $encryptor
     */
    public function __construct(ConfigHelper $data, Provider $provider, EncryptorInterface $encryptor)
    {
        $this->provider = $provider;
        $this->_encryptor = $encryptor;
        parent::__construct($data);
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
        } catch (IdentityProviderException $e) {
            //TODO - ADD LOGGING
            if ($e->getMessage() === 'invalid_grant') {

            }
            var_dump($e->getMessage());
        }
    }

    /**
     * Get saved access token
     *
     * @return AccessToken|null
     */
    public function getAccessToken(): ?AccessToken
    {
        if ($this->token) {
            return $this->token;
        }
        $accessToken = $this->helper->getConfigValue(self::OAUTH_ACCESS_TOKEN);

        if ($accessToken) {
            try {
                $this->token = new AccessToken(json_decode($accessToken, true));

            } catch (InvalidArgumentException $e) {

            }
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
     * Get saved access token
     *
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        if ($this->getAccessToken()) {
            return $this->getAccessToken()->getRefreshToken();
        }
        return null;
    }

    /**
     * Save access token
     *
     * @param AccessToken $token
     * @return void
     */
    public function saveAccessToken(AccessToken $token): void
    {
        $this->helper->setConfigValue(self::OAUTH_ACCESS_TOKEN, $this->_encryptor->encrypt(json_encode($token->jsonSerialize())));

        if ($token->getRefreshToken() === null) {
            $this->saveRefreshTokenExpiry('REFRESH TOKEN NOT SET');
        } else {
            $this->saveRefreshTokenExpiry($this->helper->getConfigValue(self::OAUTH_REFRESH_TOKEN_LIFETIME));
        }

        $this->helper->clearConfigCache();
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

    /**
     * Refresh access token
     *
     * @return bool
     */
    public function refreshToken(): bool
    {
        if ($this->getAccessToken() && $this->getRefreshToken() !== null) {
            if ($this->getAccessToken()->hasExpired()) {
                $provider = $this->provider->getProvider();

                try {
                    $newAccessToken = $provider->getAccessToken('refresh_token', [
                        'refresh_token' => $this->getRefreshToken()
                    ]);
                    $this->saveAccessToken($newAccessToken);
                    $this->helper->clearConfigCache();
                } catch (IdentityProviderException $e) {
                    //TODO - Notify user there was an error
                    var_dump($e->getMessage());
                    return false;
                }
            }
            return true;
        }
        return false;
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

        return [
            'secondsToExpire' => $this->getAccessToken()->getExpires() - time(),
            'expiryDate' => date('d/m/Y H:i:s', $this->getAccessToken()->getExpires())
        ];
    }

    /**
     * Check API refresh token expiry
     *
     * @return array|bool
     */
    public function checkRefreshTokenExpiry()
    {
        $tokenExpiry = $this->helper->getConfigValue(self::OAUTH_REFRESH_TOKEN_EXPIRY);
        if ($this->getRefreshToken() === null || (int)$tokenExpiry < time()) {
            return false;
        }

        return [
            'secondsToExpire' => $tokenExpiry - time(),
            'expiryDate' => date('d/m/Y H:i:s', $tokenExpiry)
        ];
    }
}