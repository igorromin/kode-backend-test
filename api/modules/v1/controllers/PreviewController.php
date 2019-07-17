<?php

namespace app\api\modules\v1\controllers;


class PreviewController
{
    public static function getPreviewByUrl($url) {
        $options = array('http' => array('method' => 'HEAD',
                                         'follow_location' => 0));

        $context = stream_context_create($options);
        file_get_contents($url, null, $context);
        $headers = self::_parseHeaders($http_response_header);
        if ( ! empty($headers['Content-Type'])
            && mb_strpos(
                $headers['Content-Type'], 'text/html'
            ) !== false
        ) {
            $content_type_arr = explode(';', $headers['Content-Type']);
            //дальнейшие функции используются без расширения mb, т.к тест в ASCII
            if (isset($content_type_arr[1])
                && $charset_start = strpos($content_type_arr[1], 'charset=')
            ) {
                $charset = strtolower(
                    substr($content_type_arr[1], $charset_start + 8)
                );
            } else {
                return null;
            }
            $page = file_get_contents($url);

            $matches = [];
            //Получаем значения OpenGraph
            preg_match(
                '/<meta property=\"og:title\" content=\"(.*?)\"[\/]?>.*?<meta property=\"og:image\" content=\"(.*?)\"/s',
                $page, $matches
            );
            //Если OpenGraph не найден, то берем title и первое изображение на странице (обычно это логотип)
            if (!$matches) {
                preg_match(
                    '/<title>(.*?)<\/title>.*?<img .*?src=\"(.*?)\"/s', $page,
                    $matches
                );
                if (isset($matches[2])) {
                    $matches[2] = self::_rel2abs($matches[2], $url);
                }
            }
            //Чтобы не менять кодировку полного вхождения
            unset($matches[0]);
            foreach ($matches as &$match) {
                if ($charset != "utf-8") {
                    $match = mb_convert_encoding($match, "UTF-8", $charset);
                }

            }
            if ($matches) {
                return ['title' => $matches[1], 'image' => $matches[2]];
            } else {
                return null;
            }
        }
    }

    private static function _parseHeaders($headers) {
        $head = array();
        foreach ($headers as $name => $value) {
            $row = explode(':', $value, 2);
            if (isset($row[1])) {
                $head[trim($row[0])] = trim($row[1]);
            } else {
                $head[] = $value;
            }
        }
        return $head;
    }

    //Из относительного пути в абсолютный
    private static function _rel2abs($rel, $base) {
        $url_components = parse_url($base);
        if (strpos($rel, "//") === 0) {
            return $url_components['scheme'] . ':' . $rel;
        }

        if (parse_url($rel,PHP_URL_SCHEME) != '') {
            return $rel;
        }

        // queries and anchors
        if ($rel[0] == '#' || $rel[0] == '?') {
            return $base . $rel;
        }

        $path = preg_replace('#/[^/]*$#', '', $url_components['path']);

        if ($rel[0] == '/') {
            $path = '';
        }

        $abs = $url_components['host'] . $path . "/" . $rel;

        $abs = preg_replace("/(\/\.?\/)/", "/", $abs);
        $abs = preg_replace("/\/(?!\.\.)[^\/]+\/\.\.\//", "/", $abs);

        return $url_components['scheme'] . '://' . $abs;
    }
}