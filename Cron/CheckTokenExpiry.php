<?php

namespace SelectCo\Sage200Api\Cron;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Mail\Email;
use SelectCo\Sage200Api\Model\Token\AccessToken;
use SelectCo\Sage200Api\Model\Token\RefreshToken;

class CheckTokenExpiry
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
     * @var Email
     */
    private $email;

    public function __construct(
        Data $helper,
        AccessToken $accessToken,
        RefreshToken $refreshToken,
        Email $email
    ) {
        $this->helper = $helper;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->email = $email;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws MailException
     */
    public function checkAccessTokenExpiry()
    {
        if (!$this->helper->isModuleEnabled() || !$this->helper->isNotificationsEnabled()) {
            return;
        }

        $token = $this->accessToken->getAccessToken();
        if ($token === null || $token->hasExpired()) {
            $this->email->send($this->helper->getAccessTokenFailedTemplate());
        }
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws MailException
     */
    public function checkRefreshTokenExpiry()
    {
        if (!$this->helper->isModuleEnabled() || !$this->helper->isNotificationsEnabled()) {
            return;
        }

        $tokenExpiry = $this->refreshToken->checkRefreshTokenExpiry();
        if (!$tokenExpiry || !is_array($tokenExpiry) || !array_key_exists('daysToExpire', $tokenExpiry)) {
            $this->email->send($this->helper->getRefreshTokenExpiringTemplate());
        }

        if ($tokenExpiry['daysToExpire'] < $this->helper->getRefreshTokenDaysExpiry()) {
            $this->email->send($this->helper->getRefreshTokenExpiringTemplate());
        }
    }
}
