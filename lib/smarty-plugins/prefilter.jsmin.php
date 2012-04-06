<?php
/**
 * --- Smarty JSMin prefilter plugin ---
 *
 * This plugin provides a simple wrapper for the PHP implementation of Douglas Crockford's JSMin (PHP port by Ryan Grove).
 *
 * Usage: Simply enclose any JavaScript in a {jsmin} ... {/jsmin} block.
 *
 * NB:     If you are also using a {literal} ... {/literal} block for your Javascript,
 *        this should be enclosed within the {jsmin} ... {/jsmin} block.
 *
 * File:     prefilter.jsmin.php
 * Type:     prefilter
 * Name:     jsmin
 * Purpose:  Minifies inline javascrip from within a Smarty template.
 *
 * @author Neil Smith <jsmin at nsmith dot me dot uk>
 * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright 2007 Ryan Grove <ryan@wonko.com> (PHP port)
 * @copyright 2007 Neil Smith <jsmin@nsmith.me.uk> (Smarty Wrapper)
 * @link http://www.nsmith.me.uk/jsmin-smarty-wrapper/
 * @link http://code.google.com/p/jsmin-php/
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.0.0 (2007-10-10)
 */
function smarty_prefilter_jsmin($source, &$smarty){

    $source = preg_replace_callback(
        '|\{jsmin\}(.*?)\{\/jsmin\}|s',
        'smarty_prefilter_jsmin_callback',
        $source
    );

    return $source;
}

function smarty_prefilter_jsmin_callback( $matches ){

    return SmartyJSMin::minify( $matches[1] );

}

// -- jsmin --------------------------------------------------------------------

/**
 * jsmin.php - PHP implementation of Douglas Crockford's JSMin.
 *
 * This is pretty much a direct port of jsmin.c to PHP with just a few
 * PHP-specific performance tweaks. Also, whereas jsmin.c reads from stdin and
 * outputs to stdout, this library accepts a string as input and returns another
 * string as output.
 *
 * PHP 5 or higher is required.
 *
 * Permission is hereby granted to use this version of the library under the
 * same terms as jsmin.c, which has the following license:
 *
 * --
 * Copyright (c) 2002 Douglas Crockford  (www.crockford.com)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * The Software shall be used for Good, not Evil.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * --
 *
 * @package JSMin
 * @author Ryan Grove <ryan@wonko.com>
 * @copyright 2002 Douglas Crockford <douglas@crockford.com> (jsmin.c)
 * @copyright 2007 Ryan Grove <ryan@wonko.com> (PHP port)
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @version 1.1.0 (2007-06-01)
 * @link http://code.google.com/p/jsmin-php/
 */

    class SmartyJSMin {
      const ORD_LF    = 10;
      const ORD_SPACE = 32;

      protected $a           = '';
      protected $b           = '';
      protected $input       = '';
      protected $inputIndex  = 0;
      protected $inputLength = 0;
      protected $lookAhead   = null;
      protected $output      = array();

      // -- Public Static Methods --------------------------------------------------

      public static function minify($js) {
        $SmartyJSMin = new SmartyJSMin($js);
        return $SmartyJSMin->min();
      }

      // -- Public Instance Methods ------------------------------------------------

      public function __construct($input) {
        $this->input       = str_replace("\r\n", "\n", $input);
        $this->inputLength = strlen($this->input);
      }

      // -- Protected Instance Methods ---------------------------------------------

      protected function action($d) {
        switch($d) {
          case 1:
            $this->output[] = $this->a;

          case 2:
            $this->a = $this->b;

            if ($this->a === "'" || $this->a === '"') {
              for (;;) {
                $this->output[] = $this->a;
                $this->a        = $this->get();

                if ($this->a === $this->b) {
                  break;
                }

                if (ord($this->a) <= self::ORD_LF) {
                  throw new SmartySmartyJSMinException('Unterminated string literal.');
                }

                if ($this->a === '\\') {
                  $this->output[] = $this->a;
                  $this->a        = $this->get();
                }
              }
            }

          case 3:
            $this->b = $this->next();

            if ($this->b === '/' && (
                $this->a === '(' || $this->a === ',' || $this->a === '=' ||
                $this->a === ':' || $this->a === '[' || $this->a === '!' ||
                $this->a === '&' || $this->a === '|' || $this->a === '?')) {

              $this->output[] = $this->a;
              $this->output[] = $this->b;

              for (;;) {
                $this->a = $this->get();

                if ($this->a === '/') {
                  break;
                }
                elseif ($this->a === '\\') {
                  $this->output[] = $this->a;
                  $this->a        = $this->get();
                }
                elseif (ord($this->a) <= self::ORD_LF) {
                  throw new SmartySmartyJSMinException('Unterminated regular expression '.
                      'literal.');
                }

                $this->output[] = $this->a;
              }

              $this->b = $this->next();
            }
        }
      }

      protected function get() {
        $c = $this->lookAhead;
        $this->lookAhead = null;

        if ($c === null) {
          if ($this->inputIndex < $this->inputLength) {
            $c = $this->input[$this->inputIndex];
            $this->inputIndex += 1;
          }
          else {
            $c = null;
          }
        }

        if ($c === "\r") {
          return "\n";
        }

        if ($c === null || $c === "\n" || ord($c) >= self::ORD_SPACE) {
          return $c;
        }

        return ' ';
      }

      protected function isAlphaNum($c) {
        return ord($c) > 126 || $c === '\\' || preg_match('/^[\w\$]$/', $c) === 1;
      }

      protected function min() {
        $this->a = "\n";
        $this->action(3);

        while ($this->a !== null) {
          switch ($this->a) {
            case ' ':
              if ($this->isAlphaNum($this->b)) {
                $this->action(1);
              }
              else {
                $this->action(2);
              }
              break;

            case "\n":
              switch ($this->b) {
                case '{':
                case '[':
                case '(':
                case '+':
                case '-':
                  $this->action(1);
                  break;

                case ' ':
                  $this->action(3);
                  break;

                default:
                  if ($this->isAlphaNum($this->b)) {
                    $this->action(1);
                  }
                  else {
                    $this->action(2);
                  }
              }
              break;

            default:
              switch ($this->b) {
                case ' ':
                  if ($this->isAlphaNum($this->a)) {
                    $this->action(1);
                    break;
                  }

                  $this->action(3);
                  break;

                case "\n":
                  switch ($this->a) {
                    case '}':
                    case ']':
                    case ')':
                    case '+':
                    case '-':
                    case '"':
                    case "'":
                      $this->action(1);
                      break;

                    default:
                      if ($this->isAlphaNum($this->a)) {
                        $this->action(1);
                      }
                      else {
                        $this->action(3);
                      }
                  }
                  break;

                default:
                  $this->action(1);
                  break;
              }
          }
        }

        return implode('', $this->output);
      }

      protected function next() {
        $c = $this->get();

        if ($c === '/') {
          switch($this->peek()) {
            case '/':
              for (;;) {
                $c = $this->get();

                if (ord($c) <= self::ORD_LF) {
                  return $c;
                }
              }

            case '*':
              $this->get();

              for (;;) {
                switch($this->get()) {
                  case '*':
                    if ($this->peek() === '/') {
                      $this->get();
                      return ' ';
                    }
                    break;

                  case null:
                    throw new SmartySmartyJSMinException('Unterminated comment.');
                }
              }

            default:
              return $c;
          }
        }

        return $c;
      }

      protected function peek() {
        $this->lookAhead = $this->get();
        return $this->lookAhead;
      }
    }

    // -- Exceptions ---------------------------------------------------------------
    class SmartyJSMinException extends Exception {}

?>