<?php
/* Copyright (C) 2026 Artexx Pro / Fixito */

/**
 * \file       htdocs/custom/fixito/lib/fixito_jalali.lib.php
 * \brief      Gregorian ↔ Jalali conversion helpers
 */

/**
 * Convert Gregorian date to Jalali (Persian) calendar.
 *
 * @param int $gy Year
 * @param int $gm Month
 * @param int $gd Day
 * @return int[] [jy, jm, jd]
 */
function fixito_gregorian_to_jalali($gy, $gm, $gd)
{
	$g_d_m = array(0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334);
	$gy2 = ($gm > 2) ? ($gy + 1) : $gy;
	$days = 355666 + (365 * $gy) + (int) (($gy2 + 3) / 4) - (int) (($gy2 + 99) / 100) + (int) (($gy2 + 399) / 400) + $gd + $g_d_m[$gm - 1];
	$jy = -1595 + (33 * (int) ($days / 12053));
	$days %= 12053;
	$jy += 4 * (int) ($days / 1461);
	$days %= 1461;
	if ($days > 365) {
		$jy += (int) (($days - 1) / 365);
		$days = ($days - 1) % 365;
	}
	$jm = ($days < 186) ? 1 + (int) ($days / 31) : 7 + (int) (($days - 186) / 30);
	$jd = 1 + (($days < 186) ? ($days % 31) : (($days - 186) % 30));
	return array($jy, $jm, $jd);
}

/**
 * Convert Jalali date to Gregorian calendar.
 *
 * @param int $jy Year
 * @param int $jm Month
 * @param int $jd Day
 * @return int[] [gy, gm, gd]
 */
function fixito_jalali_to_gregorian($jy, $jm, $jd)
{
	$jy += 1595;
	$days = -355668 + (365 * $jy) + (int) ($jy / 33) * 8 + (int) ((($jy % 33) + 3) / 4) + $jd + (($jm < 7) ? ($jm - 1) * 31 : (($jm - 7) * 30) + 186);
	$gy = 400 * (int) ($days / 146097);
	$days %= 146097;
	if ($days > 36524) {
		$gy += 100 * (int) (--$days / 36524);
		$days %= 36524;
		if ($days >= 365) {
			$days++;
		}
	}
	$gy += 4 * (int) ($days / 1461);
	$days %= 1461;
	if ($days > 365) {
		$gy += (int) (($days - 1) / 365);
		$days = ($days - 1) % 365;
	}
	$gd = $days + 1;
	$sal_a = array(0, 31, (($gy % 4 === 0 && $gy % 100 !== 0) || ($gy % 400 === 0)) ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	$gm = 0;
	while ($gm < 13 && $gd > $sal_a[$gm]) {
		$gd -= $sal_a[$gm];
		$gm++;
	}
	return array($gy, $gm, $gd);
}

/**
 * Format Unix timestamp as Jalali date string for display.
 *
 * @param int|string $timestamp Unix timestamp
 * @param string     $pattern   day|dayhour|full
 * @return string
 */
function fixito_print_jalali_date($timestamp, $pattern = 'day')
{
	if (empty($timestamp) && $timestamp !== 0 && $timestamp !== '0') {
		return '';
	}
	$ts = (int) $timestamp;
	$gy = (int) gmdate('Y', $ts + (int) date('Z', $ts));
	$gm = (int) gmdate('n', $ts + (int) date('Z', $ts));
	$gd = (int) gmdate('j', $ts + (int) date('Z', $ts));
	list($jy, $jm, $jd) = fixito_gregorian_to_jalali($gy, $gm, $gd);
	$date = sprintf('%04d/%02d/%02d', $jy, $jm, $jd);
	if ($pattern === 'dayhour' || $pattern === 'full') {
		$date .= ' '.dol_print_date($ts, 'hour', 'tzserver');
	}
	return $date;
}

/**
 * Parse Jalali date string (Y/m/d) to Unix timestamp at noon server time.
 *
 * @param string $jalaliDate Date as YYYY/MM/DD or YYYY-MM-DD
 * @return int|false
 */
function fixito_jalali_string_to_timestamp($jalaliDate)
{
	$jalaliDate = preg_replace('/[^0-9\/\-]/', '', $jalaliDate);
	$parts = preg_split('/[\/\-]/', $jalaliDate);
	if (count($parts) < 3) {
		return false;
	}
	list($gy, $gm, $gd) = fixito_jalali_to_gregorian((int) $parts[0], (int) $parts[1], (int) $parts[2]);
	return dol_mktime(12, 0, 0, $gm, $gd, $gy);
}

/**
 * Convert digits to Persian if configured.
 *
 * @param string $str Input
 * @return string
 */
function fixito_to_persian_digits($str)
{
	if (!getDolGlobalString('FIXITO_PERSIAN_DIGITS')) {
		return $str;
	}
	$en = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
	$fa = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
	return str_replace($en, $fa, $str);
}
