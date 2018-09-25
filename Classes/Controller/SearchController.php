<?php

namespace AgoraTeam\Agora\Controller;

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

use RecursiveIteratorIterator;
use RecursiveArrayIterator;

use AgoraTeam\Agora\Domain\Model\Forum;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfiguration;
use AgoraTeam\Agora\Domain\Model\Dto\Search;
use AgoraTeam\Agora\Domain\Model\Dto\ForumDemand;
use AgoraTeam\Agora\Domain\Repository\ForumRepository;
use AgoraTeam\Agora\Domain\Repository\ThreadRepository;
use AgoraTeam\Agora\Domain\Repository\PostRepository;

/**
 * Class SearchController
 *
 * @package AgoraTeam\Agora\Controller
 */
class SearchController extends ActionController
{

    /**
     * forumRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ForumRepository
     * @inject
     */
    protected $forumRepository = null;

    /**
     * threadRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\ThreadRepository
     * @inject
     */
    protected $threadRepository = null;

    /**
     * postRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\PostRepository
     * @inject
     */
    protected $postRepository;

    /**
     * Action initializeList
     *
     * @return void
     */
    public function initializeListAction()
    {
        /** @var MvcPropertyMappingConfiguration $propertyMappingConfiguration * */
        $propertyMappingConfiguration = $this->arguments['search']->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->allowProperties('themes');
        $propertyMappingConfiguration->allowProperties('radius');
        $propertyMappingConfiguration->allowProperties('orderBy');
    }

    /**
     * Action list
     *
     * @param Search|null $search
     * @throws \Exception
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException
     */
    public function listAction(
        Search $search = null
    ) {
        $demand = $this->createDemandObjectFromSettings($this->settings);
        $demand->setActionAndClass(__METHOD__, __CLASS__);

        $openUserForums = $this->forumRepository->findAccessibleUserForums();

        if (is_null($search)) {
            $search = $this->objectManager->get(Search::class);
        }

        // If order field is empty, generate field value form fields OrderBy and Directions
        if (empty($search->getOrder())) {
            $search->setOrder($search->getOrderBy() . ' ' . $search->getOrderDirection());
        }
        $demand->setSearch($search);

        // If themes are not included from the search filter, as themes set openUserforums
        if (empty($search->getThemes())) {
            $search->setThemes($openUserForums);
        }

        // Initialise search query
        $whereToSearch = $demand->getSearch()->getRadius();
        $results = $this->postRepository->findDemanded($demand, $this->threadRepository);

        if ($whereToSearch == 2) {
            $results2 = [];
            foreach ($results as $key => $value) {
                $firstPost = $this->postRepository->findFirstPostOnThread($value->getThread()->getUid(),
                    $search->getSword());
                $currentUid = $value->getUid();
                $firstPostUid = $firstPost->getUid();
                if ($currentUid == $firstPostUid) {
                    $results2[] = $value;
                }
            }
            $results = $results2;
        }


        $accessibleUserForums = $this->forumRepository->findVisibleRootForums();
        $user = $this->authenticationService->getUser();
        /** @var Forum $forum */
               foreach ($accessibleUserForums as $forum) {
//            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($forum,__FILE__ . " " . __LINE__);
            if ($forum->isAccessibleForUser($user)) {

                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($forum,__FILE__ . " " . __LINE__);



//                    $forumMatrix = [];
//
//
//                    $subforum =  $forum->getSubForums();
//
//                    $forumArray = [
//                        'item' => $forum,
//                        'children' => $subforum
//                    ];
//
//                    array_push($forumMatrix, $forumArray);
//
                $themes = $this->forumRepository->findAccessibleUserForumsAsMatrix();
                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($themes,__FILE__ . " " . __LINE__);
//
//
//
//                    foreach($subforum as $key => $currentForum) {
//                            $forumArray['item'] = $currentForum;
//                            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($forumArray['item'],__FILE__ . " " . __LINE__);
//                        do {
//                            $children = $currentForum->getSubForums();
//                            $forumArray['children'] = $children;
//                            array_push($forumMatrix, $forumArray);
//                            \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($forumMatrix,__FILE__ . " " . __LINE__);die;
//                        } while ($children > 0);
//                   }



            } else {
                \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump('false',__FILE__ . " " . __LINE__);
            }
        }

        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($accessibleUserForums,__FILE__ . " " . __LINE__);
        $themes = $this->forumRepository->findAccessibleUserForumsAsMatrix();
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($themes,__FILE__ . " " . __LINE__);
        die;
//
//        die;
        $assignedValues = [
            'results' => $results,
            'search' => $search,
            'themes' => $themes,
            'demand' => $demand,
            'settings' => $this->settings
        ];

        $this->view->assignMultiple($assignedValues);
    }

    /**
     *
     * @param array $settings
     * @param string $class
     * @throws \UnexpectedValueException
     * @return ForumDemand
     */
    protected function createDemandObjectFromSettings(
        $settings,
        $class = 'AgoraTeam\\Agora\\Domain\\Model\\Dto\\ForumDemand'
    ) {
        $class = isset($settings['demandClass']) && !empty($settings['demandClass']) ? $settings['demandClass'] : $class;
        /* @var $demand \AgoraTeam\Agora\Domain\Model\Dto\ForumDemand */
        $demand = $this->objectManager->get($class, $settings);
        if (!$demand instanceof ForumDemand) {
            throw new \UnexpectedValueException(
                sprintf('The demand object must be an instance of \AgoraTeam\\Agora\\Domain\\Model\\Dto\\ForumDemand, but %s given!',
                    $class),
                1423157953);
        }

        if ($settings['search']['limit'] != null) {
            $demand->setLimit($settings['search']['limit']);
        }

        if ($settings['orderBy']) {
            $demand->setOrder($settings['orderBy'] . ' ' . $settings['orderDirection']);
        }

        $this->contentObj = $this->configurationManager->getContentObject();
        $demand->setStoragePage($this->contentObj->data['pages']);

        return $demand;
    }
}
