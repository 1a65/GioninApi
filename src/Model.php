<?php

class Model extends Api {

    protected $_findTypes = [
        'all',
        'first'
    ];

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

        $this->_debug = $debug;

        if ($app) {
            $this->setApp($app);
        }

        if ($table) {
            $this->setTable($table);
        }
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

    public function find($type = 'all', $data = []){

        if (!in_array($type, $this->_findTypes)) {
            throw new Exception("Error type for find", 1);
        }

        return $this->setOperation('GET', $data);

    }

    public function findFirst($data){

        return $this->find('first', $data);

    }

    public function findById($id){
        return $this->find('first', ['_id' => $id]);
    }

}