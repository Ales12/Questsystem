########
README - QUESTSYSTEM 2.0
by Ales
######


VARIABELN 
###############

header
-----------------
{$quest_newadd_alert}{$quest_done_alert}
--------
{$quest_link}
--------------

member_profile
------------------
{$quest_profil}
------------------

postbit_classic
---------------------
{$post['quest']}

postbit 
-------------------
{$post['quest']}


TEMPLATES
###############

quest
---------
<html>
<head>
<title>{$mybb->settings['bbname']} - Questsystem</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
---------------------

quest_add
---------------------
<html>
<head>
<title>{$mybb->settings['bbname']} - Quest hinzufügen</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
----------------------

quest_add_group
------------------------
<html>
<head>
<title>{$mybb->settings['bbname']} - Gruppenquest hinzufügen</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
--------------------------

quest_alert
--------------------------
div class="pm_alert" id="quest_alert">
	<div>{$quest_alert}</div>
</div>
-------------------------

quest_choice
------------------------
<html>
<head>
<title>{$mybb->settings['bbname']} - Quest Auswählen</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
------------------------

quest_choice_bit
-----------------------
<table width="800px" align="center">
	<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Eingereicht von {$user} am {$datum}</td></tr>
		<tr><td class="trow2" align="center" width='20%'><i class="fas fa-thumbtack"></i> Quest</td><td class="trow1"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten}</td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-hourglass-start"></i> Mit der Quest kannst du <b>{$points}</b> Punkte sammeln.   </td></tr>
	<tr><td class="trow1" align="center" colspan="2">{$accept_quest}</td></tr>
</table><br />
------------------------

quest_done_choice
---------------------------
<tr>
	<td class="trow1"><div style="text-align: center;"><i class="fas fa-clock"></i> {$anfang} von {$user} am {$datum}</div>
		<div style="text-align: justify"><i class="fas fa-thumbtack"></i> 
{$aufgabe}</div>
		<div style="text-align: center;"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten} <i class="fas fa-hourglass"></i> {$points} Punkte</div>
	</td>
</tr>
-----------------------

quest_index
-----------------------
<table width="80%" style="margin: auto;">
	<tr><td class="thead" width="50%">Neue Quests <div style="float: right; font-weight: bold;"><a href='quest.php?action=choice_quest'>(Zu den Quests)</a></div></td><td class="thead" width="50%">Angenommen/Erledigte Quests</td></tr>
	<tr><td valign="top"><table width="100%">
		{$quest_newquest}
		</table>
		</td>
	<td valign="top"><table width="100%">
		{$quest_done_choice}
		</table>
		</td>
	</tr>
</table>
-----------------------

quest_menu
-----------------------
<td width="180px" valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</td>
-------------------------------

quest_menu_moderation
-------------------------------
	<tr>
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
</tr>
----------------------------

quest_modcp_all
------------------------------
<html>
<head>
<title>{$mybb->settings['bbname']} - Alle Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
-----------------------------------

quest_modcp_all_bit
-----------------------------------
<table width="800px" align="center">
	<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Eingereicht von {$user} am {$datum}</td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td>
			<td class="trow1" colspan="2"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten} {$gruppe}</td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-hourglass-start"></i> Mit dieser Quest können <b>{$points}</b> Punkte gesammelt werden.  </td></tr>
{$options}
</table><br />
-------------------------------------

quest_modcp_done
------------------------------------
<html>
<head>
<title>{$mybb->settings['bbname']} - Alle Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
---------------------------

quest_modcp_done_bit
------------------------------
<table width="800px" align="center">
<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Erledigt von {$user} am {$datum}</td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td>
				<td class="trow1" colspan="2"><div style="text-align: justify">{$aufgabe}</div></td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten} <i class="fas fa-hourglass-start"></i> {$points}   </td></tr>
	<tr><td class="trow2" align="center">{$options}</td><td class="trow2" align="center"> {$post}</td></tr>
</table><br />
---------------------------

quest_modcp_groupquest
-----------------------------
<table width="800px" align="center">
<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Gruppenquest vom {$datum} ({$erledigt} / {$group})</td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td>
				<td class="trow1" colspan="2"><div style="text-align: justify">{$aufgabe}</div></td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten} <i class="fas fa-hourglass-start"></i> {$points}   </td></tr>
		<tr><td class="trow2" align="center">{$options}</td><td class="trow2" align="center"> {$post}</td></tr>
</table><br />
--------------------------

quest_modcp_newadd
-------------------------
<html>
<head>
<title>{$mybb->settings['bbname']} -Nicht angenommene Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
------------------------

quest_modcp_newadd_bit
-----------------------
<table width="800px" align="center">
	<tr><td class="trow1" align="center" colspan="2"><i class="fas fa-user"></i> Eingereicht von {$user} am {$datum}</td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td>
			<td class="trow1" colspan="2"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i> {$besonderheiten}</td></tr>
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-hourglass-start"></i> Mit dieser Quest können<b>{$points}</b> Punkte gesammelt werden.  </td></tr>
			<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-check"></i> {$take_one} <i class="fas fa-times"></i> {$take_none} </td></tr>
</table><br />
-----------------------------

quest_modcp_newquest
-----------------------------
<tr>
	<td class="trow1"><div style="text-align: center;"><i class="fas fa-clock"></i> Eingereicht am {$datum}</div>
		<div style="text-align: justify"><i class="fas fa-thumbtack"></i> 
{$aufgabe}</div>
		<div style="text-align: center;"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten} <i class="fas fa-hourglass"></i> {$points} Punkte</div>
	</td>
</tr>
-----------------------------

quest_options
-----------------------------
<tr><td class="trow2" align="center" colspan='2'><i class="fas fa-edit"></i> {$edit}  <i class="fas fa-trash-alt"></i> {$delete}</td></tr
--------------------------

quest_own
-------------------------
<html>
<head>
<title>{$mybb->settings['bbname']} -Die ausgewählten Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
-----------------------------

quest_own_add
-----------------------------
<html>
<head>
<title>{$mybb->settings['bbname']} -Alle eingereichten Quests</title>
{$headerinclude}
</head>
<body>
{$header}
<table width="100%" border="0" align="center">
<tr>
{$menu}
<td valign="top">
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
------------------------------

quest_own_add_bit
-------------------------------
<table width="800px">
		<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-clock"></i> Eingereicht am {$datum} </td></tr>
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td> <td class="trow1"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten} </td></tr>
	{$options}
</table><br />
----------------------------

quest_own_bit
---------------------------

<table width="800px">
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td> <td class="trow1"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten} <i class="fas fa-clock"></i> Angenommen am {$datum}</td></tr>
{$points}
		{$quest_back}
</table><br />
---------------------------

quest_own_edit
-----------------------------
<style>.infopop { position: fixed; top: 0; right: 0; bottom: 0; left: 0; background: hsla(0, 0%, 0%, 0.5); z-index: 1; opacity:0; -webkit-transition: .5s ease-in-out; -moz-transition: .5s ease-in-out; transition: .5s ease-in-out; pointer-events: none; } .infopop:target { opacity:1; pointer-events: auto; } .infopop > .pop {width: 300px; position: relative; margin: 10% auto; padding: 25px; z-index: 3; } .closepop { position: absolute; right: -5px; top:-5px; width: 100%; height: 100%; z-index: 2; }</style>

<div id="popinfo$row[quid]" class="infopop">
  <div class="pop"><form method="post" action=""><input type='hidden' value='{$row['quid']}' name='getquid'>
 <table width="50%" style="margin: auto;" class="tborder">
	<tr><td>
		<label id="aufgabe"><b>Questaufgabe</b></label></td>
		<td align="center">
	<textarea rows="3" cols="40" wrap="hard" name="aufgabe">{$row['aufgabe']}</textarea>
		</td></tr>
		<tr><td>
		<label id="aufgabe"><b>Örtlichkeit</b></label></td>
		<td align="center">
<input type="text" value="{$row['ort']}" name="ort" id="ort" class="textbox" /> 
		</td></tr>
	 		<tr><td>
		<label id="aufgabe"><b>Besonderheit</b></label></td>
		<td align="center">
 <input type="text" value="{$row['besonderheiten']}" name="besonderheiten" id="besonderheiten" class="textbox" />
		</td></tr>
			<tr><td>
		<label id="aufgabe"><b>Questpunkte</b></label></td>
		<td align="center">
<input type="number" name="points" id="points" class="textbox" value="{$row['points']}" />
		</td></tr>
<tr><td colspan='2' align='center'><input type="submit" name="edit" value="Quest bearbeiten" id="submit" class="button"></td></tr></table>
	  </form>
		</div><a href="#closepop" class="closepop"></a>
</div>

<a href="#popinfo$row[quid]">Quest editieren</a>
-------------------------------------

quest_postbit
-----------------------------------
<html>
<head>
<title>{$mybb->settings['bbname']} - Quest als erledigt markieren</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme['borderwidth']}" cellpadding="{$theme['tablespace']}" class="tborder">
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
</html>
----------------------------------------

quest_postbit_bit
-------------------------------------
<table width="800px">
		<tr><td class="trow2" align="center" width="20%"><i class="fas fa-thumbtack"></i> Quest</td> <td class="trow1"><div style="text-align: justify">{$aufgabe}</div></td></tr>
	<tr><td class="trow2" align="center" colspan="2"><i class="fas fa-map-pin"></i> {$ort}  <i class="fas fa-star"></i>{$besonderheiten}</td></tr>
{$points}
{$time}
	<tr><td colspan="2" align="center">
<form method="post" action="misc.php?action=quest_take" id="take_quest">
	<input value="{$row['auid']}" type="hidden" name="auid">
		<input value="{$row['gid']}" type="hidden" name="gid">
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
</table><br />
--------------------------------

quest_profil
-------------------------------
						<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
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
</table><br />
--------------------------------

quest_profil_checked
--------------------------------------
<tr><td valign="top" width="1"><input type="checkbox" class="checkbox" name="questoption" id="questoption" value="1" {$checkoption} /></td>
<td><span class="smalltext"><label for="questoption">Möchtest du die Questbox auf dem Index sehen?</label></span></td>
</tr>