<?php
namespace hack3r3d\REST\API;

/**
 * @author hack3r3d
 * @desc Abstract class to build REST APIs using PHP.
 */
abstract class Rest
{
    /**
     * @var string|NULL This is the http method coming in (GET, POST, PUT, PATCH or DELETE)
     */
    protected $method = NULL;
    
    /**
     * @var string|NULL This is the endpoint - a noun such as subscription or account
     */
    protected $endpoint = NULL;
    
    /**
     * @var array arguments such as /<endpoint>/<verb>/<arg0>/<arg1>
     */
    protected $args = array();
    
    /**
     * @var string|NULL on PUT|POST|PATCH|DELETE stores php://input in this member variable
     * This data should be base64 coming in from the client.
     */
    protected $data = NULL;
    
    protected $request = NULL;
    
    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * @param Ambigous <string, multitype:NULL > $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }
    
    /**
     * Sets up headers and parses GET,POST,PUT,DELETE
     */
    public function __construct($request)
    {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
        
        $this->args = explode('/', rtrim($request, '/'));
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }
        
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PATCH') {
                $this->method = 'PATCH';
            } else {
                throw new \Exception("Unexpected Header");
            }
        }
        
        switch($this->method) {
            case 'DELETE':
                $this->setRequest($this->_cleanInputs($_POST));
                $this->data = file_get_contents('php://input');
                break;
            case 'POST':
                $this->getRequest($this->_cleanInputs($_POST));
                $this->data = file_get_contents('php://input');
                break;
            case 'GET':
                $this->setRequest($this->_cleanInputs($_GET));
                break;
            case 'PUT':
                $this->setRequest($this->_cleanInputs($_POST));
                $this->data = file_get_contents("php://input");
                break;
            case 'PATCH':
                $this->setRequest($this->_cleanInputs($_POST));
                $this->data = file_get_contents("php://input");
                break;
            default:
                $this->_response('Invalid Method', 405);
                break;
        }
    }
    
    /**
     * Run the request.
     * @return string|'404' if endpoint doesn't exist.
     */
    public function processAPI()
    {
        if (method_exists($this, $this->endpoint)) {
            return $this->_response($this->{$this->endpoint}($this->args));
        }
        return $this->_response("No Endpoint: $this->endpoint", 404);
    }
    
    /**
     * Builds the json response
     * @param string $data
     * @param number $status
     * @return string JSON
     */
    private function _response($data, $status = 200)
    {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }
    
    private function _cleanInputs($data)
    {
        $clean_input = array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }
    
    /**
     * Maps status code to message. This can be overridden in implementing classes
     * @param string $code
     * @return string
     */
    protected function requestStatus($code)
    {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code]) ? $status[$code] : $status[500];
    }
}