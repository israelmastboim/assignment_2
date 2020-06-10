<?php
require './vendor/autoload.php';
use App\DBActions;
use App\ScrapingActions;

define("WIKI_BASE_URL", "https://en.wikipedia.org");

$db = new DBActions();
$scraping = new ScrapingActions();
if (isset($_POST['network_id'])) {
    $network_detail = $db->getNetworkDetails($_POST['network_id']);
    if ($network_detail) {
        $network_detail['wiki_url'] = '';
        $network_detail['bands'] = stripslashes($network_detail['bands']);
    }
    echo json_encode($network_detail);
    exit();
}

if (isset($_POST['country_id'])) {
    $networks = $db->getNetworksOfCountry($_POST['country_id']);
    if (!count($networks)) {
        $country_url = $db->getCountryById($_POST['country_id']);
        $country_networks = $scraping->getNetworksOfCountry($_POST['country_id'], WIKI_BASE_URL . $country_url['wiki_url']);
        foreach ($country_networks as $network) {
            $db->addNetwork($network);
        }
    }
    $networks = $db->getNetworksOfCountry($_POST['country_id']);

    if($networks) {
        echo json_encode($networks);
    }
    exit();
}
