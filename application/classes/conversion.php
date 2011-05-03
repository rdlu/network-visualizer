<?php
/*
 * conversion
 *
 * This class convert units to the SI/IEC standard. I have developed
 * this class for my framework psx <http://phpsx.org> but you can also use
 * this outside.
 *
 * Copyright (c) 2009 Christoph Kappestein <k42b3.x@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author  Christoph Kappestein <k42b3.x@gmail.com>
 * @license http://www.gnu.org/licenses/gpl.html GPLv3
 * @link    http://phpsx.org
 */


/**
 * conversion_exception
 *
 */
class conversion_exception extends exception
{
}

/**
 * conversion
 *
 */
class conversion
{
	/**
	 * The IEC standard
	 *
	 */
	private $bi = array(

		'Y' => 1208925819614629174706176, # yobi
		'Z' => 1180591620717411303424,    # zebi
		'E' => 1152921504606846976,       # exbi
		'P' => 1125899906842624,          # pebi
		'T' => 1099511627776,             # tebi
		'G' => 1073741824,                # gibi
		'M' => 1048576,                   # mebi
		'K' => 1024,                      # kibi

	);

	/**
	 * The SI standard
	 *
	 */
	private $si = array(

		'Y'  => 1000000000000000000000000, # Yotta
		'Z'  => 1000000000000000000000,    # Zetta
		'E'  => 1000000000000000000,       # Exa
		'P'  => 1000000000000000,          # Peta
		'T'  => 1000000000000,             # Tera
		'G'  => 1000000000,                # Giga
		'M'  => 1000000,                   # Mega
		'k'  => 1000,                      # Kilo
		'h'  => 100,                       # Hekto
		'da' => 10,                        # Deka
		''   => 1,                         # Unit
		'd'  => 0.1,                       # Dezi
		'c'  => 0.01,                      # Zenti
		'm'  => 0.001,                     # Milli
		'U'  => 0.000001,                  # Mikro
		'n'  => 0.000000001,               # Nano
		'p'  => 0.000000000001,            # Pico
		'f'  => 0.000000000000001,         # Femto
		'a'  => 0.000000000000000001,      # Atto
		'z'  => 0.000000000000000000001,   # Zepto
		'y'  => 0.000000000000000000000001 # Yocto

	);

	public function bi($byte, $decimal_place = 2)
	{
		foreach($this->bi as $u => $v)
		{
			if($byte >= $v)
			{
				$r = $byte / $v;

				return round($r, $decimal_place) . ' ' . $u . 'b';
			}
		}

		return $byte . ' byte';
	}

	public function bitpersecond($bit) {
		return $this->bit($bit).'ps';
	}

	public function bit($byte)
	{
		if($byte < 1024)
		{
			return $byte . ' b';
		}
		else
		{
			return $this->bi($byte/8);
		}
	}

	public function byte($byte)
	{
		if($byte < 1000)
		{
			return $byte . ' byte';
		}
		else
		{
			return $this->si('B', $byte);
		}
	}


	public function milisseconds($s) {
		return $s*1000 ." ms";
	}

	public function percent($num) {
		return $num*100 ."%";
	}

	public function perc($num) {
		return $num."%";
	}

	public function meter($meter)
	{
		return $this->si('m', $meter);
	}

	public function gram($gram)
	{
		return $this->si('g', $gram);
	}

	public function seconds($seconds)
	{
		return $this->si('s', $seconds);
	}

	public function si($unit, $value, $decimal_place = 2)
	{
		foreach($this->si as $u => $v)
		{
			if($value >= $v)
			{
				$r = $value / $v;

				return round($r, $decimal_place) . ' ' . $u . $unit;
			}
		}

		$r = $value / end($this->si);
		$u = key($this->si);

		return round($r, $decimal_place) . ' ' . $u . $unit;
	}
}