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

/**
 * CreatorViewHelper
 */
class ThreadViewHelper extends AbstractLinkViewHelper
{
    /**
     * initializeArguments
     *
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerUniversalTagAttributes();
        $this->registerLinkTagAttributes();

        $this->registerArgument('thread', Thread::class, 'Thread item', true);
    }

    /**
     * Render link to post in thread
     *
     * @return string link
     */
    public function render()
    {
        $thread = $this->arguments['thread'];
        $linkContent = $this->renderChildren();

        // CHeck if post is given
        if (is_null($thread)) {
            return $linkContent;
        }

        $tsSettings = $this->mergeSettings();
        $configuration = $this->getConfiguration();
        $configuration = $this->getLinkToThread($thread, $tsSettings, $configuration);

        $tag = $this->renderTag($configuration, $linkContent);

        return $tag;
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
}
