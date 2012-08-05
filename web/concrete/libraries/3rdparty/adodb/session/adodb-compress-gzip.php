<?php

/*
V5.10 10 Nov 2009   (c) 2000-2009 John Lim (jlim#natsoft.com). All rights reserved.
         Contributed by Ross Smith (adodb@netebb.com).
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
      Set tabs to 4 for best viewing.

*/

if (!function_exists('gzcompress')) {
    trigger_error('gzip functions are not available', E_USER_ERROR);

    return 0;
}

/*
*/
class ADODB_Compress_Gzip
{
    /**
     */
    public $_level = null;

    /**
     */
    public $_min_length = 1;

    /**
     */
    public function getLevel()
    {
        return $this->_level;
    }

    /**
     */
    public function setLevel($level)
    {
        assert('$level >= 0');
        assert('$level <= 9');
        $this->_level = (int) $level;
    }

    /**
     */
    public function getMinLength()
    {
        return $this->_min_length;
    }

    /**
     */
    public function setMinLength($min_length)
    {
        assert('$min_length >= 0');
        $this->_min_length = (int) $min_length;
    }

    /**
     */
    public function ADODB_Compress_Gzip($level = null, $min_length = null)
    {
        if (!is_null($level)) {
            $this->setLevel($level);
        }

        if (!is_null($min_length)) {
            $this->setMinLength($min_length);
        }
    }

    /**
     */
    public function write($data, $key)
    {
        if (strlen($data) < $this->_min_length) {
            return $data;
        }

        if (!is_null($this->_level)) {
            return gzcompress($data, $this->_level);
        } else {
            return gzcompress($data);
        }
    }

    /**
     */
    public function read($data, $key)
    {
        return $data ? gzuncompress($data) : $data;
    }

}

return 1;
