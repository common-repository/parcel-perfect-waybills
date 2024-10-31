<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}
// Parcel Perfect class for white label plugin

class ParcelPerfect {
	private $Soap_Url;
	private $Json_Url;
	private $Username;
	private $Password;
	private $EncryptedPassword;
	private $PP_Token;
	
	function __construct( $PostData ) {
		ini_set('soap.wsdl_cache_enabled', 0);
        ini_set('soap.wsdl_cache_ttl', 900);
        ini_set('default_socket_timeout', 600);
		
		$this->Soap_Url = $PostData->PP_Url.'/Soap/index.php?wsdl';
		$this->Json_Url = $PostData->PP_Url.'/Json';
		$this->Username = $PostData->PP_User;
		$this->Password = $PostData->PP_Password;
	}
	
	private function Auth() {
		$client = new SoapClient($this->Soap_Url);

		$salt = '';

		$query = new stdClass;
		$query->email = $this->Username;
		
		$params = $this->prepare_body($query);
		
		$Url = $this->Json_Url.'?class=Auth&method=getSalt&params='.$params;
		
		$Result = $this->call($Url);
		
		if($Result->errorcode != 0) {
			// echo $result->errormessage."<br>";
		}
		else{
			$salt = $Result->results[0];
			$password_crypt = md5($this->Password.$salt->salt);
			$this->EncryptedPassword = $password_crypt;
		}

		if(!empty($this->EncryptedPassword)){
			$query = new stdClass;
			$query->email = $this->Username;
			$query->password = $this->EncryptedPassword;
			
			$params = $this->prepare_body($query);

			$Url = $this->Json_Url.'?class=Auth&method=getSecureToken&params='.$params;
		
			$Result = $this->call($Url);
			
			if($Result->errorcode != 0){
				// echo $result->errormessage."<br>";
			}
			else {
				$token = $Result->results[0];
				$this->PP_Token = $token->token_id;
			}
		}
	}
	
	public function GenerateToken() {
		$this->Auth();
		
		return $this->PP_Token;
	}
	
	public function GetPlaceByCode($query) {
		$params = $this->prepare_body($query);
		
		$Url = $this->Json_Url.'?class=Quote&method=getPlacesByPostcode&token_id='.$this->PP_Token.'&params='.$params;
		
		return $this->call($Url);
	}
	
	public function GetPlaceByName($query) {
		$params = $this->prepare_body($query);
		
		$Url = $this->Json_Url.'?class=Quote&method=getPlacesByName&token_id='.$this->PP_Token.'&params='.$params;

		return $this->call($Url);
	}
	
	public function GetQuote($query) {
		$params = $this->prepare_body($query);
		
		$Url = $this->Json_Url.'?class=Quote&method=requestQuote&token_id='.$this->PP_Token.'&params='.$params;

		return $this->call($Url);
	}
	
	public function SubmitWaybill($query) {
		$params = $this->prepare_body($query);
		
		$Url = $this->Json_Url.'?class=Waybill&method=submitWaybill&token_id='.$this->PP_Token.'&params='.$params;

		return $this->call($Url);
	}
	
	private function prepare_body($query) {
		return urlencode(json_encode($query));
	}
	
	private function call($Url) {
		$args = array(
			'method' 	  => 'POST',
			'timeout'     => '100',
			'redirection' => '5',
			'httpversion' => '1.0',
			'blocking'    => true,
			'data_format' => 'body'
		);
		
		$response = wp_remote_post( $Url, $args );
		$body = wp_remote_retrieve_body($response);

		return json_decode($body);
	}
}