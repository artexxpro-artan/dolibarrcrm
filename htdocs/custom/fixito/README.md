# Fixito – Iranized CRM for Dolibarr (Artexx Pro)

ماژول **Fixito** برای استقرار روی `dolibarrcrm.artexxpro.ir` طراحی شده و تجربه‌ای شبیه CRMهای ایرانی (مثل دیدار) روی Dolibarr فراهم می‌کند:

- راست‌چین و فونت فارسی (Vazirmatn)
- تقویم شمسی در نمایش و ورودی تاریخ
- ثبت سریع **معامله** (پیشنهاد + فرصت فروش)
- ثبت سریع **فروش** (سفارش)
- ثبت و پیگیری **گارانتی**

## نصب

1. در `conf/conf.php` مسیر custom را فعال کنید:

```php
$dolibarr_main_url_root_alt='/custom';
$dolibarr_main_document_root_alt='/path/to/htdocs/custom/';
```

2. از **خانه → راه‌اندازی → ماژول‌ها** ماژول **Fixito** را فعال کنید.

3. در **کاربر → زبان** مقدار `fa_IR` و در **شرکت** کشور ایران و ارز `IRR` را تنظیم کنید.

4. از منوی بالا **فیکسیتو** وارد داشبورد شوید.

## صفحات اصلی

| مسیر | کاربرد |
|------|--------|
| `/fixito/fixitoindex.php` | داشبورد و میانبرها |
| `/fixito/quickdeal.php` | ثبت معامله در یک فرم |
| `/fixito/quicksale.php` | ثبت فروش سریع |
| `/fixito/warranty_list.php` | لیست گارانتی |
| `/fixito/warranty_card.php` | کارت گارانتی |

## تنظیمات

`/fixito/admin/setup.php` – شمسی‌سازی، RTL، مدت پیش‌فرض گارانتی، ارقام فارسی.

## مجوز

GPL v3+ (هم‌راستا با Dolibarr)
