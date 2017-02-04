<?php
require_once("aesauth.php");

class cars extends AesAuthorization {
	
	var $method = '';
	var $arguments = array();
	var $uri = array();
	var $result = array("status"=>200,"response"=>array("message"=>'',"data"=>array()));
	var $dbHost = "localhost";
	var $dbUser = "root";
	var $dbPass = "";
	var $dbName = "testapi";
	var $token = "";
	var $responseFlag = true;
	
	function __construct($method = NULL, $arguments = NULL, $uri = NULL) {
		$this->method = $method;
		$this->arguments = $arguments;
		$this->uri = $uri;
	}
	
/*
 * @function name	: processQuery
 * @purpose			: function to process database queries and return result if needed
 * @arguments		: Following are the arguments to be passed:
		* sql			: sql query to be passed to process
		* returnFlag    : flag to see if need to return data from it
 * @return			: data to be fethched from db
 * @created by		: shivam sharma
 * @created on		: 4th feb 2017
 * @description		: NA
*/
	
	function processQuery($sql = NULL, $returnFlag = false) {
		$con = mysqli_connect($this->dbHost,$this->dbUser,$this->dbPass,$this->dbName);
		$row1 = array();
		if ( $result = mysqli_query($con,$sql)) {
			if ( $returnFlag ) {
				while($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
					$row1[] = $row;
				}
				return $row1;
			}
		} else {
			$this->responseFlag = false;
		}
	}

/*
 * @function name	: processRequest
 * @purpose			: function to process request
 * @arguments		: NA
 * @return			: NONE
 * @created by		: shivam sharma
 * @created on		: 4th feb 2017
 * @description		: process request and print json data 
*/	
	function processRequest() {
		switch($this->method){
			// code to process get request using id or without id
			case 'GET':
				$id = isset($this->uri[2])?$this->uri[2]:'';
				$this->listCars($id);
			break;
			// code to process update car data
			case 'PUT':
				$id = $this->uri[2];
				$isRating = isset($this->uri[3])?true:false;
				if ( !$isRating ) {
					$this->verifyToken();
				}
				$this->createOrUpdate($this->arguments,$id,$isRating);
			break;
			// code to process update car data
			case 'PATCH':
				$id = $this->uri[2];
				$isRating = isset($this->uri[3])?true:false;
				if ( !$isRating ) {
					$this->verifyToken();
				}
				$this->createOrUpdate($this->arguments,$id);
			break;
			// code to delete car data
			case 'DELETE':
				$this->verifyToken();
				$id = $this->uri[2];
				$this->deleteCar($id);
			break;
			// code to create car data
			case 'POST':
				$this->verifyToken();
				$this->createOrUpdate($this->arguments);
			break;
			default:
				$this->result = array("status"=>404,"response"=>array("message"=>'Invalid request',"data"=>array()));
			break;
		}
		echo json_encode($this->result);
	}
	
	function createOrUpdate($data = NULL,$id = NULL,$isRating = false) {
		$message = '';
		if ( !empty($data) ) {
			if ( empty($id) ) {
				$sql = "INSERT INTO `cars`(`car_name`, `car_year`, `car_model`, `electric`) VALUES ('".$data['Name']."','".$data['Year']."','".$data['Model']."','".$data['Electric']."')";
				$message = 'A new record is added.';
			} else if ($isRating) {
				$sql = "insert into car_ratings(car_id,ratings) values (".$id.",".$data['rating'].")";
				$message = 'Rating is added.';
			} else {
				$sql = "UPDATE `cars` SET `car_name`='".$data['Name']."',`car_year`='".$data['Year']."',`car_model`='".$data['Model']."',`electric`='".$data['Electric']."' WHERE `unique_id`=".$id;
				$message = 'Car record is updated.';
			}
		}
		$this->processQuery($sql);
		if ( $this->responseFlag ) {
			$this->result['response']['message'] = $message;
		} else {
			$this->result['status'] = 500;
			$this->result['response']['message'] = "Internal error, Please try again";
		}
		
	}
	
	function deleteCar($id) {
		if ( !empty($id) ) {
			$sql = "delete from cars where unique_id =".$id;
		}
		$this->processQuery($sql);
		if ( $this->responseFlag ) {
			$this->result['response']['message'] = "Car record is deleted.";
		} else {
			$this->result['status'] = 500;
			$this->result['response']['message'] = "Internal error, Please try again";
		}
	}
	
	function listCars($id = NULL) {
		if (empty($id)) { 
			$sql = "select * from cars";
		} else {
			$sql = "select * from cars where unique_id =".$id;
		}
		$arr = $this->processQuery($sql,true);
		if ( $this->responseFlag ) {
			$this->result['response']['message'] = empty($arr)?'No record found':'Car list available';
			$this->result['response']['data'] = $arr;
		} else {
			$this->result['status'] = 500;
			$this->result['response']['message'] = "Internal error, Please try again";
		}
	}
	
	function getToken() {
		$timestamp = strtotime(date("Y-m-d h:i:s"));
		echo $this->encrypt($timestamp);
	}
	
	
/*
 * @function name	: verifyToken
 * @purpose			: function to verify a request if needed
 * @arguments		: NA
 * @return			: NONE
 * @created by		: shivam sharma
 * @created on		: 4th feb 2017
 * @description		: verify token will work in following steps:
	** User can get a new token as mentioned in redme.md file
	** the token is basically aes encrypted string of current timestamp
	** the function will get token from passed argument and will decrypt and calculate time in milisec from current time, if time difference will be < 600 the request will process otherwise requester will have to generate a new token and send it along
	** i have created token just for testing purpose otherwise requester will have aes salt and IV to encrypt a key which will be sent out in request.
*/	
	function verifyToken() {
		if ( !empty($this->token) ) {
			$sentTimestamp = $this->decrypt($this->token);
			$currTimestamp = strtotime(date("Y-m-d h:i:s"));
			if ( ($currTimestamp-$sentTimestamp) > 600 ) { 
				$this->result['status'] = 500;
				$this->result['response']['message'] = "Invalid token, Plesae try again.";
				echo json_encode($this->result);
				die;
			} else {
				return true;
			}
		}
	}
	
}
parse_str(file_get_contents("php://input"),$post_vars);

if (isset($post_vars) && !empty($post_vars)) {
	$params = $post_vars;
} else {
	$params = array();
}
$paramUri = explode("/",$_SERVER['PHP_SELF']);
$headers = getallheaders();
$obj = new cars($_SERVER['REQUEST_METHOD'],$params,$paramUri);

if ( isset($_REQUEST['q']) && $_REQUEST['q'] == 'token' ) {
	$obj->getToken();
	die;
}
if ( isset($_REQUEST['authToken'])) {
	$obj->token = $_REQUEST['authToken'];
}
$obj->processRequest();

?>