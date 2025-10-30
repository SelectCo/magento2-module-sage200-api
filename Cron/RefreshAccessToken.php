<?php

namespace SelectCo\Sage200Api\Cron;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Mail\Email;
use SelectCo\Sage200Api\Model\OAuth\SageToken;
use SelectCo\Sage200Api\Model\Token\AccessToken;

class RefreshAccessToken
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
     * @var AccessToken
     */
    private $accessToken;
    /**
     * @var Email
     */
    private $email;

    public function __construct(Data $helper, SageToken $sageToken, AccessToken $accessToken, Email $email)
    {
        $this->helper = $helper;
        $this->sageToken = $sageToken;
        $this->accessToken = $accessToken;
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

        $token = $this->accessToken->getAccessToken();
        if ($token === null || $token->hasExpired()) {
            return;
        }

        $tokenExpiry = $token->getExpires() - time();
        if ($tokenExpiry < $this->helper->getAccessTokenExpiry() && !$this->sageToken->refreshAccessToken()) {
            $this->email->send($this->helper->getAccessTokenFailedTemplate());
        }
    }
}
