<#1>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php';
xdglRequest::updateDB();
?>
<#2>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Config/class.xdglConfig.php';
xdglConfig::updateDB();
?>
<#3>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Library/class.xdglLibrary.php';
xdglLibrary::updateDB();
if (!xdglLibrary::where(array( 'is_primary' => 1 ))->hasSets()) {
	$xdglLibrary = new xdglLibrary();
	$xdglLibrary->setTitle('Primary Library');
	$xdglLibrary->setDescription('');
	$xdglLibrary->setActive(true);
	$xdglLibrary->setIsPrimary(true);
	$xdglLibrary->setEmail(xdglConfig::getConfigValue(xdglConfig::F_MAIL));
	$xdglLibrary->create();
}
xdglConfig::setConfigValue(xdglConfig::F_USE_LIBRARIES, true);
?>
<#4>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php';
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Library/class.xdglLibrary.php';
xdglRequest::updateDB();
global $ilDB;
/**
 * @var $ilDB ilDB
 */
$ilDB->manipulate('UPDATE ' . xdglRequest::TABLE_NAME . ' SET library_id = ' . $ilDB->quote(xdglLibrary::getPrimaryId(), 'integer'));
$ilDB->manipulate('UPDATE ' . xdglRequest::TABLE_NAME . ' SET librarian_id = ' . $ilDB->quote(xdglRequest::LIBRARIAN_ID_NONE, 'integer') . ' WHERE librarian_id IS NULL');
?>
<#5>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Librarian/class.xdglLibrarian.php');
xdglLibrarian::updateDB();
?>
<#6>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php');
xdglRequest::updateDB();
foreach(xdglRequest::get() as $xdglRequest) {
	/**
	 * @var $xdglRequest xdglRequest
	 */
	$xdglRequest->setLastChange($xdglRequest->getDateLastStatusChange());
	$xdglRequest->setLibrarianId(xdglRequest::LIBRARIAN_ID_NONE);
	$xdglRequest->update(true, false);
}
?>
<#7>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Librarian/class.xdglLibrarian.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Library/class.xdglLibrary.php');
global $ilUser;
if(xdglLibrarian::count() == 0) {
	$xdglLibrarian = new xdglLibrarian();
	$xdglLibrarian->setLibraryId(xdglLibrary::getPrimaryId());
	$xdglLibrarian->setUsrId($ilUser->getId());
	$xdglLibrarian->create();
}
?>
<#8>
<?php
// Base Configuration
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Config/class.xdglConfig.php';
if (! xdglConfig::getConfigValue(xdglConfig::F_MAX_DIGILITS)) {
	xdglConfig::setConfigValue(xdglConfig::F_MAX_DIGILITS, 10);
}
xdglConfig::setConfigValue(xdglConfig::F_USE_LIBRARIES, true);
xdglConfig::setConfigValue(xdglConfig::F_OWN_LIBRARY_ONLY, true);
?>
<#9>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/RequestUsage/class.xdglRequestUsage.php';
xdglRequestUsage::updateDB();
?>
<#10>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php';
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/RequestUsage/class.xdglRequestUsage.php';
global $ilDB;

global $ilDB;


$res = $ilDB->query('SELECT * FROM '.xdglRequest::TABLE_NAME);

while($row = $ilDB->fetchAssoc($res))
{
	$xdglRequestUsage = new xdglRequestUsage();
	$xdglRequest = new xdglRequest($row['id']);
	if($row['status'] == 5) {
		$xdglRequestUsage->setRequestId($row['copy_id']);
		$xdglRequest->delete();
	} else {
		$xdglRequestUsage->setRequestId($row['id']);
	}
	$xdglRequestUsage->setObjId($row['digi_lit_object_id']);
	$xdglRequestUsage->setCrsRefId($row['crs_ref_id']);
	$xdglRequestUsage->create();
}

?>
<#11>
<?php
require_once "Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php";
global $ilDB;
if ($ilDB->tableColumnExists(xdglRequest::TABLE_NAME, 'digi_lit_object_id')) {
	$ilDB->dropTableColumn(xdglRequest::TABLE_NAME, 'digi_lit_object_id');

}
if ($ilDB->tableColumnExists(xdglRequest::TABLE_NAME, 'crs_ref_id')) {
	$ilDB->dropTableColumn(xdglRequest::TABLE_NAME, 'crs_ref_id');
}
?>
