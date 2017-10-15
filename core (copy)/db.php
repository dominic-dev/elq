<?php

namespace Elastique\Core;
use Elastique\Core\Config;
require('config.php');

class Database{
    public function __construct(){
        $config = new Config();
        $this->db = new PDO($config->db['sqlite'] . ':' . $config->db['conn_syntax']);
    }
}
