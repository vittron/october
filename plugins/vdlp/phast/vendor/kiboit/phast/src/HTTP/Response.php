<?php

namespace Kibo\Phast\HTTP;

class Response {
    /**
     * @var int
     */
    private $code = 200;

    /**
     * @var array
     */
    private $headers = [];

    /**
     * @var string|iterable
     */
    private $content;

    /**
     * @return int
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code) {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    public function setHeaders(array $headers) {
        $this->headers = $headers;
    }

    /**
     * @param $name
     * @param $value
     */
    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
    }

    /**
     * @return string|iterable
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param string|iterable $content
     */
    public function setContent($content) {
        $this->content = $content;
    }
}
