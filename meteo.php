<?php
/*
Plugin Name: meteo
Plugin URI: http://meteo.com/
Description: Ce plugin est un TP.
Version: 0.0.1
Author: Isabu77
Author URI: http://meteo.com/
License: GPL3
Text Domain: meteo
*/

// Affichage du shortcode [meteo]ville[/meteo]
function fctmeteo($param)
{
    //var_dump($param);
    // les paramètres : la ville
    // $atts = 
    //     shortcode_atts(
    //         array('ville' => 'Paris'),
    //         array('date' => ''),
    //         array('temp' => ''),
    //         $param,
    //         "meteo"
    //     )
    //     ;

    // la météo du jour de la ville
    $weather = meteo::getMeteo($param['ville']);
    
    // pour demain (appel de l'api avec forecast)
    $datetime = new DateTime('tomorrow');
    $getDate = $datetime->format('Y-m-d');

    // XML résultat affiché par le shortcode
    return "<h2>la température à " . $param['ville'] 
    . " pour le " . $getDate . " sera  " . $weather->temperature['value']
    . " °C </h2>";

}

// le WIDGET afficheMeteo
class afficheMeteo extends WP_Widget
{
    public function __construct()
    {
        parent::__construct('idAfficheMeteo', 'Affiche Meteo', array('description' => 'la Météo du lendemain'));
    }

    public function widget($args,$instance){
        // application d'un filtre
        //$meteo = apply_filters('widget_text', $instance['meteo']);
        $ville = "Moulins";
        // quelle ville ? 
        // lecture dans la base de la ville
        global $wpdb;
        $table =$wpdb->prefix.'villemeteo';
        $query = "SELECT * FROM " . $table;
        $resultats = $wpdb->get_results($query);
        if ($resultats){
            foreach($resultats as $rep){
                $ville = $rep->ville;
                break;
            }
        }

        //$ville = get_field_name('ville');
        // affichage du widget
        echo "<h1>"."la météo de demain"."</h1>
        <article id=\"meteo\" class=\"card justify-content-center mb-4\">
        <p> " . afficheMeteo::viewWidget(meteo::getMeteo($ville), $ville) . "</p>
        </article>
        ";

    }

    // la vue du widget avec son style
    public static function viewWidget($getweather, $ville)
    {
        // pour la date du jour (appel de l'api avec weather)
        // $getName = $getweather->city['name'];
        // $getHumidity = $getweather->humidity['value'];
        // $getTemp = $getweather->temperature['value'];
        // $getminTemp = $getweather->temperature['min'];
        // $getmaxTemp = $getweather->temperature['max'];
        // $getWindspeed = $getweather->wind->speed['value'];
        // $getClouds = $getweather->clouds['name'];
        // $getDate = substr($getweather->lastupdate['value'], 0, 10);
        
        // pour demain (appel de l'api avec forecast)
        $datetime = new DateTime('tomorrow');
        $getDate = $datetime->format('Y-m-d');
        $getJour = $datetime->format('l');
        $getName = $ville;
        $getHumidity = $getweather->humidity['value'];
        $getTemp = $getweather->temperature['value'];
        $getminTemp = $getweather->temperature['min'];
        $getmaxTemp = $getweather->temperature['max'];
        $getWindspeed = $getweather->windSpeed['mps'];
        $getClouds = $getweather->symbol['name'];

        return '
        <style type="text/css">
        #meteo {
            background: white;
            margin: auto;
            width: 800px;
            height: 500px;
            overflow: visible;
            opacity: 0.75;
            flex-direction: row;
            color: white;
        }
        
        h2:before{
        content:"";
        height:0px;
        }
        .tour {
            background: linear-gradient(#00728B, #3EC9BF);
            width: 600px;
            height: 400px;
            margin: auto;
            border-radius: 12px;
            padding: 20px 10px 5px 10px;
            box-shadow: 2px 2px 10px 1px #00728B;
        }
        
        .centre {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .centrebis {
            display: flex;
            justify-content: space-between;
            padding-bottom: 0;
            margin-bottom: 0;
        }
        
        .centre-in {
            padding-bottom: 0;
            margin-bottom: 0;
        }
        
        .marginpad0 {
            padding: 0;
            margin: 0;
        }
        
        .sizeJour {
            font-family: BebasNeueRegular;
            font-size: 1.4em;
            letter-spacing: .2rem;
            padding: 0;
            margin: 0;
        }

        .sizeMinMax {
            font-family: BebasNeueLight;
            letter-spacing: .1rem;
            font-size: 1.5em;
        }
        .sizeITemp  {
            font-size: 0.5em;
        }
        
        .sizeVille {
            font-family: BebasNeueLight;
            font-size: 2.2em;
            letter-spacing: .2rem;
        }
        
        .sizeDate {
            font-family: BebasNeueLight;
            font-size: 1.2em;
            letter-spacing: .1rem;
        }
        
        .sizeWind {
            padding: 0;
            margin: 0;
            font-size: 0.9em;
        }
        
        .sizeTemp {
            font-size: 4em;
        }
        
        .sizeDesc {
            font-family: BebasNeueLight;
            letter-spacing: .1em;
            font-size: 2em;
            text-align: center;
            padding: 0 0 5px 0;
            margin: 0 0 10px 0;
        }
        </style>' .
        '<div class="tour">' .
        '    <div class="row col-12">' .
        '        <div class="centre col-12">' .
        '            <div>' .
        '                <h2 id="ville" class="marginpad0 sizeVille"> ' . $getName . '</h2>' .
        '            </div>' .
        '            <div class="centre">' .
        '                <h2 id="tempmin" class="sizeMinMax"><i class="sizeITemp fa fa-long-arrow-alt-down" aria-hidden="true"></i>' . $getminTemp . '°</h2>' .
        '                <h2 id="tempmax" class="sizeMinMax"><i class="sizeITemp fa fa-long-arrow-alt-up" aria-hidden="true"></i>' . $getmaxTemp . '°</h2>' .
        '            </div>' .
        '        </div>' .
        '        <div class="centrebis col-12">' .
        '            <div class="centre-in">' .
        '                <h3 id="joursemaine" class="marginpad0 sizeJour">' . $getJour . '</h3>' .
        '                <h3 id="demain" class="marginpad0 sizeDate">' . $getDate . '</h3>' .
        '                <p id="vitessevent" class="marginpad0 sizeWind">Wind ' . $getWindspeed . 'km/h </p>' .
        '                <p id="humidite" class="marginpad0"><i class="fa fa-tint" aria-hidden="true"></i>' . $getHumidity . '%</p>' .
        '            </div>' .
        '            <div class="centre-in">' .
        '                <p id="temperature" class="centre-in marginpad0 sizeTemp"><i class="fa fa-cloud-sun" aria-hidden="true"></i> ' . $getTemp . '°' . '</p>' .
        '            </div>' .
        '        </div>' .
        '    </div>' .
        '    <h3 id="ciel" class="st4 sizeDesc">' . $getClouds . '</h3>' .
        '</div>';


    }    

}

// le PLUGIN meteo
class meteo
{
    public function __construct()
    {
        // ajouter le widget afficheMeteo
        add_action('widgets_init',function(){register_widget('afficheMeteo');});

        // ajout du shortcode [meteo]ville[/meteo]
        add_shortcode('meteo', 'fctmeteo');

        // ajout de l'adminstration du plugin (ville et token)
        add_action('admin_menu',array($this,'declareAdmin'));
    }

    public static function install()
    {
        meteo::install_db();
    }

    public static function uninstall()
    {
        meteo::uninstall_db();
    }

    public static function desactivate()
    {
    }

    public static function install_db()
    {
        global $wpdb;
        // la météo
        $wpdb->query("CREATE TABLE IF NOT EXISTS " . $wpdb->prefix 
        . "getmeteo (id int(11) AUTO_INCREMENT PRIMARY KEY, token varchar(24), jour varchar(255), ville varchar(255), weather longtext);");
        
        // la ville choisie par l'administration
        $wpdb->query("CREATE TABLE IF NOT EXISTS " . $wpdb->prefix 
        . "villemeteo (id int(11) AUTO_INCREMENT PRIMARY KEY, token varchar(24), ville varchar(255));");
    }

    public static function uninstall_db(){
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS
        ".$wpdb->prefix."getmeteo;");
    }

    // ADMINISTRATION du plugin : changer ville et token 
    public static function declareAdmin(){
        add_menu_page('Configuration Météo', 'Meteo',
        'manage_options', 'Meteo', array($this, 'menuHtml'));

    }

    // formulaire d'administration du plugin
    public static function menuHtml(){
        echo '<h1>'.get_admin_page_title().'</h1>';
        echo '<p>Page du plugin METEO : Changez la ville et son token</p>';
        echo "<form method='POST' action='#'>
        Ville : <input type='text' name='ville' id='ville' required/>
        Token : <input type='text' name='token' id='token' required/>
        <input type='submit' name='init'>
        </form>";

        if(isset($_POST['ville']) && isset($_POST['token'])){
            // insertion dans la base de la météo du jour
            global $wpdb;
            $table =$wpdb->prefix.'villemeteo';

            // remplacer la ville 
            $query = "SELECT * FROM " . $table;
            if ($wpdb->get_results($query)){
                $query = 'TRUNCATE TABLE '. $table;
                $wpdb->query($query);
            }
            $wpdb->insert( $table,
                array('token'=>$_POST['token'],
                        'ville' => $_POST['ville']
                    ));
        }

    }

    // appel à l'API météo une fois par jour et par ville
    public static function getMeteo($ville)
    {
        // lire dans la base la  météo lue pour demain
        $datetime = new DateTime('tomorrow');
        $datetime = $datetime->format('Y-m-d');

        global $wpdb;
        $query = "SELECT * FROM ".$wpdb->prefix."getmeteo";
        $resultats = $wpdb->get_results($query);

        $weatherStr = "";
        foreach($resultats as $rep){
            if(strstr($rep->jour,$datetime) != false && $rep->ville == $ville){
                $weatherStr = $rep->weather;
                break;
            }
        }

        //$url= "https://api.openweathermap.org/data/2.5/weather?q=".$ville."&type=accurate&units=metric&APPID=e90412f382450840141c857f1baac572&lang=fr&mode=xml";
        // TODO pour la météo de demain :
        // remplacer weather par forecast pour avoir 5 jours toutes les 3 heures
        // et explorer forecast->time['from'] pour trouver demain yyyy-mm-jjT09:00:00
        // time->temperature
        // time->humidity
        // time->clouds
        // time->windSpeed['mps']
        // pour la base : 
        // jour = demain
        // weather = time
        $url= "https://api.openweathermap.org/data/2.5/forecast?q=".$ville."&type=accurate&units=metric&APPID=e90412f382450840141c857f1baac572&lang=fr&mode=xml";

        // si pas trouvé : apppeler l'API
        if ($weatherStr == ""){
            // appel de l'api meteo en php : 
            // pour la météo du jour :
            //$weather = simplexml_load_file($url);
            
            // avec le wp_remote_request : 
            $weather_wp = wp_remote_request($url, array(
                'method' 	=> 'GET'
            )
            );
            $body = wp_remote_retrieve_body($weather_wp);
            $weather = simplexml_load_string($body);

            foreach($weather->forecast->time as $time){
                if (strstr($time['from'], $datetime) != false){
                    var_dump($time);
                    $weather = $time;
                    break;
                }
            }
    

            // insertion dans la base de la météo du jour
            global $wpdb;
            $table =$wpdb->prefix.'getmeteo';
            $wpdb->insert( $table,
            array('token'=>"12345",
                    'jour' => $datetime, //$weather->lastupdate['value'], 
                    'ville' => $ville, 
                    'weather' => $weather->asXML()
                ));
        }
        else{
            // sinon récupérer l'XML
            $weather = new SimpleXMLElement($weatherStr);
        }

        return($weather);

    }


}

new meteo();

register_activation_hook(__FILE__,array('meteo','install'));
register_deactivation_hook(__FILE__,array('meteo','desactivate'));
register_uninstall_hook(__FILE__,array('meteo','uninstall'));
