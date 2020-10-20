<?php

namespace Kibo\Phast\ValueObjects;

class URL {
    /**
     * @var string
     */
    private $scheme;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $fragment;

    /**
     * @param $string
     * @return URL
     */
    public static function fromString($string) {
        $components = parse_url($string);
        if (!$components) {
            return new self();
        }
        return self::fromArray($components);
    }

    /**
     * @param array $arr Should follow the format produced by parse_url()
     * @return URL
     * @see parse_url()
     */
    public static function fromArray(array $arr) {
        $url = new self();
        foreach ($arr as $key => $value) {
            $url->$key = $key == 'path' ? $url->normalizePath($value) : $value;
        }
        return $url;
    }

    /**
     * If $this can be interpreted as relative to $base,
     * will produce URL that is $base/$this.
     * Otherwise the returned URL will point to the same place as $this
     *
     * @param URL $base
     * @return URL
     *
     * @example this: www/htdocs + base: /var -> /var/www/htdocs
     * @example this: /var + base: http://example.com -> http://example.com/var
     * @example this: /var + base: /www -> /var
     */
    public function withBase(URL $base) {
        $new = clone $this;

        foreach (['scheme', 'host', 'port', 'user', 'pass', 'path'] as $key) {
            if ($key == 'path') {
                $new->path = $this->resolvePath($base->path, $this->path);
            } elseif (!isset($this->$key) && isset($base->$key)) {
                $new->$key = $base->$key;
            } elseif (isset($this->$key)) {
                break;
            }
        }

        return $new;
    }

    /**
     * Tells whether $this can be interpreted as at the same host as $url
     *
     * @param URL $url
     * @return bool
     */
    public function isLocalTo(URL $url) {
        return empty($this->host) || $this->host === $url->host;
    }

    /**
     * @return string
     */
    public function toString() {
        $scheme   = isset($this->scheme) ? $this->scheme . '://' : '';
        $host     = isset($this->host) ? $this->host : '';
        $port     = isset($this->port) ? ':' . $this->port : '';
        $user     = isset($this->user) ? $this->user : '';
        $pass     = isset($this->pass) ? ':' . $this->pass  : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($this->path) ? $this->getPath() : '';
        $query    = isset($this->query) ? '?' . $this->query : '';
        $fragment = isset($this->fragment) ? '#' . $this->fragment : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    private function normalizePath($path) {
        $stack = [];
        $head = null;
        foreach (explode('/', $path) as $part) {
            if ($part == '.' || $part == '') {
                continue;
            }
            if (!is_null($head) && $part == '..' && $head != '..') {
                array_pop($stack);
                $head = empty($stack) ? null : $stack[count($stack) - 1];
            } else {
                $stack[] = $head = $part;
            }
        }

        $normalized = substr($path, 0, 1) == '/' ? '/' : '';
        if (!empty($stack)) {
            $normalized .= join('/', $stack);
            $normalized .= substr($path, -1) == '/' ? '/' : '';
        }
        return $normalized;
    }

    private function resolvePath($base, $requested) {
        if (!$requested) {
            return $base;
        }
        if ($requested[0] == '/') {
            return $requested;
        }
        if (substr($base, -1, 1) == '/') {
            $usedBase = $base;
        } else {
            $usedBase = dirname($base);
        }
        return rtrim($usedBase, '/') . '/' . $requested;
    }

    /**
     * @return string
     */
    public function getScheme() {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPass() {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * @return string
     */
    public function getExtension() {
        $matches = [];
        if (preg_match('/\.([^.]*)$/', $this->path, $matches)) {
            return $matches[1];
        }
        return '';
    }

    /**
     * @return string
     */
    public function getFragment() {
        return $this->fragment;
    }

    /**
     * @param string|null $query
     * @return self
     */
    public function withQuery($query) {
        $url = clone $this;
        if ($query === null) {
            $url->query = null;
        } else {
            $url->query = (string) $query;
        }
        return $url;
    }

    /**
     * @return self
     */
    public function withoutQuery() {
        $url = clone $this;
        $url->query = null;
        return $url;
    }

    public function __toString() {
        return $this->toString();
    }
}
