<?php
/**
 * @file ListOfAttendeesDAO.inc.php
 *
 * Copyright (c) 2013 Péter Fankhauser
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.listOfAttendees
 * @class ListOfAttendeesDAO
 *
 * Operations for retrieving list of attendees.
 *
 */
import('db.DAO');

class ListOfAttendeesDAO extends DAO {

	/**
	 * Retrieve a list of users who are registered the specified sched conf.
	 * @param $schedConfId int
	 * @return DAOResultFactory matching Users
	 */
	function &getListOfAttendees($schedConfId, $paid = false) {
		$result =& $this->retrieve(
			'SELECT DISTINCT u.* FROM users u, registrations r
					WHERE u.user_id = r.user_id AND r.sched_conf_id = ?
					' . ($paid ? ' AND r.date_paid IS NOT NULL ': ' ') 
					. 'ORDER BY last_name ASC',
					$schedConfId
		);

		$userDao =& DAORegistry::getDAO('UserDAO');

		$returner = new DAOResultFactory($result, $userDao, '_returnUserFromRow');
		return $returner;
	}

}
?>
