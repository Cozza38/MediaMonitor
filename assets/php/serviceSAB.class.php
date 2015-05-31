<?php
include_once( "functions.php" );

class serviceSAB {

	public $name;

	public $port;

	public $url;

	public $host;

	public $status;


	function __construct($name, $port, $url = "", $host, $api, $ssl)
	{
		$this->name = $name;
		$this->port = $port;
		$this->url  = $url;
		$this->host = $host;
		$this->api = $api;
		$this->ssl = $ssl;

		$this->status = $this->check_port();
	}


	function check_port()
	{
		$conn = @fsockopen($this->host, $this->port, $errno, $errstr, 0.5);
		if ( $conn )
		{
			fclose($conn);

			return true;
		}
		else
		{
			return false;
		}
	}


	function makeButton()
	{
		$protocol = protocolCheck($this->ssl);
		$sabnzbdXML = simplexml_load_file( $protocol . $this->host . ':' . $this->port . '/sabnzbd/api?mode=qstatus&output=xml&apikey=' . $this->api);
		$sabState = $sabnzbdXML->state;
		$speed = filter_var(($sabnzbdXML->speed), FILTER_SANITIZE_NUMBER_INT);
		$mb = $sabnzbdXML->jobs->job[0]->mb;
		$mbleft = $sabnzbdXML->jobs->job[0]->mbleft;
		$downloadedPercent = intval(($mb-$mbleft)/$mb * 100)."%";
		// Truncated Filename
		if (strlen($sabnzbdXML->jobs->job[0]->filename) < 18)
		{
			$filename = $sabnzbdXML->jobs->job[0]->filename;
		} else {
			$filename = substr(($sabnzbdXML->jobs->job[0]->filename), 0, 18);
			$filename .= "...";
		}


		if ( $sabState == 'Downloading' )
		{
			if ( strpbrk(($sabnzbdXML->speed), 'K') )
			{
				$txt  = $this->status ? $speed . ' KB/s' : 'Offline';
			} else {
				$txt  = $this->status ? $speed . ' MB/s' : 'Offline';
			}
			$btn    = $this->status ? 'success' : 'danger';
			$prefix = $this->url == "" ? '<button style="width:62px" class="btn btn-xs btn-' . $btn . '">' : '<a href="' . $this->url . '" style="width:62px" target="_blank" class="btn btn-xs btn-' . $btn . '"rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="'. $downloadedPercent . ' - ' . $filename .'">';
		} elseif ( $sabState == 'Paused' ) {
			$txt  = $this->status ? 'Paused' : 'Offline';
			$btn    = $this->status ? 'warning' : 'danger';
			$prefix = $this->url == "" ? '<button style="width:62px" class="btn btn-xs btn-' . $btn . '">' : '<a href="' . $this->url . '" style="width:62px" target="_blank" class="btn btn-xs btn-' . $btn . '"rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="'. $downloadedPercent . ' - ' . $filename .'">';
		} else {
			$txt  = $this->status ? 'Online' : 'Offline';
			$btn    = $this->status ? 'success' : 'danger';
			$prefix = $this->url == "" ? '<button style="width:62px" class="btn btn-xs btn-' . $btn . '">' : '<a href="' . $this->url . '" style="width:62px" target="_blank" class="btn btn-xs btn-' . $btn . '">';
		}
		$suffix = $this->url == "" ? '</button>' : '</a>';
		return $prefix . $txt . $suffix;
	}
}
