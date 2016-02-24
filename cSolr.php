<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//Delete all http://127.0.0.1:8080/solr/collection1/update?commit=true -d '<delete><query>*:*</query></delete>'
//include '../mquery/cModel.php';

require_once AppRoot . AppQueryModule . 'cModel.php';

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

        try {


            if (is_array($this->column)) {
                foreach ($this->column as $column => $value) {
                    //Used to identify/insert Multivalue based on the Value type if string it is scalar else if array then it is multi valued (to store array of values)
                    if (is_array($value)) {

                        foreach ($value as $index => $multivalue) {
                            $this->document->addField($column, $multivalue, $index);
                        }
                    } else {
                        $this->document->addField($column, $value);
                    }
                }
                $this->result = $this->client->addDocument($this->document, false, 1);
                $this->commit();
                return $this->result->getResponse();
            }
        } catch (Exception $ex) {
            print_r($ex);
            exit;
        }
    }

    function update() {
        $data = $this->column;
        unset($this->column);
        if ($data['id'] != '') {
            $this->condition = $data['id'];
        } else {
            debug_print_backtrace();
            echo "Solr id cannot be empty";
            exit;
        }
        $this->delete();
        $this->column = $data;
        return $this->create();
    }

    function delete() {
        if ($this->condition != '') {
            $this->client->deleteById($this->condition);
        } else {
            $this->client->deleteByQuery("*:*");
        }
        return $this->commit();
    }

    public function addOrderBy($orderby) {
        foreach ($orderby as $column => $order) {
            $order = $order == "asc" ? SolrQuery::ORDER_ASC : SolrQuery::ORDER_DESC;
            $this->query->addSortField($column, $order);
        }
    }

    public function addLimit($limit) {
        $this->query->setRows($limit);

        return $this;
    }

    public function addOffset($offset) {
        $this->query->setStart($offset);

        return $this;
    }

    public function addGroupBy($groupby) {

        return $this;
    }

    public function addWhereCondition($condition) {
//Possible comparison types

        $this->condition = $condition;
        if (!$condition['__AND__'] && !$condition['__OR__']) {
            foreach ($this->condition as $key => $value) {
                //encoding the user data so it wont break with space or special characters
                $condition_data .= $key . ":" . $value . ' AND ';
            }
            $this->condition = rtrim($condition_data, ' AND ');
        }
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
