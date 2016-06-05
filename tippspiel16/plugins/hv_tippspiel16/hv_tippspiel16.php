<?php
/*
 * Plugin Name: Tippspiel EURO 2016
 * Plugin URI: http://www.havogt.de
 * Description: Tippspiel for the UEFA EURO 2016
 * Version: 0.1
 * Author Hannes Vogt
 * Author URI: http://www.havogt.de
 * License: GNU General Public License v2 or later
 * Text Domain: hv-tippspiel16
 */

 
 require_once( "hv_tippspiel16_variables.php" );
 
 function hv_tippspiel16_register_style()
 {
   wp_register_style('hv_tippspiel16_stylesheet', plugins_url('hv_tippspiel16.css', __FILE__));
   wp_enqueue_style('hv_tippspiel16_stylesheet');
 }
 
 function hv_tippspiel16_get_table_teams()
 {
   global $wpdb;
   return $wpdb->prefix.'_hv_tippspiel16_teams';
 }
 
 function hv_tippspiel16_get_table_matches()
 {
   global $wpdb; 
   return $wpdb->prefix.'_hv_tippspiel16_matches';
 }
 
 function hv_tippspiel16_get_table_tipps()
 {
   global $wpdb;
   return $wpdb->prefix.'_hv_tippspiel16_tipps';
 }
 
 function hv_tippspiel16_get_table_users()
 {
   global $wpdb;
   return $wpdb->prefix.'_hv_tippspiel16_users';
 }
 
 function hv_tippspiel16_get_table_clubs()
 {
   global $wpdb;
   return $wpdb->prefix.'_hv_tippspiel16_clubs';
 }
 
 function hv_tippspiel16_get_table_torschuetzen()
 {
   global $wpdb;
   return $wpdb->prefix.'_hv_tippspiel16_torschuetzen';
 }
 
 function hv_tippspiel16_get_team_name( $teamid )
 {
   global $wpdb;
   return $wpdb->get_var( "SELECT Name FROM ".hv_tippspiel16_get_table_teams()." WHERE TeamID = ".$teamid );
 }
 
 function hv_tippspiel16_get_torschuetze_name( $id )
 {
   global $wpdb;
   return $wpdb->get_var( "SELECT Name FROM ".hv_tippspiel16_get_table_torschuetzen()." WHERE ID = ".$id );
 }
 
 function hv_tippspiel16_get_club_name( $id )
 {
   global $wpdb;
   return $wpdb->get_var( "SELECT Name FROM ".hv_tippspiel16_get_table_clubs()." WHERE ClubID = ".$id );
 }
 
 function hv_tippspiel16_get_profile( $id )
 {
   global $wpdb;
   return $wpdb->get_row( "SELECT * FROM ".hv_tippspiel16_get_table_users()." WHERE ID = ".$id );
 }
 
 function hv_tippspiel16_is_started()
 {
   global $wpdb;
   global $hv_tippspiel16_changetime;
   $firstmatch_time = $wpdb->get_var( "SELECT Datum FROM ".hv_tippspiel16_get_table_matches()." ORDER BY Datum LIMIT 0,1");
   return (time() + $hv_tippspiel16_changetime > $firstmatch_time );
 }
 
 function hv_tippspiel16_init( $content )
 {
    if( isset( $_GET['activate'] ) && $_GET['activate'] == 'true' )
    {
      $sql_teams = str_replace(
          "HV_TIPPSPIEL16_TEAMS",
          hv_tippspiel16_get_table_teams(),
          file_get_contents( plugin_dir_path( __FILE__ ).'sql/hv_tippspiel16_teams.sql' )
        );
      $sql_matches = str_replace(
          "HV_TIPPSPIEL16_MATCHES",
          hv_tippspiel16_get_table_matches(),
          file_get_contents( plugin_dir_path( __FILE__ ).'sql/hv_tippspiel16_matches.sql' )
      );
      
      $sql_tipps = str_replace(
          "HV_TIPPSPIEL16_TIPPS",
          hv_tippspiel16_get_table_tipps(),
          file_get_contents( plugin_dir_path( __FILE__ ).'sql/hv_tippspiel16_tipps.sql' )
      );

      $sql_users = str_replace(
          "HV_TIPPSPIEL16_USERS",
          hv_tippspiel16_get_table_users(),
          file_get_contents( plugin_dir_path( __FILE__ ).'sql/hv_tippspiel16_users.sql' )
      );
      
      $sql_clubs = str_replace(
          "HV_TIPPSPIEL16_CLUBS",
          hv_tippspiel16_get_table_clubs(),
          file_get_contents( plugin_dir_path( __FILE__ ).'sql/hv_tippspiel16_clubs.sql' )
      );
      
      $sql_torschuetzen = str_replace(
          "HV_TIPPSPIEL16_TORSCHUETZEN",
          hv_tippspiel16_get_table_torschuetzen(),
          file_get_contents( plugin_dir_path( __FILE__ ).'sql/hv_tippspiel16_torschuetzen.sql' )
      );   
      
      require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
      dbDelta( $sql_teams );
      dbDelta( $sql_matches );
      dbDelta( $sql_tipps );
      dbDelta( $sql_users );
      dbDelta( $sql_clubs );
      dbDelta( $sql_torschuetzen );
    }
 }
 
 function hv_tippspiel16_calculate_match( $tipp1, $tipp2, $goal1, $goal2 )
 {
   global $hv_tippspiel16_points_exact;
   global $hv_tippspiel16_points_diff;
   global $hv_tippspiel16_points_tendency;
   if( ($tipp1 == $goal1) && ($tipp2 == $goal2) ) $result = $hv_tippspiel16_points_exact;
   elseif( ($tipp1 - $tipp2 ) == ($goal1 - $goal2 ) ) $result =  $hv_tippspiel16_points_diff;
   elseif( ( ($tipp1 > $tipp2 ) && ($goal1 > $goal2 ) ) || (($tipp1 < $tipp2 ) && ($goal1 < $goal2 ) ) ) $result = $hv_tippspiel16_points_tendency;
   else $result = 0;
   return $result;
 }

 function hv_tippspiel16_get_cupwinner_points( $id )
 {
   global $hv_tippspiel16_cup_winner_id;
   global $hv_tippspiel16_points_for_cup;
   if( $hv_tippspiel16_cup_winner_id == $id ) return $hv_tippspiel16_points_for_cup;
   else return 0;
 }
 
 function hv_tippspiel16_get_torschuetze_points( $id )
 {
   global $hv_tippspiel16_torschuetze_id;
   global $hv_tippspiel16_points_for_torschuetze;
   if( $hv_tippspiel16_torschuetze_id == $id ) return $hv_tippspiel16_points_for_torschuetze;
   else return 0;
 }
 
 function hv_tippspiel16_get_matches()
 {
   global $wpdb;
   $temp = $wpdb->get_results( "SELECT * FROM ".hv_tippspiel16_get_table_matches(). " ORDER BY Datum" );
   foreach( $temp as $t )
   {
     $matches[$t->ID] = $t;
   }
   return $matches;
 }
 
 function hv_tippspiel16_get_teams()
 {
   global $wpdb;
   $temp = $wpdb->get_results( "SELECT * FROM ".hv_tippspiel16_get_table_teams() );
   foreach( $temp as $t )
   {
     $team[$t->TeamID]=$t;
   }
   return $team;
 }
 
 /**
  * From http://php.net/manual/de/function.sort.php
  */
 function array_sort($array, $on, $order=SORT_ASC)
 {
     $new_array = array();
     $sortable_array = array();
 
     if (count($array) > 0) {
         foreach ($array as $k => $v) {
             if (is_array($v)) {
                 foreach ($v as $k2 => $v2) {
                     if ($k2 == $on) {
                         $sortable_array[$k] = $v2;
                     }
                 }
             } else {
                 $sortable_array[$k] = $v;
             }
         }
 
         switch ($order) {
             case SORT_ASC:
                 asort($sortable_array);
             break;
             case SORT_DESC:
                 arsort($sortable_array);
             break;
         }
 
         foreach ($sortable_array as $k => $v) {
             $new_array[$k] = $array[$k];
         }
     }
 
     return $new_array;
 }
 
 function hv_tippspiel16_calculate_user( $userid, $matches )
 {
   global $wpdb;
   $u = get_userdata( $userid );
   $profile = $wpdb->get_row( "SELECT * FROM ".hv_tippspiel16_get_table_users()." WHERE ID = ".$userid );
   if( count( $profile ) > 0 )
   {
     $profileClubID = $profile->ClubID;
     $profileWeltmeisterID = $profile->WeltmeisterID;
     $profileTorschuetzeID = $profile->TorschuetzeID;
   }
   else
   {
     $profileClubID = 0;
     $profileWeltmeisterID = 0;
     $profileTorschuetzeID = 0;
   }
   
   
   $teilnehmer["ID"] = $u->ID;
   $teilnehmer["Name"] = $u->display_name;
   $teilnehmer["ClubID"] = $profileClubID;
   $teilnehmer["WeltmeisterID"] = $profileWeltmeisterID;
   $teilnehmer["TorschuetzeID"] = $profileTorschuetzeID;
   
   $tipps = $wpdb->get_results( "SELECT * FROM ".hv_tippspiel16_get_table_tipps()." WHERE UserID = ".$u->ID );
  
   $teilnehmer["points"] = 0;
   $teilnehmer["points"] += hv_tippspiel16_get_cupwinner_points( $teilnehmer["WeltmeisterID"] );
   $teilnehmer["points"] += hv_tippspiel16_get_torschuetze_points( $teilnehmer["TorschuetzeID"] );
   if( count( $tipps ) > 0 )
   {
     foreach( $tipps as $t )
     {
       $teilnehmer["tipp"][$t->MatchID] = $t;
       if( ($matches[$t->MatchID]->Goals1 != -1) && ($matches[$t->MatchID]->Goals2 != -1) )
       {
         $teilnehmer["points"] += hv_tippspiel16_calculate_match( $t->Goals1, $t->Goals2, $matches[$t->MatchID]->Goals1, $matches[$t->MatchID]->Goals2 );
       }
     }
   }
   return $teilnehmer;
 }
 
 function hv_tippspiel16_sort_and_make_rank( $array )
 {
   $array = array_sort( $array, 'points', SORT_DESC );
   $curpoints = PHP_INT_MAX;
   $currank = 1;
   $currank_display = $currank;
   foreach( $array as $key => $t )
   {
     if( $t["points"] < $curpoints )
     {
       $curpoints = $t["points"];
       $currank_display = $currank;
     }
     $array[$key]["rank"] = $currank_display;
     $currank++;
   }
   return $array;
 }
 
 function hv_tippspiel16_calculate_users( $clubid="all" )
 {
   global $wpdb;
   if( $clubid == "" ) $clubid = "all";
  
   $matches = hv_tippspiel16_get_matches();
   
   $tmp_users = get_users( 'role=subscriber' );
   foreach( $tmp_users as $u )
   {
    $profile = $wpdb->get_row( "SELECT * FROM ".hv_tippspiel16_get_table_users()." WHERE ID = ".$u->ID );
    if( $clubid == "all" || (count($profile) > 0 && $clubid == $profile->ClubID ) )
    {
      $teilnehmer[$u->ID] = hv_tippspiel16_calculate_user( $u->ID, $matches );
    }
   }
   return hv_tippspiel16_sort_and_make_rank( $teilnehmer );
 }
 
 function hv_tippspiel16_calculate_clubs()
 {
   $users = hv_tippspiel16_calculate_users();
   
   foreach( $users as $u )
   {
     if( $u["ClubID"] > 0 )
     {
       if( !isset( $clubs[$u["ClubID"]] ) )
       {
         $clubs[$u["ClubID"]]["points"] = 0;
         $clubs[$u["ClubID"]]["count"] = 0;
         $clubs[$u["ClubID"]]["Name"] = hv_tippspiel16_get_club_name( $u["ClubID"] );
       }
       $clubs[$u["ClubID"]]["points"] += $u["points"];
       $clubs[$u["ClubID"]]["count"]++;
     }
   }
   
   foreach( $clubs as $key => $c )
   {
     $clubs[$key]["points"] = $c["points"] / $c["count"];
   }
   
   return hv_tippspiel16_sort_and_make_rank( $clubs );
 }
 
 function groupid2name( $id )
 {
   $name[8] = "1/8";
   $name[4] = "1/4";
   $name[2] = "1/2";
   $name[3] = "Platz 3";
   $name[1] = "Platz 1";
   if( isset( $name[$id] ) ) return $name[$id];
   else return $id;
 }
 
 
 function hv_tippspiel16_tabelle_user( $userid )
 {
   global $hv_tippspiel16_club_label;
   global $hv_tippspiel16_changetime;
   
   $matches = hv_tippspiel16_get_matches();
   $team = hv_tippspiel16_get_teams();
   $user = hv_tippspiel16_calculate_user( $userid, $matches );
   
   
   
   $result = "";
   $result .= "<h3>".$user["Name"]."</h3>";
   if( hv_tippspiel16_is_started() )
   {
     $result .= "<p>Europameister: ".hv_tippspiel16_get_team_name( $user["WeltmeisterID"] );
     if( hv_tippspiel16_get_cupwinner_points( $user["WeltmeisterID"] ) > 0 )
     {
       $result .= " + ".hv_tippspiel16_get_cupwinner_points( $user["WeltmeisterID"] )." Punkte";
     }
     $result .= "</br>";
     $result .= "Torsch&uuml;tzenk&ouml;nig: ".hv_tippspiel16_get_torschuetze_name( $user["TorschuetzeID"] );
     if( hv_tippspiel16_get_torschuetze_points( $user["TorschuetzeID"] ) > 0 )
     {
       $result .= " + ".hv_tippspiel16_get_torschuetze_points( $user["TorschuetzeID"] )." Punkte";
     }
     $result .= "</p>";
   }
   
   $result .= "<table class='tippspiel16'>";
   foreach( $matches as $m )
   {
     $result .= "<tr class='zebra'>";
     $result .= "<td class='hv_tippspiel_teamlong'>".$m->Gruppe."</td>";
     $result .= "<td class='hv_tippspiel_datelong hv_tippspiel_tipp_date'>".hv_tippspiel16_get_datestring( $m->Datum )."</td>";
     
     $result .= "<td class='hv_tippspiel_team1'>";
     $result .= "<div class='hv_tippspiel_teamlong'>".$team[$m->Team1]->Name."</div>";
     $result .= "<div class='hv_tippspiel_teamshort'>".$team[$m->Team1]->NameShort."</div>";
     $result .= "</td>";
     if( ( time()+$hv_tippspiel16_changetime > $m->Datum ) && isset( $user["tipp"][$m->ID]->Goals1 ) && isset( $user["tipp"][$m->ID]->Goals1 ) )
     {
       $result .= "<td style='width:6ch;text-align:center;'>".$user["tipp"][$m->ID]->Goals1.":".$user["tipp"][$m->ID]->Goals2."</td>";
     }
     else
     {
       $result .= "<td style='width:6ch;text-align:center;'></td>";
     }
     $result .= "<td class='hv_tippspiel_team2'>";
     $result .= "<div class='hv_tippspiel_teamlong'>".$team[$m->Team2]->Name."</div>";
     $result .= "<div class='hv_tippspiel_teamshort'>".$team[$m->Team2]->NameShort."</div>";
     $result .= "</td>";
     $result .= "<td style='width:2ch'>";
     if( isset( $user["tipp"][$m->ID]->Goals1 ) && isset( $user["tipp"][$m->ID]->Goals2 ) && ( $m->Goals1 > -1 ) && ( $m->Goals2 > -1 )  )
     {
       $result .= hv_tippspiel16_calculate_match( $user["tipp"][$m->ID]->Goals1, $user["tipp"][$m->ID]->Goals2, $m->Goals1, $m->Goals2  );
     }
     $result .= "</td>";
     
     if( ( $m->Goals1 > -1 ) && ( $m->Goals2 > -1 ) )
     {
       $result .= "<td style='width:6ch;text-align:center;'>".$m->Goals1.":".$m->Goals2."</td>";
     }
     else
     {
       $result .= "<td style='width:6ch;text-align:center;'></td>";
     }
     
     $result .= "</tr>";
}
   
   $result .= "</table>";
   
   return $result;
 }
 
 function hv_tippspiel16_tabelle( $clubid = 'all' )
 {
   global $hv_tippspiel16_club_label;
   $users = hv_tippspiel16_calculate_users( $clubid );
   
   $result = "";
   
   if( $clubid != 'all' )
   {
     $result .= "<h3>".$hv_tippspiel16_club_label." ".hv_tippspiel16_get_club_name( $clubid )."</h3>";
   }
   
   $result .= "<table class='tippspiel16'><thead class='tippspiel16'><tr><th class='hv_tippspiel_tab_rank'></th><th>Name</th><th>".$hv_tippspiel16_club_label."</th><th class='hv_tippspiel_tab_points'>Punkte</th></tr></thead><tbody>";
   
   foreach( $users as $u )
   {
     $userlink = array(
        'show' => 'user',
        'userid' => $u["ID"]
        );
     $clublink = array(
        'show' => 'club',
        'clubid' => $u["ClubID"]
        );
     
     $result .= "<tr class='zebra'>";
     $result .= "<td class='hv_tippspiel_tab_rank'>".$u["rank"]."</td>";
     $result .= "<td><a href='".add_query_arg( $userlink, get_page_link() )."'>".$u["Name"]."</a></td>";
     $result .= "<td>";
     if( $u["ClubID"] > 0 )
     {
       $result .= "<a href='".add_query_arg( $clublink, get_page_link() )."'>";
       $result .= hv_tippspiel16_get_club_name( $u["ClubID"] )."</a>";
     }
     $result .= "</td>";
     $result .= "<td class='hv_tippspiel_tab_points'>".$u["points"]."</td>";
     $result .= "</tr>";
   }
   
   $result .= "</tbody></table>";
   
   return $result;
 }
 
 function hv_tippspiel16_add_clubtabelle()
 {
   global $hv_tippspiel16_club_label;
   $clubs = hv_tippspiel16_calculate_clubs();
   
   $result = "";
   
   $result .= "<table class='tippspiel16'><thead class='tippspiel16'><tr><th class='hv_tippspiel_tab_rank'></th><th>Name</th><th>Mitglieder</th><th class='hv_tippspiel_tab_points'>Punkte</th></tr></thead><tbody>";
   
   foreach( $clubs as $c )
   {
     $result .= "<tr class='zebra'>";
     $result .= "<td class='hv_tippspiel_tab_rank'>".$c["rank"]."</td>";
     $result .= "<td>".$c["Name"]."</td>";
     $result .= "<td>".$c["count"]."</td>";
     $result .= "<td class='hv_tippspiel_tab_points'>".round($c["points"])."</td>";
     $result .= "</tr>";
   }
   
   $result .= "</tbody></table>";
   
   return $result;
 }
 
 function hv_tippspiel16_add_tabelle( $atts, $content = null )
 {
   switch( $_GET['show'] )
   {
     case 'user':
       return hv_tippspiel16_tabelle_user( $_GET['userid'] );
       break;
     case 'club':
       return hv_tippspiel16_tabelle( $_GET['clubid'] );
       break;
     default :
       return hv_tippspiel16_tabelle();
       break;
   }
 }
 
 function hv_tippspiel16_is_player( $user )
 {
   return user_can( $user, "subscriber" );
 }
 
 function hv_tippspiel16_get_datestring( $time )
 {
   return date("d.m. H:i", $time + get_option( 'gmt_offset' ) * 3600);
 }
 
 function hv_tippspiel16_make_goal_options( $currentTippGoals )
 {
   $result = "<option value='-1' ";
   if( !($currentTippGoals >= 0 ) ) $result .= "selected='selected'";
   $result .= ">-</option>";
   
			for( $i = 0; $i <= 12; $i++ )
   {
					$result .= "<option ";
     if( $currentTippGoals == $i ) $result .= "selected='selected'";
     $result .= "value='".$i."'>".$i."</option>";
   }
   return $result;
 }
 
 function hv_tippspiel16_add_tippform( $atts, $content = null )
 {
   global $hv_tippspiel16_changetime;
   if( !is_user_logged_in() )
   {
     $result = "Bitte <a href='".wp_login_url( get_permalink() )."' title='Login'>einloggen</a> um einen Tipp abzugeben!";
   }
   else
   {
     $current_user = wp_get_current_user();
     if( hv_tippspiel16_is_player( $current_user ) )
     {
       global $wpdb;
       $query = "SELECT * FROM ".hv_tippspiel16_get_table_matches()." WHERE Datum > ".(time()+$hv_tippspiel16_changetime)." ORDER BY DATUM";
       $temp = $wpdb->get_results($query);
       foreach( $temp as $t )
       {
         $matches[$t->ID]=$t;
       }
       unset($temp);
       $temp = $wpdb->get_results( "SELECT * FROM ".hv_tippspiel16_get_table_teams() );
       foreach( $temp as $t )
       {
         $team[$t->TeamID]=$t;
       }
       unset($temp);
       
       if( $_REQUEST["request"] == "submit" )
       {
         $profile_is_submitted = true;
         if( $_REQUEST["tippspiel16_torschuetze"] < 0 )
         {
           if( strlen( $_REQUEST["tippspiel16_torschuetze_new"] ) > 1 )
           {
             $wpdb->insert( hv_tippspiel16_get_table_torschuetzen(),
                     array(
                    'Name' => $_REQUEST["tippspiel16_torschuetze_new"]
                     ) );
             $newtorschuetzeid = $wpdb->insert_id;
           }
           else $newtorschuetzeid = 0;
         }
         else
         {
           $newtorschuetzeid = $_REQUEST["tippspiel16_torschuetze"];
         }
         
         $oldprofile = hv_tippspiel16_get_profile( $current_user->ID );
         if( !hv_tippspiel16_is_started() )
         {
           if( count( $oldprofile) > 0 )
           {
             $wpdb->update( hv_tippspiel16_get_table_users(),
                    array(
                   'WeltmeisterID' => $_REQUEST["tippspiel16_meister"],
                   'TorschuetzeID' => $newtorschuetzeid,
                    ),
                    array( 'ID' => $current_user->ID ) );
           }
           else
           {
             $wpdb->insert( hv_tippspiel16_get_table_users(),
                    array(
                   'ID' => $current_user->ID,
                   'WeltmeisterID' => $_REQUEST["tippspiel16_meister"],
                   'TorschuetzeID' => $newtorschuetzeid,
                   'ClubID' => 0
                    ) );
           }
         }
         
         foreach( $matches as $m )
         {
           if( $m->Datum > time()+$hv_tippspiel16_changetime )
           {
             $tippID = $wpdb->get_var( "SELECT ID FROM ".hv_tippspiel16_get_table_tipps()." WHERE UserID = ".$current_user->ID." AND MatchID = ".$m->ID );
             if( isset( $tippID ) && $tippID > 0 )
             {
               $wpdb->update( hv_tippspiel16_get_table_tipps(),
                   array(
                   'Goals1' => $_REQUEST["tipp"][$m->ID][1],
                   'Goals2' => $_REQUEST["tipp"][$m->ID][2]
                    ),
                   array( 'ID' => $tippID ) );
             }
             else
             {
               if( ($_REQUEST["tipp"][$m->ID][1] != -1) && ($_REQUEST["tipp"][$m->ID][2] != -1))
               {
                 $wpdb->insert( hv_tippspiel16_get_table_tipps(),
                     array(
                    'UserID' => $current_user->ID,
                    'MatchID' => $m->ID,
                    'Goals1' => $_REQUEST["tipp"][$m->ID][1],
                    'Goals2' => $_REQUEST["tipp"][$m->ID][2]
                     ) );
               }
             }
           }
         }
       }
       
       $torschuetzen = $wpdb->get_results( "SELECT * FROM ".hv_tippspiel16_get_table_torschuetzen() );
       $currentProfile = hv_tippspiel16_get_profile( $current_user->ID );
       $currentMeister = 0;
       $currentTorschuetze = 0;
       if( count($currentProfile) > 0 )
       {
         $currentMeister = $currentProfile->WeltmeisterID;
         $currentTorschuetze = $currentProfile->TorschuetzeID;
       }
       
       $result  = "<form name='form1' method='post' action='".get_permalink()."'>";
       $result .= "<input type='hidden' name='action' value='tipp' />";
       
       $result .= "<table class='tippspiel16'>";
              
       $result .= "<tr>";
       $result .= "<td>Europameister</td>";
       $result .= "<td>";
       if( $profile_is_submitted  || hv_tippspiel16_is_started()  )
       {
         if( $currentMeister > 0 )
           $result .= hv_tippspiel16_get_team_name( $currentMeister);
         else
           $result .= "-";
       }
       else
       {
         $result .= "<select style='width:100%' name='tippspiel16_meister'>";
         $result .= "<option value='0'>-</option>";
         foreach( $team as $t )
         {
           $result .= "<option value='".$t->TeamID."'";
           if( $currentMeister == $t->TeamID )
             $result .= " selected='selected'";
           $result .= ">".$t->Name."</option>";
         }
         $result .= "</select>";
       }
       $result .= "</td>";
       $result .= "<td></td>";
       $result .= "</tr>";
       
       $result .= "<tr>";
       $result .= "<td>Torsch&uuml;tzenk&ouml;nig</td>";
       $result .= "<td>";
       if( $profile_is_submitted || hv_tippspiel16_is_started() )
       {
        if( $currentTorschuetze > 0 )
           $result .= hv_tippspiel16_get_torschuetze_name( $currentTorschuetze);
         else
           $result .= "-";
       }
       else
       {
         $result .= "<select style='width:100%' name='tippspiel16_torschuetze'>";
         $result .= "<option value='0'>-</option>";
         $result .= "<option value='-1'>Hinzufügen:</option>";
         foreach( $torschuetzen as $t )
         {
           $result .= "<option value='".$t->ID."'";
           if( $currentTorschuetze == $t->ID )
             $result .= " selected='selected'";
           $result .= ">".$t->Name."</option>";
         }
         $result .= "</select>";
       }
       $result .= "</td>";
       $result .= "<td>";
       if( !$profile_is_submitted && !hv_tippspiel16_is_started() ) $result .= "<input type='text' name='tippspiel16_torschuetze_new' />";
       $result .= "</td>";
       $result .= "</tr></table>";
       
       $result .= "<table class='tippspiel16'>";
       foreach( $matches as $m )
       {
         $currentTipp = $wpdb->get_row( "SELECT * FROM ".hv_tippspiel16_get_table_tipps()." WHERE UserID = ".$current_user->ID." AND MatchID = ".$m->ID );
         if( count( $currentTipp ) > 0 )
         {
           $currentTippGoals1 = $currentTipp->Goals1;
           $currentTippGoals2 = $currentTipp->Goals2;
         }
         else
         {
           $currentTippGoals1 = -1;
           $currentTippGoals2 = -1;
         }
         
         $result .= "<tr class='zebra'>";
         
         $result .= "<td class='hv_tippspiel_teamlong'>".$m->Gruppe."</td>";
         $result .= "<td class='hv_tippspiel_datelong hv_tippspiel_tipp_date'>".hv_tippspiel16_get_datestring( $m->Datum )."</td>";
         
         $result .= "<td class='hv_tippspiel_team1'>";
         $result .= "<div class='hv_tippspiel_teamlong'>".$team[$m->Team1]->Name."</div>";
         $result .= "<div class='hv_tippspiel_teamshort'>".$team[$m->Team1]->NameShort."</div>";
         $result .= "</td>";
         

         $result .= "<td class='hv_tippspiel_tipp_option'>";
         if( $_REQUEST["request"] != "submit" )
         {
           $result .= "<select name='tipp[".$m->ID."][1].'>";
           $result .= hv_tippspiel16_make_goal_options( $currentTippGoals1 );
           $result .= "</select>";
           $result .= ":";
           $result .= "<select name='tipp[".$m->ID."][2].'>";
           $result .= hv_tippspiel16_make_goal_options( $currentTippGoals2 );
           $result .= "</select>";
           
         }
         else
         {
           if( count($currentTipp) > 0 )
           {
             $result .= $currentTipp->Goals1.":".$currentTipp->Goals2;
           }
         }
         $result .= "</td>";
           
         $result .= "<td class='hv_tippspiel_team2'>";
         $result .= "<div class='hv_tippspiel_teamlong'>".$team[$m->Team2]->Name."</div>";
         $result .= "<div class='hv_tippspiel_teamshort'>".$team[$m->Team2]->NameShort."</div>";
         $result .= "</td>";
         
         $result .= "</tr>";
       }
       $result .= "</table>";
       
       if( $_REQUEST["request"] != "submit" )
       {
         $result .= "<input type='hidden' name='request' value='submit'>";
         $result .= "<p><input type='submit' name='Submit' value='Tipp speichern'></p>";
       }
       else
       {
         $result .= "<input type='hidden' name='request' value='edit'>";
         $result .= "<p><input type='submit' name='Submit' value='Tipp bearbeiten'></p>";
       }
       
       $result .= "</form>";
     }
     else
     {
       $result = "Dieser Account ist nicht als Tippspieler eingetragen.";
     }
   }
   return $result;
 }
 
 
 function hv_tippspiel16_add_show_profile( $atts, $content = null )
 {
   global $hv_tippspiel16_club_label;
  
   $current_user = wp_get_current_user();
   $userid = $current_user->ID;
  
   $profile = hv_tippspiel16_get_profile( $userid );
   
   $result .= "<table class='tippspiel16'>";
              
   $result .= "<tr>";
   $result .= "<td>Europameister</td>";
   $result .= "<td>".hv_tippspiel16_get_team_name($profile->WeltmeisterID)."</td>";
   $result .= "<td></td>";
   $result .= "</tr>";
   
   $result .= "<tr>";
   $result .= "<td>Torsch&uuml;tzenk&ouml;nig</td>";
   $result .= "<td>".hv_tippspiel16_get_torschuetze_name( $profile->TorschuetzeID )."</td>";
   $result .= "<td></td>";
   $result .= "</tr>";
   
   $result .= "<tr>";
   $result .= "<td>".$hv_tippspiel16_club_label."</td>";
   $result .= "<td>".hv_tippspiel16_get_club_name( $profile->ClubID )."</td>";
   $result .= "<td></td>";
   $result .= "</tr>";
   $result .= "</table>";
   return $result;
 }
 
 function hv_tippspiel16_add_profileform( $atts, $content = null )
 {
   global $hv_tippspiel16_club_label;
   
   $result = "";
   if( !is_user_logged_in() )
   {
     $result = "Bitte <a href=\"".wp_login_url( get_permalink() )." title=\"Login\">einloggen</a> um deinen Tipp abzugeben!";
   }
   else
   {
     $current_user = wp_get_current_user();
     if( hv_tippspiel16_is_player( $current_user ) )
     {
       global $wpdb;
      
       if( $_REQUEST["profile_request"] == "profile_submit" )
         $profile_is_submitted = true;
      
       if( $profile_is_submitted )
       {
         if( $_REQUEST["tippspiel16_torschuetze"] < 0 )
         {
           if( strlen( $_REQUEST["tippspiel16_torschuetze_new"] ) > 1 )
           {
             $wpdb->insert( hv_tippspiel16_get_table_torschuetzen(),
                     array(
                    'Name' => $_REQUEST["tippspiel16_torschuetze_new"]
                     ) );
             $newtorschuetzeid = $wpdb->insert_id;
           }
           else $newtorschuetzeid = 0;
         }
         else
         {
           $newtorschuetzeid = $_REQUEST["tippspiel16_torschuetze"];
         }
        
         if( $_REQUEST["tippspiel16_clubs"] < 0 )
         {
           if( strlen( $_REQUEST["tippspiel16_club_new"] ) > 1 )
           {
             $wpdb->insert( hv_tippspiel16_get_table_clubs(),
                     array(
                    'Name' => $_REQUEST["tippspiel16_club_new"]
                     ) );
             $newclubid = $wpdb->insert_id;
           }
           else
           {
             $newclubid = 0;
           }
         }
         else
         {
           $newclubid = $_REQUEST["tippspiel16_clubs"];
         }
         
         $oldprofile = hv_tippspiel16_get_profile( $current_user->ID );
         if( count( $oldprofile) > 0 )
         {
           if( hv_tippspiel16_is_started() )
           {
             $wpdb->update( hv_tippspiel16_get_table_users(),
                    array(
                   'ClubID' => $newclubid
                    ),
                    array( 'ID' => $current_user->ID ) );
           }
           else
           {
             $wpdb->update( hv_tippspiel16_get_table_users(),
                    array(
                   'WeltmeisterID' => $_REQUEST["tippspiel16_meister"],
                   'TorschuetzeID' => $newtorschuetzeid,
                   'ClubID' => $newclubid
                    ),
                    array( 'ID' => $current_user->ID ) );
           }
         }
         else
         {
           if( hv_tippspiel16_is_started() )
           {
             $wpdb->insert( hv_tippspiel16_get_table_users(),
                    array(
                   'ID' => $current_user->ID,
                   'WeltmeisterID' => 0,
                   'TorschuetzeID' => 0,
                   'ClubID' => $newclubid
                    ) );
           }
           else
           {
             $wpdb->insert( hv_tippspiel16_get_table_users(),
                    array(
                   'ID' => $current_user->ID,
                   'WeltmeisterID' => $_REQUEST["tippspiel16_meister"],
                   'TorschuetzeID' => $newtorschuetzeid,
                   'ClubID' => $newclubid
                    ) );
           }
         }
        
       }
       
       $teams = $wpdb->get_results( "SELECT * FROM ".hv_tippspiel16_get_table_teams() );
       $torschuetzen = $wpdb->get_results( "SELECT * FROM ".hv_tippspiel16_get_table_torschuetzen() );
       $clubs = $wpdb->get_results( "SELECT * FROM ".hv_tippspiel16_get_table_clubs() );

       $currentProfile = hv_tippspiel16_get_profile( $current_user->ID );
       $currentMeister = 0;
       $currentClub = 0;
       $currentTorschuetze = 0;
       if( count($currentProfile) > 0 )
       {
         $currentMeister = $currentProfile->WeltmeisterID;
         $currentClub = $currentProfile->ClubID;
         $currentTorschuetze = $currentProfile->TorschuetzeID;
       }
       
       
       $result  .= "<form name='form_hv_tipppspiel16_profile' method='post' action='".get_permalink()."'>";
       
       $result .= "<table class='tippspiel16'>";
              
       $result .= "<tr>";
       $result .= "<td>Europameister</td>";
       $result .= "<td>";
       if( $profile_is_submitted  || hv_tippspiel16_is_started()  )
       {
         if( $currentMeister > 0 )
           $result .= hv_tippspiel16_get_team_name( $currentMeister);
         else
           $result .= "-";
       }
       else
       {
         $result .= "<select style='width:100%' name='tippspiel16_meister'>";
         $result .= "<option value='0'>-</option>";
         foreach( $teams as $t )
         {
           $result .= "<option value='".$t->TeamID."'";
           if( $currentMeister == $t->TeamID )
             $result .= " selected='selected'";
           $result .= ">".$t->Name."</option>";
         }
         $result .= "</select>";
       }
       $result .= "</td>";
       $result .= "<td></td>";
       $result .= "</tr>";
       
       $result .= "<tr>";
       $result .= "<td>Torsch&uuml;tzenk&ouml;nig</td>";
       $result .= "<td>";
       if( $profile_is_submitted || hv_tippspiel16_is_started() )
       {
        if( $currentTorschuetze > 0 )
           $result .= hv_tippspiel16_get_torschuetze_name( $currentTorschuetze);
         else
           $result .= "-";
       }
       else
       {
         $result .= "<select style='width:100%' name='tippspiel16_torschuetze'>";
         $result .= "<option value='0'>-</option>";
         $result .= "<option value='-1'>Hinzufügen:</option>";
         foreach( $torschuetzen as $t )
         {
           $result .= "<option value='".$t->ID."'";
           if( $currentTorschuetze == $t->ID )
             $result .= " selected='selected'";
           $result .= ">".$t->Name."</option>";
         }
         $result .= "</select>";
       }
       $result .= "</td>";
       $result .= "<td>";
       if( !$profile_is_submitted && !hv_tippspiel16_is_started() ) $result .= "<input type='text' name='tippspiel16_torschuetze_new' />";
       $result .= "</td>";
       $result .= "</tr>";
       
       $result .= "<tr>";
       $result .= "<td>".$hv_tippspiel16_club_label."</td>";
       $result .= "<td>";
       if( $profile_is_submitted )
       {
        if( $currentClub > 0 )
           $result .= hv_tippspiel16_get_club_name( $currentClub);
         else
           $result .= "-";
       }
       else
       {
         $result .= "<select style='width:100%' name='tippspiel16_clubs'>";
         $result .= "<option value='0'>-</option>";
         $result .= "<option value='-1'>Hinzufügen:</option>";
         foreach( $clubs as $c )
         {
           $result .= "<option value='".$c->ClubID."'";
           if( $currentClub == $c->ClubID )
             $result .= " selected='selected'";
           $result .= ">".$c->Name."</option>";
         }
         $result .= "</select>";
       }
       $result .= "</td>";
       $result .= "<td>";
       if( !$profile_is_submitted ) $result .= "<input type='text' name='tippspiel16_club_new'/>";
       $result .= "</td>";
       $result .= "</tr>";
       
       $result .= "</table>";
        
       if( !$profile_is_submitted )
       {
         $result .= "<input type='hidden' name='profile_request' value='profile_submit'>";
         $result .= "<p><input type='submit' name='Submit' value='Speichern'></p>";
       }
       else
       {
         $result .= "<input type='hidden' name='profile_request' value='profile_edit'>";
         $result .= "<p><input type='submit' name='Submit' value='Bearbeiten'></p>";
       }
       $result .= "</form>";
     }
     else
     {
       $result = "Dieser Account ist nicht als Tippspieler eingetragen.";
     }
   }
   return $result;
 } 
 
 add_filter( 'init', 'hv_tippspiel16_init' );
 add_action('wp_enqueue_scripts', 'hv_tippspiel16_register_style');
 add_shortcode( 'hv_tippspiel16_show_profile', 'hv_tippspiel16_add_show_profile' );
 add_shortcode( 'hv_tippspiel16_tabelle', 'hv_tippspiel16_add_tabelle' );
 add_shortcode( 'hv_tippspiel16_tippform', 'hv_tippspiel16_add_tippform' );
 add_shortcode( 'hv_tippspiel16_profileform', 'hv_tippspiel16_add_profileform' );
 add_shortcode( 'hv_tippspiel16_clubtabelle', 'hv_tippspiel16_add_clubtabelle' );
 