<?php
/*
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * @copyright      {@link http://xoops.org/ XOOPS Project}
 * @license        {@link http://www.gnu.org/licenses/gpl-2.0.html GNU GPL 2 or later}
 * @package
 * @since
 * @author         XOOPS Development Team
 */
include_once __DIR__ . '/admin_header.php';
xoops_cp_header();
$adminObject->addItemButton(_AM_SUICO_CATEGORY, 'fieldscategory.php?op=new', 'add');
$adminObject->displayNavigation(basename(__FILE__));
$adminObject->displayButton('left');
$op = $_REQUEST['op'] ?? (isset($_REQUEST['id']) ? 'edit' : 'list');
/* @var SuicoCategoryHandler $handler */
$handler = $helper->getHandler('Category');
switch ($op) {
    default:
    case 'list':
        $criteria = new CriteriaCompo();
        $criteria->setSort('cat_weight');
        $criteria->setOrder('ASC');
        $GLOBALS['xoopsTpl']->assign('categories', $handler->getObjects($criteria, true, false));
        $template_main = 'admin/suico_admin_fieldscategory.tpl';
        break;
    case 'new':
        include_once dirname(__DIR__) . '/include/forms.php';
        $obj  = $handler->create();
        $form = $obj->getForm();
        $form->display();
        break;
    case 'edit':
        include_once dirname(__DIR__) . '/include/forms.php';
        $obj  = $handler->get($_REQUEST['id']);
        $form = $obj->getForm();
        $form->display();
        break;
    case 'save':
        if (!$GLOBALS['xoopsSecurity']->check()) {
            redirect_header('fieldscategory.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
        }
        if (isset($_REQUEST['id'])) {
            $obj = $handler->get($_REQUEST['id']);
        } else {
            $obj = $handler->create();
        }
        $obj->setVar('cat_title', $_REQUEST['cat_title']);
        $obj->setVar('cat_description', $_REQUEST['cat_description']);
        $obj->setVar('cat_weight', $_REQUEST['cat_weight']);
        if ($handler->insert($obj)) {
            redirect_header('fieldscategory.php', 3, sprintf(_AM_SUICO_SAVEDSUCCESS, _AM_SUICO_CATEGORY));
        }
        include_once dirname(__DIR__) . '/include/forms.php';
        echo $obj->getHtmlErrors();
        /* @var  XoopsThemeForm $form */
        $form = $obj->getForm();
        $form->display();
        break;
    case 'delete':
        $obj = $handler->get($_REQUEST['id']);
        if (isset($_REQUEST['ok']) && 1 == $_REQUEST['ok']) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('fieldscategory.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }
            if ($handler->delete($obj)) {
                redirect_header('fieldscategory.php', 3, sprintf(_AM_SUICO_DELETEDSUCCESS, _AM_SUICO_CATEGORY));
            } else {
                echo $obj->getHtmlErrors();
            }
        } else {
            xoops_confirm(
                [
                    'ok' => 1,
                    'id' => $_REQUEST['id'],
                    'op' => 'delete',
                ],
                $_SERVER['REQUEST_URI'],
                sprintf(_AM_SUICO_RUSUREDEL, $obj->getVar('cat_title'))
            );
        }
        break;
}
if (isset($template_main)) {
    $GLOBALS['xoopsTpl']->display("db:{$template_main}");
}
include_once __DIR__ . '/admin_footer.php';