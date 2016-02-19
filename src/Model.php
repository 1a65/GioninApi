<?php

namespace Gionin;

class Model extends Api {

    protected $_findTypes = [
        'all',
        'first'
    ];

    public $total;

    public function __construct(
        $user = '',
        $appUsername = '',
        $appSecret = '',
        $app = false,
        $table = false,
        $debug = false
    ){

        $this->setUser($user);

        $this->setCredentials($appUsername, $appSecret);

        $varifyValue = function($obj, $method, $value){
               $value && $obj->$method($value);
        };

        $varifyValue($this, 'setApp', $app);
        $varifyValue($this, 'setTable', $table);
        $varifyValue($this, 'setDebug', $debug);

    }

    protected function setOperation($method, $data){
        $this->setTableUrl();
        $this->setMethod($method);
        $this->setData($data);
        return $this->request();
    }

    public function insert($data){
        return $this->setOperation('POST', $data);

    }

    public function update($data){
        return $this->setOperation('PUT', $data);

    }
    public function delete($data){
        return $this->setOperation('DELETE', $data);

    }

    public function find($type = 'all', $data = [], $page = 1, $limit = 20){

        if (!in_array($type, $this->_findTypes)) {
            throw new Exception("Error type for find", 1);
        }
        $fields = [];

        $data['json'] = json_encode([
            'q' => $data,
            'page' => $page,
            'limit' => $limit,
            'fields' => $fields,
            'order' => [
                 'default' => 'asc'
            ]
        ]);

        if($return = $this->setOperation('GET', $data)){
            if ($type=='first') {
                return $return[0];
            }
        }
        $this->total = $return['_total'];
        unset($return['_total']);
        return $return;

    }

    public function findAll($data = []){
        return $this->find('all', $data, 1 , 100000);
    }

    public function findFirst($data = []){

        return $this->find('first', $data);

    }

    public function findById($id){
        return $this->find('first', ['_id' => $id], 1, 1);
    }

}
