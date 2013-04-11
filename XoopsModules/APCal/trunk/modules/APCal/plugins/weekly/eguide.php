<?php

//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
 
/**
 * @copyright   The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license     http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author      A plugin for eguide 1.6 by nobu
 * @version     $Id:$
 */

	if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

	/*
		$db : db instance
		$myts : MyTextSanitizer instance
		$this->year : year
		$this->month : month
		$this->date : date
		$this->week_start : sunday:0 monday:1
		$this->user_TZ : user's timezone (+1.5 etc)
		$this->server_TZ : server's timezone (-2.5 etc)
		$tzoffset_s2u : the offset from server to user
		$now : the result of time()
		$plugin = array('dirname'=>'dirname','name'=>'name','dotgif'=>'*.gif')
		
		$plugin_returns[ DATE ][]
	*/

	// set range (added 86400 second margin "begin" & "end")
	$wtop_date = $this->date - ( $this->day - $this->week_start + 7 ) % 7 ;
	$range_start_s = mktime(0,0,0,$this->month,$wtop_date-1,$this->year) ;
	$range_end_s = mktime(0,0,0,$this->month,$wtop_date+8,$this->year) ;

	// query
	$result = $db->query( "SELECT title,eid,edate,summary FROM ".$db->prefix("eguide")." WHERE edate >= $range_start_s AND edate < $range_end_s AND expire > '$now'" ) ;

	while( list( $title , $id , $server_time , $description ) = $db->fetchRow( $result ) ) {
		$user_time = $server_time + $tzoffset_s2u ;
		// if( date( 'n' , $user_time ) != $this->month ) continue ;
		$target_date = date('j',$user_time) ;
		$tmp_array = array(
			'dotgif' => $plugin['dotgif'] ,
			'dirname' => $plugin['dirname'] ,
			'link' => XOOPS_URL."/modules/{$plugin['dirname']}/event.php?eid=$id&amp;caldate={$this->year}-{$this->month}-$target_date" ,
			'id' => $id ,
			'server_time' => $server_time ,
			'user_time' => $user_time ,
			'name' => 'eid' ,
			'title' => $myts->htmlSpecialChars( $title ) ,
			'description' => $myts->displayTarea( $description )
		) ;

		// multiple gifs allowed per a plugin & per a day
		$plugin_returns[ $target_date ][] = $tmp_array ;
	}


?>