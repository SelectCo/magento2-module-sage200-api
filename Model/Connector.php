<?php
declare(strict_types=1);

namespace SelectCo\Sage200Api\Model;

use RuntimeException;
use SelectCo\Sage200Api\Helper\Data;
use SelectCo\Sage200Api\Model\OAuth\SageToken;
use SelectCo\Sage200Api\Model\Token\AccessToken;

class Connector extends Bootstrap
{
    /**
     * @var SageToken
     */
    private $sageToken;
    /**
     * @var AccessToken
     */
    private $accessToken;

    public function __construct(Data $data, SageToken $sageToken, AccessToken $accessToken)
    {
        parent::__construct($data);
        $this->sageToken = $sageToken;
        $this->accessToken = $accessToken;
    }

    /**
     * @param string $endpoint
     * @param string $method
     * @param string|null $queryParam
     * @param string|null $sage200xCompany
     * @return string|null
     */
    public function send(
        string $endpoint,
        string $method,
        ?string $queryParam = null,
        ?string $sage200xCompany = null
    ): ?string {
        $methods = ['POST', 'GET', 'DEL', 'PUT', 'PATCH', 'DELETE'];
        if (!$this->helper->isModuleEnabled() || !$this->checkStatus() || !in_array($method, $methods)) {
            return null;
        }

        $curlHeaders = array(
            'Content-Type: application/json',
            'X-Site: ' . $this->helper->getConfigValue(self::OAUTH_X_SITE_ID),
            'Authorization: Bearer ' . $this->accessToken->getToken()
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
        curl_setopt_array($curl, [
            CURLOPT_URL => str_replace(' ', '%20', $this->helper->getBaseUrl() . $endpoint),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $curlHeaders,
        ]);
        $response = curl_exec($curl);

        $curl = null;

        return $response;
    }

    /**
     * @return bool
     */
    public function checkStatus(): bool
    {
        if (!$this->accessToken->checkAccessTokenExpiry() && !$this->sageToken->refreshToken()) {
            return false;
        }
        return true;
    }

    /**
     * Retrieves a new instance of the Resource object.
     * Throws an exception if the EndPoints class does not exist.
     *
     * @return \SelectCo\Sage200ApiEndPoints\Resource|null
     * @throws RuntimeException
     */
    public function getResources(): ?\SelectCo\Sage200ApiEndPoints\Resource
    {
        if (!$this->helper->isModuleEnabled() || !$this->checkStatus()) {
            return null;
        }

        if (class_exists(\SelectCo\Sage200ApiEndPoints\Resource::class)) {
            $connector = new \SelectCo\Sage200ApiClient\Sage200Connector($this->accessToken->getToken());
            $connector->setSite($this->helper->getConfigValue(self::OAUTH_X_SITE_ID));
            $connector->setCompany($this->helper->getConfigValue(self::SAGE200_X_COMPANY_ID));

            return new \SelectCo\Sage200ApiEndPoints\Resource($connector);
        }
        throw new RuntimeException('Sage200ApiEndPoints not installed');
    }
}
