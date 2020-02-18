<?php

namespace Oblik\Outsource\Walker;

/**
 * Class for storing walked data.
 */
class Model {
    protected $site;
    protected $pages;
    protected $files;
    protected $variables;

    public function __construct($data = [])
    {
        $this->site = $data['site'] ?? [];
        $this->pages = $data['pages'] ?? [];
        $this->files = $data['files'] ?? [];
        $this->variables = $data['variables'] ?? [];
    }

    public function __call($name, $arguments)
    {
        return $this->$name ?? null;
    }

    public function setSite($data)
    {
        $this->site = $data;
    }

    public function addPage(string $key, $data)
    {
        $this->pages[$key] = $data;
    }

    public function addFile(string $key, $data)
    {
        $this->files[$key] = $data;
    }

    public function setVariables($data)
    {
        $this->variables = $data;
    }

    public function toArray()
    {
        $result = null;

        foreach (['site', 'pages', 'files', 'variables'] as $prop) {
            if (!empty($this->$prop)) {
                $result[$prop] = $this->$prop;
            }
        }

        return $result;
    }
}