<?php

namespace Elastique\Core;

class Request{
    private $domain;
    private $path;
    private $method;

    public function __construct(){
        $this->domain = $_SERVER['HTTP_HOST'];
        $this->path = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    public function getURL(): string {
        return $this->domain . $this->path;
    }

    public function getDomain(): string {
        return $this->domain;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function isGet(): bool {
        return $this->method == 'GET';
    }

    public function isPost(): bool {
        return $this->method == 'POST';
    }
    
}

?>
