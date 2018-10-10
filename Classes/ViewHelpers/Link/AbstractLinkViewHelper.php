<?php

namespace AgoraTeam\Agora\ViewHelpers\Link;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Björn Christopher Bresser <bjoern.bresser@gmail.com>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * CreatorViewHelper
 */
abstract class AbstractLinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'a';

    /** @var $cObj ContentObjectRenderer */
    protected $cObj;

    /**
     * @var \AgoraTeam\Agora\Service\PaginationService
     */
    protected $paginationService;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager;

    /**
     * Injects the Configuration Manager and loads the settings
     *
     * @param \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
     */
    public function injectConfigurationManager(
        \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface $configurationManager
    ) {
        $this->configurationManager = $configurationManager;
    }

    /**
     * @param \AgoraTeam\Agora\Service\PaginationService $paginationService
     */
    public function injectPaginationService(\AgoraTeam\Agora\Service\PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    /**
     * registerLinkTagAttributes
     */
    protected function registerLinkTagAttributes()
    {
        $this->registerArgument('settings', 'array', 'Settings', false, []);
        $this->registerArgument('uriOnly', 'bool', 'url only', false, false);
        $this->registerArgument('configuration', 'array', 'configuration', false, []);
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerTagAttribute('section', 'string', 'Anchor for links', false);
    }

    /**
     * Merge Settings from fluid with typoscript settings
     *
     * @return mixed
     */
    protected function mergeSettings()
    {
        $settings = $this->arguments['settings'];

        $tsSettings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Agora',
            'Forum'
        );
        ArrayUtility::mergeRecursiveWithOverrule($tsSettings, (array)$settings);

        return $tsSettings;
    }

    /**
     * @return mixed
     */
    protected function getConfiguration()
    {
        $pageUid = $this->arguments['pageUid'];
        $configuration = $this->arguments['configuration'];
        $configuration['linkAccessRestrictedPages'] = true;

        if ('BE' == TYPO3_MODE && array_key_exists('forceAbsoluteUrl', $configuration)) {
            unset($configuration['forceAbsoluteUrl']);
        }

        if ($pageUid) {
            $configuration['parameter'] = $pageUid;
        }

        return $configuration;
    }

    /**
     * @param $configuration
     * @param $linkContent
     * @param $tsSettings
     * @return string
     */
    protected function renderTag($configuration, $linkContent, $tsSettings)
    {
        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $url = $this->cObj->typoLink_URL($configuration);
        if ($this->hasArgument('section')) {
            $url .= '#' . $this->arguments['section'];
        }

        if ('BE' == TYPO3_MODE) {
            $parsedUrl = parse_url($url);
            if (!array_key_exists('host', $parsedUrl)) {
                $fallbackHost = $tsSettings['email']['fallbackHost'];
                $url = $fallbackHost . $url;
            }
        }

        if ($this->arguments['uriOnly']) {
            return $url;
        }

        // link could not be generated
        if ($url === '' || $linkContent === $url) {
            return $linkContent;
        }

        $this->tag->addAttribute('href', $url);
        $this->tag->setContent($linkContent);

        return $this->tag->render();
    }
}
