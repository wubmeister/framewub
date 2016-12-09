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
    protected $params = [];

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
     * Matches a string against a regular expression. If there is a match, all matches will be mapped to the specified variable names.
     *
     * @param string $regexp
     *   The PCRE regeular expression to match agains
     * @param array $params
     *   The parameter names
     * @param string $string
     *   The string to match
     * @param bool $urlMode
     *   OPTIONAL. Flag to enable URL mode. In URL mode, slashes before a variable will be included in the regular expression subpatterns, so that optional URL parts can be matched properly.
     *
     * @return array
     *   An associative array with all the matches mapped to variable names (keys of the array). The key '*' holds the entire macthed string.
     *   If no match is found, this method will return null
     */
    public static function matchPattern($regex, $params, $string, $urlMode = false)
    {
        $result = [];
        array_unshift($params, '*');
        if (preg_match($regex, $string, $match)) {
            foreach ($match as $i => $m) {
                $result[$params[$i]] = $urlMode && $m[0] == '/' ? substr($m, 1) : $m;
            }
            return $result;
        }
        return null;
    }

    /**
     * Matches a string against the pattern. If there is a match, all matches will be mapped to the specified variable names.
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
        return self::matchPattern($this->regex, $this->params, $string, $this->urlMode);
    }

    /**
     * Uses the pattern as template to fill with values. The variables (keys) present in the specified array will be replaced. Optional variables (keys) which are not present in the specified array will be omitted from the resulting string. Non-optional variables (keys) which are not present in the specified array will be left as-is, with their curly braces.
     *
     * @param array $pattern
     *   The pattern
     * @param array $variables
     *   Associative array of variables
     * @param bool $urlMode
     *   OPTIONAL. Flag to enable URL mode. In URL mode, slashes before a variable will be included in the regular expression subpatterns, so that optional URL parts can be matched properly.
     *
     * @return string
     *   The resulting string
     */
    public static function buildPattern($pattern, $variables, $urlMode = false)
    {
        $tokens = [];
        foreach ($variables as $key => $value) {
            $tkey = '{' . $key . '}';
            if ($urlMode) {
                $tokens['/'.$tkey.'?'] = '/'.$value;
                $tokens['/'.$tkey] = '/'.$value;
            }
            $tokens[$tkey.'?'] = $value;
            $tokens[$tkey] = $value;
        }
        $result = strtr($pattern, $tokens);
        $result = preg_replace('/' . ($urlMode ? '\\/?' : '') . '\{[^\}]+\}\?/', '', $result);

        return $result;
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
        return self::buildPattern($this->pattern, $variables, $this->urlMode);
    }

    /**
     * Reveals the regular expression to the world
     *
     * @return string
     *   The regular expression
     */
    public function getRegex()
    {
        return $this->regex;
    }

    /**
     * Reveals the pattern to the world
     *
     * @return string
     *   The pattern
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Reveals the parameters to the world
     *
     * @return array
     *   The parameter names
     */
    public function getParams()
    {
        return $this->params;
    }
}