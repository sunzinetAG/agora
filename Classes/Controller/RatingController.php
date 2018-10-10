<?php

namespace AgoraTeam\Agora\Controller;

/***************************************************************
 *  Copyright notice
 *  (c) 2017 Philipp Thiele <philipp.thiele@phth.de>
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

use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\Rating;
use AgoraTeam\Agora\Domain\Model\User;
use AgoraTeam\Agora\Domain\Repository\PostRepository;
use AgoraTeam\Agora\Domain\Repository\RatingRepository;

/**
 * Class RatingController
 * @package AgoraTeam\Agora\Controller
 */
class RatingController extends ActionController
{

    const RATE_TYPE_UP = 'up';
    const RATE_TYPE_DOWN = 'down';
    const RATE_TYPE_NEUT = '0';

    /**
     * postRepository
     *
     * @var PostRepository
     * @inject
     */
    protected $postRepository = null;

    /**
     * ratingRepository
     *
     * @var RatingRepository
     * @inject
     */
    protected $ratingRepository = null;

    /**
     * @var \Sunzinet\SzComments\Service\AuthorizationService
     * @inject
     */
    protected $authorizationService;

    /**
     * @param Post $post
     * @param string $rateType
     * @throws \TYPO3\CMS\Core\Error\Http\PageNotFoundException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function rateAction($post, $rateType)
    {
        $userWritableAccessUid = $this->settings['userWritableAccess'];
        $this->authorizationService->hasUserWritableAccess($userWritableAccessUid);

        // @todo add returnMessage in JsonFormat
        $this->authenticationService->assertReadAuthorization($post);

        /** @var User $user */
        $user = $this->authenticationService->getUser();
        /** @var Rating $rating */
        $rating = $this->ratingRepository->findOneByUserAndPost($user, $post);

        if (!is_null($rating)) {
            $rateType = self::RATE_TYPE_NEUT;
        }

        switch ($rateType) {
            case self::RATE_TYPE_UP:
                $this->rate($user, $post, 1);
                break;
            case self::RATE_TYPE_DOWN:
                $this->rate($user, $post, -1);
                break;
            default:
                $this->neutralize($post, $rating);
        }

        $this->signalSlotDispatcher->dispatch(
            __CLASS__,
            'rateConfirmed',
            ['post' => $post, 'rateType' => $rateType]
        );

        $this->view->assignMultiple([
            'user' => $this->authenticationService->getUser(),
            'post' => $post,
            'rateType' => $rateType
        ]);
    }

    /**
     * @param User $user
     * @param Post $post
     * @param int $rateType
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @return void
     */
    protected function rate($user, $post, $rateType)
    {
        $rating = new Rating();
        $rating->setUser($user);
        $rating->setPost($post);
        $rating->setValue($rateType);

        $post->addRating($rating);

        $this->postRepository->update($post);
        $this->ratingRepository->add($rating);
        $this->persistenceManager->persistAll();
    }

    /**
     * @param Post $post
     * @param Rating $rating
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @return void
     */
    protected function neutralize($post, $rating)
    {
        $post->removeRating($rating);
        $this->ratingRepository->remove($rating);
        $this->postRepository->update($post);
        $this->persistenceManager->persistAll();
    }
}
