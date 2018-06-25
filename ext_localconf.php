<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AgoraTeam.' . $_EXTKEY,
    'Forum',
    array(
        'Forum' => 'list',
        'Thread' => 'list, delete, edit, update, new, create',
        'Post' => 'list, show, showHistory, delete, confirmDelete, edit, update, new, create',
        'Rating' => 'rate',
        'User' => 'addObservedThread, removeObservedThread, addFavoritePost, removeFavoritePost',
        'Attachment' => 'download',
        'Report' => 'new, report, confirm'
    ),
    array(
        'Forum' => 'list',
        'Thread' => 'list, delete, edit, update, new, create',
        'Post' => 'list, show, showHistory, delete, confirmDelete, edit, update, new, create',
        'User' => 'addObservedThread, removeObservedThread, addFavoritePost, removeFavoritePost',
        'Attachment' => 'download',
        'Report' => 'new, report, confirm'
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AgoraTeam.' . $_EXTKEY,
    'Widgets',
    array(
        'Post' => 'listLatest',
        'Thread' => 'listLatest',
        'User' => 'favoritePosts, observedThreads, removeObservedThread, removeFavoritePost'
    ),
    array(
        'Post' => 'listLatest',
        'Thread' => 'listLatest',
        'User' => 'favoritePosts, observedThreads, removeObservedThread, removeFavoritePost'
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AgoraTeam.' . $_EXTKEY,
    'Forumpages',
    array(
        'User' => 'removeObservedThread, listObservedThreads',
    ),
    array(
        'User' => 'removeObservedThread, listObservedThreads',
    )
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'AgoraTeam.' . $_EXTKEY,
    'Ajax',
    array(
        'Rating' => 'rate',
        'Tag' => 'autocomplete, list',
    ),
    array(
        'Rating' => 'rate',
        'Tag' => 'autocomplete, list',
    )
);

// Add hook for Access-Writing
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
    AgoraTeam\Agora\Hooks\Tcemain::class;


// Add signal slots
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
);
$signalSlotDispatcher->connect(
    'AgoraTeam\Agora\Controller\PostController',
    'postCreated',
    'AgoraTeam\Agora\Service\Action\ActionListener',
    'onPostCreated'
);
$signalSlotDispatcher->connect(
    'AgoraTeam\Agora\Controller\PostController',
    'postUpdated',
    'AgoraTeam\Agora\Service\Action\ActionListener',
    'onPostUpdated'
);
$signalSlotDispatcher->connect(
    'AgoraTeam\Agora\Controller\PostController',
    'postDeleted',
    'AgoraTeam\Agora\Service\Action\ActionListener',
    'onPostDeleted'
);
$signalSlotDispatcher->connect(
    'AgoraTeam\Agora\Controller\PostController',
    'threadDeleted',
    'AgoraTeam\Agora\Service\Action\ActionListener',
    'onThreadDeleted'
);
$signalSlotDispatcher->connect(
    'AgoraTeam\Agora\Controller\ThreadController',
    'threadCreated',
    'AgoraTeam\Agora\Service\Action\ActionListener',
    'onThreadCreated'
);

$signalSlotDispatcher->connect(
    'AgoraTeam\Agora\Controller\RatingController',
    'rateConfirmed',
    'AgoraTeam\Agora\Service\Action\ActionListener',
    'onConfirmedRating'
);


// Add CommandController
if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers']) == false) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'] = array();
}
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
    'AgoraTeam\\Agora\\Command\\ActionConverterCommandController';
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['extbase']['commandControllers'][] =
    'AgoraTeam\\Agora\\Command\\NotificationMailerCommandController';
