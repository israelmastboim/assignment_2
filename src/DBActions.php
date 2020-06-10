<?php


namespace App;
use PDO;

class DBActions
{
    private $pdo;

    const db_file = "networks_code.db";
    public function __construct() {
        $this->createDbConnection();
    }
    public function createDbConnection() {
        $this->pdo = new PDO("sqlite:".__DIR__."/" . self::db_file);
    }

    public function createTables() {
        $commands = ['CREATE TABLE IF NOT EXISTS countries (
                        country_id   INTEGER PRIMARY KEY,
                        country_name TEXT NOT NULL,
                        wiki_url
                      )',
            'CREATE TABLE IF NOT EXISTS networks (
                    network_id INTEGER PRIMARY KEY,
                    network_name  VARCHAR (255),
                    network_operator_name VARCHAR (255),
                    operational_status VARCHAR (255),
                    mnc VARCHAR (255),
                    bands VARCHAR (255),
                    mcc INTEGER NOT NULL
                )'];
        foreach ($commands as $command) {
            $this->pdo->exec($command);
        }
    }

    public function getCountries() {
        $stm = $this->pdo->query("SELECT * FROM countries");

        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNetworks() {
        $stm = $this->pdo->query("SELECT * FROM networks");

        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNetworksOfCountry($country_code) {
        $stm = $this->pdo->prepare("SELECT * FROM networks WHERE mcc =?");
        $stm->execute([(int)$country_code]);
        return $stm->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addCountry($country) {
        $stm = $this->pdo->prepare("INSERT INTO countries (country_id,country_name,wiki_url) VALUES (?,?,?)");
        $stm->bindValue(1, $country['id']);
        $stm->bindValue(2, $country['name']);
        $stm->bindValue(3, $country['url']);
        return $stm->execute();

    }

    public function getCountryById($country_id) {
        $stm = $this->pdo->prepare("SELECT * FROM countries WHERE country_id = ?");
        $stm->execute([$country_id]);
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

    public function addNetwork($network) {
        $stm = $this->pdo->prepare("INSERT INTO networks
                                    (network_name,network_operator_name,operational_status,mnc,bands,mcc) VALUES (?,?,?,?,?,?)");
        if ($stm) {
            $stm->bindValue(1, $network['network_name']);
            $stm->bindValue(2, $network['network_operator_name']);
            $stm->bindValue(3, $network['operational_status']);
            $stm->bindValue(4, $network['mnc']);
            $stm->bindValue(5, $network['bands']);
            $stm->bindValue(6, $network['mcc']);
            $stm->execute();
            return $stm->fetch();
        }

    }

    public function getNetworkDetails($network_id) {
        $stm = $this->pdo->prepare("SELECT * FROM networks 
                                    LEFT JOIN countries
                                    ON networks.mcc = countries.country_id
                                    WHERE network_id = ?");
        $stm->bindValue(1, (int)$network_id);
        $stm->execute();
        return $stm->fetch(PDO::FETCH_ASSOC);
    }

}