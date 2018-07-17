<?php

namespace AgoraTeam\Agora\ViewHelpers\Be;

/***************************************************************
 *  Copyright notice
 *  (c) 2015 Philipp Thiele <philipp.thiele@phth.de>
 *           Björn Christopher Bresser <bjoern.bresser@gmail.com>
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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * CreatorViewHelper
 */
class LinkViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * @param int $pageUid
     * @param string $pluginName
     * @param array $arguments
     * @param string $controller
     * @param string $action
     * @param string $extensionName
     * @param int $absolute
     * @return string content
     */
    public function render(
        $pageUid,
        $pluginName = null,
        $arguments = null,
        $controller = null,
        $action = null,
        $extensionName = null,
        $absolute = 0
    ) {
        $linkConf = [
            'parameter' => $pageUid,
            'forceAbsoluteUrl' => $absolute,
            'linkAccessRestrictedPages' => 1
        ];

        /** @var ContentObjectRenderer $cObj */
        $cObj = GeneralUtility::makeInstance(ContentObjectRenderer::class);

        if ($extensionName) {
            $argumentPrefix = 'tx_' . GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName);
            if ($pluginName) {
                $argumentPrefix .= '_' . GeneralUtility::camelCaseToLowerCaseUnderscored($pluginName);
            }
        }
        if ($argumentPrefix) {
            if ($action) {
                $key = $argumentPrefix . '[action]';
                $linkConf['additionalParams'][$key] = $action;
            }

            if ($controller) {
                $key = $argumentPrefix . '[controller]';
                $linkConf['additionalParams'][$key] = $controller;
            }

            if (isset($arguments) && is_array($arguments)) {
                foreach ($arguments as $key => $value) {
                    $argKey = $argumentPrefix . '[' . $key . ']';
                    $linkConf['additionalParams'][$argKey] = $value;
                }
            }
            $linkConf['additionalParams'] = GeneralUtility::implodeArrayForUrl(null, $linkConf['additionalParams']);
        }

        $uri = $cObj->typoLink_URL($linkConf);

        $this->tag->addAttribute('href', $uri, false);
        $this->tag->setContent($this->renderChildren());
        $this->tag->forceClosingTag(true);

        return $this->tag->render();
    }
}
