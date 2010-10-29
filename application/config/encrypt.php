<?php defined('SYSPATH') or die('No direct script access.');

return array(

	'default' => array(
		/**
		 * The following options must be set:
		 *
		 * string   key     secret passphrase
		 * integer  mode    encryption mode, one of MCRYPT_MODE_*
		 * integer  cipher  encryption cipher, one of the Mcrpyt cipher constants
		 */
        'key' => 'n3tmEtr1C_k3Y',
		'cipher' => MCRYPT_RIJNDAEL_128,
		'mode'   => MCRYPT_MODE_NOFB,
	),
    'cookies' => array(
        'key' => 'c0ok1e_n3tmEtr1C',
        'cipher' => MCRYPT_RIJNDAEL_256,
        'mode'  => MCRYPT_MODE_NOFB
    )

);
