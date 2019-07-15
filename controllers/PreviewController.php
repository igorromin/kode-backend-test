<?php

namespace app\controllers;


class PreviewController
{
    public static function getPreviewByUrl($url) {
        $options = array(
            'http' => array(
                'method' => 'HEAD',
                'follow_location' => 0
            )
        );

        $context = stream_context_create($options);
        file_get_contents($url, NULL, $context);

        $headers = self::_parseHeaders($http_response_header);
        if (!empty($headers['Content-Type']) && mb_strpos($headers['Content-Type'], 'text/html') !== false) {
            $page = file_get_contents($url);
            $matches = [];
            preg_match('/<meta property=\"og:title\" content=\"(.*?)\"[\/]?>.*?<meta property=\"og:image\" content=\"(.*?)\"/s', $page,$matches);
            if (!$matches) {
                preg_match('/<title>(.*?)<\/title>.*?<img .*? src=\"(.*?)\"/s', $page,$matches);
            }
            foreach ($matches as &$match) {

                $match = iconv(mb_detect_encoding($match, mb_detect_order(), true), "UTF-8", $match);

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
}
