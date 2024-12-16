<?php

namespace Barn2App\Utils;

class Util
{
    /**
     * Parse query strings the same way as Rack::Until in Ruby. (This is a port from Rack 2.3.0.).
     *
     * From Shopify docs, they use Rack::Util.parse_query, which does *not* parse array parameters properly.
     * Array parameters such as `name[]=value1&name[]=value2` becomes `['name[]' => ['value1', 'value2']] in Shopify.
     * See: https://github.com/rack/rack/blob/f9ad97fd69a6b3616d0a99e6bedcfb9de2f81f6c/lib/rack/query_parser.rb#L36
     *
     * @param  string  $queryString  The query string.
     * @param  string|null  $delimiter  The delimiter.
     * @return mixed
     */
    public static function parseQueryString(string $queryString, ?string $delimiter = null): array
    {
        $commonSeparator = [';' => '/[;]\s*/', ';,' => '/[;,]\s*/', '&' => '/[&]\s*/'];
        $defaultSeparator = '/[&;]\s*/';

        $params = [];
        $split = preg_split(
            $delimiter ? $commonSeparator[$delimiter] || '/['.$delimiter.']\s*/' : $defaultSeparator,
            $queryString ?? ''
        );

        foreach ($split as $part) {
            if (! $part) {
                continue;
            }

            [$key, $value] = strpos($part, '=') !== false ? explode('=', $part, 2) : [$part, null];

            $key = urldecode($key);
            $value = $value !== null ? urldecode($value) : $value;

            if (isset($params[$key])) {
                $cur = $params[$key];

                if (is_array($cur)) {
                    $params[$key][] = $value;
                } else {
                    $params[$key] = [$cur, $value];
                }
            } else {
                $params[$key] = $value;
            }
        }

        return $params;
    }
}
