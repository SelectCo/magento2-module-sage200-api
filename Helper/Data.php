<?php

declare(strict_types=1);

namespace SelectCo\Sage200Api\Helper;

use SelectCo\Core\Helper\Data as CoreHelper;

class Data extends CoreHelper
{
    /**
     * General Group
     */
    const MODULE_ENABLED_CONFIG = 'selectco_s200_api/general/enabled';
    const DATE_FORMAT_CONFIG = 'selectco_s200_api/general/date_format';
    const BASE_URL_CONFIG = 'selectco_s200_api/general/base_url';

    /**
     * Token Group
     */
    const PAUSE_TOKEN_FAILED_NOTIFICATION_CONFIG = 'selectco_s200_api/token/pause_failed_notification';
    const REFRESH_TOKEN_EXPIRY_DAYS_CONFIG = 'selectco_s200_api/token/refresh_token_expiry_days';

    /**
     * Notification Group
     */
    const NOTIFICATION_ENABLED_CONFIG = 'selectco_s200_api/notification/enabled';
    const EMAIL_SELECT_CONFIG = 'selectco_s200_api/notification/email_select';
    const EMAIL_SENDER_CONFIG = 'selectco_s200_api/notification/identity';
    const EMAIL_SENDER_NAME_CONFIG = 'selectco_s200_api/notification/sender_name';
    const EMAIL_SENDER_EMAIL_CONFIG = 'selectco_s200_api/notification/sender_email';
    const EMAIL_TO_CONFIG = 'selectco_s200_api/notification/email_to';
    const COPY_METHOD_CONFIG = 'selectco_s200_api/notification/copy_method';
    const ACCESS_TOKEN_FAILED_TEMPLATE = 'selectco_s200_api/notification/access_token_failed';
    const REFRESH_TOKEN_EXPIRING_TEMPLATE = 'selectco_s200_api/notification/refresh_token_expiring';

    /**
     * Get is module enabled config
     *
     * @return bool|null
     */
    public function isModuleEnabled(): ?bool
    {
        return (bool)$this->getConfigValue(self::MODULE_ENABLED_CONFIG);
    }

    /**
     * Get date format
     *
     * @return string|null
     */
    public function getDateFormat(): ?string
    {
        return $this->getConfigValue(self::DATE_FORMAT_CONFIG);
    }

    /**
     * Get sage200 base url
     *
     * @return string|null
     */
    public function getBaseUrl(): ?string
    {
        return $this->getConfigValue(self::BASE_URL_CONFIG);
    }

    /**
     * Are token failed notifications paused
     *
     * @return bool|null
     */
    public function isTokenFailedNotificationsPaused(): ?bool
    {
        return (bool)$this->getConfigValue(self::PAUSE_TOKEN_FAILED_NOTIFICATION_CONFIG);
    }

    /**
     * Get number of days to check before refresh token expires
     *
     * @return int|null
     */
    public function getRefreshTokenDaysExpiry(): ?int
    {
        return (int)$this->getConfigValue(self::REFRESH_TOKEN_EXPIRY_DAYS_CONFIG);
    }

    /**
     * Get notifications enabled config
     *
     * @return bool|null
     */
    public function isNotificationsEnabled(): ?bool
    {
        return (bool)$this->getConfigValue(self::NOTIFICATION_ENABLED_CONFIG);
    }

    /**
     * Get whether email sender is custom or not
     *
     * @return bool|null
     */
    public function isEmailSelect(): ?bool
    {
        return (bool)$this->getConfigValue(self::EMAIL_SELECT_CONFIG);
    }

    /**
     * Get email sender name from config
     *
     * @return string|null
     */
    public function getEmailSender(): ?string
    {
        return $this->getConfigValue(self::EMAIL_SENDER_CONFIG);
    }

    /**
     * Gets custom email sender from config
     *
     * @return array|null
     */
    public function getCustomEmailSender(): ?array
    {
        $emailName = $this->getConfigValue(self::EMAIL_SENDER_NAME_CONFIG);
        $emailAddr = $this->getConfigValue(self::EMAIL_SENDER_EMAIL_CONFIG);

        if ($emailAddr && $emailName) {
            return [
                'name' => $emailName,
                'email' => $emailAddr
            ];
        }
        return null;
    }

    /**
     * Get emails config where notifications are sent
     *
     * @return string|null
     */
    public function getEmailTo(): ?string
    {
        return $this->getConfigValue(self::EMAIL_TO_CONFIG);
    }

    /**
     * How to send emails to multiple recipients
     *
     * @return string|null
     */
    public function getCopyMethod(): ?string
    {
        return $this->getConfigValue(self::COPY_METHOD_CONFIG);
    }

    /**
     * Get access token failed template from config
     *
     * @return mixed
     */
    public function getAccessTokenFailedTemplate()
    {
        return $this->getConfigValue(self::ACCESS_TOKEN_FAILED_TEMPLATE);
    }

    /**
     * Get access token failed template from config
     *
     * @return mixed
     */
    public function getRefreshTokenExpiringTemplate()
    {
        return $this->getConfigValue(self::REFRESH_TOKEN_EXPIRING_TEMPLATE);
    }
}
