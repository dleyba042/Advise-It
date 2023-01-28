<?php 

ini_set('display_errors',1);
error_reporting(E_ALL);
date_default_timezone_set("America/Los_Angeles");

require_once("../Model/Model.php");

$model = new Model();

$model->initNewPlanYear($_POST["token"],$_POST["year"]);
