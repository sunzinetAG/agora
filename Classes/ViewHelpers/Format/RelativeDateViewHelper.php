<?php

namespace AgoraTeam\Agora\ViewHelpers\Format;

/*  | This extension is part of the TYPO3 project. The TYPO3 project is
 *  | free software and is licensed under GNU General Public License.
 *  |
 *  | (c) 2018 Björn Bresser <bjoern.bresser@sunzinet.com>
 */
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Formats a unix timestamp to a human-readable, relative string
 *
 * Class RelativeDateViewHelper
 * @package AgoraTeam\Agora\ViewHelpers\Format
 */
class RelativeDateViewHelper extends AbstractViewHelper
{

    /**
     * @var bool $dateIsAbsolute
     */
    protected $dateIsAbsolute = false;

    /**
     * Render the supplied unix timestamp in a localized human-readable string.
     *
     * @param \DateTime $timestamp unix timestamp
     * @param string $format Format String to be parsed by strftime
     * @param string $wrap Uses sprintf to wrap relative date (use %s for date)
     * @param string $wrapAbsolute Same like $wrap, but used if date is absolute
     * @return string Formatted date
     */
    public function render($timestamp = null, $format = null, $wrap = '%s', $wrapAbsolute = '%s')
    {
        $this->dateIsAbsolute = false;
        $timestamp = $this->normalizeTimestamp($timestamp);
        $relativeDate = $this->makeDateRelative($timestamp, $format);
        if ($this->dateIsAbsolute === true) {
            return sprintf($wrapAbsolute, $relativeDate);
        }

        return sprintf($wrap, $relativeDate);
    }

    /**
     * handle all the different input formats and return a real timestamp
     *
     * @param int|string|\DateTime|null $timestamp
     * @return int
     */
    protected function normalizeTimestamp($timestamp)
    {
        if (is_null($timestamp)) {
            $timestamp = time();
        } elseif (is_numeric($timestamp)) {
            $timestamp = intval($timestamp);
        } elseif (is_string($timestamp)) {
            $timestamp = strtotime($timestamp);
        } elseif ($timestamp instanceof \DateTime) {
            $timestamp = $timestamp->format('U');
        } else {
            throw new \InvalidArgumentException('Timestamp might be an integer, a string or a DateTimeObject only.');
        }

        return $timestamp;
    }

    /**
     * Makes a given unix timestamp relative and returns the string.
     *
     * @param int $timestamp Unix timestamp to make relative
     * @param string $format Format to use, if relative time is older than 4 weeks
     * @return string Relative time or formatted time
     */
    protected function makeDateRelative($timestamp, $format = null)
    {
        $diff = time() - $timestamp;
        if ($diff < 60) {
            return $this->getLabel('fewSeconds');
        }

        $diff = round($diff / 60);
        if ($diff < 60) {
            return $diff . ' ' . $this->getLabel('minute') . $this->plural($diff);
        }

        $diff = round($diff / 60);
        if ($diff < 24) {
            return $diff . ' ' . $this->getLabel('hour') . $this->plural($diff);
        }

        $diff = round($diff / 24);
        if ($diff < 7) {
            return $diff . ' ' . $this->getLabel('day') . $this->plural($diff, 'forDay');
        }

        $diff = round($diff / 7);
        if ($diff < 4) {
            return $diff . ' ' . $this->getLabel('week') . $this->plural($diff);
        }

        $this->dateIsAbsolute = true;

        return strftime($format, $timestamp);
    }

    /**
     * Returns plural suffix, if given integer is greater than one
     *
     * @param int $num Integer which defines if it is plural or not
     * @param string $suffix Suffix to add to key of plural suffix
     * @return string Returns the plural suffix (may be empty)
     */
    protected function plural($num, $suffix = '')
    {
        return ($num > 1) ? $this->getLabel('pluralSuffix' . ucfirst($suffix)) : '';
    }

    /**
     * Shortcut for translate method
     *
     * @param string $key the key as string
     * @return string string which matches the key, containing in locallang.xml
     */
    protected function getLabel($key)
    {
        return LocalizationUtility::translate('tx_agora.relativeDate.' . $key, 'Agora');
    }
}
