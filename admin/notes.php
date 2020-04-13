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
 * Module: Yogurt
 *
 * @category        Module
 * @package         yogurt
 * @author          XOOPS Development Team <https://xoops.org>
 * @copyright       {@link https://xoops.org/ XOOPS Project}
 * @license         GPL 2.0 or later
 * @link            https://xoops.org/
 * @since           1.0.0
 */

use Xmf\Request;
use Xmf\Module\Helper\Permission;

require __DIR__ . '/admin_header.php';
xoops_cp_header();
//It recovered the value of argument op in URL$
$op    = Request::getString('op', 'list');
$order = Request::getString('order', 'desc');
$sort  = Request::getString('sort', '');

$adminObject->displayNavigation(basename(__FILE__));
$permHelper = new Permission();
$uploadDir  = XOOPS_UPLOAD_PATH . '/yogurt/images/';
$uploadUrl  = XOOPS_UPLOAD_URL . '/yogurt/images/';

switch ($op) {
    case 'new':
        $adminObject->addItemButton(AM_YOGURT_NOTES_LIST, 'notes.php', 'list');
        $adminObject->displayButton('left');

        $notesObject = $notesHandler->create();
        $form        = $notesObject->getForm();
        $form->display();
        break;

    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('notes.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (0 !== Request::getInt('note_id', 0)) {
            $notesObject = $notesHandler->get(Request::getInt('note_id', 0));
        } else {
            $notesObject = $notesHandler->create();
        }
        // Form save fields
        $notesObject->setVar('note_text', Request::getText('note_text', ''));
        $notesObject->setVar('note_from', Request::getVar('note_from', ''));
        $notesObject->setVar('note_to', Request::getVar('note_to', ''));
        $notesObject->setVar('private', (1 === Request::getInt('private', 0) ? '1' : '0'));
        $notesObject->setVar('date', $_REQUEST['date']);
        if ($notesHandler->insert($notesObject)) {
            redirect_header('notes.php?op=list', 2, AM_YOGURT_FORMOK);
        }

        echo $notesObject->getHtmlErrors();
        $form = $notesObject->getForm();
        $form->display();
        break;

    case 'edit':
        $adminObject->addItemButton(AM_YOGURT_ADD_NOTES, 'notes.php?op=new', 'add');
        $adminObject->addItemButton(AM_YOGURT_NOTES_LIST, 'notes.php', 'list');
        $adminObject->displayButton('left');
        $notesObject = $notesHandler->get(Request::getString('note_id', ''));
        $form        = $notesObject->getForm();
        $form->display();
        break;

    case 'delete':
        $notesObject = $notesHandler->get(Request::getString('note_id', ''));
        if (1 === Request::getInt('ok', 0)) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('notes.php', 3, implode(', ', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($notesHandler->delete($notesObject)) {
                redirect_header('notes.php', 3, AM_YOGURT_FORMDELOK);
            } else {
                echo $notesObject->getHtmlErrors();
            }
        } else {
            xoops_confirm(
                [
                    'ok'      => 1,
                    'note_id' => Request::getString('note_id', ''),
                    'op'      => 'delete',
                ],
                Request::getUrl('REQUEST_URI', '', 'SERVER'),
                sprintf(
                    AM_YOGURT_FORMSUREDEL,
                    $notesObject->getVar('note_id')
                )
            );
        }
        break;

    case 'clone':

        $id_field = Request::getString('note_id', '');

        if ($utility::cloneRecord('yogurt_notes', 'note_id', $id_field)) {
            redirect_header('notes.php', 3, AM_YOGURT_CLONED_OK);
        } else {
            redirect_header('notes.php', 3, AM_YOGURT_CLONED_FAILED);
        }

        break;
    case 'list':
    default:
        $adminObject->addItemButton(AM_YOGURT_ADD_NOTES, 'notes.php?op=new', 'add');
        $adminObject->displayButton('left');
        $start                = Request::getInt('start', 0);
        $notesPaginationLimit = $helper->getConfig('userpager');

        $criteria = new CriteriaCompo();
        $criteria->setSort('note_id ASC, note_id');
        $criteria->setOrder('ASC');
        $criteria->setLimit($notesPaginationLimit);
        $criteria->setStart($start);
        $notesTempRows  = $notesHandler->getCount();
        $notesTempArray = $notesHandler->getAll($criteria);
        /*
        //
        //
                            <th class='center width5'>".AM_YOGURT_FORM_ACTION."</th>
        //                    </tr>";
        //            $class = "odd";
        */

        // Display Page Navigation
        if ($notesTempRows > $notesPaginationLimit) {
            xoops_load('XoopsPageNav');

            $pagenav = new XoopsPageNav(
                $notesTempRows, $notesPaginationLimit, $start, 'start', 'op=list' . '&sort=' . $sort . '&order=' . $order . ''
            );
            $GLOBALS['xoopsTpl']->assign('pagenav', null === $pagenav ? $pagenav->renderNav() : '');
        }

        $GLOBALS['xoopsTpl']->assign('notesRows', $notesTempRows);
        $notesArray = [];

        //    $fields = explode('|', note_id:int:11::NOT NULL::primary:ID:0|note_text:text:0::NOT NULL:::Text:1|note_from:int:11::NOT NULL:::From:2|note_to:int:11::NOT NULL:::To:3|private:tinyint:1::NOT NULL:::Private:4|date:int:11::NOT NULL:CURRENT_TIMESTAMP::Date:5);
        //    $fieldsCount    = count($fields);

        $criteria = new CriteriaCompo();

        //$criteria->setOrder('DESC');
        $criteria->setSort($sort);
        $criteria->setOrder($order);
        $criteria->setLimit($notesPaginationLimit);
        $criteria->setStart($start);

        $notesCount     = $notesHandler->getCount($criteria);
        $notesTempArray = $notesHandler->getAll($criteria);

        //    for ($i = 0; $i < $fieldsCount; ++$i) {
        if ($notesCount > 0) {
            foreach (array_keys($notesTempArray) as $i) {
                //        $field = explode(':', $fields[$i]);

                $GLOBALS['xoopsTpl']->assign(
                    'selectornote_id',
                    AM_YOGURT_NOTES_NOTE_ID
                );
                $notesArray['note_id'] = $notesTempArray[$i]->getVar('note_id');

                $GLOBALS['xoopsTpl']->assign('selectornote_text', AM_YOGURT_NOTES_NOTE_TEXT);
                $notesArray['note_text'] = $notesTempArray[$i]->getVar('note_text');

                $GLOBALS['xoopsTpl']->assign('selectornote_from', AM_YOGURT_NOTES_NOTE_FROM);
                $notesArray['note_from'] = strip_tags(
                    XoopsUser::getUnameFromId($notesTempArray[$i]->getVar('note_from'))
                );

                $GLOBALS['xoopsTpl']->assign('selectornote_to', AM_YOGURT_NOTES_NOTE_TO);
                $notesArray['note_to'] = strip_tags(
                    XoopsUser::getUnameFromId($notesTempArray[$i]->getVar('note_to'))
                );

                $GLOBALS['xoopsTpl']->assign('selectorprivate', AM_YOGURT_NOTES_PRIVATE);
                $notesArray['private'] = $notesTempArray[$i]->getVar('private');

                $GLOBALS['xoopsTpl']->assign('selectordate', AM_YOGURT_NOTES_DATE);
                $notesArray['date']        = date(
                    _SHORTDATESTRING,
                    strtotime((string)$notesTempArray[$i]->getVar('date'))
                );
                $notesArray['edit_delete'] = "<a href='notes.php?op=edit&note_id=" . $i . "'><img src=" . $pathIcon16 . "/edit.png alt='" . _EDIT . "' title='" . _EDIT . "'></a>
               <a href='notes.php?op=delete&note_id=" . $i . "'><img src=" . $pathIcon16 . "/delete.png alt='" . _DELETE . "' title='" . _DELETE . "'></a>
               <a href='notes.php?op=clone&note_id=" . $i . "'><img src=" . $pathIcon16 . "/editcopy.png alt='" . _CLONE . "' title='" . _CLONE . "'></a>";

                $GLOBALS['xoopsTpl']->append_by_ref('notesArrays', $notesArray);
                unset($notesArray);
            }
            unset($notesTempArray);
            // Display Navigation
            if ($notesCount > $notesPaginationLimit) {
                xoops_load('XoopsPageNav');
                $pagenav = new XoopsPageNav(
                    $notesCount, $notesPaginationLimit, $start, 'start', 'op=list' . '&sort=' . $sort . '&order=' . $order . ''
                );
                $GLOBALS['xoopsTpl']->assign('pagenav', $pagenav->renderNav(4));
            }

            //                     echo "<td class='center width5'>

            //                    <a href='notes.php?op=edit&note_id=".$i."'><img src=".$pathIcon16."/edit.png alt='"._EDIT."' title='"._EDIT."'></a>
            //                    <a href='notes.php?op=delete&note_id=".$i."'><img src=".$pathIcon16."/delete.png alt='"._DELETE."' title='"._DELETE."'></a>
            //                    </td>";

            //                echo "</tr>";

            //            }

            //            echo "</table><br><br>";

            //        } else {

            //            echo "<table width='100%' cellspacing='1' class='outer'>

            //                    <tr>

            //                     <th class='center width5'>".AM_YOGURT_FORM_ACTION."XXX</th>
            //                    </tr><tr><td class='errorMsg' colspan='7'>There are noXXX notes</td></tr>";
            //            echo "</table><br><br>";

            //-------------------------------------------

            echo $GLOBALS['xoopsTpl']->fetch(
                XOOPS_ROOT_PATH . '/modules/' . $GLOBALS['xoopsModule']->getVar(
                    'dirname'
                ) . '/templates/admin/yogurt_admin_notes.tpl'
            );
        }

        break;
}
require __DIR__ . '/admin_footer.php';
