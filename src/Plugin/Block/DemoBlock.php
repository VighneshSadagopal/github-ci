<?php

namespace Drupal\githubci\Plugin\Block;


use Drupal\Core\Block\BlockBase;
use Drupal\githubci\Services\GithubClient;
use Drupal\githubci\Services\GithubUtils;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides a 'Total Commits demo' Block.
 *
 * @Block(
 *   id = "demo_block",
 *   admin_label = @Translation("demo_block"),
 *   category = @Translation("demo block"),
 * )
 */



class DemoBlock extends BlockBase implements ContainerFactoryPluginInterface
{


    /**
     * Github Client.
     *
     * @var \Drupal\githubci\Services\GithubClient
     */
    protected $githubClient;

    /**
     * Github Utils.
     *
     * @var \Drupal\githubci\Services\GithubUtils
     */
    protected $githubUtils;


  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface.
   */
  protected $configFactory;


    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, GithubClient $githubClient, GithubUtils $githubUtils, ConfigFactoryInterface $configFactory)
    {
        $this->githubClient = $githubClient;
        $this->githubUtils = $githubUtils;
        $this->configFactory = $configFactory;


        parent::__construct($configuration, $plugin_id, $plugin_definition);
       
     
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('github.client'),
            $container->get('github.utils'),
            $container->get('config.factory')
        );
    }

    /**
     * Overrides \Drupal\block\BlockBase::settings().
     */
    public function defaultConfiguration()
    {
        return [
            'github_url' => (''),
            'access_token' => (''),
            'block_category' => (''),
            'chart_type' => (''),
        ];
    }


    /**
     * Overrides \Drupal\block\BlockBase::blockForm().
     */
    public function blockForm($form, FormStateInterface $form_state)
    {
        $term_name = [
            'total_commits' => 'Total Commits Made By Each User',
            'total_issues' => 'Total Issues with their State(Open or Closed)',
            'total_commits_over_time' => 'Total Commits growth chart In each month',
            'total_pulls_state' => 'Total Pull Request with their State(Open or Closed)'
        ];

        $chart_options = [
            "BarChart" => 'Bar Chart',
            "ColumnChart" => 'Column Chart',
            "DonutChart" => 'Donut Chart',
            "PieChart" => 'Pie Chart',
            "ScatterChart" => 'Scatter Chart',
            "BubbleChart" => 'Bubble Chart',
            "AreaChart" => 'Area Chart',
            "LineChart" => 'Line Chart',
            "Gauge" => 'Gauge',
            "ComboChart" => 'Combo Chart',
            "GeoChart" => 'Geo Chart',
            "TableChart" => 'Table Chart'
    
        ];

        $form['formblock_github_url'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Github URL'),
            '#default_value' => $this->configuration['github_url']?$this->configuration['github_url']:'',
            '#description' => $this->t('Enter a Github Repo Url ID'),
            '#required' => TRUE,
        ];


        $form['formblock_access_token'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Access Token'),
            '#default_value' => $this->configuration['access_token']?$this->configuration['access_token']:'',
            '#description' => $this->t('Enter the credentials for access token '),
            '#required' => TRUE,
        ];

        $form['formblock_block_category'] = [
            '#type' => 'select',
            '#title' => $this->t('Block Categories'),
            '#default_value' => $this->configuration['block_category']?$this->configuration['block_category']:'',
            '#options' => $term_name,
        ];

        $form['formblock_chart_type'] = [
            '#type' => 'radios',
            '#title' => $this->t('Chart Type'),
            '#default_value' => $this->configuration['chart_type']?$this->configuration['chart_type']:'',
            '#options' => $chart_options,
            '#attributes' => [
                'class' => ['chart-type-radio']
            ]
        ];

        $form['settings'] = [
            '#attached' => [
                'library' => ['githubci/global-styling'],
            ],
        ];

        return $form;
    }

    /**
     * Overrides \Drupal\block\BlockBase::blockSubmit().
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
        $this->configuration['github_url'] = $form_state->getValue('formblock_github_url');
        $this->configuration['access_token'] = $form_state->getValue('formblock_access_token');
        $this->configuration['block_category'] = $form_state->getValue('formblock_block_category');
        $this->configuration['chart_type'] = $form_state->getValue('formblock_chart_type');
    }



    /**
     * {@inheritdoc}
     */
    public function build()
    {
        $term_name = [
            'total_commits' => 'Total Commits Made By Each User',
            'total_issues' => 'Total Issues with their State(Open or Closed)',
            'total_commits_over_time' => 'Total Commits growth chart In each month',
            'total_pulls_state' => 'Total Pull Request with their State(Open or Closed)'
        ];

        $url = $this->configuration['github_url'];
        $access_token = $this->configuration['access_token'];
        $block_category = $this->configuration['block_category'];

        


        switch ($block_category) {
            case "total_commits":
                $arrayData = $this->githubUtils->getTotalCommitsByEachUser($url, $access_token);
                break;
            case "total_issues":
                $arrayData = $this->githubUtils->getTotalIssuesByTheirState($url, $access_token);
                break;
            case "total_commits_over_time":
                $arrayData = $this->githubUtils->getTotalCommitsOverThePeriod($url, $access_token);
                break;
            case "total_pulls_state":
                $arrayData = $this->githubUtils->getTotalPullsByTheirState($url, $access_token);
                break;
            default:
                echo "No Block Category is selected";
        }
       
        $chartType = $this->configuration['chart_type'];

        $build = $this->githubUtils->buildBlockStructure($arrayData, $chartType);
        $build['#attached']['drupalSettings']['githubci']['block_category'] = $term_name[$block_category];
        return $build;
    }
}
