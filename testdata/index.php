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
 * @category        Module
 * @package         suico
 * @copyright       {@link https://xoops.org/ XOOPS Project}
 * @license         GNU GPL 2 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author          Marcello Brandão aka  Suico, Mamba, LioMJ  <https://xoops.org>
 */

use Xmf\Database\TableLoad;
use Xmf\Module\Helper;
use Xmf\Request;
use Xmf\Yaml;
use XoopsModules\Suico;
use XoopsModules\Suico\Common;
use XoopsModules\Suico\Utility;

require_once dirname(__DIR__, 3) . '/include/cp_header.php';
require dirname(__DIR__) . '/preloads/autoloader.php';

$op = Request::getCmd('op', '');

$moduleDirName      = basename(dirname(__DIR__));
$moduleDirNameUpper = mb_strtoupper($moduleDirName);

$helper = Suico\Helper::getInstance();
// Load language files
$helper->loadLanguage('common');

switch ($op) {
    case 'load':
        if (Request::hasVar('ok', 'REQUEST') && 1 === Request::getInt('ok', 0, 'REQUEST')) {
            if (!$GLOBALS['xoopsSecurity']->check()) {
                redirect_header('../admin/index.php', 3, implode(',', $GLOBALS['xoopsSecurity']->getErrors()));
            }

            loadSampleData();
        } else {
            xoops_cp_header();

            xoops_confirm(
                [
                    'ok' => 1,
                    'op' => 'load',
                ],
                'index.php',
                sprintf(constant('CO_' . $moduleDirNameUpper . '_' . 'ADD_SAMPLEDATA_OK')),
                constant(
                    'CO_' . $moduleDirNameUpper . '_' . 'CONFIRM'
                ),
                true
            );

            xoops_cp_footer();
        }
        break;
    case 'save':
        saveSampleData();
        break;
}

// XMF TableLoad for SAMPLE data

function loadSampleData()
{
    global $xoopsConfig;

    $moduleDirName = basename(dirname(__DIR__));

    $moduleDirNameUpper = mb_strtoupper($moduleDirName);

    $utility = new Suico\Utility();

    $configurator = new Common\Configurator();

    $tables = Helper::getHelper($moduleDirName)->getModule()->getInfo('tables');

    $language = 'english/';

    if (is_dir(__DIR__ . '/' . $xoopsConfig['language'])) {
        $language = $xoopsConfig['language'] . '/';
    }

    foreach ($tables as $table) {
        $tabledata = Yaml::readWrapped($language . $table . '.yml');

        if (is_array($tabledata)) {
            TableLoad::truncateTable($table);

            TableLoad::loadTableFromArray($table, $tabledata);
        }
    }

    //  ---  COPY test folder files ---------------

    if (is_array($configurator->copyTestFolders)
        && count(
               $configurator->copyTestFolders
           ) > 0) {
        //        $file =  dirname(__DIR__) . '/testdata/images/';

        foreach (
            array_keys(
                $configurator->copyTestFolders
            ) as $i
        ) {
            $src = $configurator->copyTestFolders[$i][0];

            $dest = $configurator->copyTestFolders[$i][1];

            $utility::rcopy($src, $dest);
        }
    }

    redirect_header('../admin/index.php', 1, constant('CO_' . $moduleDirNameUpper . '_' . 'SAMPLEDATA_SUCCESS'));
}

function saveSampleData()
{
    global $xoopsConfig;

    $moduleDirName = basename(dirname(__DIR__));

    $moduleDirNameUpper = mb_strtoupper($moduleDirName);

    $tables = Helper::getHelper($moduleDirName)->getModule()->getInfo('tables');

    $language = 'english/';

    if (is_dir(__DIR__ . '/' . $xoopsConfig['language'])) {
        $language = $xoopsConfig['language'] . '/';
    }

    $languageFolder = __DIR__ . '/' . $language;

    if (!file_exists($languageFolder . '/')) {
        Utility::createFolder($languageFolder . '/');
    }

    $exportFolder = $languageFolder . '/Exports-' . date('Y-m-d-H-i-s') . '/';

    Utility::createFolder($exportFolder);

    foreach ($tables as $table) {
        TableLoad::saveTableToYamlFile($table, $exportFolder . $table . '.yml');
    }

    redirect_header('../admin/index.php', 1, constant('CO_' . $moduleDirNameUpper . '_' . 'SAMPLEDATA_SUCCESS'));
}

function exportSchema()
{
    $moduleDirName = basename(dirname(__DIR__));

    $moduleDirNameUpper = mb_strtoupper($moduleDirName);

    try {
        // TODO set exportSchema
        //        $migrate = new Suico\Migrate($moduleDirName);
        //        $migrate->saveCurrentSchema();
        //
        //        redirect_header('../admin/index.php', 1, constant('CO_' . $moduleDirNameUpper . '_' . 'EXPORT_SCHEMA_SUCCESS'));
    } catch (Throwable $e) {
        exit(constant('CO_' . $moduleDirNameUpper . '_' . 'EXPORT_SCHEMA_ERROR'));
    }
}
