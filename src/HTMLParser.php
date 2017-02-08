<?php
namespace HTMLParser;

class HTMLParser {

    /**
     * @var string
     * @access private
     */
    private $url;

    /**
     * @var string
     * @access private
     */
    private $html;

    public function __construct($url) {
        $this->url = $url ? $url : "";
    }

    public function getHTML($url = null) {
        $this->url  = $url ? $url : $this->url;
        $this->html = file_get_contents($this->url);
        return $this->html;
    }

    public function getMetaData() {
        $meta_array = [
            'title' => self::getMetaTitle(),
        ];
        return $meta_array;
    }

    private function getMetaTitle() {
        if (empty($this->html)) return null;
        preg_match_all("/<title[^>]*?>(.*?)<\/title[^>]*?>/si", $this->html, $titles);
        $title = $titles ? $titles[1][0] : NULL;

        if (!$title) {
            preg_match_all("'<meta[^>]*>'", $this->html, $metas);
            if (empty($metas)) return $title;
            $og_titles = preg_grep("/og:title/si", $metas[0]);
            if ($og_titles) {
                $got_title = false;
                foreach ($og_titles as $og_title) {
                    if (!$got_title) {
                        if (stripos($og_title, 'content="')) {
                            $title = substr($og_title, stripos($og_title, 'content="') + 9);
                        } else {
                            $title = substr($og_title, stripos($og_title, "content='") + 9);
                        }
                        if (stripos($title, '"')) {
                            $title = substr($title, 0, stripos($title, '"'));
                        } elseif (stripos($title, "'")) {
                            $title = substr($title, 0, stripos($title, "'"));
                        }
                        $got_title = true;
                    }
                }
            }
        }
        return $title;
    }
}
