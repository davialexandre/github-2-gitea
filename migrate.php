<?php

use Github\ResultPager;
use Symfony\Component\Dotenv\Dotenv;

require_once "vendor/autoload.php";

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');
$ignoredRepos = explode(',', getenv('IGNORE_REPO'));

$client = new \Github\Client();

$client->authenticate(
  getenv('GITHUB_TOKEN'),
  NULL,
  \Github\Client::AUTH_HTTP_TOKEN
);

$pager = new ResultPager($client);
$userApi = $client->api('user');
$userApi->setPerPage(10);
$repos = $userApi->myRepositories(['affiliation' => 'owner']);
$myRepositories = $pager->fetchAll($userApi, 'myRepositories', [['affiliation' => 'owner']]);

echo count($myRepositories) . ' repositories found' . PHP_EOL;

if (empty($myRepositories)) {
  return;
}

echo 'Starting the migration' . PHP_EOL;

$httpClient = new GuzzleHttp\Client();

foreach ($myRepositories as $repo) {
  echo 'Importing ' . $repo['full_name'] . PHP_EOL;

  if (in_array($repo['name'], $ignoredRepos)) {
    echo '  This repository is ignored and will not be imported' . PHP_EOL;
    continue;
  }

  $res = $httpClient->request('POST', getenv('GITEA_API_ENDPOINT') . 'repos/migrate', [
    'query' => ['token' => getenv('GITEA_TOKEN')],
    'json' => [
      'clone_addr' => $repo['clone_url'],
      'description' => $repo['description'],
      'mirror' => TRUE,
      'private' => FALSE,
      'repo_name' => $repo['name'],
      'uid' => 1,
    ]
  ]);

}

echo 'Migration finished'. PHP_EOL;
