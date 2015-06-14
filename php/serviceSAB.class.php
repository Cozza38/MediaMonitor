<?php
include_once( "functions.php" );

class ServiceSAB {

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

	function make_button()
	{
		$protocol = protocolCheck($this->ssl);
		$sabnzbd_xml = simplexml_load_file( $protocol . $this->host . ':' . $this->port . '/sabnzbd/api?mode=queue&output=xml&apikey=' . $this->api);
		$sab_state = $sabnzbd_xml->status;
		$sab_items = $sabnzbd_xml->slots;

		if ($sab_state == 'Downloading') {
			$sab_speed = filter_var(($sabnzbd_xml->speed), FILTER_SANITIZE_NUMBER_INT);
			if ( strpbrk(($sabnzbd_xml->speed), 'K')) {
				$btn_txt  = $this->status ? $sab_speed . ' KB/s' : 'Offline';
			} elseif ( strpbrk(($sabnzbd_xml->speed), 'M')) {
				$btn_txt  = $this->status ? $sab_speed . ' MB/s' : 'Offline';
			} else {
				$btn_txt  = $this->status ? $sab_speed . ' KB/s' : 'Offline';
			}
			foreach ($sab_items->slot as $sab_item) {
				if ($sab_item->status == 'Downloading') {
					$item_mb = $sab_item->mb;
					$item_mbleft = $sab_item->mbleft;
					$item_percent = $sab_item->percentage . "%";
					// Truncate Filename
					if (strlen($sab_item->filename) < 20) {
						$item_filename = $sab_item->filename;
					} else {
							$item_filename = substr(($sab_item->filename), 0, 20);
							$item_filename .= "...";
					}
					$btn_status    = $this->status ? 'success' : 'danger';
					$btn_prefix = $this->url == "" ? '<button style="width:62px" class="btn btn-xs btn-' . $btn_status . '">' : '<a href="' . $this->url . '" style="width:62px" target="_blank" class="btn btn-xs btn-' . $btn_status . '"rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="'. $item_percent . ' - ' . $item_filename .'">';
					// We found what we need, lets get out of here
					break;
				}
			}
		} elseif ($sab_state == 'Paused') {
			$btn_txt  = $this->status ? 'Paused' : 'Offline';
			$btn_status    = $this->status ? 'warning' : 'danger';
			$btn_prefix = $this->url == "" ? '<button style="width:62px" class="btn btn-xs btn-' . $btn_status . '">' : '<a href="' . $this->url . '" style="width:62px" target="_blank" class="btn btn-xs btn-' . $btn_status . '">';
		} else {
			$btn_txt  = $this->status ? 'Online' : 'Offline';
			$btn_status    = $this->status ? 'success' : 'danger';
			$btn_prefix = $this->url == "" ? '<button style="width:62px" class="btn btn-xs btn-' . $btn_status . '">' : '<a href="' . $this->url . '" style="width:62px" target="_blank" class="btn btn-xs btn-' . $btn_status . '">';
		}
		$btn_suffix = $this->url == "" ? '</button>' : '</a>';
		return $btn_prefix . $btn_txt . $btn_suffix;
	}
}
