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
   $result =$wpdb->get_row( "SELECT * FROM ".hv_tippspiel16_get_table_users()." WHERE ID = ".$id );
   return $results;
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
 
 function hv_tippspiel16_add_tabelle( $atts, $content = null )
 {
   global $wpdb;
   
   if( isset( $_GET['matchid'] ) && $_GET['matchid'] > 0 )
   {
     $m = $wpdb->get_row( "SELECT * FROM ".hv_tippspiel16_get_table_matches()." WHERE ID=".$_GET['matchid'].";" );
     $team1 = $wpdb->get_row("SELECT * FROM ".hv_tippspiel16_get_table_teams()." WHERE TeamID=".$m->Team1.";");
     $team2 = $wpdb->get_row("SELECT * FROM ".hv_tippspiel16_get_table_teams()." WHERE TeamID=".$m->Team2.";");
     
     $result = $team1->Name." vs. ".$team2->Name;
     $result .= "<p><a href=\"".get_page_link()."\">Zur&uuml;ck</a></p>";
   }
   else
   {
     $matches = $wpdb->get_results("SELECT * FROM ".hv_tippspiel16_get_table_matches()." ORDER BY Datum;");
     $result = "<table><thead><tr><th>Zeit</th><th>Team 1</th><th>Team 2</th><th>Resultat</th></tr></thead><tbody>";
  
     foreach ( $matches as $m )
     {
       $team1 = $wpdb->get_row("SELECT * FROM ".hv_tippspiel16_get_table_teams()." WHERE TeamID=".$m->Team1.";");
       $team2 = $wpdb->get_row("SELECT * FROM ".hv_tippspiel16_get_table_teams()." WHERE TeamID=".$m->Team2.";");
      
       $result .= "<tr onclick=\"document.location = '";
       $result .= get_page_link();
       $result .= "?matchid=".$m->ID."';\">";
       $result .= "<td>".date("d.m. H:i", $m->Datum+get_option( 'gmt_offset' ) * 3600)."</td>";
       $result .= "<td>".$team1->Name."</td>";
       $result .= "<td>".$team2->Name."</td>";
       $result .= "<td>0</td>";
       $result .= "</tr>";
     }
     $result .= "</tbody>";
     $result .= "</table>";
   }
   return $result;
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
   if( !is_user_logged_in() )
   {
     $result = "Bitte <a href=\"".wp_login_url( get_permalink() )." title=\"Login\">einloggen</a> um einen Tipp abzugeben!";
   }
   else
   {
     $current_user = wp_get_current_user();
     if( hv_tippspiel16_is_player( $current_user ) )
     {
       global $wpdb;
       $query = "SELECT * FROM ".hv_tippspiel16_get_table_matches()." WHERE Datum > ".(time()+30*60)." ORDER BY DATUM";
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
         foreach( $matches as $m )
         {
           if( $m->Datum > time()+30*60 )
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
       
       $result  = "<form name='form1' method='post' action='".get_permalink()."'>";
       $result .= "<input type='hidden' name='action' value='tipp' />";
       
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
   require_once( "hv_tippspiel16_variables.php" );
  
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
   $result .= "<td>Torsch&uuml;tze</td>";
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
   require_once( "hv_tippspiel16_variables.php" );
   
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
         print_r( $oldprofile );
         if( count( $oldprofile) > 0 )
         {
           $wpdb->update( hv_tippspiel16_get_table_users(),
                    array(
                   'WeltmeisterID' => $_REQUEST["tippspiel16_meister"],
                   'TorschuetzeID' => $newtorschuetzeid,
                   'ClubID' => $newclubid
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
                   'ClubID' => $newclubid
                    ) );
           $result .= $wpdb->last_error;
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
       if( $profile_is_submitted )
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
       $result .= "<td>Torsch&uuml;tze</td>";
       $result .= "<td>";
       if( $profile_is_submitted )
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
       if( !$profile_is_submitted ) $result .= "<input type='text' name='tippspiel16_torschuetze_new' />";
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