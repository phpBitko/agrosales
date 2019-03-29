<?php

namespace AppBundle\Service\ApiClient;

use GuzzleHttp\{
    Client, RequestOptions
};
use GuzzleHttp\Exception\ClientException;
use Psr\Log\LoggerInterface;

class OsmRequestClient
{
    /** @var Client $client */
    private $client;

    /** @var array $headers */
    private $headers = [];

    /** @var string $lastErrorMessage */
    private $lastErrorMessage;

    private $isConnect;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(string $baseUri)
    {
        $this->client = new Client([
            'base_uri' => $baseUri
        ]);
    }

    /**
     * @return null|string
     */
    public function getLastErrorMessage(): ?string
    {
        return $this->lastErrorMessage;
    }


    /**
     * @param string $osmServicesApiClientId
     * @param string $osmServicesApiClientSecret
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     *
     * @required
     *
     */
    public function setAccessToken(string $osmServicesApiClientId, string $osmServicesApiClientSecret)
    {
        try {
            $response = $this->client->request('POST', '/oauth/v2/token', [
                RequestOptions::FORM_PARAMS => [
                    'client_id' => $osmServicesApiClientId,
                    'client_secret' => $osmServicesApiClientSecret,
                    'grant_type' => 'client_credentials'
                ],
                RequestOptions::VERIFY => false
            ]);
            $contents = json_decode($response->getBody()->getContents(), true);

            if (is_array($contents) && array_key_exists('access_token', $contents)) {
                $this->headers['Authorization'] = sprintf('Authorization: Bearer %s', $contents['access_token']);
                return $this->isConnect = true;
            }

            $this->lastErrorMessage = $response->getBody()->getContents();

        } catch (ClientException $exception) {
            $msg = $exception->getResponse()->getBody()->getContents();
            $msg = json_decode($msg, true);
            $this->lastErrorMessage = $msg['error_description'];
            $this->getLogger()->critical($exception->getResponse()->getBody()->getContents());

        } catch (\Exception $e) {
            $this->lastErrorMessage = 'Виникла помилка доступу до API E-сервісу!';
            $this->getLogger()->critical($e->getMessage());
            $this->lastErrorMessage = $e->getMessage();
        }

        return $this->isConnect = false;

    }

    /**
     * @return mixed
     */
    public function isConnect()
    {
        return $this->isConnect;
    }


    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @param array $data
     * @return bool|mixed
     */
    public function getClosestObject(array $data)
    {
        try {

            $response = $this->client->post('/api/getClosestObjects', [
                RequestOptions::HEADERS => $this->headers,
                RequestOptions::JSON => $data,
                RequestOptions::SYNCHRONOUS => true,
                RequestOptions::VERIFY => false,
            ]);

            $contents = json_decode($response->getBody()->getContents(), true);

            if (is_array($contents)) {
                return $contents;
            }

        } catch (ClientException  $exception) {

            $msg = $exception->getResponse()->getBody()->getContents();
            $msg = json_decode($msg, true);
            $this->lastErrorMessage = $msg['error_description'];
            $this->getLogger()->critical($exception->getResponse()->getBody()->getContents());
        } catch (\Exception $exception) {
            $this->lastErrorMessage = 'Виникла критична помилка.';
            $this->getLogger()->critical($exception->getMessage());
        }

        return false;
    }

}