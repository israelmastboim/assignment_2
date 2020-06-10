<?php


namespace App;
use PHPHtmlParser\Dom;

class ScrapingActions
{
    private $dom;
    function __construct()
    {
        $this->dom = new Dom;
    }

    function getCountries($url) {
        $this->dom->loadFromUrl($url);
        $contents = $this->dom->find('table');
        $country_list = array();
        foreach ($contents as $content)
        {
            if ($content->getAttribute('class') == "wikitable sortable") {
                foreach ($content->find('tbody > tr') as $row) {
                    $fields = $row->find('td');
                    if($row->find('td')[0]->innerHtml != null) {
                        $country_code = str_replace(' ', '', $fields[0]->innerHtml);
                        if ($fields[1] && count($fields[1]->find('a'))) {
                            $country_name = $fields[1]->find('a')->innerHtml;
                        }
                        if($fields[3] && $fields[3]->find('a') && count($fields[3]->find('a')) > 0){
                            $networks_url = $fields[3]->find('a')->getAttribute('href');
                        }
                        if ($country_code && $country_name && $networks_url) {
                            array_push($country_list, array('id' => $country_code, 'name' => $country_name, 'url' => $networks_url));
                        }
                    }
                }
            }

        }
        return $country_list;

    }

    function getNetworksOfCountry($country_code, $url) {
        $this->dom->loadFromUrl($url);
        $rows = $this->dom->find('tr');
        $networks_list = array();
        $counter = 0;
        foreach ($rows as $row) {
            if(count($row->find('td')) > 0 && $row->find('td')[0]->innerHtml == $country_code) {
                $fields = $row->find('td');
                $network_name = $fields[2]->innerHtml;
                $network_operator_name = $fields[3]->innerHtml;
                if (count($fields[2]->find('a'))) {
                    $network_name = $fields[2]->find('a')->innerHtml;
                }
                if (count($fields[3]->find('a'))) {
                    $network_operator_name = $fields[3]->find('a')->innerHtml;
                }
                $network = array(
                    'network_name' => $network_name,
                    'network_operator_name' => $network_operator_name,
                    'operational_status' => $fields[4]->innerHtml,
                    'mnc' => $fields[1]->innerHtml,
                    'bands' => str_replace('\\', '', $fields[5]->innerHtml) ,
                    'mcc' => $country_code,
                );
                array_push($networks_list, $network);
            }
        }
        return $networks_list;
    }

}