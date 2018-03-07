<?php

function parseHeaders( $headers )
{
    $head = array();
    foreach( $headers as $k=>$v )
    {
        $t = explode( ':', $v, 2 );
        if( isset( $t[1] ) )
            $head[ trim($t[0]) ] = trim( $t[1] );
        else
        {
            $head[] = $v;
            if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                $head['response_code'] = intval($out[1]);
        }
    }
    return $head;
}

// Fix fucked musicbrainz dates

function convert_date($date) {
	
	//strip dashes 
	$date = str_replace("-", "", $date);
	$year = substr($date,0,4); 
	$month = substr($date,4,2); 
	$day = substr($date,6,2);
	
	if ($year == '') { $year = '0000'; $month = '00'; $day = '00';} else {
		if ($month == '') { $month = '01'; }
		if ($day == '') { $day = '01'; }
	}
	
	return $year."-".$month."-".$day;
	
}