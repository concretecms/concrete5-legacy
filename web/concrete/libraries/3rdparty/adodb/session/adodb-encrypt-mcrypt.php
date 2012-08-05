<?php

/*
V5.10 10 Nov 2009   (c) 2000-2009 John Lim (jlim#natsoft.com). All rights reserved.
         Contributed by Ross Smith (adodb@netebb.com).
  Released under both BSD license and Lesser GPL library license.
  Whenever there is any discrepancy between the two licenses,
  the BSD license will take precedence.
      Set tabs to 4 for best viewing.

*/

if (!function_exists('mcrypt_encrypt')) {
    trigger_error('Mcrypt functions are not available', E_USER_ERROR);

    return 0;
}

/**
 */
class ADODB_Encrypt_MCrypt
{
    /**
     */
    public $_cipher;

    /**
     */
    public $_mode;

    /**
     */
    public $_source;

    /**
     */
    public function getCipher()
    {
        return $this->_cipher;
    }

    /**
     */
    public function setCipher($cipher)
    {
        $this->_cipher = $cipher;
    }

    /**
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;
    }

    /**
     */
    public function getSource()
    {
        return $this->_source;
    }

    /**
     */
    public function setSource($source)
    {
        $this->_source = $source;
    }

    /**
     */
    public function ADODB_Encrypt_MCrypt($cipher = null, $mode = null, $source = null)
    {
        if (!$cipher) {
            $cipher = MCRYPT_RIJNDAEL_256;
        }
        if (!$mode) {
            $mode = MCRYPT_MODE_ECB;
        }
        if (!$source) {
            $source = MCRYPT_RAND;
        }

        $this->_cipher = $cipher;
        $this->_mode = $mode;
        $this->_source = $source;
    }

    /**
     */
    public function write($data, $key)
    {
        $iv_size = mcrypt_get_iv_size($this->_cipher, $this->_mode);
        $iv = mcrypt_create_iv($iv_size, $this->_source);

        return mcrypt_encrypt($this->_cipher, $key, $data, $this->_mode, $iv);
    }

    /**
     */
    public function read($data, $key)
    {
        $iv_size = mcrypt_get_iv_size($this->_cipher, $this->_mode);
        $iv = mcrypt_create_iv($iv_size, $this->_source);
        $rv = mcrypt_decrypt($this->_cipher, $key, $data, $this->_mode, $iv);

        return rtrim($rv, "\0");
    }

}

return 1;
