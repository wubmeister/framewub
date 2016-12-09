<?php

/**
 * Utility to handle semi-regular expressions with variables.
 *
 * One would provide a pattern which can be matched against a string to return the matches mapped to variable names. This pattern could also be filled with variables stored in an associative array.
 *
 * @package    framewub/storage
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\Util;

/**
 * Variable expression class
 */
class VarExp
{
    /**
     * The basic pattern
     *
     * @var string
     */
    protected $pattern = '';

    /**
     * The regular expression (PCRE)
     *
     * @var string
     */
    protected $regex = '';

    /**
     * The parameter names in matching order
     *
     * @var string
     */
    protected $params = [ '*' ];

    /**
     * The URL mode flag
     *
     * @var string
     */
    protected $urlMode = false;

    /**
     * The constructor.
     *
     * @param string $pattern
     *   The pattern with variables. Variables can be specified with curly braces: "pattern with {variable} or {optional_variable}?"
     * @param bool $urlMode
     *   OPTIONAL. Flag to enable URL mode. In URL mode, slashes before a variable will be included in the regular expression subpatterns, so that optional URL parts can be matched properly.
     */
    public function __construct($pattern, $urlMode = false)
    {
        $this->urlMode = $urlMode;
        $this->pattern = preg_replace('/\{([^:]+):[^\}]\}/', '{$1}', $pattern);

        $pattern = str_replace('\\{', '#ESCACC#', $pattern);
        $matchExp = '/' . ($urlMode ? '\/?' : '') . '\{([a-zA-Z][a-zA-Z0-9_]*)(:([^\}]+))?\}(\?)?/';

        while (preg_match($matchExp, $pattern, $match, PREG_OFFSET_CAPTURE)) {
            $this->params[] = $match[1][0];
            if ($match[2][0]) {
                switch ($match[3][0]) {
                    case 'int':
                        $pat = '\d+';
                        break;
                    case 'float':
                        $pat = '\d*\.?\d+';
                        break;
                    case 'string':
                        $pat = '.*';
                        break;
                    default:
                        $pat = $match[3][0];
                        break;
                }
            } else {
                $pat = '.*';
            }

            $exp = $exp . str_replace('/', '\\/', preg_quote(substr($pattern, 0, $match[0][1]))) . "(" . ($urlMode ? '\\/' : '') . "{$pat}){$match[4][0]}";
            $pattern = substr($pattern, $match[0][1] + strlen($match[0][0]));
        }

        $exp .= preg_quote($pattern);

        $this->regex = '/^' . str_replace('#ESCACC#', '\{', $exp) . '$/';
    }

    /**
     * Matches the string against the pattern. If there is a match, all matches will be mapped to the specified variable names.
     *
     * @param string $string
     *   The string to match
     *
     * @return array
     *   An associative array with all the matches mapped to variable names (keys of the array). The key '*' holds the entire macthed string.
     *   If no match is found, this method will return null
     */
    public function match($string)
    {
        $result = [];
        if (preg_match($this->regex, $string, $match)) {
            foreach ($match as $i => $m) {
                $result[$this->params[$i]] = $this->urlMode && $m[0] == '/' ? substr($m, 1) : $m;
            }
            return $result;
        }
        return null;
    }

    /**
     * Uses the pattern as template to fill with values. The variables (keys) present in the specified array will be replaced. Optional variables (keys) which are not present in the specified array will be omitted from the resulting string. Non-optional variables (keys) which are not present in the specified array will be left as-is, with their curly braces.
     *
     * @param array $variables
     *   Associative array of variables
     *
     * @return string
     *   The resulting string
     */
    public function build($variables)
    {
        $tokens = [];
        foreach ($variables as $key => $value) {
            $tkey = '{' . $key . '}';
            if ($this->urlMode) {
                $tokens['/'.$tkey.'?'] = '/'.$value;
                $tokens['/'.$tkey] = '/'.$value;
            }
            $tokens[$tkey.'?'] = $value;
            $tokens[$tkey] = $value;
        }
        $result = strtr($this->pattern, $tokens);
        $result = preg_replace('/' . ($this->urlMode ? '\\/?' : '') . '\{[^\}]+\}\?/', '', $result);

        return $result;
    }
}