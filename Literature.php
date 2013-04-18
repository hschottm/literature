<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight webCMS
 * Copyright (C) 2005 Leo Feyer
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at http://www.gnu.org/licenses/.
 *
 * PHP version 5
 * @copyright  Helmut Schottmüller 20089
 * @author     Helmut Schottmüller <typolight@aurealis.de>
 * @package    literature 
 * @license    LGPL 
 * @filesource
 */


/**
 * Class Literature
 *
 * @copyright  Helmut Schottmüller 2009
 * @author     Helmut Schottmüller <typolight@aurealis.de>
 * @package    Controller
 */
class Literature
{
	/**
	 * Callback to check the ISBN of an ISBN text input field
	 * @param string
	 * @param string
	 * @param object
	 * @return boolean
	 */
	public function checkISBN($strRegexp, $varValue, Widget $objWidget)
	{
		if ($strRegexp == 'isbn')
		{
			// remove non numbers
			$isbn = preg_replace("/[^0-9X]/", "", $varValue);
			if (preg_match("/^([0-9]{9}X)|([0-9]{10})|([0-9]{13})$/", $isbn, $matches))
			{
				if (strlen($isbn) == 10)
				{
					$check = 0;
					for ($i = 0; $i < 9; $i++) $check += (10 - $i) * substr($isbn, $i, 1);
					$t = substr($isbn, 9, 1); // tenth digit (aka checksum or check digit)
					$check += ($t == 'x' || $t == 'X') ? 10 : $t;
					$valid = $check % 11 == 0;
					if (!$valid)
					{
						$objWidget->addError(sprintf($GLOBALS['TL_LANG']['tl_literature']['errWrongISBN10'], $objWidget->label));
					}
				}
				else if (strlen($isbn) == 13)
				{
					$check = 0;
					for ($i = 0; $i < 13; $i+=2) $check += substr($isbn, $i, 1);
					for ($i = 1; $i < 12; $i+=2) $check += 3 * substr($isbn, $i, 1);
					$valid = $check % 10 == 0;
					if (!$valid)
					{
						$objWidget->addError(sprintf($GLOBALS['TL_LANG']['tl_literature']['errWrongISBN13'], $objWidget->label));
					}
				}
				else
				{
					$objWidget->addError(sprintf($GLOBALS['TL_LANG']['tl_literature']['errNoISBN'], $objWidget->label));
				}
			}
			else
			{
				$objWidget->addError(sprintf($GLOBALS['TL_LANG']['tl_literature']['errNoISBN'], $objWidget->label));
			}
			return true;
		} else if ($strRegexp == 'issn')
		{
			if (preg_match("/^([0-9]{4})-([0-9]{3}[0-9X])$/", $varValue, $matches))
			{
				// calculate checksum
				$sum = $varValue[0]*8 + $varValue[1]*7 + $varValue[2]*6 + $varValue[3]*5 + $varValue[5]*4 + $varValue[6]*3 + $varValue[7]*2;
				$mod = $sum % 11;
				$last = 0;
				if ($mod > 0)
				{
					$last = 11 - $mod;
					if ($last == 10) $last = 'X';
				}
				if ($last != $varValue[8])
				{
					$objWidget->addError(sprintf($GLOBALS['TL_LANG']['tl_literature']['errWrongISSN'], $objWidget->label));
				}
			}
			else
			{
				$objWidget->addError(sprintf($GLOBALS['TL_LANG']['tl_literature']['errWrongISSNFormat'], $objWidget->label));
			}
			return true;
		}
		return false;
	}
}

?>