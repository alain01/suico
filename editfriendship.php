<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * @copyright    XOOPS Project https://xoops.org/
 * @license      GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @author       Marcello Brandão aka  Suico
 * @author       XOOPS Development Team
 * @since
 */

require __DIR__ . '/header.php';

if (!$xoopsUser) {
	redirect_header('index.php');
}

$friendshipFactory = new Yogurt\FriendshipHandler($xoopsDB);
$friend2_uid        = (int)$_POST['friend_uid'];
$marker             = (!empty($_POST['marker'])) ? (int)$_POST['marker'] : 0;

$friend = new \XoopsUser($friend2_uid);

if (1 == $marker) {
	$level         = $_POST['level'];
	$cool          = $_POST['cool'];
	$sexy          = $_POST['hot'];
	$trusty        = $_POST['trust'];
	$fan           = $_POST['fan'];
	$friendship_id = (int)$_POST['friendship_id'];

	$criteria    = new \Criteria('friendship_id', $friendship_id);
	$friendships = $friendshipFactory->getObjects($criteria);
	$friendship  = $friendships[0];
	$friendship->setVar('level', $level);
	$friendship->setVar('cool', $cool);
	$friendship->setVar('hot', $sexy);
	$friendship->setVar('trust', $trusty);
	$friendship->setVar('fan', $fan);
	$friend2_uid = (int)$friendship->getVar('friend2_uid');
	$friendship->unsetNew();
	$friendshipFactory->insert($friendship);
	redirect_header('friends.php', 2, _MD_YOGURT_FRIENDSHIPUPDATED);
} else {
	$friendshipFactory->renderFormSubmit($friend);
}

include __DIR__.'/../../footer.php';