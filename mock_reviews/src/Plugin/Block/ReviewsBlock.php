<?php

namespace Drupal\mock_reviews\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'Reviews' block.
 *
 * @Block(
 *   id = "reviews_block",
 *   admin_label = @Translation("Reviews Block"),
 *   category = @Translation("Custom")
 * )
 */
class ReviewsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The HTTP client.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $httpClient;

  /**
   * Constructs a new ReviewsBlock.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The HTTP client.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http_client) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->httpClient = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'reviews_count' => 3,
      'show_rating' => TRUE,
      'show_date' => TRUE,
      'show_verified' => TRUE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['reviews_count'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of reviews to display'),
      '#default_value' => $config['reviews_count'],
      '#min' => 1,
      '#max' => 10,
    ];

    $form['show_rating'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show star rating'),
      '#default_value' => $config['show_rating'],
    ];

    $form['show_date'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show review date'),
      '#default_value' => $config['show_date'],
    ];

    $form['show_verified'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show verified badge'),
      '#default_value' => $config['show_verified'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['reviews_count'] = $values['reviews_count'];
    $this->configuration['show_rating'] = $values['show_rating'];
    $this->configuration['show_date'] = $values['show_date'];
    $this->configuration['show_verified'] = $values['show_verified'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $reviews_count = $config['reviews_count'];
    $url = "http://localhost/drupal9_project/mock-reviews";

    // print_r('test');
    // exit;

    try {
      $response = $this->httpClient->request('GET', $url, [
        'verify' => false, // ⚠️ Only use this in dev or with trusted endpoints
      ]);

      $data = json_decode($response->getBody()->getContents(), TRUE);
      if (!is_array($data)) {
        throw new \Exception('Invalid JSON response');
      }

      return [
        '#theme' => 'mock_reviews_block',
        '#reviews' => array_slice($data, 0, $reviews_count),
        '#config' => $config,
        '#attached' => [
          'library' => ['mock_reviews/reviews'],
        ],
        '#cache' => [
          'tags' => ['mock_reviews_block'],
          'contexts' => ['url.path'],
          'max-age' => 3600,
        ],
      ];
    }
    catch (\Exception $e) {
      return [
        '#markup' => $this->t('Unable to load reviews at this time. Error: @error', [
          '@error' => $e->getMessage(),
        ]),
      ];
    }
  }

  /**
   * Gets the request URL for the mock reviews.
   *
   * @return string
   *   The request URL.
   */
  protected function getRequestUrl() {
    return \Drupal::request()->getSchemeAndHttpHost() . '/mock-reviews';
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['mock_reviews_block']);
  }

}
