<#1>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Request/class.xdglRequest.php';
xdglRequest::installDB();
?>
<#2>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Config/class.xdglConfig.php';
xdglConfig::installDB();
?>
<#3>
<?php
require_once './Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Library/class.xdglLibrary.php';
xdglLibrary::installDB();
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

$ilDB->manipulate('UPDATE ' . xdglRequest::returnDbTableName() . ' SET library_id = ' . $ilDB->quote(xdglLibrary::getPrimaryId(), 'integer'));
$ilDB->manipulate('UPDATE ' . xdglRequest::returnDbTableName() . ' SET librarian_id = ' . $ilDB->quote(xdglRequest::LIBRARIAN_ID_NONE, 'integer') . ' WHERE librarian_id IS NULL');
?>
<#5>
<?php
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/DigiLit/classes/Librarian/class.xdglLibrarian.php');
xdglLibrarian::installDB();
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