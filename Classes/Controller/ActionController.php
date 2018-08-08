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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\ResponseInterface;
use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use AgoraTeam\Agora\Domain\Model\Post;
use AgoraTeam\Agora\Domain\Repository\UserRepository;
use AgoraTeam\Agora\Service\PaginationService;
use AgoraTeam\Agora\Service\Authentication\AuthenticationService;


/**
 * Class ActionController
 * @package AgoraTeam\Agora\Controller
 */
abstract class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * persistenceManager
     *
     * @var PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * userRepository
     *
     * @var UserRepository
     * @inject
     */
    protected $userRepository;

    /**
     * @var PaginationService
     * @inject
     */
    protected $paginationService;

    /**
     * @var AuthenticationService
     * @inject
     */
    protected $authenticationService;

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @throws \Exception
     */
    public function processRequest(RequestInterface $request, ResponseInterface $response)
    {
        try {
            parent::processRequest($request, $response);
        } catch (\Exception $exception) {
            $this->handleKnownExceptionsElseThrowAgain($exception);
        }
    }

    /**
     * @param \Exception $exception
     * @throws \Exception
     */
    private function handleKnownExceptionsElseThrowAgain(\Exception $exception)
    {
        if ($exception instanceof TargetNotFoundException
            && isset($this->settings['errorHandling'])
        ) {
            $this->handleTargetNotFoundException($this->settings['errorHandling']);
        } else {
            throw $exception;
        }
    }

    /**
     * @param $configuration
     * @throws \InvalidArgumentException
     * @return string
     */
    protected function handleTargetNotFoundException($configuration)
    {
        switch ($configuration['handling']) {
            case 'pageNotFoundHandler':
                $GLOBALS['TSFE']->pageNotFoundAndExit('Target not found');
                break;
            case 'standaloneTemplate':
                $statusCode = constant(HttpUtility::class . '::HTTP_STATUS_' . $configuration['statuscode']);
                HttpUtility::setResponseCode($statusCode);
                $standaloneTemplate = GeneralUtility::makeInstance(StandaloneView::class);
                $standaloneTemplate->setTemplatePathAndFilename(
                    GeneralUtility::getFileAbsFileName($configuration['templatePath'])
                );
                $html = $standaloneTemplate->render();
                $this->response->setContent($html);
                break;
            default:
                // Do nothing, it might be handled in the view
        }
    }

    /**
     * @param $key
     * @param array $arguments
     * @param null $titleKey
     * @param int $severity
     */
    protected function addLocalizedFlashmessage(
        $key,
        array $arguments = [],
        $titleKey = null,
        $severity = FlashMessage::OK
    ) {
        $this->addFlashMessage(
            LocalizationUtility::translate($key, 'agora', $arguments),
            LocalizationUtility::translate($titleKey, 'agora'),
            $severity
        );
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @param $newPost
     * @param $addHash
     * @return string $uri
     */
    protected function generatePostUri(Post $newPost, $addHash = false)
    {
        $redirectArgs = ['thread' => $newPost->getThread()];
        $pageNumber = $this->paginationService->getPostPagePosition($newPost, $this->settings);
        if ($pageNumber > 1) {
            $redirectArgs['page'] = $pageNumber;
        }
        $uri = $this->buildRedirectUri('list', 'Post', 'agora', $redirectArgs);
        if ($addHash) {
            $uri .= '#post' . $newPost->getUid();
        }

        return $uri;
    }

    /**
     * @param $actionName
     * @param null $controllerName
     * @param null $extensionName
     * @param array|null $arguments
     * @param null $pageUid
     * @return string
     */
    protected function buildRedirectUri(
        $actionName,
        $controllerName = null,
        $extensionName = null,
        array $arguments = null,
        $pageUid = null
    ) {
        if ($controllerName === null) {
            $controllerName = $this->request->getControllerName();
        }
        $this->uriBuilder->reset()->setTargetPageUid($pageUid)->setCreateAbsoluteUri(true);
        if (GeneralUtility::getIndpEnv('TYPO3_SSL')) {
            $this->uriBuilder->setAbsoluteUriScheme('https');
        }
        $uri = $this->uriBuilder->uriFor($actionName, $arguments, $controllerName, $extensionName);

        return $uri;
    }
}

