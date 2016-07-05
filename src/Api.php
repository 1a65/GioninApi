<?php

namespace Gionin;

/**
 * Class to connection API in Gionin
 *
 * @package Gionin
 * @author Raphael Giovanini
 **/
class Api{

    protected $_debug = false;

    protected $_baseUrl = 'https://api.gionin.com';
    protected $_queryUrl = [
        'schema' => '/v1/{user}/{app}',
        'table'  => '/v1/{user}/{app}/{table}',
    ];

    protected $_user = '';
    protected $_authUser;
    protected $_authKey;
    protected $_method = 'GET';
    protected $_data = [];

    protected $app = '';
    protected $table = '';

    /**
     * Set credentials
     *
     * @param string $user Master account user
     * @author Raphael Giovanini
     **/
    public function setUser($user){
        $this->_user = $user;
    }

    /**
     * Set key to credentials
     *
     * @param string $key application user password
     * @author Raphael Giovanini
     **/
    public function setAuthKey($key){
        $this->_authKey = $key;
    }

    /**
     * Set user to credentials
     *
     * @param string $user application user
     * @author Raphael Giovanini
     **/
    public function setAuthUser($user){
        $this->_authUser = $user;
    }

    /**
     * Set credentials
     *
     * @param string $user application user
     * @param string $secret application user password
     * @author Raphael Giovanini
     **/
    public function setCredentials($user, $secret){
        $this->setAuthKey($secret);
        $this->setAuthUser($user);
    }

    /**
     * Set method to call api
     *
     * @param string $method Type of method that should be used ('GET', 'POST', 'PUT', 'DELETE', 'PATCH')
     * @author Raphael Giovanini
     **/
    public function setMethod($method){
        if (in_array($method, ['GET','POST','PUT','DELETE'])) {
            $this->_method = $method;
        }
    }

    /**
     * Set data to sent in api
     *
     * @param array $data Data to be sent along with the reques
     * @author Raphael giovanini
     **/
    public function setData($data = [])
    {
        $this->_data = $data;
    }

    /**
     * Set parameter app
     *
     * @return void
     * @author Raphael Giovanini
     **/
    public function setApp($v){
        $this->app = $v;
    }

    /**
     * Set parameter table
     *
     * @return void
     * @author Raphael Giovanini
     **/
    public function setTable($v){
        $this->table = $v;
    }

    /**
     * Set parameter url
     *
     * @return void
     * @author Raphael Giovanini
     **/
    public function setUrl($type = 'table'){
        $this->_url = $this->_baseUrl.$this->_queryUrl[$type];
    }

    /**
     * Set parameters user to url in api
     *
     * @return void
     * @author Raphael Giovanini
     **/
    protected function setUserUrl()
    {
        if (!isset($this->_user[1])) {
            throw new Exception("user not declared", 1);
        }
        $this->_url = str_replace('{user}', $this->_user, $this->_url);
    }

    /**
     * Set parameters app to url in api
     *
     * @return void
     * @author Raphael Giovanini
     **/
    protected function setAppUrl()
    {
        if (!isset($this->app[1])) {
            throw new Exception("App not declared", 1);
        }
        $this->_url = str_replace('{app}', $this->app, $this->_url);
    }

    /**
     * Set parameters table in url to api
     *
     * @return void
     * @author Raphael Giovanini
     **/
    protected function setTableUrl()
    {
        $this->setUrl('table');

        $this->setUserUrl();

        $this->setAppUrl();

        if (!isset($this->table[1])) {
            throw new Exception("Table not declared", 1);
        }
        $this->_url = str_replace('{table}', $this->table, $this->_url);
    }

    /**
     * Set parameters debug to teste APIi
     *
     * @return void
     * @author Raphael Giovanini
     **/
    public function setDebug($v){
	   $this->_debug = ($v) ?: false;
    }

    /**
     * API call method for sending requests using GET, POST, PUT, DELETE OR PATCH
     *
     * @return array
     * @author Raphael Giovanini
     **/
    public function request(){
        $url = $this->_url;

        if ($this->_method=='GET') {
            $url .= '?' . http_build_query($this->_data);
        }

        $ch = \curl_init();

        if (!empty($this->_headers)) {
            \curl_setopt($ch, CURLOPT_HTTPHEADER, $this->_headers);
        }

        $curl_options = [
            CURLOPT_VERBOSE        => false,
            CURLOPT_FORBID_REUSE   => true,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HEADER         => false,
            CURLOPT_TIMEOUT        => 500,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FOLLOWLOCATION => true
        ];

        \curl_setopt($ch, CURLOPT_URL, $url);

        \curl_setopt($ch, CURLOPT_USERPWD, $this->_authUser . ":" . $this->_authKey);

        \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->_method);

        \curl_setopt_array($ch, $curl_options);

        if ($this->_method!='GET') {
            $dataString = json_encode($this->_data);
            \curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            \curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($dataString))
            );
        }

        $result      = \curl_exec($ch);
        $error       = \curl_error($ch);
        $information = \curl_getinfo($ch);
        $http_code   = \curl_getinfo($ch, CURLINFO_HTTP_CODE);

        \curl_close($ch);

        if ($this->_debug) {

            echo '<pre>';
            echo date('Y-m-d H:i:s')."\n";
            echo 'Url: ' . $this->_url . ' - Method: ' . $this->_method ."\n";
            var_dump(
                $this->_data,
                $result

            );
            if ($error) {
                echo $error . "\n";
            }
            echo "\n------------------\n";
            echo '</pre>';
        }

        if ($result) {
            return json_decode($result, true);
        }

        return $result;
    }

}
