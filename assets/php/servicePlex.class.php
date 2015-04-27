<?php

class servicePlex
{
    public $name;
    public $port;
    public $url;
    public $host;
    public $status;

    function __construct($name, $port, $url = "", $host = "localhost", $plexToken)
    {
        $this->name = $name;
        $this->port = $port;
        $this->url = $url;
        $this->host = $host;
        $this->plexToken = $plexToken;

        $this->status = $this->check_port();
    }

    function check_port()
    {
        $conn = simplexml_load_file('http://' . $this->host . ':' . $this->port . '/?X-Plex-Token=' . $this->plexToken);
        if ($conn != null) {
            return true;
        } else
            return false;
    }

    function makeButton()
    {
        $icon = '<i class="icon-' . ($this->status ? 'ok' : 'remove') . ' icon-white"></i>';
        $btn = $this->status ? 'success' : 'warning';
        $prefix = $this->url == "" ? '<button style="width:62px" class="btn btn-xs btn-' . $btn . ' disabled">' : '<a href="' . $this->url . '" style="width:62px" class="btn btn-xs btn-' . $btn . '">';
        $txt = $this->status ? 'Online' : 'Offline';
        $suffix = $this->url == "" ? '</button>' : '</a>';

        return $prefix . $icon . " " . $txt . $suffix;
    }
}

?>
