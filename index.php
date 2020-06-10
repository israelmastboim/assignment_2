<?php
require './vendor/autoload.php';

use App\DBActions;
use App\ScrapingActions;


define("GET_COUNTRIES_URL", "https://en.wikipedia.org/wiki/Mobile_country_code");

$db = new DBActions();
$db->createTables();
$scraping = new ScrapingActions();
$countries_in_db = $db->getCountries();
if (count($countries_in_db) < 1 ) {
    $countries = $scraping->getCountries(GET_COUNTRIES_URL);
    foreach ($countries as $country) {
        $db->addCountry($country);
    }
    $countries_in_db = $db->getCountries();
}



?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <style>
            select {
                margin: 10px 0;
                min-width: 100%;
            }
        </style>

    </head>
    <body>
    <div class="container">
        <div class="col">
            <div>
                <h1>Get Network Codes By Country</h1>
            </div>
            <div style="max-width: 45%">
                <div>
                    <label for="countries">Choose Country:</label>
                </div>
                <div>
                    <select onchange="getNetworksAjax(event)" name="countries" id="countries">
                        <option value="">Select Country</option>
                        <?php foreach($countries_in_db as $country){ ?>
                            <option value="<?php echo $country['country_id'];?>"><?php echo $country['country_name'];     ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label for="networks">Choose Network:</label>
                </div>
                <div>
                    <select  onchange="getNetworkDetails(event.target)" name="networks" id="networks">
                        <option value="">--------</option>
                    </select>
                </div>


            </div>
        </div>
        <div class="network_details col" style="display: none">
            <table class="table">
                <thead>
                    <tr>
                        <th>Country Name</th>
                        <th>Country Code</th>
                        <th>MCC</th>
                        <th>MNC</th>
                        <th>Brand</th>
                        <th>Operator Name</th>
                        <th>Operational Status</th>
                        <th>Bands</th>
                    </tr>
                </thead>

                <tbody>
                   <tr>

                       <td id="country_name"></td>
                       <td id="country_id"></td>
                       <td id="mcc"></td>
                       <td id="mnc"></td>
                       <td id="network_name"></td>
                       <td id="network_operator_name"></td>
                       <td id="operational_status"></td>
                       <td id="bands"></td>



                   </tr>
                </tbody>
            </table>
        </div>
        

    </div>

    <script>
        function getNetworksAjax(event) {
            $('.network_details').css('display', 'none');
            $.ajax({
                type: "POST",
                url: "/api.php",
                data: {country_id : event.target.value},
                success: function(data){
                    var networks = $('#networks');
                    networks.find('option').remove();
                    var dataObj = JSON.parse(data);
                    dataObj.map(net => {
                        networks.append($('<option>', {
                            value: net.network_id,
                            text: net.network_operator_name
                        }));
                    });
                    getNetworkDetails(document.getElementById('networks'));
                }
            });
        }

        function getNetworkDetails(ele) {
            $.ajax({
                type: "POST",
                url: "/api.php",
                data: {network_id: ele.value},
                cache: false,
                success: function(data){
                    $('.network_details').css('display', 'block');
                    var dataObj = JSON.parse(data);
                    console.log(dataObj);
                    Object.keys(dataObj).map(key => {
                        var ele =document.getElementById(key);
                        if (ele) {
                            ele.innerText = dataObj[key];
                        }
                    })

                }
            });
        }

    </script>


    </body>
</html>

