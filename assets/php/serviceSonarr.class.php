<?php

class serviceSonarr {

	public $name;
	public $port;
	public $url;
	public $host;
	public $status;


	function __construct($name, $port, $url = "", $host, $api, $ssl)
	{
		$this->name      = $name;
		$this->port      = $port;
		$this->url       = $url;
		$this->host      = $host;
		$this->api 		 = $api;
		$this->ssl 		 = $ssl;

		$this->status = $this->check_port();
	}


	function check_port()
	{

		$protocol = protocolCheck($this->ssl);
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL,
			$protocol . $this->host . ":" . $this->port . "/tv/api/system/status?apikey=" . $this->api );
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);
	    curl_setopt($ch, CURLOPT_HEADER, false);

		$response = curl_exec($ch);

	    if ( $response )
	    {
	        curl_close($ch);

	        return true;
	    }
	    else
	    {
	        $error = curl_error($ch);
	        curl_close($ch);

	        return false;
	    }
	}


	function make_button()
	{
		$btn_status = $this->status ? 'success' : 'danger';
		$btn_prefix = $this->url == "" ? '<button style="width:62px" class="btn btn-xs btn-' . $btn_status . ' disabled">' : '<a href="' . $this->url . '" style="width:62px" target="_blank" class="btn btn-xs btn-' . $btn_status . '">';
		$btn_txt    = $this->status ? 'Online' : 'Offline';
		$btn_suffix = $this->url == "" ? '</button>' : '</a>';

		return $btn_prefix . $btn_txt . $btn_suffix;
	}
}
