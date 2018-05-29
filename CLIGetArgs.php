<?php

/**
 * PHP-CLI Parameters Handler
 *
 * You can check for parameter handling references on:
 * http://pubs.opengroup.org/onlinepubs/9699919799/basedefs/V1_chap12.html
 * http://www.gnu.org/software/libc/manual/html_node/Getopt-Long-Options.html
 *
 * The standards above are not fully handled by this class but we have a good start.
 *
 * Basically we have:
 *  - short options like: scriptname -a -b -cde -f value
 *  - long options like: scriptname --file=xyz
 *  - operands like: scriptname file_in.xyz file_out.xyz
 * Or all of them togheter:
 *  scriptname -abc c_value --option=value operand1 operand2
 *
 * Features not implemented:
 *  - options with required values
 *
 * @author Gustavo Jantsch <jantsch@gmail.com>
 */
class CLIGetArgs
{
    /**
     * @var CLIGetArgs
     */
    protected static $instance;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $operands;

    /**
     * @var string
     */
    protected $script;

    /**
     * @var int
     */
    public  $opPointer = 0;

    public function __construct()
    {
        global $argv, $argc;

        $this->options = [];
        $this->operands = [];
        $this->script = $argv[0];

        if (php_sapi_name() == 'cli' && $argc > 1) {

            for ($i = 1; $i < $argc; $i++) {

                $arg = $argv[$i];

                if (substr($arg, 0, 2) == '--') {
                    // is a long option
                    $arg = substr($arg, 2);
                    // if is a short parameter explode
                    if (strpos($arg, '=') === false) {
                        $this->options[$arg] = true;
                    } else {
                        $parts = explode('=', $arg);
                        $this->options[$parts[0]] = $parts[1];
                    }

                } elseif (substr($arg, 0, 1) == '-') {
                    // is a short option
                    $arg = substr($arg, 1);
                    $letters = str_split($arg);
                    foreach($letters as $letter) {
                        $this->options[$letter] = true;
                    }

                    // on single options if the next piece doesn't start with an - then we have
                    // the value of the last option
                    if (isset($argv[$i+1]) && substr($argv[$i+1], 0, 1) != '-') {
                        $this->options[$letter] = $argv[++$i];
                    }
                } else {
                    $this->operands[] = $arg;
                }
            }
        }
    }


    /**
     * @return CLIGetArgs
     */
    public static function getInstance() 
    {

        if (static::$instance === null) {
            static::$instance = new CLIGetArgs();
        }

        return static::$instance;

    }

    /**
     * @return mixed
     */
    public function getScript() 
    {
        return $this->script;
    }

    /**
     * @return int
     */
    public function count() 
    {
        return is_array($this->options) ? count($this->options) : 0;
    }

    /**
     * Get option
     *
     * @param string $option
     * @return null
     */
    public function get($option)
    {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

    /**
     * @return array
     */
    public function getOptions() 
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getOperands() 
    {
        return $this->operands;
    }

    /**
     * @return bool|mixed
     */
    public function popOperand() 
    {
        return $this->opPointer >= count($this->operands) ? false : $this->operands[$this->opPointer++];
    }

    /**
     * @return int
     */
    public function getOperandCounter() 
    {
        return count($this->operands);
    }
}
