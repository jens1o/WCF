<?php
namespace wcf\action;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;
use wcf\util\HeaderUtil;

/**
 * Does the user logout.
 * 
 * @author	Marcel Werk
 * @copyright	2001-2018 WoltLab GmbH
 * @license	GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package	WoltLabSuite\Core\Action
 */
class LogoutAction extends \wcf\acp\action\LogoutAction {
	const AVAILABLE_DURING_OFFLINE_MODE = true;
	
	/**
	 * @inheritDoc
	 */
	public function execute() {
		AbstractSecureAction::execute();
		
		// do logout
		WCF::getSession()->delete();
		
		// remove cookies
		if (isset($_COOKIE[COOKIE_PREFIX.'userID'])) {
			HeaderUtil::setCookie('userID', 0);
		}
		if (isset($_COOKIE[COOKIE_PREFIX.'password'])) {
			HeaderUtil::setCookie('password', '');
		}
		
		$this->executed();
		
		// forward to index page
		HeaderUtil::redirect(LinkHandler::getInstance()->getLink());
		exit;
	}
}
