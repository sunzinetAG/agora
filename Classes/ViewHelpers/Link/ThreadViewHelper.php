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
use AgoraTeam\Agora\Domain\Model\Thread;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * CreatorViewHelper
 */
class ThreadViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
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
     * initializeArguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();

        $this->registerArgument('thread', Thread::class, 'Thread item', true);
        $this->registerArgument('settings', 'array', 'Settings', false, []);
        $this->registerArgument('uriOnly', 'bool', 'url only', false, false);
        $this->registerArgument('configuration', 'array', 'configuration', false, []);
        $this->registerArgument('pageUid', 'int', 'Target page. See TypoLink destination');
        $this->registerTagAttribute('section', 'string', 'Anchor for links', false);
    }

    /**
     * Render link to post in thread
     *
     * @return string link
     */
    public function render()
    {
        /** @var Thread $thread */
        $thread = $this->arguments['thread'];
        $pageUid = $this->arguments['pageUid'];
        $settings = $this->arguments['settings'];
        $uriOnly = $this->arguments['uriOnly'];
        $configuration = $this->arguments['configuration'];

        $tsSettings = (array)$this->getSettings();
        ArrayUtility::mergeRecursiveWithOverrule($tsSettings, (array)$settings);
        $linkContent = $this->renderChildren();

        // CHeck if post is given
        if (is_null($thread)) {
            return $linkContent;
        }
        $this->cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $configuration = $this->getLinkToThread($thread, $tsSettings, $configuration);

        if ($pageUid) {
            $configuration['parameter'] = $pageUid;
        }

        $url = $this->cObj->typoLink_URL($configuration);
        if ($uriOnly) {
            return $url;
        }

        // link could not be generated
        if ($url === '' || $linkContent === $url) {
            return $linkContent;
        }

        if (!$this->tag->hasAttribute('target')) {
            if (!empty($target)) {
                $this->tag->addAttribute('target', $target);
            }
        }

        if ($this->hasArgument('section')) {
            $url .= '#' . $this->arguments['section'];
        }

        $this->tag->addAttribute('href', $url);
        $this->tag->setContent($linkContent);

        return $this->tag->render();
    }

    /**
     * @param Thread $thread
     * @param array $settings
     * @param array $configuration
     * @return array
     */
    protected function getLinkToThread(Thread $thread, $settings, array $configuration = [])
    {
        $detailPid = $GLOBALS['TSFE']->id;
        $configuration['parameter'] = $detailPid;

        $paginationSite = $this->paginationService->getThreadPagePosition($thread, $settings);
        if ($paginationSite > 1) {
            $configuration['additionalParams'] .= '&tx_agora_forum[page]=' . $paginationSite;
        }

        $configuration['additionalParams'] .= '&tx_agora_forum[controller]=Thread' .
            '&tx_agora_forum[action]=list';
        $configuration['additionalParams'] .= '&tx_agora_forum[forum]=' . $thread->getForum()->getUid();

        return $configuration;
    }

    /**
     * @return array
     */
    protected function getSettings()
    {
        $settings = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'Agora',
            'Forum'
        );

        return $settings;
    }
}
