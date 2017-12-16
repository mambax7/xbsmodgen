<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author:    Ashley Kitson                                                  //
// Copyright: (c) 2006, Ashley Kitson
// URL:       http://xoobs.net			                                     //
// Project:   The XOOPS Project (http://www.xoops.org/)                      //
// Module:    XBS Module Generator (XBS_MODGEN)                                     //
// ------------------------------------------------------------------------- //
/**
 * XBS Modgen Object handler
 * 
 * @package XBS_MODGEN
 * @subpackage Object
 * @author Ashley Kitson http://xoobs.net
 * @copyright (c) 2006 Ashley Kitson, Great Britain
*/

if (!defined('XOOPS_ROOT_PATH')) { 
  exit('Call to include XBS_MODGENObject.php failed as XOOPS_ROOT_PATH not defined');
}

/**
 * ModGen definitions
 */
require_once XOOPS_ROOT_PATH."/modules/xbs_modgen/include/defines.inc";

/**
 * Modgen common functions
 */
require_once XBS_MODGEN_PATH."/include/functions.inc";

/**
* ModGen base classes
*/
require_once XBS_MODGEN_PATH."/class/class.xbs_modgen.base.inc";

/**
 * CDM Base classes
 */
require_once(CDM_PATH."/class/class.cdm.base.php");

/**
 * Object handler for XBS_MODGENObject
 *
 * @subpackage XBS_MODGENSObject
 * @package XBS_MODGEN
 */
class Xbs_ModgenXBS_MODGENObjectHandler extends CDMBaseHandler {

  /**
   * Constructor
   *
   * @param  xoopsDB &$db Handle to xoopsDb object
   */
  function Xbs_ModgenXBS_MODGENObjectHandler(&$db) {
    $this->CDMBaseHandler($db); //call ancestor constructor
    $this->classname = 'xbs_modgen_Object';  //set name of object that this handler handles
  }


  /** 
   * Create a new Object object
   *
   * @access private
   * @return  xbs_modgen_Object object
   */

  function &_create() {
    $obj = new xbs_modgen_Object();
    return $obj;
  }//end function _create

  /**
   * Returns sql code to get a object data record
   *
   * OVERIDE ancestor
   *
   * @param   int $id internal id of the object. Internal code is a unique int value. 
   * @param   string $row_flag  default null (get all), Option(CDM_RSTAT_ACT, CDM_RSTAT_DEF, CDM_RSTAT_SUS)
   * @param   string $lang  default null (get all), Valid LANGUAGE code.  Will only return object of that language set
   * @return  string SQL string to get data
   */
  function &_get($id,$row_flag,$lang) { //overide in ancestor and supply the sql string to get the data
    $sql = sprintf("select * from %s where id = %u",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$id);
    return $sql;
  }
  

  /**
   * Get internal identifier (primary key) based on user visible code 
   *
   * @param string $modname Name of module
   * @param string $objectname Name of object item
   * @return int Internal identifier of module else false on failure
   */
  function getKey($modname,$objectname) {
  	$modHandler =& xoops_getmodulehandler("XBS_MODGENModule");
  	if ($modid = $modHandler->getKey($modname)) {
	  	$sql = sprintf("select id from %s where objname = %s and modid = %u",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$this->db->quoteString($objectname),$modid);
	    if ($result = $this->db->query($sql)) {
		  if ($this->db->getRowsNum($result)==1) {
		    $ret = $this->db->fetchArray($result);
		    return $ret[0];
		  }
	    }
  	}
    return false;
  }
  
  /**
  * Function: return sql to insert object to database 
  *
  * OVERIDE ancestor
  *
  * @version 1
  * @param array $cleanVars module parameters array
  * @return string SQL insert string
  */
  function _ins_insert($cleanVars) {
    extract($cleanVars);
    $sql = sprintf("insert into %s (modid, objname, objdesc, objtype, objloc,  objoptions) values (%u, %s, %s, %s, %s, %s)",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$modid, $this->db->quoteString($objname), $this->db->quoteString($objdesc), $this->db->quoteString($objtype), $this->db->quoteString($objloc), $this->db->quoteString($objoptions));
    return $sql;
  }
  
  /**
  * Function: return sql to update object to database 
  *
  * OVERIDE ancestor
  *
  * @version 1
  * @param array $cleanVars module parameters array
  * @return string SQL insert string
  */
  function _ins_update($cleanVars) {
    extract($cleanVars);
  $sql = sprintf("update %s set objname =%s, objdesc = %s, objtype = %s, objloc = %s,  objoptions = %s where id = %u",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$this->db->quoteString($objname), $this->db->quoteString($objdesc), $this->db->quoteString($objtype), $this->db->quoteString($objloc), $this->db->quoteString($objoptions),$id);
    return $sql;
  }
  
 /**
   * Delete object item from the database
   *
   * OVERIDE ancestor
   * 
   * @param xbs_modgen_Object $obj Handle to object object
   * @return bool TRUE on success else False
   */
  function delete(&$obj) {
  	$id = $obj->getvar('id');
	$sql = sprintf("delete from %s where id = %u",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$id);
	return $this->db->queryF($sql);
  }
  
  /**
  * Function: Count the number of Objects belonging to a module
  *
  * @param int $modid id of parent module
  * @version 1
  * @return int number of modules
  */
	function countAllObjects($modid) {
		$sql = sprintf("SELECT count(*) from %s where modid = %u",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$modid);
		$result = $this->db->queryF($sql);
		$ret = $this->db->fetchRow($result);
		$ret = $ret[0];
		return $ret;
	}//end function countObjects

  /**
  * Function: Count the number of Objects of a particular belonging to a module
  *
  * @param int $modid id of parent module
  * @param string $otype type of object
  * @version 1
  * @return int number of modules
  */
	function countTypeObjects($modid,$otype) {
		$sql = sprintf("SELECT count(*) from %s where modid = %u and objtype = %s",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$modid,$this->db->quoteString($otype));
		$result = $this->db->queryF($sql);
		$ret = $this->db->fetchRow($result);
		$ret = $ret[0];
		return $ret;
	}//end function countObjects
	
 /**
   * return an array of Id, objectName pairs for use in a select box
   * 
   * @param int $modid id of parent module
   * @return array
   */
  function getAllSelectList($modid) {
    $sql = sprintf("select id, objectname from %s where modid = %u",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$modid);
    $result = $this->db->query($sql);
    $ret = array();
    while ($res = $this->db->fetchArray($result)) {
		$ret[$res['id']] = $res['objname'];
    }//end while
    return $ret;
  }

/**
   * return an array of Id, objectName pairs for use in a select box for objects of a certain type
   * 
   * @param int $modid id of parent module
   * @param string $type
   * @return array
   */
  function getTypeSelectList($modid,$type) {
    $sql = sprintf("select id, objectname from %s where modid = %u and objtype = %s",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$modid,$this->db->quoteString($type));
    $result = $this->db->query($sql);
    $ret = array();
    while ($res = $this->db->fetchArray($result)) {
		$ret[$res['id']] = $res['objname'];
    }//end while
    return $ret;
  }
   
  /**
   * Return all object item objects for a module
   *
   * @param int $modid Module internal identifier
   * @return array Object objects
   */
  function getAllObjects($modid) {
  	$sql = sprintf("select id from %s where modid = %u",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$modid);
    $result = $this->db->query($sql);
    $ret = array();
    while ($res = $this->db->fetchArray($result)) {
		$ret[] = $this->getall($res['id']);
    }//end while
    return $ret;
  }//end function

  /**
   * Return all object item objects of a given type for a module
   *
   * @param int $modid Module internal identifier
   * @param string $type
   * @return array Object objects
   */
  function getTypeObjects($modid,$type) {
  	$sql = sprintf("select id from %s where modid = %u and objtype = %s",$this->db->prefix(XBS_MODGEN_TBL_OBJ),$modid,$this->db->quoteString($type));
    $result = $this->db->query($sql);
    $ret = array();
    while ($res = $this->db->fetchArray($result)) {
		$ret[] = $this->getall($res['id']);
    }//end while
    return $ret;
  }//end function
  
} //end class Xbs_ModgenXBS_MODGENObjectHandler

?>