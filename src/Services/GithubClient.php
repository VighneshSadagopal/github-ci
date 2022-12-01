<?php

namespace Drupal\githubci\Services;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class githubciClient.
 *
 * @package \Drupal\githubci\Services
 */
class GithubClient
{

  /**
   * Config Factory Interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Guzzle HTTP Client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   Config Factory Interface.
   * @param \GuzzleHttp\Client $client
   *   Guzzle HTTP Client.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger
   *   A logger instance.
   */
  public function __construct(ConfigFactoryInterface $config, Client $client, LoggerChannelFactoryInterface $logger)
  {
    $this->client = $client;
    $this->config = $config->get('githubci.settings');
    $this->logger = $logger->get('githubci');
  }
  /**
   * Makes an HTTP request.
   *
   * @param string $method
   *   Request method.
   * @param string $endpoint
   *   Request Endpoint.
   * @param array $options
   *   Request Options.
   *
   * @return array|null
   *   Response data.
   */
  protected function request($url, $access_token, $method, $endpoint, array $options = [])
  {
    $data = NULL;
    try {
      $uri = $url . $endpoint;
      $options['headers'] = ['Authorization' => 'Bearer ' . $access_token];
      $response = $this->client->request($method, $uri, $options);
      if ($response->getStatusCode() === Response::HTTP_OK) {
        $data = json_decode($response->getBody(), TRUE);
      }
    } catch (GuzzleException $e) {
      $this->logger->error($e->getMessage());
    }
    return $data;
  }

    /**
     * Retrieves per user commits in their request.
     *
     * @return array
     *   Response data.
     */
  public function getCommitsByUsername($url,$access_token,$name)
  {
      $data = $this->request(
        $url,
        $access_token,
        $this->config->get('endpoints.commits.name.method'),
        str_replace('{author}', $name, $this->config->get('endpoints.commits.name.uri'))
      );
   
      return $data;
  }

     /**
     * Retrieves per user commits in their request.
     *
     * @return array
     *   Response data.
     */
    public function getCommitsData($url,$access_token)
    {
        $data = $this->request(
          $url,
          $access_token,
          $this->config->get('endpoints.commits.base.method'),
          $this->config->get('endpoints.commits.base.uri')
        );
     
        return $data;
    }

  /**
   * Retrieves all users data in their request.
   *
   * @return array
   *   Response data.
   */
  public function getUsers($url, $access_token)
  {
    $data = $this->request(
      $url,
      $access_token,
      $this->config->get('endpoints.users.method'),
      $this->config->get('endpoints.users.uri')
    );
    return $data;
  }

    /**
     * Retrieves all the issues with status open in the request.
     *
     * @return array
     *   Response data.
     */
    public function getIssuesWithStateOpen($url,$access_token) {
      return $this->request(
        $url,
        $access_token,
        $this->config->get('endpoints.issues.status.open.method'),
        $this->config->get('endpoints.issues.status.open.uri')

      );
    }

    /**
     * Retrieves all the issues with status closed in the request.
     *
     * @return array
     *   Response data.
     */
    public function getIssuesWithStateClosed($url,$access_token) {
      return $this->request(
        $url,
        $access_token,
        $this->config->get('endpoints.issues.status.closed.method'),
        $this->config->get('endpoints.issues.status.closed.uri')

      );
    }

      /**
     * Retrieves per user commits in their request.
     *
     * @return array
     *   Response data.
     */
  public function getPullsByUsername($url,$access_token,$name)
  {
      $data = $this->request(
        $url,
        $access_token,
        $this->config->get('endpoints.pulls.name.method'),
        str_replace('{author}', $name, $this->config->get('endpoints.pulls.name.uri'))
      );
   
      return $data;
  }

  /**
     * Retrieves all the issues with status open in the request.
     *
     * @return array
     *   Response data.
     */
    public function getPullsWithStateOpen($url,$access_token) {
      return $this->request(
        $url,
        $access_token,
        $this->config->get('endpoints.pulls.status.open.method'),
        $this->config->get('endpoints.pulls.status.open.uri')

      );
    }

    /**
     * Retrieves all the issues with status closed in the request.
     *
     * @return array
     *   Response data.
     */
    public function getPullsWithStateClosed($url,$access_token) {
      return $this->request(
        $url,
        $access_token,
        $this->config->get('endpoints.pulls.status.closed.method'),
        $this->config->get('endpoints.pulls.status.closed.uri')

      );
    }

}
