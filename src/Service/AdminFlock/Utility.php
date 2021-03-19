<?php

namespace App\Service\AdminFlock;

use Cron\CronExpression;
use DateTime;
use DateTimeImmutable;

class Utility
{
    static function clamp($value, $min, $max)
    {
        return max($min, min($max, $value));
    }

    static function toSnakeCase($value)
    {
        $value = preg_replace('/\s+/', '', $value);
        return preg_replace("/([a-z])([A-Z])/", "$1_$2", $value);
    }

    static function toPascalCase($value)
    {
        return ucfirst(str_replace('_', '', ucwords(strtolower($value), '_')));
    }

    static function toCamelCase($value)
    {
        return lcfirst(str_replace('_', '', ucwords(strtolower($value), '_')));
    }

    static function cleanProxyClassName(string $class)
    {
        return preg_replace("/Proxies\\\\([^\\\\]*)\\\\/", '', $class);
    }

    static function numval($val)
    {
        if (is_numeric($val)) {
            return $val + 0;
        }
        return 0;
    }

    /**
     * @param string $listString
     * @return array
     */
    static function listToArray(string $listString): array
    {
        $list = preg_split("/[,;]+/", trim($listString));

        array_walk($list, function(&$element) {
            $element = trim($element);
        });

        return array_filter($list, function($element) {
            return $element && trim($element) != '';
        });;
    }

    static function italianMonth($month)
    {
        $month = intval($month);

        switch ($month) {
            case 1:
                return "Gennaio";
            case 2:
                return "Febbraio";
            case 3:
                return "Marzo";
            case 4:
                return "Aprile";
            case 5:
                return "Maggio";
            case 6:
                return "Giugno";
            case 7:
                return "Luglio";
            case 8:
                return "Agosto";
            case 9:
                return "Settembre";
            case 10:
                return "Ottobre";
            case 11:
                return "Novembre";
            case 12:
                return "Dicembre";
        }
    }

    /**
     * @param string $text
     * @param array $parameters
     * @return string
     */
    static function replaceParameters(string $text, array $parameters)
    {
        if (!$parameters) {
            return $text;
        }

        $searches = [];
        foreach (array_keys($parameters) as $key) {
            $searches[] = '/{\{[\s]*([a-zA-Z_\-\.]+)[\s]*\}\}/';
        }

        return preg_replace_callback($searches, function($matches) use ($parameters) {
            return $parameters[$matches[1]];
        }, $text);
    }

    static function startsWith($haystack, $needle)
    {
        return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }

    static function endsWith($haystack, $needle)
    {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    static function getExcelColumnsList()
    {
        $column_sequence_base = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
        $column_sequence = [];

        foreach ($column_sequence_base as $letter) {
            $column_sequence[] = $letter;
        }

        foreach ($column_sequence_base as $startLetter) {
            foreach ($column_sequence_base as $letter) {
                $column_sequence[] = $startLetter . $letter;
            }
        }

        return $column_sequence;
    }

    static function convertExcelDate(string $excelDate): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp(self::excelTimestampToUnix(intval($excelDate)));
        return $date;
    }

    static function excelTimestampToUnix(string $excelDate)
    {
        return (($excelDate - 25569) * 86400);
    }

    static function convertExcelTime(string $excelTime): DateTime
    {
        $time = (floatval($excelTime) * 86400) - 3599;
        $date = new DateTime();
        $date->setTimestamp($time);
        $date->setDate(1970, 1, 1);
        return $date;
    }

    static function isTimestamp($timestamp)
    {
        if (ctype_digit($timestamp) && strtotime(date('Y-m-d H:i:s', $timestamp)) === (int)$timestamp) {
            return true;
        } else {
            return false;
        }
    }

    static function secToHourMin(int $seconds)
    {
        return sprintf("%02d:%02d", floor($seconds / 3600), ($seconds / 60) % 60);
    }

    static function cleanCrontabExpression(string $frequency)
    {
        return str_replace("\/", "/", $frequency);
    }

    static function evaluateCrontab($frequency = '* * * * *', $time = false)
    {
        $frequency = self::cleanCrontabExpression($frequency);
        $cron = CronExpression::factory($frequency);
        return $cron->isDue($time ?? 'now');
    }

    // keep for debugging crontab strings
    static function crontabDebug($frequency = '* * * * *')
    {
        print "Evaluating " . self::cleanCrontabExpression($frequency) . "\nI'm running at: \n\n";
        for ($i = 0; $i < 24; $i++) {
            $run = false;
            for ($j = 0; $j < 60; $j++) {
                $date = sprintf("%d:%02d", $i, $j);
                if (self::evaluateCrontab($frequency, $date)) {
                    print "$date\t";
                    $run = true;
                }
            }
            if ($run) print "\n";
        }

        print "\n\n";
    }

    static function padAndCenter(string $text, int $width, string $filler = ' ', int $padding = 0)
    {
        $text = substr($text, 0, $width);
        $start = $end = str_repeat($filler, floor(($width - strlen($text)) / 2) - $padding);
        $pad = str_repeat(' ', $padding);
        $length = strlen($start . $pad . $text . $pad . $end);
        if ($length > $width) {
            $start = substr($start, 1);
        } else if ($length < $width) {
            $end .= $filler;
        }
        return $start . $pad . $text . $pad . $end;
    }

    static function floorDateToMinutes(?DateTime $dateTime): ?DateTime
    {
        return $dateTime ? $dateTime->modify(sprintf('-%d seconds', (int)$dateTime->format('s'))) : null;
    }

}