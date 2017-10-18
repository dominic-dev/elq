<?php

namespace Elastique\Core;

class Request{
    private $domain;
    private $path;
    private $method;
    private $params;

    public function __construct(){
        $this->domain = $_SERVER['HTTP_HOST'];
        $this->path = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->params = array_merge($_POST, $_GET);
        $this->cookies = $_COOKIE;
    }

    /**
     * Return parameters from GET/POST
     *
     * @return array The parameters from GET/POST
     */
    
    public function getParams(): array {
        // GET and POST are not used.
        // Upon implementation they will be filtered.
        // TODO filter
        // return $this->params;
    }

    public function getCookies(): array {
        // Cookies are not used.
        // Upon implementation they will be filtered.
        // TODO filter
        // return $this->cookies;
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
