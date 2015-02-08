<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * @author Admin
 */
// TODO: check include path
//ini_set('include_path', ini_get('include_path'));
// put your code here

define('AppRoot', dirname(__FILE__));
define('AppQueryModule', './../../' . 'mquery/src/');
/* * *
 * SOLR Search Settings
 */
define('SOLR_HOST', "192.168.1.200");
define('SOLR_PORT', "8080");
define('SOLR_PATH', "/solr/");
define('SOLR_CORE1', "collection1");
define('NO_OF_RECORDS_PER_PAGE', 10);
?>
