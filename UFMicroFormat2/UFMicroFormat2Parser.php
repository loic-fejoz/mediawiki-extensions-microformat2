<?php

class UFMicroFormat2Parser {
    /* See  https://doc.wikimedia.org/mediawiki-core/master/php/html/Sanitizer_8php_source.html */
    const EVIL_URI_PATTERN = '!(^|\s|\*/\s*)(javascript|vbscript)([^\w]|$)!i';

    public static function parseMicroFormat2(&$parser, $frame, $args) {
        /* First argument is the tag */
        $tag = array_shift($args);

        /* Last element is the content */
        $content = array_pop($args);
        if (isset($content)) {
            $content = $frame->expand($content);
        }

        /* Expand classes and filter out attributes*/
        $classes = array();
        $attributes = array();
        foreach ($args as $class) {
            $expanded = $frame->expand($class);
            if (UFMicroFormat2Parser::is_attribute($expanded)) {
                array_push($attributes, $expanded);
            } else {
                array_push($classes, $expanded);
            }
        }

        /* check for authorisation */
        if (!UFMicroFormat2Parser::is_authorised($tag, $classes)) {
            return wfMsgForContent('unauthorised_microformat2');
        }

        /* Output */
        $output = '<' . $tag;
        $output = $output . ' class="';
        foreach ($classes as $class) {
            $output = $output . $class . ' ';
        }
        $output = $output . '" ';
        foreach($attributes as $attr) {
            if ( !UFMicroFormat2Parser::contains_evil_value($attr) ) {
                $output = $output . ' ' . $attr;
            }            
        }
        $output = $output . '>' . $content . '</' . $tag . '>';
        return array( $output, 'noparse' => false, 'isHTML' => true );
    }

    public static function is_attribute($value) {
        return strpos($value, '=') !== false;
    }

    public static function contains_evil_value($attr) {
        $values = preg_split('/\s*=\s*("|\')\s*/', $attr);
        foreach($values as $v) {
            if ( preg_match( self::EVIL_URI_PATTERN, $v) ) {
                return true;
            }
        }
        return false;
    }

    public static function is_authorised($tag, $classes) {
        global $wgRawHtml;
        global $wgAllowImageTag;
        /* Everything is authorised if raw HTML is permitted by mediawiki configuration */
        if (isset( $wgRawHtml ) &&  $wgRawHtml) {
            return true;
        }
        if ($tag === 'a' or $tag === 'img') {
            /* <a> tag needs at least one of u-* classes*/
            /* <a> is ok for h-card */
            /* <img> tag needs at least one of u-* classes*/
            foreach($classes as $class) {
                if (strpos($class, 'u-') === 0) {
                    return true;
                }
                if (($class === 'h-card') && ($tag === 'a')) {
                    return true;
                }
            }
            return false;
        }
        if ($tag === 'time') {
            /* <time> tag needs at least one of dt-* classes*/
            foreach($classes as $class) {
                if (strpos($class, 'dt-') === 0) {
                    return true;
                }
            }
            return false;
        }
        $safe_tags = array('div', 'span', 'abbr', 'p');
        if (in_array($tag, $safe_tags)) {
            return true;
        }
        return false;
    }

    public static function registerHooks() {
        global $wgParser;
        $wgParser->setFunctionHook('uf2', 'UFMicroFormat2Parser::parseMicroFormat2', SFH_OBJECT_ARGS);
        return true;
    }
}
?>