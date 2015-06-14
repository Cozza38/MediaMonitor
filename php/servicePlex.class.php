<?php

class servicePlex {

	public $name;
	public $port;
	public $url;
	public $host;
	public $status;


	function __construct($name, $port, $url = "", $host, $ssl)
	{
		$this->name      = $name;
		$this->port      = $port;
		$this->url       = $url;
		$this->host      = $host;
		$this->ssl 		 = $ssl;

		$this->status = $this->check_port();
	}


	function check_port()
	{
		$protocol = protocolCheck($this->ssl);
		$conn = simplexml_load_file($protocol . $this->host . ':' . $this->port);
		if ( $conn != null )
		{
			return true;
		}
		else
		{
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
