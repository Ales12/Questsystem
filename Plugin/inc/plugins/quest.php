<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 03.10.2017
 * Time: 14:03
 */

if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

//Postbit
$plugins->add_hook('postbit', 'quest_postbit');
$plugins->add_hook('misc_start', 'quest_postbit_misc');

//usercp
$plugins->add_hook("usercp_do_options_end", "quest_usercp_do_options");
$plugins->add_hook("usercp_options_end", "quest_usercp_options");

//global
$plugins->add_hook("global_intermediate", "quest_global_alert");

//Index
$plugins->add_hook("global_intermediate", "quest_global_index");


//Profil
$plugins->add_hook("member_profile_end", "quest_profile");

function quest_info()
{
    return array(
        "name" => "Questsystem",
        "description" => "Dies ist ein Questsystem, welches Ermöglicht, dass User Aufgaben innerhalb ihrer Posts erfüllen können. Für jede Erfüllte Quest bekommen sie Punkte.",
        "website" => "",
        "author" => "Ales",
        "authorwebsite" => "",
        "version" => "1.0",
        "compatibility" => "18*"
    );
}

function quest_install()
{

    global $db, $mybb;
    /*
    * hier werden nun alle Datenbankänderungen und die, die neue Tabellen dazu bekommen aktiviert.
    */
    if($db->engine=='mysql'||$db->engine=='mysqli')
    {
        $db->query("CREATE TABLE `".TABLE_PREFIX."quest` (
          `quid` int(10) NOT NULL auto_increment,
           `uid` int(10) NOT NULL,
          `aufgabe` varchar(10000) NOT NULL,
          `ort` varchar(255) NOT NULL,
          `besonderheiten` varchar(255) NOT NULL,
           `points` int(10) NOT NULL,
          `admin` int(11) NOT NULL default '0',
          `datum` int(11) NOT NULL,
          PRIMARY KEY (`quid`)
        ) ENGINE=MyISAM".$db->build_create_table_collation());
    }

    if($db->engine=='mysql'||$db->engine=='mysqli')
    {
        $db->query("CREATE TABLE `".TABLE_PREFIX."ausgquest` (
          `auid` int(10) NOT NULL auto_increment,
          `quid` int(10) NOT NULL,
          `uid` int(10) NOT NULL,
          `gid` int(10) NOT NULL default '0',
          `aufgabe` varchar(10000) CHARACTER SET utf8 NOT NULL,
          `ort` varchar(255) CHARACTER SET utf8 NOT NULL,
          `besonderheiten` varchar(255) CHARACTER SET utf8 NOT NULL,
           `points` int(10) NOT NULL,
          `erledigt` varchar(4) CHARACTER SET utf8 NOT NULL default 'nein',
          `pid` int(10) NOT NULL,
          `tid` int(10) NOT NULL,
          `admin` int(11) NOT NULL default '0',
          `datum` int(1) NOT NULL,
          PRIMARY KEY (`auid`)
        ) ENGINE=MyISAM".$db->build_create_table_collation());
    }

    if($db->engine=='mysql'||$db->engine=='mysqli')
    {
        $db->query("CREATE TABLE `".TABLE_PREFIX."groupquest` (
          `gquid` int(10) NOT NULL auto_increment,
          `auid` int(10) NOT NULL,
          `uid` int(10) NOT NULL,
          `gid` int(10) NOT NULL default '0',
          `aufgabe` varchar(10000) CHARACTER SET utf8 NOT NULL,
          `ort` varchar(255) CHARACTER SET utf8 NOT NULL,
          `besonderheiten` varchar(255) CHARACTER SET utf8 NOT NULL,
           `points` int(10) NOT NULL,
          `pid` int(10) NOT NULL,
          `tid` int(10) NOT NULL,
          `admin` int(11) NOT NULL default '0',
          `datum` int(1) NOT NULL,
          PRIMARY KEY (`gquid`)
        ) ENGINE=MyISAM".$db->build_create_table_collation());
    }
    $db->add_column("users", "questoption", "int NOT NULL default '1'");
    $db->add_column("users", "questpoints", "int NOT NULL");

    /*
     * nun kommen die Einstellungen
     */
    $setting_group = array(
        'name' => 'questsystem',
        'title' => 'Questsystem',
        'description' => 'Einstellungen für das Questsystem',
        'disporder' => 2,
        'isdefault' => 0
    );

    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = array(
        'name' => 'forum_id',
        'title' => 'Kategorien ID',
        'description' => 'Gib hier die ID deiner Inplaykategorie an.',
        'optionscode' => 'forumselect',
        'value' => '1',
        'disporder' => 2,
        "gid" => (int)$gid
    );
    $db->insert_query('settings', $setting_array);


    $setting_array = array(
        'name' => 'group_quest',
        'title' => 'Gruppenquest',
        'description' => 'Soll es möglich sein, auch ganzen Gruppen Quests erledigen zu lassen?',
        'optionscode' => 'yesno',
        'value' => '0',
        'disporder' => 3,
        "gid" => (int)$gid
    );
    $db->insert_query('settings', $setting_array);

    $setting_array = array(
        'name' => 'group_quest_groups',
        'title' => 'Gruppen wählen',
        'description' => 'Welche Gruppen sind möglich?',
        'optionscode' => 'groupselect ',
        'value' => '0',
        'disporder' => 4,
        "gid" => (int)$gid
    );
    $db->insert_query('settings', $setting_array);

    $setting_array = array(
        'name' => 'housepoints',
        'title' => 'Housepoints-Plugin',
        'description' => 'Ist der Hauspunkte-Plugin aktiv? Wenn ja kannst du die Questpoints auf die Hauspunkte draufrechnen.',
        'optionscode' => 'yesno',
        'value' => '0',
        'disporder' => 5,
        "gid" => (int)$gid
    );
    $db->insert_query('settings', $setting_array);

    $setting_array = array(
        'name' => 'all_quest',
        'title' => 'Alle Quests anzeigen',
        'description' => 'Sollen alle annehmbaren Quests angezeigt werden, oder per Zufall ausgegeben werden.',
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => 6,
        "gid" => (int)$gid
    );
    $db->insert_query('settings', $setting_array);

    $setting_array = array(
        'name' => 'profil_quest',
        'title' => 'Quests im Profil anzeigen?',
        'description' => 'Sollen die offenen/erledigten Quests im Profil angezeigt werden?',
        'optionscode' => 'yesno',
        'value' => '1',
        'disporder' => 7,
        "gid" => (int)$gid
    );
    $db->insert_query('settings', $setting_array);

    //Templates
    $insert_array = array(
        'title' => 'quest',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Questsystem</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Willkommen beim Questsystem</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
	<h1>Willkommen beim Questsystem</h1>
	<div style="text-align:justify; width:500px; ">Herzlich Willkommen bei unserem Quest System. Hier findest du neben dem <b>Einsenden von Quests und der Auswahl einer Quest</b>, auch <b>all deine Quests</b>, die <i>offen oder von dir schon erledigt</i> worden sind. Zudem kannst du auf der <b>Rangliste</b> sehen, ob du dich mittlerweile unter unseren Top-Ten befindest.
<br /><br/>
Du kannst jeder Zeit Quests einsenden. Das sind kleine Aufgaben, ähnlich dem <b>duck, duck… goose</b>, die man dann im Inplay erfüllen muss. Hast du das getan, findest du im Post Bit über dem Button zum Bearbeiten einen weiteren Button, welcher <b>Quests als erledigt markiert</b>. Diese werden dann vom Team abgesegnet und du bekommst eine PM, wenn sie angenommen wurden.
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_add',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Quest hinzufügen</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Eine Quest Einreichen</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
<form method="post" action="quest.php?action=add_quest" id="add_quest">
<table width="50%" style="margin: auto;">
	<tr><td>
		<label id="aufgabe"><b>Questaufgabe</b></label></td>
		<td align="center">
	<textarea rows="3" cols="40" wrap="hard" name="aufgabe"></textarea>
		</td></tr>
		<tr><td>
		<label id="aufgabe"><b>Weitere Informationen</b></label></td>
		<td align="center">
<input type="text" placeholder="Ort" name="ort" id="ort" class="textbox" /> <input type="text" placeholder="Besonderheiten" name="besonderheiten" id="besonderheiten" class="textbox" />
		</td></tr>
			<tr><td>
		<label id="aufgabe"><b>Questpunkte</b></label></td>
		<td align="center">
<input type="number" value="15" name="points" id="points" class="textbox" />
		</td></tr>
	<tr><td colspan="2" align="center">
		<input type="submit" name="add_quest" value="eintragen" id="submit" class="button">
		</td></tr>
	</table>
	</form>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_add_group',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Gruppenquest hinzufügen</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Eine Quest Einreichen</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
<form method="post" action="quest.php?action=add_group_quest" id="add_quest_group">
<table width="80%" style="margin: auto;">
		<tr><td>
		<label id="aufgabe"><b>Gruppe wählen</b></label></td>
		<td align="center"><select name="gid">
			<option>Gruppe wählen</option>
{$group}
			</select>		</td></tr>
	<tr><td>
		<label id="aufgabe"><b>Questaufgabe</b></label></td>
		<td align="center">
	<textarea rows="3" cols="40" wrap="hard" name="aufgabe"></textarea>
		</td></tr>
		<tr><td>
		<label id="aufgabe"><b>Weitere Informationen</b></label></td>
		<td align="center">
<input type="text" placeholder="Ort" name="ort" id="ort" class="textbox" /> <input type="text" placeholder="Besonderheiten" name="besonderheiten" id="besonderheiten" class="textbox" />
		</td></tr>
			<tr><td>
		<label id="aufgabe"><b>Questpunkte</b></label></td>
		<td align="center">
<input type="number" value="15" name="points" id="points" class="textbox" />
		</td></tr>
	<tr><td colspan="2" align="center">
		<input type="submit" name="add_quest_group" value="eintragen" id="submit" class="button">
		</td></tr>
	</table>
	</form>
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_alert',
        'template' => $db->escape_string('<div class="pm_alert" id="quest_alert">
	<div>{$quest_alert}</div>
</div>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_choice',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Quest Auswählen</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Quest wählen</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_bit}
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_choice_bit',
        'template' => $db->escape_string('<table width="800px" align="center">
	<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Eingereicht von {$user} am {$datum}</td></tr>
		<tr><td class="trow2" align="center" width=\'20%\'><i class="fas fa-thumbtack"></i> Quest</td><td class="trow1"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten}</td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-hourglass-start"></i> Mit der Quest kannst du <b>{$points}</b> Punkte sammeln.   </td></tr>
	<tr><td class="trow1" align="center" colspan="2">{$accept_quest}</td></tr>
</table><br />'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_done_choice',
        'template' => $db->escape_string('<tr>
	<td class="trow1"><div style="text-align: center;"><i class="fas fa-clock"></i> {$anfang} von {$user} am {$datum}</div>
		<div style="text-align: justify"><i class="fas fa-thumbtack"></i> 
{$aufgabe}</div>
		<div style="text-align: center;"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten} <i class="fas fa-hourglass"></i> {$points} Punkte</div>
	</td>
</tr>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_index',
        'template' => $db->escape_string('<table width="80%" style="margin: auto;">
	<tr><td class="thead" width="50%">Neue Quests <div style="float: right; font-weight: bold;"><a href=\'quest.php?action=choice_quest\'>(Zu den Quests)</a></div></td><td class="thead" width="50%">Angenommen/Erledigte Quests</td></tr>
	<tr><td valign="top"><table width="100%">
		{$quest_newquest}
		</table>
		</td>
	<td valign="top"><table width="100%">
		{$quest_done_choice}
		</table>
		</td>
	</tr>
</table>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_menu',
        'template' => $db->escape_string('<td width="180px" valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
		<tr>
		<td class="thead">Navigation</td>
	</tr>
	<tr><td class="trow1 smalltext"><i class="fa fa-home" aria-hidden="true"></i> <a href="quest.php">Willkommen</a> </td></tr>
	<tr>
			<td class="tcat">Einreichen/Wählen</div>
		</td>
	</tr>
	<tr><td class="trow1 smalltext"><i class="fas fa-pencil-alt"></i> <a href="quest.php?action=add_quest">Quest einreichen</a></td></tr>
{$quest_group}
		<tr><td class="trow1 smalltext"><i class="fa fa-book" aria-hidden="true"></i> <a href="quest.php?action=choice_quest">Quest wählen</a></td></tr>

		<tr>
			<td class="tcat">Userübersicht</div>
		</td>
	</tr>
	<tr><td class="trow1 smalltext"><i class="fas fa-list-ul"></i> <a href="quest.php?action=own_quests">Deine ausgewählten Quests</a></td></tr>
	<tr><td class="trow1 smalltext"><i class="fas fa-list-ul"></i> <a href="quest.php?action=own_add_quest">Deine eingereichten Quest</a></td></tr>
{$quest_moderation}

	</table>
</td>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_menu_moderation',
        'template' => $db->escape_string('	<tr>
			<td class="tcat">Moderation</div>
		</td>
	</tr>
	<tr>
		<td class="trow1 smalltext"><i class="fas fa-edit"></i> <a href="quest.php?action=modcp_quests_newadd">Neue Quests bearbeiten</a></td>
</tr>
	<tr>
		<td class="trow1 smalltext"><i class="fas fa-list"></i> <a href="quest.php?action=modcp_quests_all">Alle Quests</a></td>
</tr>
	<tr>
		<td class="trow1 smalltext"><i class="fas fa-list"></i> <a href="quest.php?action=modcp_quests_done">Alle erledigten Quests</a></td>
</tr>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);


    $insert_array = array(
        'title' => 'quest_modcp_all',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Alle Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Alle Quests im Pool</strong></td>
</tr>
<tr>
<td class="tcat"><strong>Userquests</strong></td>
</tr>	
<tr>
<td class="trow1" align="center">
{$quest_all_bit}
</td>
</tr>
	<tr>
<td class="tcat"><strong>Gruppenquests</strong></td>
</tr>	
<tr>
<td class="trow1" align="center">
{$quest_all_group_bit}
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_modcp_all_bit',
        'template' => $db->escape_string('<table width="800px" align="center">
	<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Eingereicht von {$user} am {$datum}</td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td>
			<td class="trow1" colspan="2"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten} {$gruppe}</td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-hourglass-start"></i> Mit dieser Quest können <b>{$points}</b> Punkte gesammelt werden.  </td></tr>
{$options}
</table><br />'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_modcp_done',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Alle Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Alle erledigten Quests</strong></td>
</tr>
	<tr>
<td class="tcat"><strong>Userquests</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_done_bit}
</td>
</tr>
		<tr>
<td class="tcat"><strong>Gruppenquest pro User</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_group_done_bit}
</td>
</tr>
	
			<tr>
<td class="tcat"><strong>Gruppenquest</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_group_bit}
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_modcp_done_bit',
        'template' => $db->escape_string('<table width="800px" align="center">
<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Erledigt von {$user} am {$datum}</td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td>
				<td class="trow1" colspan="2"><div style="text-align: justify">{$aufgabe}</div></td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten} <i class="fas fa-hourglass-start"></i> {$points}   </td></tr>
	<tr><td class="trow2" align="center">{$options}</td><td class="trow2" align="center"> {$post}</td></tr>
</table><br />'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_modcp_groupquest',
        'template' => $db->escape_string('<table width="800px" align="center">
<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Gruppenquest vom {$datum} ({$erledigt} / {$group})</td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td>
				<td class="trow1" colspan="2"><div style="text-align: justify">{$aufgabe}</div></td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten} <i class="fas fa-hourglass-start"></i> {$points}   </td></tr>
		<tr><td class="trow2" align="center">{$options}</td><td class="trow2" align="center"> {$post}</td></tr>
</table><br />'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);


    $insert_array = array(
        'title' => 'quest_modcp_newadd',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} -Nicht angenommene Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Alle nicht angenommenen Quests</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
	{$take_all}<br />
{$quest_newadd_bit}
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_modcp_newadd_bit',
        'template' => $db->escape_string('<table width="800px" align="center">
	<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Eingereicht von {$user} am {$datum}</td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td>
			<td class="trow1" colspan="2"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten}</td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-hourglass-start"></i> Mit dieser Quest können<b>{$points}</b> Punkte gesammelt werden.  </td></tr>
			<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-check"></i> {$take_one} <i class="fas fa-times"></i> {$take_none} </td></tr>
</table><br />'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);


    $insert_array = array(
        'title' => 'quest_newquest',
        'template' => $db->escape_string('<tr>
	<td class="trow1"><div style="text-align: center;"><i class="fas fa-clock"></i> Eingereicht am {$datum}</div>
		<div style="text-align: justify"><i class="fas fa-thumbtack"></i> 
{$aufgabe}</div>
		<div style="text-align: center;"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten} <i class="fas fa-hourglass"></i> {$points} Punkte</div>
	</td>
</tr>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_options',
        'template' => $db->escape_string('<tr><td class="trow2" align="center" colspan=\'2\'><i class="fas fa-edit"></i> {$edit}  <i class="fas fa-trash-alt"></i> {$delete}</td></tr>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_own',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} -Die ausgewählten Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Deine ausgewählten Quests</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_own_bit}
</td>
</tr>
		<tr>
<td class="thead"><strong>Deine Gruppenquests</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_group_bit}
</td>
</tr>
	<tr>
<td class="thead"><strong>Deine erledigten Quests</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_done_bit}
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_own_add',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} -Alle eingereichten Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Deine eingereichten Quests - nicht angenommen</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_own_add_bit}
</td>
</tr>
	<tr>
<td class="thead"><strong>Deine eingereichten Quests - angenommen</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_own_add_bit2}
</td>
</tr>
</table>
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_own_add_bit',
        'template' => $db->escape_string('<table width="800px">
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-clock"></i> Eingereicht am {$datum} </td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td> <td class="trow1"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten} </td></tr>
	{$options}
</table><br />'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_own_bit',
        'template' => $db->escape_string('<table width="800px">
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td> <td class="trow1"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten} <i class="fas fa-clock"></i> Angenommen am {$datum}</td></tr>
{$points}
		{$quest_back}
</table><br />'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_own_edit',
        'template' => $db->escape_string('<style>.infopop { position: fixed; top: 0; right: 0; bottom: 0; left: 0; background: hsla(0, 0%, 0%, 0.5); z-index: 1; opacity:0; -webkit-transition: .5s ease-in-out; -moz-transition: .5s ease-in-out; transition: .5s ease-in-out; pointer-events: none; } .infopop:target { opacity:1; pointer-events: auto; } .infopop > .pop {width: 300px; position: relative; margin: 10% auto; padding: 25px; z-index: 3; } .closepop { position: absolute; right: -5px; top:-5px; width: 100%; height: 100%; z-index: 2; }</style>

<div id="popinfo$row[quid]" class="infopop">
  <div class="pop"><form method="post" action=""><input type=\'hidden\' value=\'{$row[\'quid\']}\' name=\'getquid\'>
 <table width="50%" style="margin: auto;" class="tborder">
	<tr><td>
		<label id="aufgabe"><b>Questaufgabe</b></label></td>
		<td align="center">
	<textarea rows="3" cols="40" wrap="hard" name="aufgabe">{$row[\'aufgabe\']}</textarea>
		</td></tr>
		<tr><td>
		<label id="aufgabe"><b>Örtlichkeit</b></label></td>
		<td align="center">
<input type="text" value="{$row[\'ort\']}" name="ort" id="ort" class="textbox" /> 
		</td></tr>
	 		<tr><td>
		<label id="aufgabe"><b>Besonderheit</b></label></td>
		<td align="center">
 <input type="text" value="{$row[\'besonderheiten\']}" name="besonderheiten" id="besonderheiten" class="textbox" />
		</td></tr>
			<tr><td>
		<label id="aufgabe"><b>Questpunkte</b></label></td>
		<td align="center">
<input type="number" name="points" id="points" class="textbox" value="{$row[\'points\']}" />
		</td></tr>
<tr><td colspan=\'2\' align=\'center\'><input type="submit" name="edit" value="Quest bearbeiten" id="submit" class="button"></td></tr></table>
	  </form>
		</div><a href="#closepop" class="closepop"></a>
</div>

<a href="#popinfo$row[quid]">Quest editieren</a>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_postbit',
        'template' => $db->escape_string('<html>
<head>
<title>{$mybb->settings[\'bbname\']} - Quest als erledigt markieren</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
<td class="thead"><strong>Quest als erledigt markieren</strong></td>
</tr>
	<tr>
<td class="tcat"><strong>Userquest</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_postbit}
</td>
</tr>
		<tr>
<td class="tcat"><strong>Gruppenquest</strong></td>
</tr>
<tr>
<td class="trow1" align="center">
{$quest_postbit_group}
</td>
</tr>
</table>
{$footer}
</body>
</html>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_postbit_bit',
        'template' => $db->escape_string('<table width="800px">
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td> <td class="trow1"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten}</td></tr>
{$points}
{$time}
	<tr><td colspan="2" align="center">
<form method="post" action="misc.php?action=quest_take" id="take_quest">
	<input value="{$row[\'auid\']}" type="hidden" name="auid">
		<input value="{$row[\'gid\']}" type="hidden" name="gid">
	<table>
		<tr><td>
			<input placeholder="tid" type="text" name="tid"  id="points" class="textbox" >
			</td><td>
			<input placeholder="pid" type="text" name="pid"  id="points" class="textbox" >
			</td><td>
					<input type="submit" name="take_quest" value="Quest erledigen" id="submit" class="button">
			</td></tr>
	</table>
		</form></td></tr>
</table><br />'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_profil',
        'template' => $db->escape_string('						<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
				<tr>
					<td class="thead"><strong>Offene Quests</strong></td>
					<td class="thead"><strong>Erledigte Quests</strong></td>
				</tr>
			<tr><td width="50%" valign="top">
					{$quest_profil_open}			
				</td>
					<td width="50%" valign="top">				
					{$quest_profil_done}
				</tr>
			</table>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_profil_bit',
        'template' => $db->escape_string('<table>
		<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-clock"></i> Angenommen am {$datum}</td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td> <td class="trow1"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten} <i class="fas fa-hourglass"></i> {$points} Punkte <i class="fas fa-clock"></i> Angenommen am {$datum}</td></tr>
	<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-bookmark"></i> {$post}  </td></tr>
</table><br />'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title' => 'quest_profil_checked',
        'template' => $db->escape_string('<tr><td valign="top" width="1"><input type="checkbox" class="checkbox" name="questoption" id="questoption" value="1" {$checkoption} /></td>
<td><span class="smalltext"><label for="questoption">Möchtest du die Questbox auf dem Index sehen?</label></span></td>
</tr>'),
        'sid' => '-1',
        'version' => '',
        'dateline' => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);




}

function quest_is_installed()
{
    global $db;

    if($db->table_exists('quest'))
    {
        return true;
    }
    return false;
}

function quest_uninstall()
{
    global $db;

    $db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='questsystem'");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='forum_id'");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='questpoints'");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='housepoints'");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='all_quest'");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='profil_quest'");
    $db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name='group_quest'");

    if($db->table_exists("quest"))
    {
        $db->drop_table("quest");
    }

    if($db->table_exists("ausgquest"))
    {
        $db->drop_table("ausgquest");
    }
    if($db->table_exists("groupquest"))
    {
        $db->drop_table("groupquest");
    }

    if($db->field_exists("questoption", "users")) {
            $db->drop_column("users", "questoption");

    }
    if($db->field_exists("questpoints", "users")) {
        $db->drop_column("users", "questpoints");

    }
   $db->delete_query("templates", "title like '%quest%'");

    rebuild_settings();


}


function quest_activate()
{
    global $db, $mybb;

    require MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets("header", "#".preg_quote('{$pm_notice}')."#i", ' {$quest_newadd_alert}{$quest_done_alert}{$pm_notice}');
    find_replace_templatesets("header", "#".preg_quote('{$menu_portal}')."#i", '{$menu_portal} {$quest_link}');
    find_replace_templatesets("member_profile", "#".preg_quote('{$awaybit}')."#i", '{$quest_profil}{$awaybit}');
    find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'button_edit\']}')."#i", '{$post[\'quest\']}{$post[\'button_edit\']}');
    find_replace_templatesets("postbit", "#".preg_quote('{$post[\'button_edit\']}')."#i", '{$post[\'quest\']}{$post[\'button_edit\']}');
}

function quest_deactivate()
{
    global $db;
    require MYBB_ROOT."/inc/adminfunctions_templates.php";
    find_replace_templatesets("header", "#".preg_quote('{$quest_newadd_alert}{$quest_done_alert}')."#i", '', 0);
    find_replace_templatesets("header", "#".preg_quote('{$quest_link}')."#i", '', 0);
    find_replace_templatesets("member_profile", "#".preg_quote('{$quest_profil}')."#i", '', 0);
    find_replace_templatesets("postbit_classic", "#".preg_quote('{$post[\'quest\']}')."#i", '', 0);
    find_replace_templatesets("postbit", "#".preg_quote('{$post[\'quest\']}')."#i", '', 0);
}

//Button im Postbit
function quest_postbit($post){

    global $db, $mybb, $templates,  $forum, $forum_id;

    $post['quest'] = "";
    $forum_id = $mybb->settings['forum_id'];
    $forum['parentlist'] = ",".$forum['parentlist'].",";
    $uid = $mybb->user['uid'];

    if(preg_match("/,$forum_id,/i", $forum['parentlist'])){
        if($post['uid'] == $uid){
            $post['quest'] = "<a href='misc.php?action=quest_take&tid=$post[tid]&pid=$post[pid]' class=\"postbit_quote postbit_mirage\">Eine Quest abschließen</a>";
        }
   }

        return $post;
}



//Quest abhaken
function quest_postbit_misc(){
    global $mybb, $templates, $db, $lang, $header, $headerinclude, $footer, $page, $parser,   $quest_postbit_group ;

//Optionen erlauben. Ermöglicht ohne html Zeilenumbrüche und Co
    $options = array(
        "allow_html" => 1,
        "allow_mycode" => 1,
        "allow_smilies" => 1,
        "allow_imgcode" =>1,
        "filter_badwords" => 0,
        "nl2br" => 1,
        "allow_videocode" => 0
    );

    if($mybb->get_input('action') == 'quest_take')
    {
        // Do something, for example I'll create a page using the hello_world_template

        require_once MYBB_ROOT."inc/class_parser.php";;
        $parser = new postParser;

        // Add a breadcrumb
        add_breadcrumb('Quest als erledigt markieren', "misc.php?action=quest_take");

        $uid = $mybb->user['uid'];

        //Einzelgesuche
        $select = $db->query("SELECT *
            FROM " . TABLE_PREFIX . "ausgquest
            WHERE uid = $uid
            and erledigt= 'nein'
            and gid=0
            ORDER by datum
        ");

        while ($row = $db->fetch_array($select)) {

            $datum = date('d.m.Y', $row['datum']);

            $aufgabe = "";
            $ort = "";
            $besonderheiten = "";
            $points = "";
            $gid = $row['gid'];

            $just_points = $row['points'];
            $aufgabe = $parser->parse_message($row['aufgabe'], $options);
            $ort = $row['ort'];
            $besonderheiten = $row['besonderheiten'];
            $points = "	<tr><td class=\"trow2\" align=\"center\" colspan=\"2\"><i class=\"fas fa-hourglass-start\"></i> Mit der Quest kannst du <b>{$row['points']}</b> Punkte sammeln.</td></tr>";
            $time = "	<tr><td class=\"trow1\" colspan=\"2\" align=\"center\"><i class=\"fas fa-clock\"></i> Angenommen am {$datum}</td></tr>";
            eval('$quest_postbit  .= "' . $templates->get('quest_postbit_bit') . '";');
        }


        //Quest bestätigen
        if (isset($mybb->input['take_quest'])) {
        $auid = $mybb->input['auid'];
        $tid = $mybb->input['tid'];
        $pid = $mybb->input['pid'];
        $gid = $mybb->input['gid'];

            $db->query("UPDATE " . TABLE_PREFIX . "ausgquest SET pid = '" . $pid . "', tid = '" . $tid . "', erledigt = 'ja' WHERE auid = '" . $auid . "'");

        redirect('misc.php?action=quest_take');

    }
        eval("\$page = \"".$templates->get("quest_postbit")."\";");
        output_page($page);
    }
}

//Questheader anzeigen oder nicht

// Option, ob Quest oben angezeigt wird, oder nicht
function quest_usercp_options(){
    global $mybb, $db, $templates, $questoption, $checkoption;

    if(isset($mybb->user['questoption']) && $mybb->user['questoption'] == 1) {
        $checkoption = "checked=\"checked\"";
    }else{
        $checkoption = "";
    }

    eval("\$questoption = \"".$templates->get("quest_profil_checked")."\";");

}

function quest_usercp_do_options(){
    global $db, $mybb;

    $uid = $mybb->user['uid'];

    $new_array = array(
        "questoption" => $mybb->get_input('questoption',MyBB::INPUT_INT )
    );

    $db->update_query("users", $new_array, "uid = $uid");
}


//Benachrichtigungen
function quest_global_alert(){
    global $db, $mybb, $templates, $quest_newadd_alert, $quest_alert, $quest_done_alert, $quest_done_alert ;

    // Neue Quest wurde eingereicht
    $select = $db->query("SELECT *
    FROM ".TABLE_PREFIX."quest
    WHERE admin = '0'
    ");

    $count_q = mysqli_num_rows ($select);

    while($row = $db->fetch_array($select)){
        if($mybb->usergroup['canmodcp'] == 1){
            $quest_alert = "Aktuell wurden <b>{$count_q} Quest(s)</b> eingereicht. <div style='float:right;'>(<a href='quest.php?action=modcp_quests_newadd'>Quest bearbeiten</a>)</div>";
            eval('$quest_newadd_alert = "'.$templates->get('quest_alert').'";');
        }
    }

// Eine Quest wurde erledigt
    $query = $db->query("SELECT *
FROM ".TABLE_PREFIX."ausgquest
WHERE erledigt = 'ja'
AND admin = '0'
");

    $count_eq = mysqli_num_rows ($query);

    while($row = $db->fetch_array($query)){
        if($mybb->usergroup['canmodcp'] == 1){

            $quest_alert = "Aktuell wurden <b>{$count_eq} Quest(s)</b> von den Usern erledigt. <div style='float:right;'>(<a href='quest.php?action=modcp_quests_done'>erledigte Quest bearbeiten</a>)</div>";

            eval('$quest_done_alert = "'.$templates->get('quest_alert').'";');
        }

    }

    // Eine Gruppenquest wurde erledigt
    $query = $db->query("SELECT *
FROM ".TABLE_PREFIX."groupquest
WHERE admin = '0'
");

    $count_gq= mysqli_num_rows ($query);

    while($row = $db->fetch_array($query)){
        if($mybb->usergroup['canmodcp'] == 1){

            $quest_alert = "Aktuell wurden <b>{$count_gq} Gruppenuest(s)</b> von den Usern erledigt. <div style='float:right;'>(<a href='quest.php?action=modcp_quests_done'>erledigte Quest bearbeiten</a>)</div>";

            eval('$quest_done_alert = "'.$templates->get('quest_alert').'";');
        }

    }
}


function quest_global_index(){
    global $db, $mybb, $templates, $quest_done_choice, $index_quest, $anfang, $quest_link;

    $quest_link = "<li><a href=\"{$mybb->settings['bburl']}/quest.php\" class=\"modcp\">Questsystem</a></li>";

    if($mybb->user['questoption'] == 1) {

        //angenommene/erledigte Quests

        $query = $db->query("SELECT *
    FROM " . TABLE_PREFIX . "ausgquest aq
    LEFT JOIN " . TABLE_PREFIX . "users u
    ON aq.uid = u.uid
    WHERE aq.gid = 0
    ORDER BY datum desc LIMIT 5
    ");

        $count = mysqli_num_rows($query);

        if ($count > 0) {
            while ($row = $db->fetch_array($query)) {

                $datum = date('d.m.Y', $row['datum']);
                $username = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
                $user = build_profile_link($username, $row['uid']);

                $aufgabe = "";
                $ort = "";
                $besonderheiten = "";
                $points = "";
                $uid = $mybb->user['uid'];
                $quest_back = "";


                if (my_strlen($row['aufgabe']) > 100) {
                    $aufgabe = my_substr($row['aufgabe'], 0, 100) . "...";
                }
                $ort = $row['ort'];
                $besonderheiten = $row['besonderheiten'];
                $points = $row['points'];

                if ($row['erledigt'] == 'ja' && $row['admin'] == '1') {
                    $anfang = "Erledigt";
                } else {
                    $anfang = "Ausgewählt";
                }
                eval("\$quest_done_choice .= \"" . $templates->get("quest_done_choice") . "\";");
            }


        }


        //Neue eingereichte Quests
        $select = $db->query("SELECT *
    FROM " . TABLE_PREFIX . "quest 
    WHERE admin = 1
    ORDER BY datum desc LIMIT 5
    ");

        $count = mysqli_num_rows($select);

        if ($count > 0) {

            while ($row = $db->fetch_array($select)) {
                $aufgabe = "";
                $ort = "";
                $besonderheiten = "";
                $points = "";
                $datum = '';
                $uid = $mybb->user['uid'];
                $quest_back = "";


                if (my_strlen($row['aufgabe']) > 100) {
                    $aufgabe = my_substr($row['aufgabe'], 0, 100) . "...";
                }
                $ort = $row['ort'];
                $besonderheiten = $row['besonderheiten'];
                $points = $row['points'];
                $datum = date('d.m.Y', $row['datum']);
                eval("\$quest_newquest .= \"" . $templates->get("quest_newquest") . "\";");
            }
        }


        eval("\$index_quest = \"" . $templates->get("quest_index") . "\";");
    }

}


function quest_profile()
{
    global $db, $templates, $page, $mybb, $memprofile, $quest_profil, $quest_profil_open, $quest_profil_done, $parser, $options;
    //Optionen erlauben. Ermöglicht ohne html Zeilenumbrüche und Co
    $options = array(
        "allow_html" => $mybb->settings['userpages_html_active'],
        "allow_mycode" => $mybb->settings['userpages_mycode_active'],
        "allow_smilies" => 1,
        "allow_imgcode" => $mybb->settings['userpages_images_active'],
        "filter_badwords" => $mybb->settings['userpages_badwords_active'],
        "nl2br" => 1,
        "allow_videocode" => $mybb->settings['userpages_videos_active']
    );

    $uid = $memprofile['uid'];
    $profil_quest = $mybb->settings['profil_quest'];
    if ($profil_quest == 1) {
        //Alle offenen Quests
        $select = $db->query("SELECT *
    FROM " . TABLE_PREFIX . "ausgquest
    WHERE uid = $uid
    and erledigt = 'nein'   
    ORDER BY datum ASC
    ");

        $count = mysqli_num_rows($select);
        if ($count == 0) {
            $quest_profil_open = "<div class='smalltext'>Keine Offenen Quests vorhanden</div>";
        } else {
            while ($row = $db->fetch_array($select)) {
                $aufgabe = "";
                $ort = "";
                $besonderheiten = "";
                $points = "";
                $datum = '';

                $aufgabe = $parser->parse_message($row['aufgabe'], $options);
                $ort = $row['ort'];
                $besonderheiten = $row['besonderheiten'];
                $points = $row['points'];
                $datum = date('d.m.Y', $row['datum']);
                eval("\$quest_profil_open .= \"" . $templates->get("quest_profil_bit") . "\";");
            }
        }
        //alle erledigten Quests
        $select = $db->query("SELECT *
    FROM " . TABLE_PREFIX . "ausgquest
    WHERE uid = $uid
    and erledigt = 'ja'
    and admin = 1
    ORDER BY datum ASC
    ");
        $count = mysqli_num_rows($select);
        if ($count == 0) {
            $quest_profil_done = "<div class='smalltext'>Keine erledigten Quests vorhanden</div>";
        } else {
            while ($row = $db->fetch_array($select)) {
                $aufgabe = "";
                $ort = "";
                $besonderheiten = "";
                $points = "";
                $datum = '';

                $aufgabe = $parser->parse_message($row['aufgabe'], $options);
                if(!empty($row['ort'])){
                    $ort = "<i class=\"fas fa-map-pin\"></i> ".$row['ort'];
                }

                if(!empty($row['besonderheiten'])){
                    $besonderheiten = "<i class=\"fas fa-star\"></i> ".$row['besonderheiten'];
                }
                $points = $row['points'];
                $datum = date('d.m.Y', $row['datum']);
                $tid = $row['tid'];
                $pid = $row['pid'];
                $post = "<a href='showthread.php?tid=$tid&pid=$pid'>Zum Post</a>";
                eval("\$quest_profil_done .= \"" . $templates->get("quest_profil_bit") . "\";");
            }
        }
            eval("\$quest_profil = \"" . $templates->get("quest_profil") . "\";");

    }
}
?>