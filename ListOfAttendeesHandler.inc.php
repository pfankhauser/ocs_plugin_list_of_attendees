<?php

/**
 * @file ListOfAttendeesHandler.inc.php
 *
 * Copyright (c) 2013 Péter Fankhauser
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.listOfAttendees
 * @class ListOfAttendeesHandler
 *
 * Find the content and display the appropriate page
 *
 */

import('handler.Handler');

class ListOfAttendeesHandler extends Handler {

	function index() {
		AppLocale::requireComponents(array(LOCALE_COMPONENT_PKP_COMMON, LOCALE_COMPONENT_APPLICATION_COMMON));		

		$templateMgr =& TemplateManager::getManager();
		
		$conference =& Request::getConference();
		$schedConf =& Request::getSchedConf();
		$schedConfId = ($schedConf ? $schedConf->getId() : $conference->getId());
		
		$templateMgr->addStyleSheet(Request::getBaseUrl().'/plugins/generic/listOfAttendees/listOfAttendees.css');
		
		$templateMgr->assign('pageHierarchy', array(
			array(Request::url(null, 'index', 'index'), $conference->getConferenceTitle(), true),
			array(Request::url(null, null, 'index'), $schedConf->getSchedConfTitle(), true)));

		$templateMgr->assign('title', __('plugins.generic.listOfAttendees.pageTitle'));

		$listOfAttendeesDAO =& DAORegistry::getDAO('ListOfAttendeesDAO');
		$attendees =& $listOfAttendeesDAO->getListOfAttendees($schedConfId);
		$attendees =& $attendees->toArray();
		$templateMgr->assign_by_ref('attendees',  $attendees);

		$countryDao =& DAORegistry::getDAO('CountryDAO');
		$countries =& $countryDao->getCountries();
		$templateMgr->assign_by_ref('countries', $countries);
		
		$listOfAttendeesPlugin =& PluginRegistry::getPlugin('generic', 'ListOfAttendeesPlugin');
		$templateMgr->display($listOfAttendeesPlugin->getTemplatePath().'index.tpl');
	}
	
}

?>
