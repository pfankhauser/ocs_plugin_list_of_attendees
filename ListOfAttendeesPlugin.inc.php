<?php

/**
 * @file ListOfAttendeesPlugin.inc.php
 *
 * Copyright (c) 2013 Péter Fankhauser
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.listOfAttendees
 * @class ListOfAttendeesPlugin
 *
 * ListOfAttendeesPlugin class
 *
 */

import('classes.plugins.GenericPlugin');

class ListOfAttendeesPlugin extends GenericPlugin {

	function getName() {
		return 'ListOfAttendeesPlugin';
	}

	function getDisplayName() {
		return __('plugins.generic.listOfAttendees.displayName');
	}

	function getDescription() {
		return __('plugins.generic.listOfAttendees.description');
	}

	/**
	 * Register the plugin, attaching to hooks as necessary.
	 * @param $category string
	 * @param $path string
	 * @return boolean
	 */
	function register($category, $path) {
		if (parent::register($category, $path)) {
			$this->addLocaleData();
			if ($this->getEnabled()) {
				$this->import('ListOfAttendeesDAO');
				if (checkPhpVersion('5.0.0')) { // WARNING: see http://pkp.sfu.ca/wiki/index.php/Information_for_Developers#Use_of_.24this_in_the_constructur
					$listOfAttendeesDAO = new ListOfAttendeesDAO();
				} else {
					$listOfAttendeesDAO =& new ListOfAttendeesDAO();
				}
				$returner =& DAORegistry::registerDAO('ListOfAttendeesDAO', $listOfAttendeesDAO);

				HookRegistry::register('LoadHandler', array(&$this, 'callbackHandleContent'));
			}
			return true;
		}
		return false;
	}

	/**
	 * Determine whether or not this plugin is enabled.
	 */
	function getEnabled() {
		$conference =& Request::getConference();
		$conferenceId = $conference?$conference->getId():0;
		return $this->getSetting($conferenceId, 0, 'enabled');
	}

	/**
	 * Set the enabled/disabled state of this plugin
	 */
	function setEnabled($enabled) {
		$conference =& Request::getConference();
		$conferenceId = $conference?$conference->getId():0;
		$this->updateSetting($conferenceId, 0, 'enabled', $enabled);

		return true;
	}
	
	/**
	 * Display verbs for the management interface.
	 */
	function getManagementVerbs() {
		$verbs = array();
		if ($this->getEnabled()) {
			$verbs[] = array(
				'disable',
				__('manager.plugins.disable')
			);
		} else {
			$verbs[] = array(
				'enable',
				__('manager.plugins.enable')
			);
		}
		return $verbs;
	}
	
	/**
	 * Declare the handler function to process the actual page PATH
	 */
	function callbackHandleContent($hookName, $args) {
		$page =& $args[0];

		if ( $page == 'attendees' ) {
			define('HANDLER_CLASS', 'ListOfAttendeesHandler');
			$this->import('ListOfAttendeesHandler');
			return true;
		}
		return false;
	}
	
	 	/*
 	 * Execute a management verb on this plugin
 	 * @param $verb string
 	 * @param $args array
	 * @param $message string Location for the plugin to put a result msg
 	 * @return boolean
 	 */
	function manage($verb, $args, &$message) {
		$templateMgr =& TemplateManager::getManager();
		$templateMgr->register_function('plugin_url', array(&$this, 'smartyPluginUrl'));
		$conference =& Request::getConference();
		$returner = true;

		switch ($verb) {
			case 'enable':
				$this->setEnabled(true);
				$returner = false;
				$message = __('plugins.generic.listOfAttendees.enabled');
				break;
			case 'disable':
				$this->setEnabled(false);
				$returner = false;
				$message = __('plugins.generic.listOfAttendees.disabled');					break;
			default:
				Request::redirect(null, null, 'manager');
		}
		return $returner;
	}
}

?>
