<?php

declare(strict_types=1);

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

use XoopsModules\Yogurt;

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
//include_once(XOOPS_ROOT_PATH."/class/criteria.php");
//require_once XOOPS_ROOT_PATH . '/modules/yogurt/class/Friendship.php';

/**
 * @param $options
 * @return array
 */
function b_yogurt_friends_show($options)
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig, $xoopsUser;
    $myts  = MyTextSanitizer::getInstance();
    $block = [];

    if ($xoopsUser) {
        /**
         * Filter for fetch votes ishot and isnothot
         */

        $criteria_2 = new Criteria(
            'friend1_uid',
            $xoopsUser->getVar(
                'uid'
            )
        );

        /**
         * Creating factories of pictures and votes
         */
        //$albumFactory      = new ImagesHandler($xoopsDB);
        $friendsFactory = new Yogurt\FriendshipHandler($xoopsDB);

        $block['friends'] = $friendsFactory->getFriends($options[0], $criteria_2);

        $block['lang_allfriends'] = _MB_YOGURT_ALLFRIENDS;
        $block['lang_nofriends']  = _MB_YOGURT_NOFRIENDSYET;
        $block['enablepm']        = $options[1];

        return $block;
    }
}

/**
 * @param $options
 * @return string
 */
function b_yogurt_friends_edit($options)
{
    $form .= _MB_YOGURT_TOTALFRIENDSTOSHOW . '&nbsp;';
    $form .= "<input type='text' name='options[0]' value='" . $options[0] . "'><br>";

    $form .= _MB_YOGURT_ENABLEPM . '&nbsp;';
    if (1 === $options[1]) {
        $chk = " checked";
    }
    $form .= "<input type='radio' name='options[1]' value='1'" . $chk . '>&nbsp;' . _YES . '';
    $chk  = '';
    if (0 === $options[1]) {
        $chk = " checked";
    }
    $form .= "&nbsp;<input type='radio' name='options[1]' value='0'" . $chk . '>' . _NO . '<br>';

    return $form;
}
