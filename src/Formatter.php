<?php

namespace Oblik\Outsource;

class Formatter
{
    protected static $serializers = [
        'markdown' => Serializer\Markdown::class,
        'kirbytags' => Serializer\KirbyTags::class,
        'yaml' => Serializer\Yaml::class,
        'tags' => Serializer\Tags::class,
    ];

    public static function serialize(array $blueprint, $field)
    {
        $options = $blueprint[BLUEPRINT_KEY] ?? null;
        $serialize = $options['serialize'] ?? [];
        $content = $field->value();

        if ($content === null) {
            // Field has no value.
            return null;
        }

        foreach ($serialize as $key => $config) {
            $serializer = self::$serializers[$key] ?? null;

            if ($serializer) {
                $content = $serializer::decode($content, [
                    'field' => $field,
                    'blueprint' => $blueprint,
                    'config' => $config
                ]);
            }
        }

        return $content;
    }

    public static function deserialize(array $blueprint, $data)
    {
        $options = $blueprint[BLUEPRINT_KEY] ?? null;
        $serializers = $options['deserialize'] ?? null;

        if (!is_array($serializers)) {
            $serializers = array_reverse($options['serialize'] ?? [], true);
        }

        foreach ($serializers as $key => $config) {
            $serializer = self::$serializers[$key] ?? null;

            if ($serializer) {
                $data = $serializer::encode($data, [
                    'blueprint' => $blueprint,
                    'config' => $config
                ]);
            }
        }

        return $data;
    }
}
