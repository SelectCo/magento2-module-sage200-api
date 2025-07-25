<?php

declare(strict_types=1);

namespace SelectCo\Sage200Api\Cron;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Mail\Email;
use SelectCo\Sage200Api\Model\OAuth\SageToken;

class Token
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
     * @var Email
     */
    private $email;

    public function __construct(Data $helper, SageToken $sageToken, Email $email)
    {
        $this->helper = $helper;
        $this->sageToken = $sageToken;
        $this->email = $email;
    }

    /**
     * @return void
     * @throws LocalizedException
     * @throws MailException
     */
    public function refreshAccessToken()
    {
        if (!$this->helper->isModuleEnabled()) {
            return;
        }

        $accessToken = $this->sageToken->getAccessToken();
        if ($accessToken === null) {
            if (!$this->helper->isTokenFailedNotificationsPaused() && $this->helper->isNotificationsEnabled()) {
                $this->email->send($this->helper->getAccessTokenFailedTemplate());
            }
            return;
        }

        if ($accessToken->hasExpired() || $accessToken->getExpires() < 1800) {
            $this->sageToken->refreshToken();
        }
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

        $accessTokenExpiry = $this->sageToken->checkRefreshTokenExpiry();
        if (!$accessTokenExpiry) {
            $this->email->send($this->helper->getAccessTokenFailedTemplate());
        }

        if ($accessTokenExpiry['daysToExpire'] < $this->helper->getRefreshTokenDaysExpiry()) {
            $this->email->send($this->helper->getRefreshTokenExpiringTemplate());
        }
    }
}
