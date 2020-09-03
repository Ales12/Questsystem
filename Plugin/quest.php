<?php
define("IN_MYBB", 1);;
//define("NO_ONLINE", 1); // Wenn Seite nicht in Wer ist online-Liste auftauchen soll

/*
 * Dies ist ein System von Ales (Alex), welche ein Quest system beinhaltet.
 * Ein großes Dankeschön an winterkind. und risuena, welche mir beide sehr geholfen haben, dass das gute Stück nun steht.
 */
error_reporting ( -1 );
ini_set ( 'display_errors', true );
require("global.php");
require_once MYBB_ROOT."inc/class_parser.php";
$parser = new postParser;
global  $db, $parser, $mybb, $templates, $page;

add_breadcrumb("Das Questsystem", "quest.php");

switch ($mybb->input['action']) {
    case "add_quest":
        add_breadcrumb("Quest einreichen");
        break;
    case "choice_quest":
        add_breadcrumb("Ein Quest auswählen");
        break;
    case "own_quests":
        add_breadcrumb("Deine ausgewählten Quests");
        break;
        case "own_add_quest":
        add_breadcrumb("Deine eingereichten Quests");
        break;
    case "modcp_quests_newadd":
        add_breadcrumb("Alle neu eingereichten Quests");
        break;
    case "modcp_quests_all":
        add_breadcrumb("Alle Quests im Pool");
        break;
    case "modcp_quests_done":
        add_breadcrumb("Alle erledigten Quests");
}


require_once MYBB_ROOT."inc/datahandlers/pm.php";
$pmhandler = new PMDataHandler();


if($mybb->settings['group_quest'] == 1) {

    $quest_group = "<tr><td class=\"trow1 smalltext\"><i class=\"fas fa-pencil-alt\"></i> <a href=\"quest.php?action=add_group_quest\">Gruppenquest einreichen</a></td></tr>";
}
//ModCP Navigation
if($mybb->usergroup['canmodcp'] == 1){
    eval("\$quest_moderation = \"" . $templates->get("quest_menu_moderation") . "\";");
}

if($mybb->settings)

// Willkommen Seite. Gäste haben keinen Zugriff auf das Questsystem
if(!$mybb->input['action'])
{
if ($mybb->usergroup['gid'] == '1') {

    error_no_permission();
} else {
    eval("\$menu = \"" . $templates->get("quest_menu") . "\";");
    eval("\$page = \"" . $templates->get("quest") . "\";"); // Hier wird das erstellte Template geladen
    output_page($page);
}
}


// Quest einreichen/einspeichern. Adresse: quest.php?action=add_quest
if($mybb->input['action'] == "add_quest") {

        //Quest eintragen
        if (isset($_POST['add_quest'])) {
            $uid = $mybb->user['uid'];
            $aufgabe = $db->escape_string($mybb->input['aufgabe']);
            $ort = $db->escape_string($mybb->input['ort']);
            $besonderheiten = $db->escape_string($mybb->input['besonderheiten']);
            $points = $mybb->input['points'];
            $datum = TIME_NOW;

            $new_record = array(
            "uid" => $db->escape_string($uid),
            "aufgabe" => $aufgabe,
            "ort" => $ort,
            "besonderheiten" => $besonderheiten,
            "points" => $db->escape_string($points),
            "datum" => $db->escape_string($datum)
            );

            $db->insert_query ("quest", $new_record);
            redirect ("quest.php?action=add_quest");

}
        eval("\$menu = \"" . $templates->get("quest_menu") . "\";");
        eval("\$page = \"" . $templates->get("quest_add") . "\";"); // Hier wird das erstellte Template geladen
        output_page($page);

}



    // Quest einreichen/einspeichern. Adresse: quest.php?action=add_quest
    if($mybb->input['action'] == "add_group_quest") {


        $select = $db->simple_select("usergroups", "gid,title", "gid IN ('".str_replace(',', '\',\'', $mybb->settings['group_quest_groups'])."')");

        while($row = $db->fetch_array($select)){
            $gid = $row['gid'];
            $title = $row['title'];
            $group .= "<option value='$gid'>$title</option>";
        }

        //Quest eintragen
        if (isset($_POST['add_quest_group'])) {
            $uid = $mybb->user['uid'];
            $gid = $db->escape_string($mybb->input['gid']);
            $aufgabe = $db->escape_string($mybb->input['aufgabe']);
            $ort = $db->escape_string($mybb->input['ort']);
            $besonderheiten = $db->escape_string($mybb->input['besonderheiten']);
            $points = $mybb->input['points'];
            $datum = TIME_NOW;

            $new_record = array(
                "uid" => $db->escape_string($uid),
                "gid" => $gid,
                "aufgabe" => $aufgabe,
                "ort" => $ort,
                "besonderheiten" => $besonderheiten,
                "points" => $db->escape_string($points),
                "datum" => $db->escape_string($datum)
            );

            $db->insert_query ("ausgquest", $new_record);
            redirect ("quest.php?action=add_group_quest");

        }
        eval("\$menu = \"" . $templates->get("quest_menu") . "\";");
        eval("\$page = \"" . $templates->get("quest_add_group") . "\";"); // Hier wird das erstellte Template geladen
        output_page($page);

    }



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


// Quest einreichen/einspeichern. Adresse: quest.php?action=choice_quest
if($mybb->input['action'] == "choice_quest") {

    global $parser;

    if($mybb->settings['all_quest'] == 1) {
        //Quest ausgeben
        $select = $db->query("SELECT *
    FROM " . TABLE_PREFIX . "quest q
    LEFT JOIN " . TABLE_PREFIX . "users u
    on q.uid = u.uid
        WHERE   q.admin = 1
     ORDER BY datum
    ");
    } else{
        //Quest ausgeben
        $select = $db->query("SELECT *
    FROM " . TABLE_PREFIX . "quest q
    LEFT JOIN " . TABLE_PREFIX . "users u
    on q.uid = u.uid
        WHERE   q.admin = 1
     ORDER BY rand() LIMIT 1
    ");
    }
$count_q = mysqli_num_rows ($select);

if($count_q == 0) {
    $quest_bit = "<div class='smalltext'><strong>Aktuell wurden noch keine (neuen) Quest(s) eingereicht.</strong></div>";
}else {


    while ($row = $db->fetch_array($select)) {
        $username = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
        $user = build_profile_link($username, $row['uid']);
        $accept_quest = "";


        //Quest annehmen

        $accept_quest = "<a href='quest.php?action=choice_quest&accept=$row[quid]'>Quest auswählen</a>";


        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";
        $uid = $mybb->user['uid'];

        $aufgabe = $parser->parse_message($row['aufgabe'], $options);

        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $datum = my_date("relative", $row['datum']);
        $points = $row['points'];


        eval("\$quest_bit .= \"" . $templates->get("quest_choice_bit") . "\";");
    }


    // Wenn Quest ausgewählt wurde, dann muss umgespeichert werden.

    $accept_quest = $mybb->get_input('accept');

    if ($accept_quest) {
        $quid = $accept_quest;
        $select = $db->query("SELECT *
           FROM " . TABLE_PREFIX . "quest
           WHERE quid = $quid
    ");

        $row = $db->fetch_array($select);

        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $datum = TIME_NOW;
        $points = $row['points'];
        $aufgabe = $row['aufgabe'];

        $new_record = array(
            "uid" => $db->escape_string($uid),
            "quid" => $db->escape_string($quid),
            "gid" => '0',
            "aufgabe" => $db->escape_string($aufgabe),
            "ort" => $db->escape_string($ort),
            "besonderheiten" => $db->escape_string($besonderheiten),
            "points" => $db->escape_string($points),
            "datum" => $db->escape_string($datum)
        );

        $db->insert_query("ausgquest", $new_record);
        $db->delete_query("quest", "quid ='$quid'");
        redirect("quest.php?action=choice_quest");
    }

}
    eval("\$menu = \"" . $templates->get("quest_menu") . "\";");
    eval("\$page = \"" . $templates->get("quest_choice") . "\";"); // Hier wird das erstellte Template geladen
    output_page($page);
}

// Die eigenen Quests. Adresse: quest.php?action=own_quests
if($mybb->input['action'] == "own_quests") {

    $uid = $mybb->user['uid'];

//alle nicht erledigten Quest

    $select = $db->query("SELECT *
    FROM ".TABLE_PREFIX."ausgquest
    WHERE uid = $uid
    and erledigt= 'nein'
    and gid = 0
    ORDER by datum
    ");
$count_q = mysqli_num_rows ($select);

if($count_q == 0) {
    $quest_own_bit  = "<div class='smalltext'><strong>Aktuell hast du keine offene(n) Quest(s).</strong></div>";
}else {

    while ($row = $db->fetch_array($select)) {


        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";
$datum = "";
        $datum = date('d.m.Y', $row['datum']);
        $aufgabe = $parser->parse_message($row['aufgabe'], $options);

        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $points = "	<tr><td class=\"trow2\" align=\"center\" colspan=\"2\"><i class=\"fas fa-hourglass-start\"></i> Mit der Quest kannst du <b>{$row['points']}</b> Punkte sammeln.</td></tr>";


        eval("\$quest_own_bit .= \"" . $templates->get("quest_own_bit") . "\";");
    }
}
//alle nicht erledigten Quest

    $gid = $mybb->user['usergroup'];

    $select = $db->query("SELECT *
    FROM ".TABLE_PREFIX."ausgquest
    WHERE uid = $uid
    and erledigt= 'nein'
    and gid = $gid
    ORDER by datum
    ");

    $count_g = mysqli_num_rows ($select);
if($count_g == 0) {
    $quest_group_bit  = "<div class='smalltext'><strong>Aktuell hast du keine offene Gruppenuest.</strong></div>";
}else {

    while ($row = $db->fetch_array($select)) {
        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";

        $datum = "";
        $datum = date('d.m.Y', $row['datum']);

        $aufgabe = $parser->parse_message($row['aufgabe'], $options);

        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $points = "	<tr><td class=\"trow2\" align=\"center\" colspan=\"2\"><i class=\"fas fa-hourglass-start\"></i> Mit der Quest kannst du <b>{$row['points']}</b> Punkte sammeln.</td></tr>";


        eval("\$quest_group_bit .= \"" . $templates->get("quest_own_bit") . "\";");
    }
}

// Alle Quests, welche schon erledigt wurden.
    $select = $db->query("SELECT *
    FROM ".TABLE_PREFIX."ausgquest
    WHERE uid = $uid
    AND erledigt = 'ja'
    ORDER by datum
    ");

$count_d = mysqli_num_rows ($select);
if($count_d == 0) {
    $quest_done_bit = "<div class='smalltext'><strong>Aktuell wurden keine Quest erledigt.</strong></div>";
}else {
    while ($row = $db->fetch_array($select)) {

        $datum = date('d.m.Y', $row['datum']);
        $time = "	<tr><td class=\"trow1\" colspan=\"2\" align=\"center\"><i class=\"fas fa-clock\"></i> Erledigt am {$datum}</td></tr>";


        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";
        $quest_back = "";
        $aufgabe = $parser->parse_message($row['aufgabe'], $options);


        $quest_back = "<a href=''></a>";
        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $points = "	<tr><td class=\"trow2\" align=\"center\" colspan=\"2\"><i class=\"fas fa-hourglass-start\"></i> Du hast <b>{$row['points']}</b> Punkte mit dieser Quest gesammelt.</td></tr>";

        eval("\$quest_done_bit .= \"" . $templates->get("quest_own_bit") . "\";");
    }
}
    $back = $mybb->get_input('back');


    //Quest wieder in den Auswahlpool schmeißen
    if($back){

            $aquid = $back;
        $select = $db->query("SELECT *
           FROM ".TABLE_PREFIX."ausgquest
           WHERE aquid = $aquid
    ");

        $row = $db->fetch_array($select);

        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $datum = TIME_NOW;
        $points = $row['points'];
        $aufgabe = $row['aufgabe'];
        $quid = $row['quid'];


        $new_record = array(
            "quid" => $db->escape_string($quid),
            "uid" => $db->escape_string($uid),
            "aufgabe" => $db->escape_string($aufgabe),
            "ort" => $db->escape_string($ort),
            "besonderheiten" => $db->escape_string($besonderheiten),
            "points" => $db->escape_string($points),
            "datum" => $db->escape_string($datum),
            "admin" => 1
        );

        $db->insert_query ("quest", $new_record);
        $db->delete_query ("ausgquest", "aquid ='$back'");
        redirect ("quest.php?action=own_quests");

    }

    eval("\$menu = \"" . $templates->get("quest_menu") . "\";");
    eval("\$page = \"" . $templates->get("quest_own") . "\";"); // Hier wird das erstellte Template geladen
    output_page($page);
}

// Quest einreichen/einspeichern. Adresse: quest.php?action=own_add_quest
if($mybb->input['action'] == "own_add_quest") {

    $uid = $mybb->user['uid'];


    //nicht angenommen
    $select = $db->query("SELECT *
    FROM ".TABLE_PREFIX."quest
    WHERE uid = $uid
    AND admin = 0
    ORDER BY datum
    ");

    $count= '';
$count = mysqli_num_rows ($select);
if($count == 0) {
    $quest_own_add_bit = "<div class='smalltext'><strong>Aktuell hast du keine neuen Quests eingereicht.</strong></div>";
}else {

    while ($row = $db->fetch_array($select)) {

        $edit = "";

        eval("\$edit = \"" . $templates->get("quest_own_edit") . "\";");
        $delete = " <a href='quest.php?action=own_add_quest&delete=$row[quid]'>Quest löschen</a>";
        eval("\$options = \"" . $templates->get("quest_options") . "\";");

        $datum = date('d.m.Y', $row['datum']);

        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";

        $aufgabe = $parser->parse_message($row['aufgabe'], $options);

        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $points = $row['points'];

        eval("\$quest_own_add_bit .= \"" . $templates->get("quest_own_add_bit") . "\";");
    }

}

    //angenommenen
    $select = $db->query("SELECT *
    FROM ".TABLE_PREFIX."quest
    WHERE uid = $uid
    AND admin = 1
    ORDER BY datum
    ");

$count= '';
$count = mysqli_num_rows ($select);
if($count == 0) {
    $quest_own_add_bit2 = "<div class='smalltext'><strong>Aktuell wurde noch keine Quest von dir angenommen.</strong></div>";
}else {

    while ($row = $db->fetch_array($select)) {

        $edit = "";
        $delete = "";
        eval("\$edit = \"" . $templates->get("quest_own_edit") . "\";");
        $delete = " <a href='quest.php?action=own_add_quest&delete=$row[quid]'>Quest löschen</a>";
        eval("\$options = \"" . $templates->get("quest_options") . "\";");

        $datum = date('d.m.Y', $row['datum']);
        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";

        $aufgabe = $parser->parse_message($row['aufgabe'], $options);

        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $points = $row['points'];

        eval("\$quest_own_add_bit2 .= \"" . $templates->get("quest_own_add_bit") . "\";");
    }
}
    //editieren

    if(isset($mybb->input['edit'])){
        $getquid = $mybb->input['getquid'];
        $aufgabe = $mybb->input['aufgabe'];
        $ort = $mybb->input['ort'];
        $besonderheiten = $mybb->input['besonderheiten'];

        $db->query("UPDATE ".TABLE_PREFIX."quest SET aufgabe = '".$aufgabe."', ort = '".$ort."', besonderheiten = '".$besonderheiten."' WHERE quid = '$getquid' ");
        redirect('quest.php?action=own_add_quest');

    }

    //löschen

    $delete = $mybb->get_input('delete');

    if($delete){
        $db->query("DELETE FROM ".TABLE_PREFIX."quest WHERE quid='$delete' LIMIT 1");
        redirect('quest.php?action=own_add_quest');
    }

    eval("\$menu = \"" . $templates->get("quest_menu") . "\";");
    eval("\$page = \"" . $templates->get("quest_own_add") . "\";"); // Hier wird das erstellte Template geladen
    output_page($page);
}

// Moderatorbereich


// Die eigenen Quests. Adresse: quest.php?action=modcp_quests_newadd
if($mybb->input['action'] == "modcp_quests_newadd") {

    $select = $db->query("SELECT *
    FROM ".TABLE_PREFIX."quest q
    LEFT JOIN ".TABLE_PREFIX."users u
    ON q.uid = u.uid
    WHERE admin = 0
    ORDER BY datum
    ");
$count= '';
$count = mysqli_num_rows ($select);
if($count == 0) {
    $quest_newadd_bit  = "<div class='smalltext'><strong>Keine Quests zum Kontrollieren offen.</strong></div>";
}else {
    $take_all = "<a href='quest.php?action=modcp_quests_newadd&take=all'>Alle Quests annehmen</a>";
    while ($row = $db->fetch_array($select)) {


        $take_one = "<a href='quest.php?action=modcp_quests_newadd&one=$row[quid]'>Quest annehmen</a>";
        $take_none = "<a href='quest.php?action=modcp_quests_newadd&none=$row[quid]'>Quest ablehnen</a>";

        $datum = date('d.m.Y', $row['datum']);
        $username = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
        $user = build_profile_link($username, $row['uid']);

        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";

        $aufgabe = $parser->parse_message($row['aufgabe'], $options);
        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $points = $row['points'];

        eval("\$quest_newadd_bit .= \"" . $templates->get("quest_modcp_newadd_bit") . "\";");
    }
}
    $all = $mybb->input['take'];
    $one = $mybb->get_input('one');
    $none = $mybb->get_input('none');

    //Alle Quests annehmen
    if($all){

        $select = $db->query("SELECT *
        FROM ".TABLE_PREFIX."quest 
        WHERE admin = 0
        ");

        $teamuid = $mybb->user['uid'];

        while($row = $db->fetch_array($select)){
            $user = $row['uid'];

            $pm_change = array(
                "subject" => "Deine Quest wurde angenommen",
                "message" => "Deine Quest wurde vom Team angenommen.",
                //to: wer muss die anfrage bestätigen
                "fromid" => $teamuid,
                //from: wer hat die anfrage gestellt
                "toid" => $user
            );
            // $pmhandler->admin_override = true;
            $pmhandler->set_data($pm_change);
            if(!$pmhandler->validate_pm())
                return false;
            else
            {
                $pmhandler->insert_pm();
            }

        }

        $db->query("UPDATE ".TABLE_PREFIX."quest SET admin = '1'");
        redirect('quest.php?action=modcp_quests_newadd');
    }

    //Eine Quest annehmen
    if($one){
        $quid = $one;
        $select = $db->query("SELECT *
        FROM ".TABLE_PREFIX."quest 
        WHERE quid = $quid
        ");
        $row = $db->fetch_array($select);

        $teamuid = $mybb->user['uid'];
        $user = $row['uid'];

        $pm_change = array(
            "subject" => "Deine Quest wurde angenommen",
            "message" => "Deine Quest wurde vom Team angenommen.",
            //to: wer muss die anfrage bestätigen
            "fromid" => $teamuid,
            //from: wer hat die anfrage gestellt
            "toid" => $user
        );
        // $pmhandler->admin_override = true;
        $pmhandler->set_data($pm_change);
        if(!$pmhandler->validate_pm())
            return false;
        else
        {
            $pmhandler->insert_pm();
        }

        $db->query("UPDATE ".TABLE_PREFIX."quest SET admin = '1' WHERE quid = '$quid'");
        redirect('quest.php?action=modcp_quests_newadd');
    }


    if($none){
        $quid = $none;
        $select = $db->query("SELECT *
        FROM ".TABLE_PREFIX."quest 
        WHERE quid = $quid
        ");
        $row = $db->fetch_array($select);

        $teamuid = $mybb->user['uid'];
        $user = $row['uid'];

        $pm_change = array(
            "subject" => "Deine Quest wurde abgelehnt",
            "message" => "Deine Quest wurde vom Team abgelehnt. <br />Bitte wende dich ans Team, was korrigiert werden muss, um die Quest angenommen werden kann.",
            //to: wer muss die anfrage bestätigen
            "fromid" => $teamuid,
            //from: wer hat die anfrage gestellt
            "toid" => $user
        );
        // $pmhandler->admin_override = true;
        $pmhandler->set_data($pm_change);
        if(!$pmhandler->validate_pm())
            return false;
        else
        {
            $pmhandler->insert_pm();
        }



        redirect('quest.php?action=modcp_quests_newadd');
    }


    eval("\$menu = \"" . $templates->get("quest_menu") . "\";");
    eval("\$page = \"" . $templates->get("quest_modcp_newadd") . "\";"); // Hier wird das erstellte Template geladen
    output_page($page);

}
// Die eigenen Quests. Adresse: quest.php?action=modcp_quests_newadd
if($mybb->input['action'] == "modcp_quests_all") {

    $select = $db->query("SELECT *
    FROM ".TABLE_PREFIX."quest q
    LEFT JOIN ".TABLE_PREFIX."users u
    ON q.uid = u.uid
    WHERE q.admin = 1
    ORDER BY datum
    ");

$count= '';
$count = mysqli_num_rows ($select);
if($count == 0) {
    $quest_all_bit = "<div class='smalltext'><strong>Im Pool befinden sich aktuell keine Quests.</strong></div>";
}else {
    while ($row = $db->fetch_array($select)) {

        //edit
        $edit = "";
        $delete = "";
        eval("\$edit = \"" . $templates->get("quest_own_edit") . "\";");
        $delete = " <a href='quest.php?action=modcp_quests_all&delete=$row[quid]'>Quest löschen</a>";
        eval("\$options = \"" . $templates->get("quest_options") . "\";");

        $datum = date('d.m.Y', $row['datum']);
        $username = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
        $user = build_profile_link($username, $row['uid']);

        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";

        $aufgabe = $parser->parse_message($row['aufgabe'], $options);
        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $points = $row['points'];

        eval("\$quest_all_bit .= \"" . $templates->get("quest_modcp_all_bit") . "\";");
    }
}
    $select = $db->query("SELECT *
    FROM ".TABLE_PREFIX."ausgquest aq
    LEFT JOIN ".TABLE_PREFIX."users u  
     ON aq.uid = u.uid
    LEFT JOIN ".TABLE_PREFIX."usergroups ug
 ON u.usergroup = ug.gid
    WHERE aq.admin = 0
    AND NOT aq.gid = 0
    ORDER BY datum
    ");

$count= '';
$count = mysqli_num_rows ($select);
if($count == 0) {
    $quest_all_group_bit = "<div class='smalltext'><strong>Im Pool befinden sich aktuell keine Gruppenquests.</strong></div>";
}else {
    while ($row = $db->fetch_array($select)) {

        //edit
        $edit = "";
        $delete = "";
        eval("\$edit = \"" . $templates->get("quest_own_edit") . "\";");
        $delete = " <a href='quest.php?action=modcp_quests_all&delete=$row[quid]'>Quest löschen</a>";
        eval("\$options = \"" . $templates->get("quest_options") . "\";");

        $datum = date('d.m.Y', $row['datum']);
        $username = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
        $user = build_profile_link($username, $row['uid']);

        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";
        $gruppe = "";

        $aufgabe = $parser->parse_message($row['aufgabe'], $options);
        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $points = $row['points'];
        $gruppe = "<i class=\"fas fa-users\"></i> " . $row['title'];

        eval("\$quest_all_group_bit .= \"" . $templates->get("quest_modcp_all_bit") . "\";");
    }
}
//editieren

if(isset($mybb->input['edit'])) {
    $getquid = $mybb->input['getquid'];
    $aufgabe = $mybb->input['aufgabe'];
    $ort = $mybb->input['ort'];
    $besonderheiten = $mybb->input['besonderheiten'];

    $db->query("UPDATE " . TABLE_PREFIX . "quest SET aufgabe = '" . $aufgabe . "', ort = '" . $ort . "', besonderheiten = '" . $besonderheiten . "' WHERE quid = '$getquid' ");
    redirect('quest.php?action=modcp_quests_all');
}

//löschen

    $delete = $mybb->get_input('delete');

if($delete){
    $db->query("DELETE FROM ".TABLE_PREFIX."quest WHERE quid='$delete' LIMIT 1");
    redirect('quest.php?action=modcp_quests_all');
}

    eval("\$menu = \"" . $templates->get("quest_menu") . "\";");
    eval("\$page = \"" . $templates->get("quest_modcp_all") . "\";"); // Hier wird das erstellte Template geladen
    output_page($page);

}

// Die erledigten Quests. Adresse: quest.php?action=modcp_quests_done
if($mybb->input['action'] == "modcp_quests_done") {

    //Userquest
    $select = $db->query("SELECT *
    FROM " . TABLE_PREFIX . "ausgquest q
    LEFT JOIN " . TABLE_PREFIX . "users u
    ON q.uid = u.uid
    WHERE erledigt = 'ja'
    AND admin = 0
    AND q.gid = 0
    ");
    $count = '';
    $count = mysqli_num_rows($select);
    if ($count == 0) {
        $quest_done_bit = "<div class='smalltext'><strong>Es wurden noch keine Quests erledigt.</strong></div>";
    } else {
        while ($row = $db->fetch_array($select)) {
            $datum = '';
            $user = '';
            $aufgabe = "";
            $ort = "";
            $besonderheiten = "";
            $points = "";
            $options = "";
            $post = "";

            $options = "<a href='quest.php?action=modcp_quests_done&check=$row[auid]'><i class=\"fas fa-check-square\"></i> Bestätigen</a> <a href='quest.php?action=modcp_quests_done&refuse=$row[auid]'>Ablehnen</a>";
            $datum = date('d.m.Y', $row['datum']);
            $username = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
            $user = build_profile_link($username, $row['uid']);
            $aufgabe = $parser->parse_message($row['aufgabe'], $options);
            $ort = $row['ort'];
            $besonderheiten = $row['besonderheiten'];
            $just_points = $row['points'];
            $points = $row['points'] . " Punkte";
            $uid = $row['uid'];
            $tid = $row['tid'];
            $pid = $row['pid'];
            $post = "<a href='showthread.php?tid=$tid&pid=$pid'>Zum Post</a>";
            eval("\$quest_done_bit .= \"" . $templates->get("quest_modcp_done_bit") . "\";");
        }
    }
    //Bestätigen

    $check = $mybb->get_input('check');


    if ($check) {
        $db->query("UPDATE " . TABLE_PREFIX . "ausgquest SET admin = '1' WHERE auid = '$check' ");

        if ($mybb->setting['housepoints'] == 1) {
            $db->query("UPDATE " . TABLE_PREFIX . "users SET questpoints =+ '$just_points', hp_points =+'$just_points'  WHERE uid = '$uid' ");
        } else {
            $db->query("UPDATE " . TABLE_PREFIX . "users SET questpoints =+ '$just_points' WHERE uid = '$uid' ");
        }


        $teamuid = $mybb->user['uid'];


        $pm_change = array(
            "subject" => "Deine erledigte Queste wurde bestätigt",
            "message" => "Deine erledigte Quest wurde vom Team bestätigt und deine Punkte hierfür Gutgeschrieben.",
            //to: wer muss die anfrage bestätigen
            "fromid" => $teamuid,
            //from: wer hat die anfrage gestellt
            "toid" => $uid
        );
        // $pmhandler->admin_override = true;
        $pmhandler->set_data($pm_change);
        if(!$pmhandler->validate_pm())
            return false;
        else
        {
            $pmhandler->insert_pm();
        }

        redirect('quest.php?action=modcp_quests_done');
    }

    //Ablehnen

    $refuse = $mybb->get_input('refuse');

    if ($refuse) {
        $db->query("UPDATE " . TABLE_PREFIX . "ausgquest SET erledigt = 'nein' WHERE auid = '$refuse' ");

        $teamuid = $mybb->user['uid'];


        $pm_change = array(
            "subject" => "Deine erledigte Queste wurde Abgelehnt",
            "message" => "Deine erledigte Quest wurde vom Team abgelehnt. Bitte schaue nochmal über die Beschreibung der Quest und passe deinen Post dahingehend ab.",
            //to: wer muss die anfrage bestätigen
            "fromid" => $teamuid,
            //from: wer hat die anfrage gestellt
            "toid" => $uid
        );
        // $pmhandler->admin_override = true;
        $pmhandler->set_data($pm_change);
        if(!$pmhandler->validate_pm())
            return false;
        else
        {
            $pmhandler->insert_pm();
        }
        redirect('quest.php?action=modcp_quests_done');
    }

//Gruppenquests pro User
    $select = $db->query("SELECT *
    FROM " . TABLE_PREFIX . "groupquest q
    LEFT JOIN " . TABLE_PREFIX . "users u
    ON q.uid = u.uid
    WHERE admin = 0
    AND not q.gid = 0
    ");

    $count = '';
    $count = mysqli_num_rows($select);
    if ($count == 0) {
        $quest_group_done_bit = "<div class='smalltext'><strong>Es wurden noch keine Gruppenquests von Usern erledigt.</strong></div>";
    } else {


        while ($row = $db->fetch_array($select)) {
            $datum = '';
            $user = '';
            $aufgabe = "";
            $ort = "";
            $besonderheiten = "";
            $points = "";
            $options = "";
$post = "";

            $options = "<a href='quest.php?action=modcp_quests_done&check_group=$row[auid]'><i class=\"fas fa-check-square\"></i> Bestätigen</a> <a href='quest.php?action=modcp_quests_done&refuse_group=$row[auid]'>Ablehnen</a>";
            $datum = date('d.m.Y', $row['datum']);
            $username = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
            $user = build_profile_link($username, $row['uid']);

            $aufgabe = $parser->parse_message($row['aufgabe'], $options);
            $ort = $row['ort'];
            $besonderheiten = $row['besonderheiten'];
            $just_points = $row['points'];
            $points = $row['points'] . " Punkte";
            $uid = $row['uid'];
            $tid = $row['tid'];
            $pid = $row['pid'];
            $post = "<a href='showthread.php?tid=$tid&pid=$pid'>Zum Post</a>";
            eval("\$quest_group_done_bit .= \"" . $templates->get("quest_modcp_done_bit") . "\";");
        }
    }
    //Bestätigen

    $check_group = $mybb->get_input('check_group');


    if ($check_group) {
        $db->query("UPDATE " . TABLE_PREFIX . "groupquest SET admin = '1' WHERE auid = '$check_group' ");
        if ($mybb->setting['housepoints'] == 1) {
            $db->query("UPDATE " . TABLE_PREFIX . "users SET questpoints =+ '$just_points', hp_points =+'$just_points'  WHERE uid = '$uid' ");
        } else {
            $db->query("UPDATE " . TABLE_PREFIX . "users SET questpoints =+ '$just_points' WHERE uid = '$uid' ");
        }

        $teamuid = $mybb->user['uid'];


        $pm_change = array(
            "subject" => "Deine erledigte Gruppenqueste wurde bestätigt",
            "message" => "Deine erledigte Gruppenquest wurde vom Team bestätigt und deine Punkte hierfür Gutgeschrieben.",
            //to: wer muss die anfrage bestätigen
            "fromid" => $teamuid,
            //from: wer hat die anfrage gestellt
            "toid" => $uid
        );
        // $pmhandler->admin_override = true;
        $pmhandler->set_data($pm_change);
        if(!$pmhandler->validate_pm())
            return false;
        else
        {
            $pmhandler->insert_pm();
        }

        redirect('quest.php?action=modcp_quests_done');
    }

    //Ablehnen

    $refuse_group = $mybb->get_input('refuse_group');

    if ($refuse_group) {
        $db->query("DELETE FROM ".TABLE_PREFIX."groupquest WHERE auid='$refuse_group' LIMIT 1");
        $teamuid = $mybb->user['uid'];


        $pm_change = array(
            "subject" => "Deine erledigte Gruppenqueste wurde Abgelehnt",
            "message" => "Deine erledigte Gruppenquest wurde vom Team abgelehnt. Bitte schaue nochmal über die Beschreibung der Quest und passe deinen Post dahingehend ab.",
            //to: wer muss die anfrage bestätigen
            "fromid" => $teamuid,
            //from: wer hat die anfrage gestellt
            "toid" => $uid
        );
        // $pmhandler->admin_override = true;
        $pmhandler->set_data($pm_change);
        if(!$pmhandler->validate_pm())
            return false;
        else
        {
            $pmhandler->insert_pm();
        }

        redirect('quest.php?action=modcp_quests_done');
    }


//Komplette Gruppenquest bestätigen

    $select = $db->query("SELECT *
FROM " . TABLE_PREFIX . "ausgquest aq
WHERE not aq.gid = 0
and admin = 0
");
    $group = 0;
    $erledigt = 0;

    $count = '';
    $count = mysqli_num_rows($select);
    if ($count == 0) {
        $quest_group_bit  = "<div class='smalltext'><strong>Es wurden noch keine Gruppenquest erledigt.</strong></div>";
    } else {


    while ($row = $db->fetch_array($select)) {
        $gid = $row['gid'];
        $auid = $row['auid'];

        $select1 = $db->query("SELECT *
        FROM " . TABLE_PREFIX . "users
        WHERE usergroup = $gid
        ");
        while ($user = $db->fetch_array($select1)) {
            $group++;
        }

        $select2 = $db->query("SELECT *
        FROM " . TABLE_PREFIX . "groupquest gq
        left join " . TABLE_PREFIX . "ausgquest aq
        on gq.auid = aq.auid
        WHERE gq.gid = $gid
        ");
        while ($quests = $db->fetch_array($select2)) {
            $erledigt++;
        }

        $datum = '';
        $user = '';
        $aufgabe = "";
        $ort = "";
        $besonderheiten = "";
        $points = "";
        $options = "";


        $options = "<a href='quest.php?action=modcp_quests_done&check_all=$row[auid]'><i class=\"fas fa-check-square\"></i> Bestätigen</a>";
        $datum = date('d.m.Y', $row['datum']);
        $username = format_name($row['username'], $row['usergroup'], $row['displaygroup']);
        $user = build_profile_link($username, $row['uid']);

        $aufgabe = $parser->parse_message($row['aufgabe'], $options);
        $ort = $row['ort'];
        $besonderheiten = $row['besonderheiten'];
        $just_points = $row['points'];
        $points = $row['points'] . " Punkte";
        $uid = $row['uid'];
        eval("\$quest_group_bit .= \"" . $templates->get("quest_modcp_groupquest") . "\";");
    }
}
    //Bestätigen

    $check_all = $mybb->get_input('check_all');


    if($check_all){
        $db->query("UPDATE " . TABLE_PREFIX . "ausgquest SET erledigt = 'ja', admin = '1' WHERE auid = '$check_all' ");
        redirect('quest.php?action=modcp_quests_done');
    }


    eval("\$menu = \"" . $templates->get("quest_menu") . "\";");
    eval("\$page = \"" . $templates->get("quest_modcp_done") . "\";"); // Hier wird das erstellte Template geladen
    output_page($page);
}



?>


