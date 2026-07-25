/**
 * Fixito Jalali – client-side Gregorian to Jalali for display
 */
(function (global) {
	'use strict';

	function gregorianToJalali(gy, gm, gd) {
		var g_d_m = [0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334];
		var gy2 = (gm > 2) ? (gy + 1) : gy;
		var days = 355666 + (365 * gy) + Math.floor((gy2 + 3) / 4) - Math.floor((gy2 + 99) / 100) + Math.floor((gy2 + 399) / 400) + gd + g_d_m[gm - 1];
		var jy = -1595 + (33 * Math.floor(days / 12053));
		days %= 12053;
		jy += 4 * Math.floor(days / 1461);
		days %= 1461;
		if (days > 365) {
			jy += Math.floor((days - 1) / 365);
			days = (days - 1) % 365;
		}
		var jm = (days < 186) ? 1 + Math.floor(days / 31) : 7 + Math.floor((days - 186) / 30);
		var jd = 1 + ((days < 186) ? (days % 31) : ((days - 186) % 30));
		return [jy, jm, jd];
	}

	function pad(n) {
		return (n < 10 ? '0' : '') + n;
	}

	function formatJalaliFromDate(d) {
		var r = gregorianToJalali(d.getFullYear(), d.getMonth() + 1, d.getDate());
		return r[0] + '/' + pad(r[1]) + '/' + pad(r[2]);
	}

	function toPersianDigits(str) {
		if (!global.fixitoPersianDigits) {
			return str;
		}
		return String(str).replace(/[0-9]/g, function (d) {
			return '۰۱۲۳۴۵۶۷۸۹'[d];
		});
	}

	function localizeDisplayedDates() {
		document.querySelectorAll('.fixito-jalali-date[data-ts]').forEach(function (el) {
			var ts = parseInt(el.getAttribute('data-ts'), 10);
			if (!ts) {
				return;
			}
			var d = new Date(ts * 1000);
			el.textContent = toPersianDigits(formatJalaliFromDate(d));
		});
		// Convert common Dolibarr date cells (MM/DD/YYYY or DD/MM/YYYY) when marked
		document.querySelectorAll('td.fixito-auto-jalali').forEach(function (el) {
			var m = el.textContent.trim().match(/(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})/);
			if (m) {
				var r = gregorianToJalali(parseInt(m[1], 10), parseInt(m[2], 10), parseInt(m[3], 10));
				el.textContent = toPersianDigits(r[0] + '/' + pad(r[1]) + '/' + pad(r[2]));
			}
		});
	}

	global.FixitoJalali = {
		gregorianToJalali: gregorianToJalali,
		formatJalaliFromDate: formatJalaliFromDate,
		localizeDisplayedDates: localizeDisplayedDates,
		toPersianDigits: toPersianDigits
	};

	global.fixitoPersianDigits = false;

	document.addEventListener('DOMContentLoaded', function () {
		localizeDisplayedDates();
	});
})(window);
