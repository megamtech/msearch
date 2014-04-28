<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Delete all http://127.0.0.1:8080/solr/collection1/update?commit=true -d '<delete><query>*:*</query></delete>'
//include '../mquery/cModel.php';

include AppRoot . AppQueryModule . 'cModel.php';

class cSolr implements cModel {

    public $column;
    private $query;
    private $client;
    private $result;
    private $document;
    public $condition;

    function __construct($core = "") {
        $connectionProps = array();
        $connectionProps['hostname'] = SOLR_HOST;
        $connectionProps['port'] = SOLR_PORT;
        $connectionProps['path'] = SOLR_PATH . $core;
        $connectionProps['wt'] = 'xml';

        $this->client = new SolrClient($connectionProps);
        $this->query = new SolrQuery();
        $this->document = new SolrInputDocument();
    }

    function read() {

        if (is_array($this->column)) {
            foreach ($this->column as $column) {
                $this->query->addField($column);
            }
        }
        if ($this->condition == "")
            $this->condition = '*:*';
        $this->query->setQuery($this->condition);
        $this->result = $this->client
                ->query($this->query);
        return $this->result->getResponse();
    }

    function create() {

        if (is_array($this->column)) {
            foreach ($this->column as $column => $value) {
                $this->document->addField($column, $value);
            }
            $this->result = $this->client->addDocument($this->document);
            $this->client->commit();
            return $this->result->getResponse();
        }
    }

    function update() {

    }

    function delete() {

        $this->client->deleteByQuery($this->condition);
        return $this->commit();
    }

    public function addOrderBy($orderby) {

    }

    public function addLimit($limit) {
        $this->query->setRows($limit);
        return $this;
    }

    public function addOffset($offset) {
        $this->query->setStart($offset);
        return $this;
    }

    public function addWhereCondition($condition) {

        $this->condition = $condition;
//        foreach ($this->condition as $key => $value) {
//
//        }
        return $this;
    }

    public function commit() {
        $solrAddress = SOLR_HOST . ':' . SOLR_PORT . SOLR_PATH;
        $output = array();
        $response = exec('curl ' . $solrAddress . '/update?commit=true', $output);
        return $output;
    }

}

//$cSolrObj = new cSolr("collection1");
//
//print_r($cSolrObj->addLimit(50)->addOffset(0)->read());
?>