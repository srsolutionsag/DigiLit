<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2009 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/

/**
 * Class ilObjDigiLit
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @author  Martin Studer <ms@studer-raimann.ch>
 * @author  Gabriel Comte <gc@studer-raimann.ch>
 *
 * @version 1.0.00
 */
class ilObjDigiLit extends ilObjectPlugin
{

    /**
     * @var bool
     */
    protected $object;
    /**
     * @var ilDBInterface
     */
    protected $db;

    /**
     * @param int $a_ref_id
     */
    public function __construct($a_ref_id = 0, $whatever = true)
    {
        global $ilDB;

        parent::__construct($a_ref_id);
        $this->db = $ilDB;
    }

    final function initType()
    {
        $this->setType(ilDigiLitPlugin::PLUGIN_ID);
    }

    public function doCreate()
    {
    }

    public function doRead()
    {
    }

    public function doUpdate()
    {
    }

    public function doDelete()
    {
        $use_search = (bool) xdglConfig::getConfigValue(xdglConfig::F_USE_SEARCH);
        $ilObjDigiLitFacadeFactory = new ilObjDigiLitFacadeFactory();
        $request_usage = $ilObjDigiLitFacadeFactory->requestUsageFactory()->getInstanceByObjectId($this->getId());
        if (!$use_search) {
            $request = xdglRequest::find($request_usage->getRequestId());
            try {
                $request->deleteFile();
            } catch (Throwable $t) {
            }
            $request->delete();
        }
        $request_usage->delete();
    }

    /**
     * @param object $new_obj
     * @param int    $a_target_id
     * @param null   $a_copy_id
     *
     * @return bool
     * @throws \Exception
     */
    protected function doCloneObject($new_obj, $a_target_id, $a_copy_id = null)
    {
        $ilObjDigiLitFacadeFactory = new ilObjDigiLitFacadeFactory();
        $xdglRequestUsage = $ilObjDigiLitFacadeFactory->requestUsageFactory()->getInstanceByObjectId($this->getId());
        $ilObjDigiLitFacadeFactory->requestUsageFactory()->copyRequestUsage($xdglRequestUsage, $new_obj->getId());

        return true;
    }

    /**
     * @param int $ref_id
     *
     * @return int
     */
    public static function returnParentCrsRefId($ref_id)
    {
        global $tree;
        /**
         * @var ilTree $tree
         */
        $pl = ilDigiLitPlugin::getInstance();
        while (ilObject2::_lookupType($ref_id, true) != 'crs') {
            if ($ref_id == 1) {
                ilUtil::sendFailure($pl->txt("course_needed"), true);
                ilUtil::redirect('/');
            }
            $ref_id = $tree->getParentId($ref_id);
        }

        return $ref_id;
    }

    public static function getObjectById($obj_id)
    {
        global $ilDB;
        $query = "SELECT * FROM ilias.object_data where obj_id = " . $ilDB->quote($obj_id, "text");
        $obj_set = $ilDB->query($query);
        $obj_rec = $ilDB->fetchAssoc($obj_set);

        return $obj_rec;
    }

    public static function updateObjDigiLitTitle($ilObjDigiLit_rec)
    {
        global $ilDB;
        $ilDB->manipulate("UPDATE object_data SET title = " . $ilDB->quote($ilObjDigiLit_rec['title']) . " WHERE obj_id = "
            . $ilDB->quote($ilObjDigiLit_rec['obj_id'], "integer"));
    }
}
