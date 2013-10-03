<?php
/**
 * Build on top of the official mandrill API PHP wrapper
 *
 * @author     Daniel Dimitrov <daniel@compojoom.com>
 * @date       01.10.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class Cmandrill
 *
 * @since  3.0
 */
class CmandrillQuery
{
	public $apikey;

	public $ch;

	public $root = 'https://mandrillapp.com/api/1.0';

	public $debug = false;

	public static $error_map = array(
		"ValidationError" => "CMandrillExceptionsValidation",
		"Invalid_Key" => "CMandrillExceptionsInvalidkey",
		"PaymentRequired" => "CMandrillExceptionsPaymentrequired",
		"Unknown_Subaccount" => "CMandrillExceptionsPaymentrequired",
		"Unknown_Template" => "CMandrillExceptionsUnknowntemplate",
		"ServiceUnavailable" => "CMandrillExceptionsUnknowntemplate",
		"Unknown_Message" => "CMandrillExceptionsUnknowMessage",
		"Invalid_Tag_Name" => "CMandrillExceptionsInvalidTagname",
		"Invalid_Reject" => "CMandrillExceptionsInvalidReject",
		"Unknown_Sender" => "Mandrill_Unknown_Sender",
		"Unknown_Url" => "CMandrillExceptionsUnknownUrl",
		"Invalid_Template" => "CMandrillExceptionsInvalidTemplate",
		"Unknown_Webhook" => "CMandrillExceptionsUnknownWebhook",
		"Unknown_InboundDomain" => "CMandrillExceptionsUnknowInbounddomain",
		"Unknown_Export" => "CMandrillExceptionsUnknownExport",
		"IP_ProvisionLimit" => "CMandrillExceptionsIpProvisionlimit",
		"Unknown_Pool" => "CMandrillExceptionsUnknownPool",
		"Unknown_IP" => "CMandrillExceptionsUnknownIp",
		"Invalid_EmptyDefaultPool" => "CMandrillExceptionsInvalidEmptydefaultpool",
		"Invalid_DeleteDefaultPool" => "CMandrillExceptionsInvalidDeleteDefaultpool",
		"Invalid_DeleteNonEmptyPool" => "CMandrillExceptionsInvalidNonemptypool"
	);

	/**
	 * The Constructor
	 *
	 * @param   string  $apikey  - the API key for Mandrill
	 *
	 * @throws CMandrillExceptionsError
	 */
	public function __construct($apikey = null)
	{
		if (!$apikey)
		{
			$apikey = getenv('MANDRILL_APIKEY');
		}

		if (!$apikey)
		{
			$apikey = $this->readConfigs();
		}

		if (!$apikey)
		{
			throw new CMandrillExceptionsError('You must provide a Mandrill API key');
		}

		$this->apikey = $apikey;

		$this->ch = curl_init();
		curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mandrill-PHP/1.0.48');
		curl_setopt($this->ch, CURLOPT_POST, true);
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->ch, CURLOPT_HEADER, false);
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($this->ch, CURLOPT_TIMEOUT, 600);

		$this->root = rtrim($this->root, '/') . '/';

		$this->templates = new CmandrillTemplates($this);
		$this->exports = new CmandrillExports($this);
		$this->users = new CmandrillUsers($this);
		$this->rejects = new CmandrillRejects($this);
		$this->inbound = new CmandrillInbound($this);
		$this->tags = new CmandrillTags($this);
		$this->messages = new CmandrillMessages($this);
		$this->whitelists = new CmandrillWhitelists($this);
		$this->ips = new CmandrillIps($this);
		$this->internal = new CmandrillInternal($this);
		$this->subaccounts = new CmandrillSubaccounts($this);
		$this->urls = new CmandrillUrls($this);
		$this->webhooks = new CmandrillWebhooks($this);
		$this->senders = new CmandrillSenders($this);
	}

	/**
	 * Deconstructor
	 *
	 */
	public function __destruct()
	{
		curl_close($this->ch);
	}

	/**
	 * Calls the mandrill API
	 *
	 * @param   string  $url     - the url to call
	 * @param   object  $params  - the params
	 *
	 * @return mixed
	 *
	 * @throws CMandrillExceptionsError
	 * @throws mixed
	 * @throws CMandrillExceptionsHttpError
	 */
	public function call($url, $params)
	{
		$params['key'] = $this->apikey;
		$params = json_encode($params);
		$ch = $this->ch;

		curl_setopt($ch, CURLOPT_URL, $this->root . $url . '.json');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_VERBOSE, $this->debug);

		$start = microtime(true);
		$this->log('Call to ' . $this->root . $url . '.json: ' . $params);

		if ($this->debug)
		{
			$curl_buffer = fopen('php://memory', 'w+');
			curl_setopt($ch, CURLOPT_STDERR, $curl_buffer);
		}

		$response_body = curl_exec($ch);
		$info = curl_getinfo($ch);
		$time = microtime(true) - $start;

		if ($this->debug)
		{
			rewind($curl_buffer);
			$this->log(stream_get_contents($curl_buffer));
			fclose($curl_buffer);
		}

		$this->log('Completed in ' . number_format($time * 1000, 2) . 'ms');
		$this->log('Got response: ' . $response_body);

		if (curl_error($ch))
		{
			throw new CMandrillExceptionsHttpError("API call to $url failed: " . curl_error($ch));
		}

		$result = json_decode($response_body, true);

		if ($result === null)
		{
			throw new CMandrillExceptionsError('We were unable to decode the JSON response from the Mandrill API: ' . $response_body);
		}

		if (floor($info['http_code'] / 100) >= 4)
		{
			throw $this->castError($result);
		}

		return $result;
	}

	/**
	 * Read Mandrills config
	 *
	 * @return bool|string
	 */
	public function readConfigs()
	{
		$paths = array('~/.mandrill.key', '/etc/mandrill.key');

		foreach ($paths as $path)
		{
			if (file_exists($path))
			{
				$apikey = trim(file_get_contents($path));

				if ($apikey)
				{
					return $apikey;
				}
			}
		}

		return false;
	}

	/**
	 * Casts an error to the appropriate Exception
	 *
	 * @param   array  $result  - the result
	 *
	 * @return mixed
	 *
	 * @throws CMandrillExceptionsError
	 */
	public function castError($result)
	{
		if ($result['status'] !== 'error' || !$result['name'])
		{
			throw new CMandrillExceptionsError('We received an unexpected error: ' . json_encode($result));
		}

		$class = (isset(self::$error_map[$result['name']])) ? self::$error_map[$result['name']] : 'Mandrill_Error';

		return new $class($result['message'], $result['code']);
	}

	/**
	 * Log a message
	 *
	 * @param   string  $msg  - the message
	 *
	 * @return void
	 */
	public function log($msg)
	{
		if ($this->debug)
		{
			error_log($msg);
		}
	}
}