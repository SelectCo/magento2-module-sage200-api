<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Model;

use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\OAuth\Bootstrap;
use SelectCo\Sage200Api\Model\OAuth\SageToken;

class Connector extends Bootstrap
{
    /**
     * @var SageToken
     */
    private $sageToken;

    public function __construct(SageToken $sageToken, Data $data)
    {
        parent::__construct($data);
        $this->sageToken = $sageToken;
    }

    /**
     * @param string $endpoint
     * @param string $method
     * @param string|null $queryParam
     * @param string|null $sage200xCompany
     * @return string|null
     */
    public function send(string $endpoint, string $method, ?string $queryParam = null, ?string $sage200xCompany = null): ?string
    {
        $methods = ['POST', 'GET', 'DEL', 'PUT', 'PATCH', 'DELETE'];
        if (!$this->helper->isModuleEnabled() || !$this->checkStatus() || !in_array($method, $methods))
        {
            return null;
        }

        $curlHeaders = array(
            'ocp-apim-subscription-key: ' . $this->helper->getConfigValue(self::OAUTH_DEVELOPER_SUBSCRIPTION_KEY),
            'Content-Type: application/json',
            'X-Site: ' . $this->helper->getConfigValue(self::OAUTH_X_SITE_ID),
            'Authorization: Bearer ' . $this->sageToken->getToken()
        );

        if ($sage200xCompany !== null) {
            $curlHeaders[] = 'X-Company: ' . $sage200xCompany;
        } elseif ($xCompany = $this->helper->getConfigValue(self::SAGE200_X_COMPANY_ID)) {
            $curlHeaders[] = 'X-Company: ' . $xCompany;
        }

        if ($queryParam) {
            $endpoint = $endpoint . '?' . $queryParam;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->helper->getBaseUrl() . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $curlHeaders,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    /**
     * @return bool
     */
    public function checkStatus(): bool
    {
        if (!$this->sageToken->checkAccessTokenExpiry()) {
            return false;
        }
        return true;
    }
}
