<?php

namespace Gionin;

/**
 * Class for using API with a model
 * @package default
 * @author  Raphael Giovanini
**/
class Model extends Api {

    protected $_findTypes = [
        'all',
        'first'
    ];
    protected $_conditions = [];
    protected $_fields = [];
    protected $_order = [
        'default' => 'asc'
    ];

    /**
     * Retorna o total da ultima busca
     *
     * @var int
     **/
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

    public function reset(){
        $this->_fields = [];
        $this->_order  = ['default' => 'asc'];
    }

    protected function setOrder($order = []){
        $order !== [] && $this->_order = $order;
    }

    protected function setFields($fields = []){
        $fields !== [] && $this->_fields = $fields;
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

    private function traitamentData($data){
        if(isset($data['order'])){
            unset($data['order']);
        }
        if(isset($data['fields'])){
            $this->traitamentFields($data);
            unset($data['fields']);
        }
        if (isset($data['conditions'])) {
            $this->_conditions = $data['conditions'];
            $this->traitamentOrder($data);
            $this->traitamentFields($data);
            return;
        }
        $this->_conditions = $data;
    }

    private function traitamentOrder($data){
        return isset($data['order']) && is_array($data['order']) && $this->setOrder($data['order']);
    }

    private function traitamentFields($data){
        isset($data['fields']) && is_array($data['fields']) && $this->setFields($data['fields']);
    }

    public function find($type = 'all', $data = [], $page = 1, $limit = 20){

        if (!in_array($type, $this->_findTypes)) {
            throw new Exception("Error type for find", 1);
        }
	$this->reset();
        $this->traitamentOrder($data);
        $this->traitamentData($data);

        $data['json'] = json_encode([
            'q' => $this->_conditions,
            'page' => $page,
            'limit' => $limit,
            'fields' => $this->_fields,
            'order' => $this->_order
        ]);

        if($return = $this->setOperation('GET', $data)){
            $this->total = $return['_total'];
            unset($return['_total']);
        }
        if(isset($return[0])){
            if ($type == 'first') {
                return $return[0];
            }
        }

        return $return;

    }

    public function findAll($data = [], $page = 1 , $limit = 1000000){
        return $this->find('all', $data, $page , $limit);
    }

    public function findFirst($data = []){

        return $this->find('first', $data);

    }

    public function findById($id){
        return $this->find('first', ['_id' => $id], 1, 1);
    }

}
