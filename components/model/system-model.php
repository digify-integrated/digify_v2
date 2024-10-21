<?php

class SystemModel {
    # -------------------------------------------------------------
    public function timeElapsedString($dateTime) { 
        $timestamp = strtotime($dateTime);
        if ($timestamp === false) {
            return 'Invalid date';
        }
    
        $currentTimestamp = time();
        $diffSeconds = $currentTimestamp - $timestamp;
    
        if ($diffSeconds > 86400) {
            return date('M j, Y \a\t h:i:s A', $timestamp);
        }
    
        $intervals = [
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        ];
    
        if ($diffSeconds < 60) {
            return 'Just Now';
        }
    
        foreach ($intervals as $seconds => $label) {
            $count = floor($diffSeconds / $seconds);
            if ($count > 0) {
                return sprintf('%d %s ago', $count, $label . ($count > 1 ? 's' : ''));
            }
        }
    
        return date('M j, Y \a\t h:i:s A', $timestamp);
    }    
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function yearMonthElapsedComparisonString($startDateTime, $endDateTime) {
        $startDate = DateTime::createFromFormat('d F Y', '01 ' . $startDateTime);
        $endDate = DateTime::createFromFormat('d F Y', '01 ' . $endDateTime);

        if ($startDate && $endDate) {
            $interval = $startDate->diff($endDate);
            $years = $interval->y;
            $months = $interval->m;

            $elapsedTime = [];
            if ($years > 0) {
                $elapsedTime[] = $years . ' ' . ($years === 1 ? 'year' : 'years');
            }
            if ($months > 0) {
                $elapsedTime[] = $months . ' ' . ($months === 1 ? 'month' : 'months');
            }

            return $elapsedTime ? implode(' and ', $elapsedTime) : 'Just Now';
        }
        return 'Error parsing dates';
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkDate($type, $date, $time, $format, $modify, $systemDate = null, $systemTime = null) {
        $systemDate = $systemDate ?? date('Y-m-d');
        $systemTime = $systemTime ?? date('H:i:s');

        if (empty($date)) {
            return $this->getDefaultReturnValue($type, $systemDate, $systemTime);
        }

        return $this->formatDate($format, $date, $modify) . ($this->needsTime($type) ? ' ' . $time : '');
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function getDefaultReturnValue($type, $systemDate, $systemTime) {
        switch ($type) {
            case 'default':
                return $systemDate;
            case 'empty':
            case 'attendance empty':
            case 'summary':
                return null;
            case 'na':
                return 'N/A';
            case 'complete':
            case 'encoded':
            case 'date time':
                return 'N/A';
            case 'default time':
                return $systemTime;
            default:
                return null;
        }
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    private function needsTime($type) {
        return in_array($type, ['complete', 'encoded', 'date time']);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function formatDate($format, $date, $modify = null) {
        $dateTime = new DateTime($date);
        if ($modify) {
            $dateTime->modify($modify);
        }
        return $dateTime->format($format);
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getDefaultImage($type) {
        $defaultImages = [
            'profile' => DEFAULT_AVATAR_IMAGE,
            'login background' => DEFAULT_BG_IMAGE,
            'login logo' => DEFAULT_LOGIN_LOGO_IMAGE,
            'menu logo' => DEFAULT_MENU_LOGO_IMAGE,
            'module icon' => DEFAULT_MODULE_ICON_IMAGE,
            'favicon' => DEFAULT_FAVICON_IMAGE,
            'company logo' => DEFAULT_COMPANY_LOGO,
            'id placeholder front' => DEFAULT_ID_PLACEHOLDER_FRONT,
            'app module logo' => DEFAULT_APP_MODULE_LOGO,
            'upload placeholder' => DEFAULT_UPLOAD_PLACEHOLDER,
        ];

        return $defaultImages[$type] ?? DEFAULT_PLACEHOLDER_IMAGE;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function checkImage($image, $type) {
        $image = $image ?? '';
        $imagePath = str_replace('./apps/', '../../../../apps/', $image);

        return (empty($image) || !file_exists($imagePath) && !file_exists($image))
            ? $this->getDefaultImage($type)
            : $image;
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getFileExtensionIcon($type) {
        $defaultImages = [
            'ai' => './assets/images/file-icon/img-file-ai.svg',
            'doc' => './assets/images/file-icon/img-file-doc.svg',
            'docx' => './assets/images/file-icon/img-file-doc.svg',
            'jpeg' => './assets/images/file-icon/img-file-img.svg',
            'jpg' => './assets/images/file-icon/img-file-img.svg',
            'png' => './assets/images/file-icon/img-file-img.svg',
            'gif' => './assets/images/file-icon/img-file-img.svg',
            'pdf' => './assets/images/file-icon/img-file-pdf.svg',
            'ppt' => './assets/images/file-icon/img-file-ppt.svg',
            'pptx' => './assets/images/file-icon/img-file-ppt.svg',
            'rar' => './assets/images/file-icon/img-file-rar.svg',
            'txt' => './assets/images/file-icon/img-file-txt.svg',
            'xls' => './assets/images/file-icon/img-file-xls.svg',
            'xlsx' => './assets/images/file-icon/img-file-xls.svg',
        ];

        return $defaultImages[$type] ?? './assets/images/file-icon/img-file-img.svg';
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function getFormatBytes($bytes, $precision = 2) {
        $units = ['B', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];

        $bytes = max($bytes, 0);
        $pow = floor(log($bytes ?: 1, 1024));
        return round($bytes / (1 << (10 * $pow)), $precision) . ' ' . $units[$pow];
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function generateMonthOptions() {
        $months = [
            'January', 'February', 'March', 'April',
            'May', 'June', 'July', 'August',
            'September', 'October', 'November', 'December'
        ];

        return implode('', array_map(function ($month, $index) {
            return sprintf('<option value="%d">%s</option>', htmlspecialchars($index + 1, ENT_QUOTES), htmlspecialchars($month, ENT_QUOTES));
        }, $months, array_keys($months)));
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function generateYearOptions($minYear, $maxYear = null) {
        if ($maxYear === null) {
            $maxYear = date('Y');
        }

        return implode('', array_map(function ($year) {
            return sprintf('<option value="%d">%d</option>', htmlspecialchars($year, ENT_QUOTES), htmlspecialchars($year, ENT_QUOTES));
        }, range($maxYear, $minYear)));
    }
    # -------------------------------------------------------------

    # -------------------------------------------------------------
    public function displaySummary($summaryText) {
        return !empty($summaryText) ? $summaryText : '--';
    }
    # -------------------------------------------------------------
}