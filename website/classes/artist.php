<?php
	
class Artist {
	
	private $artist;
	private $artist_id;
	private $recent_release;
	private $total_releases;
	private $unread;
	private $artist_art; 
	private $status;
	
	public function __construct($artist_array) {
		$this->artist = $artist_array['artist'];
	    $this->artist_id = $artist_array['artist_id'];
	    $this->recent_release = $artist_array['recent_date'];
	    $this->artist_art = $artist_array['artist_art']['full'];
	    $this->status = $artist_array['status'];
	    $this->total_releases = $artist_array['total_releases'];
	    $this->unread = $artist_array['unread'];
	}
	
	public function show() {
		$string = "<div class='release'>";
		$string .= "<div class='table'><div class='img'><a href='/artist/".$this->artist_id."'><img src='";
	    $string .= $this->artist_art;
	    $string .= "'/></a></div><div class='info'>";
	    $out = strlen($this->artist) > 26 ? substr($this->artist,0,26)."..." : $this->artist;
	    $string .= "<div class='title'><a href='/artist/".$this->artist_id."'>".$out."</a></div>";
	    //echo "<div class='type'>".$release['type']."</div>";
	    $string .= "<div><div class='type'>";
	    if ($this->total_releases > 0) { $string .= round(100-(($this->unread/$this->total_releases)*100),0)."%"; } else { $string .= "100%"; }
	    $string .= "</div>";
	    $string .= "<div class='date'>".$this->recent_release."</div></div>";   
	    $string .= "</div>";
		$string .= "<div class='status_marker'>";
		if ($this->status == 0) {
			$string .= "<div class='follow_marker follow' artist_id='".$this->artist_id."' title='Not Following'></div>";
		} else {
			$string .= "<div class='follow_marker following' artist_id='".$this->artist_id."' title='Following'></div>";
		}
		$string .= "</div>";
	    $string .= "</div></div>";
    	return $string;
	}
	
}
	
?>