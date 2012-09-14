<?php

namespace Momentum;
use Exception;

/**
 * This class manages all the CURL requests.
 * @author David Boskovic
 */
class Curl {

	/**
	 * The headers that you want to post along with each request.
	 */
	public static $headers = array(
		'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg',
		'Connection: Keep-Alive',
		'Content-type: application/x-www-form-urlencoded;charset=UTF-8'
	);

	/**
	 * The UserAgent that theq requests are being made from.
	 */
	public static $user_agent = 'momentum-plugin';

	/**
	 * The compression options for the response.
	 */
	public static $compression = 'gzip';

	/**
	 * BasicAuth User
	 */
	private static $user = '';

	/**
	 * BasicAuth Password.
	 */
	private static $password = '';

	/**
	 * Base URL
	 */
	public static $base_url = '';


	/**
	 * Issue a GetRequest
	 * @param $url [string]
	 * @param $args [array]
	 */
	public static function GetRequest($url, $args = array()) {

		/**
		 * Initiate a curl request object.
		 */
		$request = self::__setupCurlRequest($url);

		/**
		 * Execute the request.
		 */
		$return = curl_exec($request);

		/**
		 * Get extra information about the request.
		 */
		$info = curl_getinfo($request);

		/**
		 * Close this request.
		 */
		curl_close($request);

		/**
		 * Return the CurlResponse Object.
		 */
		return new CurlResponse($return, $info);
	}

	/**
	 * Issue a PostRequest
	 * @param $url [string]
	 * @param $args [array]
	 */
	public static function PostRequest($url, $args = array()) {

		/**
		 * Initiate a curl request object.
		 */
		$request = self::__setupCurlRequest($url);

		/**
		 * Setup the post options.
		 */
		curl_setopt($request, CURLOPT_POSTFIELDS, self::__cleanPostData($args));
		curl_setopt($request, CURLOPT_POST, 1);

		/**
		 * Execute the request.
		 */
		$return = curl_exec($request);

		/**
		 * Get extra information about the request.
		 */
		$info = curl_getinfo($request);

		/**
		 * Close this request.
		 */
		curl_close($request);

		/**
		 * Return the CurlResponse Object.
		 */
		return new CurlResponse($return, $info);
	}

	/**
	 * Set the Credentials for accessing the API
	 * @param $user [string]
	 * @param $pass [string]
	 */
	public static function setCredentials($user, $pass) {
		self::$user = $user;
		self::$password = $pass;
	}

	/**
	 * Setup a default CURL Request
	 * @param $url [string]
	 */
	private static function __setupCurlRequest($url) {

		/**
		 * New Curl Request Object
		 */
		$request = curl_init(self::$base_url.$url);

		curl_setopt($request, CURLOPT_HTTPHEADER, self::$headers);
		curl_setopt($request, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($request, CURLOPT_USERPWD, self::$user.':'.self::$password);
		//curl_setopt($request, CURLOPT_HEADER, 1);
		curl_setopt($request, CURLOPT_USERAGENT, self::$user_agent);
		curl_setopt($request, CURLOPT_ENCODING , self::$compression);
		curl_setopt($request, CURLOPT_TIMEOUT, 30);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_FOLLOWLOCATION, 1);

		return $request;
	}

	/**
	 * Clean the data that's being posted.
	 */
	private static function __cleanPostData($data) {

		if(is_array($data)) {
			$t = ''; $and = false;
			foreach($data as $key=>$value) {
				$t .= ($and ? '&' : '') . urlencode($key) . '=' . urlencode($value);
				if(!$and) $and = true;
			}
			$data = $t;
		}
		return $data;
	}
}

class CurlResponse {
	var $body;
	var $headers;
	var $info;

	function __construct($text, $info) {


		/**
		 * fix issue with continue header
		 */
		/*if(strpos($text, 'HTTP/1.1 100 Continue'));
			$text = substr($text, strpos($text, "\r\n\r\n")+4);

		$this->info = $info;
		$split1 = strpos($text, "\r\n\r\n");
		$split2 = strpos($text, "\r\r");
		$split3 = strpos($text, "\n\n");

		$split = $split1 > 0 						  ? $split1 : 100000000;
		$split = $split2 > 0 && $split2 < $split ? $split2 : $split;
		$split = $split3 > 0 && $split3 < $split ? $split3 : $split;

		$splen = $split == $split1 ? 4 : 2;*/

		$this->body = $text;
		$this->headers = substr($text, 0, $split);
	}

	function headers( )
    {
		return $this->http_parse_headers($this->headers);
    }

	/**
	 * Parse through the http headers returned.
	 *
	 * @param string $headers
	 * @return array
	 * @author David Boskovic
	 */
	function http_parse_headers($headers=false){
		if($headers === false) return false;

		$headers = str_replace("\r","",$headers);
		$headers = explode("\n",$headers);
		foreach($headers as $value){
			$header = explode(": ",$value);
			if($header[0] && $header[1] === NULL){
				$headerdata['status'] = $header[0];
			}
			elseif($header[0] && $header[1] !== NULL){
				if(isset($headerdata[$header[0]]) AND !is_array($headerdata[$header[0]])) {
					$headerdata[$header[0]] = array($headerdata[$header[0]]);
				}
				elseif($headerdata[$header[0]])$headerdata[$header[0]][] = $header[1];
				else
					$headerdata[$header[0]] = $header[1];
			}
		}
		return $headerdata;
	}
}