<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Model\OAuth;

use SelectCo\Sage200Api\Helper\Data;

class Bootstrap
{
    const OAUTH_CLIENT_ID = 'selectco_s200_api/api_config/client_id';
    const OAUTH_CLIENT_SECRET = 'selectco_s200_api/api_config/client_secret';
    const OAUTH_DEVELOPER_SUBSCRIPTION_KEY = 'selectco_s200_api/api_config/developer_subscription_key';
    const OAUTH_X_SITE_ID = 'selectco_s200_api/api_config/x_site_id';
    const SAGE200_X_COMPANY_ID = 'selectco_s200_api/api_config/x_company_id';
    const AUTHORIZATION_SERVER_AUTHORIZE_URL = 'selectco_s200_api/api_config/auth_url';
    const AUTHORIZATION_SERVER_ACCESS_TOKEN_URL = 'selectco_s200_api/api_config/token_url';
    const AUTHORIZATION_SERVER_RESOURCE_OWNER_URL = 'selectco_s200_api/api_config/resource_owner_url';
    const OAUTH_REFRESH_TOKEN_LIFETIME = 'selectco_s200_api/api_config/refresh_token_lifetime';
    const OAUTH_SCOPE_ACCESS = 'selectco_s200_api/api_config/scope_access';
    const OAUTH_ACCESS_TOKEN = 'selectco_s200_api/token/access_token';
    const OAUTH_REFRESH_TOKEN_EXPIRY = 'selectco_s200_api/token/refresh_token_expiry';
    const CLIENT_REDIRECT_URI = 's200/oauth/callback';

    /**
     * @var Data
     */
    protected $helper;

    public function __construct(Data $data)
    {
        $this->helper = $data;
    }
}
