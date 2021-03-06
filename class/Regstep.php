<?php

declare(strict_types=1);

namespace XoopsModules\Suico;

/**
 * Extended User Profile
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             profile
 * @since               2.3.0
 * @author              Jan Pedersen
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
// defined('XOOPS_ROOT_PATH') || exit("XOOPS root path not defined");

/**
 * Class Regstep
 */
class Regstep extends \XoopsObject
{
    public function __construct()
    {
        $this->initVar('step_id', \XOBJ_DTYPE_INT);
        $this->initVar('step_name', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('step_desc', \XOBJ_DTYPE_TXTAREA);
        $this->initVar('step_order', \XOBJ_DTYPE_INT, 1);
        $this->initVar('step_save', \XOBJ_DTYPE_INT, 0);
    }
}
