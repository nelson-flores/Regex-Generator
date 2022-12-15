<?php

namespace Flores;
use stdClass;

/**
 * Build Regular Expressions
 * 
 * @author Nelson Flores <nelson.flores@live.com> 
 */
class Regex
{
    private static $instance;
    private $pattern = "";
    private $preg = "";
    private $delimiter = "'";
    private $debug = false;
    private $match_str = "TEST";
    private $match;
    private $rules = [];
    #Rules
    public static $RULE_UPPERCASE = "UpperCase";


    public function __construct($debug = false)
    {
        $this->debug = $debug;
    }

    public static function getInstance($debug = false)
    {
        if (Regex::$instance == null) {
            Regex::$instance = new Regex($debug);
        }

        return Regex::$instance;
    }


    private function build()
    {
        if ($this->preg !== $this->pattern) {
            $this->preg = $this->pattern;
        }
        return $this;
    }
    private function getRules()
    {
        $std = new stdClass();

        $std->toString = function () {
            return implode(", ", $this->rules);
        };

        $std->json = function () {
            return json_encode($this->rules);
        };

        $std->object = function () {
            return json_decode(json_encode($this->rules));
        };

        $std->arr = $this->rules;

        return $std;
    }

    private function test()
    {
        try {
            $this->match = preg_match($this->preg, $this->match_str);
            return $this;
        } catch (\Exception $e) {
            if ($this->debug) {
                throw new \Exception($this->preg . " : " . $e->getMessage());
            }
        }
        return $this;
    }

    private function setMatchStr($str){
        $this->match_str = $str;
        return $this;
    }
    private function getMatch(){
        return $this->match;
    }

    private function delimit()
    {
        if ((str_starts_with($this->preg, $this->delimiter) and str_ends_with($this->preg, $this->delimiter)) == false) {
            $this->preg = implode([
                $this->delimiter,
                $this->preg,
                $this->delimiter,
            ]);
        }
        return $this;
    }

    private function addRule($rule)
    {

        if (!in_array($this->rules, $rule)) {
            array_push($this->rules, $rule);
        }
        return $this;
    }
    

    /**
     * Set Custom Delimiter String
     * @param mixed $str
     * @return Regex
     */
    public function setDelimiter($str = "'")
    {
        $this->delimiter = $str;
        return $this;
    }
    /**
     * Get delimiter String
     * @param mixed $str
     * @return mixed|string
     */ 
    public function getDelimiter($str = "'")
    {
        return $this->delimiter;
    }

    /**
     * Use a custom Regex
     * @param mixed $pattern
     * @return Regex
     */
    public function custom($pattern = ".*")
    {
        $this->pattern .= $pattern;
        return $this;    
    }

    /**
     * Match String to Regex
     * @param mixed $str
     * @return bool|int
     */
    public function match($str)
    {
        return $this->setMatchStr($str)->build()->delimit()->test()->getMatch();   
    }

    /**
     * UpperCase, LowerCase, Number
     * @param int $min_size
     * @param int $max_size
     * @return Regex
     */
    public function alphaNumeric(int $min_size = 1, int $max_size = 100)
    {
        $size = "{" . $min_size . "," . (empty($max_size) ? "" : $max_size) . "}";
        $rules = "Must have UpperCase, LowerCase, Number";
        $this->pattern .= "[a-zA-Z0-9]" . $size . "+";
        return $this;
    }
    /**
     * UpperCase, LowerCase, Number
     * @param int $min_size
     * @param int $max_size
     * @return Regex
     */
    public function userName(int $min_size = 1, int $max_size = 20)
    {
        $size = "{" . $min_size . "," . (empty($max_size) ? "" : $max_size) . "}";
        $this->pattern .=  "^[a-zA-Z][a-zA-Z0-9-_\.]" . $size . "$";
        return $this;
    }
    /**
     * UpperCase, LowerCase, Number/SpecialChar
     * @param int $min_size
     * @param int $max_size
     * @return Regex
     */
    public function password(int $min_size = 8, int $max_size = null)
    {
        $size = "{" . $min_size . "," . (empty($max_size) ? "" : $max_size) . "}";
        $this->pattern .= "(?=^." . $size . "$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$";
        return $this;
    }

    /**
     * UpperCase, LowerCase and Number 
     * @return Regex
     */
    public function basicPassword()
    {
        $this->pattern .= "^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?!.*\s).*$";
        return $this;
    }


    /**
     * IPv4 Address
     * @return Regex
     */
    public function ipv4()
    {
        $this->pattern .= "((^|\.)((25[0-5])|(2[0-4]\d)|(1\d\d)|([1-9]?\d))){4}$";
        return $this;
    }

    /**
     * IPv6 Address
     * @return Regex
     */
    public function ipv6()
    {
        $this->pattern .= "((^|:)([0-9a-fA-F]{0,4})){1,8}$";
        return $this;
    }

    /**
     * Hexadecimal color
     * @return Regex
     */
    public function hexColor()
    {
        $this->pattern .= "^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$";
        return $this;
    }
    /**
     * Network domain
     * @return Regex
     */
    public function domain()
    {
        $this->pattern .= "^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$";
        return $this;
    }
    /**
     * Credit Card
     * @return Regex
     */
    public function creditCard()
    {
        $this->pattern .= "[0-9]{13,16}";
        return $this;
    }

    /**
     * Universally Unique Identifier
     * @return Regex
     */
    public function uuid()
    {
        $this->pattern .= "^[0-9A-Fa-f]{8}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{12}$";
        return $this;
    }
    /**
     * Universal ICQ Number
     * @return Regex
     */
    public function icq()
    {
        $this->pattern .= "^[0-9A-Fa-f]{8}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{4}\-[0-9A-Fa-f]{12}$";
        return $this;
    }
    /**
     * Datetime
     * @return Regex
     */
    public function time()
    {
        $this->pattern .= "(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}";
        return $this;
    }


    /**
     * HH:MM:SS
     * @return Regex
     */
    public function datetime()
    {
        $this->pattern .= "([0-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])T([0-5][0-9])\:([0-5][0-9])\:([0-5][0-9])(Z|([\-\+]([0-1][0-9])\:00))";
        return $this;
    } 

    /**
     * Get Regex Pattern
     * @return string
     */
    public function pattern()
    {
        return $this->pattern;
    }

    /**
     * Get Regex Pattern With Delimiter
     * @return string
     */
    public function preg()
    {
        return $this->build()->delimit()->test()->preg;
    }
}