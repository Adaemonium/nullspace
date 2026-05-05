<?php

/**
*  ultimateXnova
*  based on 2moons by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package ultimateXnova
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2022 Koray Karakuş <koraykarakus@yahoo.com>
 * @copyright 2024 Pfahli (https://github.com/Pfahli)
 * @licence MIT
 * @version 1.8.x Koray Karakuş <koraykarakus@yahoo.com>
 * @link https://github.com/ultimateXnova/ultimateXnova
 */

/**
 *
 */
class ShowMessageListPage extends AbstractAdminPage
{

	function __construct()
	{
		parent::__construct();
	}

	function show(){

		global $LNG, $USER;
		$page		= HTTP::_GP('side', 1);
		$type		= HTTP::_GP('type', 100);
		$sender		= HTTP::_GP('sender', '', UTF8_SUPPORT);
		$receiver	= HTTP::_GP('receiver', '', UTF8_SUPPORT);
		$dateStart	= HTTP::_GP('dateStart', array());
		$dateEnd	= HTTP::_GP('dateEnd', array());

		$db = Database::get();

		$perSide	= 50;

		$messageList	= array();
		$userWhereSQL	= $dateWhereSQL	= '';


		$categories	= $LNG['mg_type'];
		unset($categories[999]);

		$dateStart	= array_filter($dateStart, 'is_numeric');
		$dateEnd	= array_filter($dateEnd, 'is_numeric');

		$useDateStart	= count($dateStart) == 3;
		$useDateEnd		= count($dateEnd) == 3;

		if($useDateStart && $useDateEnd)
		{
			$dateWhereSQL = ' AND message_time BETWEEN '.mktime(0, 0, 0, (int) $dateStart['month'], (int) $dateStart['day'], (int) $dateStart['year']).' AND '.mktime(23, 59, 59, (int) $dateEnd['month'], (int) $dateEnd['day'], (int) $dateEnd['year']);
		}
		elseif($useDateStart)
		{
			$dateWhereSQL = ' AND message_time > '.mktime(0, 0, 0, (int) $dateStart['month'], (int) $dateStart['day'], (int) $dateStart['year']);
		}
		elseif($useDateEnd)
		{
			$dateWhereSQL = ' AND message_time < '.mktime(23, 59, 59, (int) $dateEnd['month'], (int) $dateEnd['day'], (int) $dateEnd['year']);
		}

		if(!empty($sender))
		{
			$userWhereSQL = ' AND us.username = '.Database::get()->quote($sender);
		}
		elseif(!empty($receiver))
		{
			$userWhereSQL = ' AND u.username = '.Database::get()->quote($receiver);
		}

		$sql = "SELECT COUNT(*) as count FROM %%MESSAGES%% as m
		LEFT JOIN %%USERS%% as u ON m.message_owner = u.id
		LEFT JOIN %%USERS%% as us ON m.message_sender = us.id
		WHERE m.message_universe = :universe
		".$dateWhereSQL."
		".$userWhereSQL.";";

		$MessageCount = $db->selectSingle($sql, array(
			':universe' => Universe::getEmulated()
		), 'count');

		$maxPage	= max(1, ceil($MessageCount / $perSide));
		$page		= max(1, min($page, $maxPage));

		$sqlLimit	= (($page - 1) * $perSide).", ".($perSide - 1);

		if ($type == 100)
		{

			$sql = "SELECT u.username, us.username as senderName, m.*
			FROM %%MESSAGES%% as m
			LEFT JOIN %%USERS%% as u ON m.message_owner = u.id
			LEFT JOIN %%USERS%% as us ON m.message_sender = us.id
			WHERE m.message_universe = :universe
			".$dateWhereSQL."
			".$userWhereSQL."
			ORDER BY message_time DESC, message_id DESC
			LIMIT ".$sqlLimit.";";

			$messageRaw	= $db->select($sql,array(
				':universe' => Universe::getEmulated()
			));
		} else {

			$sql = "SELECT u.username, us.username as senderName, m.*
			FROM %%MESSAGES%% as m
			LEFT JOIN %%USERS%% as u ON m.message_owner = u.id
			LEFT JOIN %%USERS%% as us ON m.message_sender = us.id
			WHERE m.message_type = ".$type." AND message_universe = :universe
			".$dateWhereSQL."
			".$userWhereSQL."
			ORDER BY message_time DESC, message_id DESC
			LIMIT ".$sqlLimit.";";

			$messageRaw	= $db->select($sql,array(
				':universe' => Universe::getEmulated()
			));
		}

		foreach($messageRaw as $messageRow)
		{
			$messageList[$messageRow['message_id']]	= array(
				'sender'	=> empty($messageRow['senderName']) ? $messageRow['message_from'] : $messageRow['senderName'].' (ID:&nbsp;'.$messageRow['message_sender'].')',
				'receiver'	=> $messageRow['username'].' (ID:&nbsp;'.$messageRow['message_owner'].')',
				'subject'	=> $messageRow['message_subject'],
				'text'		=> $messageRow['message_text'],
				'type'		=> $messageRow['message_type'],
				'deleted'	=> $messageRow['message_deleted'] != NULL,
				'time'		=> str_replace(' ', '&nbsp;', _date($LNG['php_tdformat'], $messageRow['message_time']), $USER['timezone']),
			);
		}

		$this->assign(array(
			'categories'	=> $categories,
			'maxPage'		=> $maxPage,
			'page'			=> $page,
			'messageList'	=> $messageList,
			'type'			=> $type,
			'dateStart'		=> $dateStart,
			'dateEnd'		=> $dateEnd,
			'sender'		=> $sender,
			'receiver'		=> $receiver,
		));

		$this->display('MessageList.tpl');

	}

}
