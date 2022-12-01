<?php

namespace Drupal\githubci\Services;

use Drupal\githubci\Services\GithubClient;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Class blockStructure.
 *
 * @package \Drupal\githubci\Services
 */
class GithubUtils
{

    /**
     * Github Client.
     *
     * @var \Drupal\githubci\Services\GithubClient
     */
    protected $githubClient;

    /**
     * Class constructor.
     *
     * @param \Drupal\githubci\Services\GithubClient $githubClient
     *   Github Client Interface.
     */
    public function __construct(GithubClient $githubClient)
    {
        $this->githubClient = $githubClient;
    }


    /**
     * A common Block Structure .
     *
     */
    public function buildBlockStructure($valuead, $chartType)
    {
        $build['githubci'] = [
            '#theme' => 'demo_block',
        ];

        // attaching library to the page where this block is presented.
        $build['#attached']['library'][] = 'githubci/global-styling';

        // passing the value from php to javascript so that the chart type can be selected.
        $build['#attached']['drupalSettings']['githubci'] = [$valuead, $chartType];

        return $build;
    }

    /**
     * A function which show the total commits done by each users
     */
    public function getTotalCommitsByEachUser($url, $access_token)
    {
        // request to get all users .
        $users = $this->githubClient->getUsers($url, $access_token);

        $valuead = [
            ['Commiters', 'Total Commits'],
        ];
        // get count of commits as per the username.
        foreach ($users as $key => $user) {
            $name = $user['login'];

            $req = $this->githubClient->getCommitsByUsername($url, $access_token, $name);
            $count = count($req);
            array_push($valuead, [$name, $count]);
        }

        return $valuead;
    }

    /**
     * A function which show the total commits done by each users
     */
    public function getTotalIssuesByTheirState($url, $access_token)
    {
        // Defined a array with basic inital Values 
        $valuead = [
            ['Issue State', 'No. of Issue'],
        ];

        // Request to get data for all issues with status open.
        $openissues = $this->githubClient->getIssuesWithStateOpen($url, $access_token);
        array_push($valuead, ['Open', count($openissues)]);

        // Request to get data for all issues with status closed.
        $closedissues = $this->githubClient->getIssuesWithStateClosed($url, $access_token);
        array_push($valuead, ['Closed', count($closedissues)]);

        return $valuead;
    }

    /**
     * A function which show the total pulls done by each users
     */
    public function getTotalPullsByEachUser($url, $access_token)
    {
        // request to get all users .
        $users = $this->githubClient->getUsers($url, $access_token);

        $valuead = [
            ['Request Owner', 'Total Pull Request'],
        ];
        // get count of commits as per the username.
        foreach ($users as $key => $user) {
            $name = $user['login'];
            $req = $this->githubClient->getPullsByUsername($url, $access_token, $name);
            $count = count($req);
            array_push($valuead, [$name, $count]);
        }

        return $valuead;
    }

    /**
     * A function which show the total commits done by each users
     */
    public function getTotalPullsByTheirState($url, $access_token)
    {
        // Defined a array with basic inital Values 
        $valuead = [
            ['Pull State', 'No. of Pulls'],
        ];

        // Request to get data for all issues with status open.
        $openissues = $this->githubClient->getPullsWithStateOpen($url, $access_token);
        array_push($valuead, ['Open', count($openissues)]);

        // Request to get data for all issues with status closed.
        $closedissues = $this->githubClient->getPullsWithStateClosed($url, $access_token);
        array_push($valuead, ['Closed', count($closedissues)]);

        return $valuead;
    }

    public function getTotalCommitsOverThePeriod($url, $access_token)
    {
        $mon = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July ',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];
        // Defined a array with basic inital Values 
        $valuead = [
            ['Month', 'No. of Commits'],
        ];

        $commits = $this->githubClient->getCommitsData($url, $access_token);

        foreach($mon as $monvalue){
            $data = $this->getCommitsInMonth($commits,$monvalue);
            array_push($valuead,[$monvalue,count($data)]);
        }

        return $valuead;
    }

    /**
     * function for getting count of commits made in a month
     */
    public function getCommitsInMonth($array, $month)
    {
        $filtered_commits = array_filter($array, function ($data) use ($month) {
            return (date('F', strtotime($data['commit']['author']['date'])) == $month);
        });

        return $filtered_commits;
    }
}
