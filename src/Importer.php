<?php

namespace KirbyOutsource;

class Importer
{
    private $settings = [];

    public function __construct($settings = [])
    {
        $this->settings = $settings;
        $walkerSettings = array_merge([], $this->settings, [
            'fieldPredicate' => function ($blueprint) {
                return !Walker::isFieldIgnored($blueprint);
            },
            'fieldHandler' => function ($blueprint, $field, $input) {
                if ($field->isEmpty() && !$input) {
                    return null;
                }

                $data = Formatter::decode($blueprint, $field);

                if (is_array($input) && is_array($data)) {
                    $data = array_replace_recursive($data, $input);
                } else if ($input) {
                    $data = $input;
                }

                return $data;
            }
        ]);
        $this->walker = new Walker($walkerSettings);
    }

    public function update($model, $data)
    {
        $mergedData = $this->walker->walk($model, $data);
        $model->writeContent($mergedData, $this->settings['language']);
    }

    public function import($data)
    {
        $site = site();

        if (!empty($data['site'])) {
            $this->update($site, $data['site']);
        }

        if (!empty($data['pages'])) {
            foreach ($data['pages'] as $id => $pageData) {
                $page = $site->page($id);

                if ($page) {
                    $this->update($page, $pageData);
                }
            }
        }

        if (!empty($data['files'])) {
            foreach ($data['files'] as $id => $fileData) {
                $file = $site->file($id);

                if ($file) {
                    $this->update($file, $fileData);
                }
            }
        }

        if (!empty($data['variables'])) {
            Variables::update($this->language, $data['variables']);
        }

        return true;
    }
}
