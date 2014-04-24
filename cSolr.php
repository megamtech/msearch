<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Delete all http://127.0.0.1:8080/solr/collection1/update?commit=true%20-d%20%27%3Cdelete%3E%3Cquery%3E*:*%3C/query%3E%3C/delete%3E%27
//include '../mquery/cModel.php';
include AppQueryModule . 'cModel.php';

class cSolr implements cModel
    {

    public $columns;
    private $query;
    private $client;
    private $result;
    private $document;

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

        if (is_array($this->columns)) {
            foreach ($this->columns as $column) {
                $this->query->addField($column);
            }
        }
        $this->result = $this->client
                ->query($this->query);
        return $this->result->getResponse();
    }

    function create() {

        if (is_array($this->columns)) {
            foreach ($this->columns as $column => $value) {
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

        $this->client->deleteByQueries($this->condition);
        return $this->client->commit();
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

        return $this;
    }

    }

$cSolrObj = new cSolr("collection1");

print_r($cSolrObj->addLimit(50)->addOffset(0)->read());
?>