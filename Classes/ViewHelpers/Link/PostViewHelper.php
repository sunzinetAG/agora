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
use AgoraTeam\Agora\Domain\Model\Post;

/**
 * CreatorViewHelper
 */
class PostViewHelper extends AbstractLinkViewHelper
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

        $this->registerArgument('post', Post::class, 'post item', true);
    }

    /**
     * Render link to post in thread
     *
     * @return string link
     */
    public function render()
    {
        $post = $this->arguments['post'];
        $linkContent = $this->renderChildren();

        // Check if post is given
        if (is_null($post)) {
            return $linkContent;
        }

        $tsSettings = $this->mergeSettings();
        $configuration = $this->getConfiguration();
        $configuration = $this->getLinkToPost($post, $tsSettings, $configuration);

        $tag = $this->renderTag($configuration, $linkContent);

        return $tag;
    }

    /**
     * @param Post $post
     * @param array $settings
     * @param array $configuration
     * @return array
     */
    protected function getLinkToPost(Post $post, $settings, array $configuration = [])
    {
        $thread = $post->getThread();
        $detailPid = $GLOBALS['TSFE']->id;
        $configuration['parameter'] = $detailPid;

        $paginationSite = $this->paginationService->getPostPagePosition($post, $settings);
        if ($paginationSite > 1) {
            $configuration['additionalParams'] .= '&tx_agora_forum[page]=' . $paginationSite;
        }

        $configuration['additionalParams'] .= '&tx_agora_forum[controller]=Post' .
            '&tx_agora_forum[action]=list';
        if ($thread) {
            $configuration['additionalParams'] .= '&tx_agora_forum[thread]=' . $thread->getUid();
        }

        return $configuration;
    }
}
