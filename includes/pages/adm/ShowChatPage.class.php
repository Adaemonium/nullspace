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
class ShowChatPage extends AbstractAdminPage
{

	function __construct()
	{
		parent::__construct();
	}

	function show(){

		global $LNG;

		$config = Config::get(Universe::getEmulated());

		$template	= new template();

		$this->assign(array(
			'chat_closed'			=> $config->chat_closed,
			'chat_allowchan'		=> $config->chat_allowchan,
			'chat_allowmes'			=> $config->chat_allowmes,
			'chat_logmessage'		=> $config->chat_logmessage,
			'chat_nickchange'		=> $config->chat_nickchange,
			'chat_botname'			=> $config->chat_botname,
			'chat_channelname'		=> $config->chat_channelname,
			'se_server_parameters'	=> $LNG['se_server_parameters'],
			'se_save_parameters'	=> $LNG['se_save_parameters'],
			'ch_closed'				=> $LNG['ch_closed'],
			'ch_allowchan'			=> $LNG['ch_allowchan'],
			'ch_allowmes'			=> $LNG['ch_allowmes'],
			'ch_allowdelmes'		=> $LNG['ch_allowdelmes'],
			'ch_logmessage'			=> $LNG['ch_logmessage'],
			'ch_nickchange'			=> $LNG['ch_nickchange'],
			'ch_botname'			=> $LNG['ch_botname'],
			'ch_channelname'		=> $LNG['ch_channelname'],
		));

		$this->display('page.chat.default.tpl');

	}


	function saveSettings(){

		global $LNG;

		$config = Config::get(Universe::getEmulated());

		$config_before = array(
			'chat_closed'			=> $config->chat_closed,
			'chat_allowchan'		=> $config->chat_allowchan,
			'chat_allowmes'			=> $config->chat_allowmes,
			'chat_allowdelmes'		=> $config->chat_allowdelmes,
			'chat_logmessage'		=> $config->chat_logmessage,
			'chat_nickchange'		=> $config->chat_nickchange,
			'chat_botname'			=> $config->chat_botname,
			'chat_channelname'		=> $config->chat_channelname,
		);

		$chat_allowchan			= isset($_POST['chat_allowchan']) && $_POST['chat_allowchan'] == 'on' ? 1 : 0;
		$chat_allowmes			= isset($_POST['chat_allowmes']) && $_POST['chat_allowmes'] == 'on' ? 1 : 0;
		$chat_allowdelmes		= isset($_POST['chat_allowdelmes']) && $_POST['chat_allowdelmes'] == 'on' ? 1 : 0;
		$chat_logmessage		= isset($_POST['chat_logmessage']) && $_POST['chat_logmessage'] == 'on' ? 1 : 0;
		$chat_nickchange		= isset($_POST['chat_nickchange']) && $_POST['chat_nickchange'] == 'on' ? 1 : 0;
		$chat_closed			= isset($_POST['chat_closed']) && $_POST['chat_closed'] == 'on' ? 1 : 0;

		$chat_channelname		= HTTP::_GP('chat_channelname', '', true);
		$chat_botname			= HTTP::_GP('chat_botname', '', true);

		$config_after = array(
			'chat_closed'			=> $chat_closed,
			'chat_allowchan'		=> $chat_allowchan,
			'chat_allowmes'			=> $chat_allowmes,
			'chat_allowdelmes'		=> $chat_allowdelmes,
			'chat_logmessage'		=> $chat_logmessage,
			'chat_nickchange'		=> $chat_nickchange,
			'chat_botname'			=> $chat_botname,
			'chat_channelname'		=> $chat_channelname,
		);

		foreach($config_after as $key => $value)
		{
			$config->$key	= $value;
		}
		$config->save();

		$LOG = new Log(3);
		$LOG->target = 3;
		$LOG->old = $config_before;
		$LOG->new = $config_after;
		$LOG->save();

		$redirectButton = array();
		$redirectButton[] = array(
			'url' => 'admin.php?page=chat&mode=show',
			'label' => $LNG['uvs_back']
		);

		$this->printMessage($LNG['settings_successful'],$redirectButton);

	}

	function log(){
		global $LNG, $USER;

		$page       = HTTP::_GP('side', 1);
		$username   = HTTP::_GP('username', '', UTF8_SUPPORT);
		$dateStart  = HTTP::_GP('dateStart', array());
		$dateEnd    = HTTP::_GP('dateEnd', array());

		$db = Database::get();

		$perSide    = 50;
		$whereSQL   = '';
		$params     = array();

		$dateStart  = array_filter($dateStart, 'is_numeric');
		$dateEnd    = array_filter($dateEnd, 'is_numeric');

		$useDateStart   = count($dateStart) == 3;
		$useDateEnd     = count($dateEnd) == 3;

		if($useDateStart && $useDateEnd)
		{
			$whereSQL .= ' AND dateTime BETWEEN :dateStart AND :dateEnd';
			$params[':dateStart'] = date('Y-m-d H:i:s', mktime(0, 0, 0, (int) $dateStart['month'], (int) $dateStart['day'], (int) $dateStart['year']));
			$params[':dateEnd']   = date('Y-m-d H:i:s', mktime(23, 59, 59, (int) $dateEnd['month'], (int) $dateEnd['day'], (int) $dateEnd['year']));
		}
		elseif($useDateStart)
		{
			$whereSQL .= ' AND dateTime > :dateStart';
			$params[':dateStart'] = date('Y-m-d H:i:s', mktime(0, 0, 0, (int) $dateStart['month'], (int) $dateStart['day'], (int) $dateStart['year']));
		}
		elseif($useDateEnd)
		{
			$whereSQL .= ' AND dateTime < :dateEnd';
			$params[':dateEnd'] = date('Y-m-d H:i:s', mktime(23, 59, 59, (int) $dateEnd['month'], (int) $dateEnd['day'], (int) $dateEnd['year']));
		}

		if(!empty($username))
		{
			$whereSQL .= ' AND userName = :username';
			$params[':username'] = $username;
		}

		$countSql = "SELECT COUNT(*) as count FROM %%CHAT_MES%% WHERE 1=1 ".$whereSQL.";";
		$MessageCount = $db->selectSingle($countSql, $params, 'count');

		$maxPage    = max(1, ceil($MessageCount / $perSide));
		$page       = max(1, min($page, $maxPage));
		$sqlLimit   = (($page - 1) * $perSide).", ".($perSide - 1);

		$sql = "SELECT id, userName, userRole, channel, dateTime, ip, text
		FROM %%CHAT_MES%%
		WHERE 1=1
		".$whereSQL."
		ORDER BY dateTime DESC
		LIMIT ".$sqlLimit.";";

		$rawLogs = $db->select($sql, $params);

		$logList = array();
		foreach($rawLogs as $row)
		{
			$logList[] = array(
				'id'        => $row['id'],
				'username'  => $row['userName'],
				'role'      => $row['userRole'],
				'channel'   => $row['channel'],
				'time'      => _date($LNG['php_tdformat'], strtotime($row['dateTime']), $USER['timezone']),
				'ip'        => inet_ntop($row['ip']),
				'text'      => $row['text'],
			);
		}

		$this->assign(array(
			'logList'       => $logList,
			'maxPage'       => $maxPage,
			'page'          => $page,
			'username'      => $username,
			'dateStart'     => $dateStart,
			'dateEnd'       => $dateEnd,
		));

		$this->display('page.chat.log.tpl');
	}

}
