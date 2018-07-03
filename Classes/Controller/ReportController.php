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
use AgoraTeam\Agora\Domain\Model\Mod\Report;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Model\User;
use AgoraTeam\Agora\Service\MailService;
use AgoraTeam\Agora\Service\TagService;

/**
 * PostController
 */
class ReportController extends ActionController
{
    /**
     * mailService
     *
     * @var \AgoraTeam\Agora\Service\MailService
     * @inject
     */
    protected $mailService;

    /**
     * reportRepository
     *
     * @var \AgoraTeam\Agora\Domain\Repository\Mod\ReportRepository
     * @inject
     */
    protected $reportRepository;

    /**
     * @param Post $post
     */
    public function reportAction(Report $report)
    {
        // For some reason the mailservice changes the TSFE id...
        $pageUid = $GLOBALS['TSFE']->id;

        $this->authenticationService->assertReadAuthorization($report->getPost());
        $report->setReporter($this->authenticationService->getUser());
        $this->reportRepository->add($report);

        if ($this->settings['report']['sendMails'] == 1) {
            $this->mailService->sendMail(
                [$this->settings['report']['adminEmailAdress'] => $this->settings['report']['adminEmailUserName']],
                [$this->settings['report']['defaultEmailAdress'] => $this->settings['report']['defaultEmailUserName']],
                $this->settings['report']['reportNotificationSubject'],
                'Report',
                ['report' => $report, 'user' => $this->authenticationService->getUser()]
            );
        }
        $this->addLocalizedFlashmessage('tx_agora_domain_model_mod_report.flashMessages.reported');
        $this->redirect(
            'list',
            'Post',
            'Agora',
            ['thread' => $report->getPost()->getThread()],
            $pageUid
        );
    }

    /**
     * @param Post $post
     * @return void
     */
    public function newAction(Post $post, Report $report = null)
    {
        $this->authenticationService->assertReadAuthorization($post);
        $this->view->assignMultiple([
            'post' => $post,
            'report' => $report,
            'settings' => $this->settings
        ]);
    }
}
