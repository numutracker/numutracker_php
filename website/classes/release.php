<?php
	
class Release {
	
	private $artist;
	private $artist_id;
	private $title;
	private $release_id;
	private $type;
	private $date;
	private $art;
	private $artist_art; 
	private $status;
	
	public function __construct($release_array) {
		$this->artist = $release_array['artist'];
	    $this->artist_id = $release_array['artist_id'];
	    $this->title = $release_array['title'];
	    $this->release_id = $release_array['release_id'];
	    $this->type = $release_array['type'];
	    $this->date = $release_array['date'];
	    $this->art = $release_array['art']['full'];
	    $this->artist_art = $release_array['artist_art']['full'];
	    $this->status = $release_array['status'];
	}
	
	public function show() {
		$string = "<div class='release' release_id='".$this->release_id."'>";
		$string .= "<div class='table'>";
		$string .= "<div class='img'><a href='/release/".$this->release_id."'><img src='";
		if ($this->art == "https://www.numutracker.com/nonly3-1024.png") {
			$string .= $this->artist_art;
		} else {
			$string .= $this->art;
		}
		$string .=  "'/></a></div>";
		$string .= "<div class='info'>";
		$string .= "<div class='artist'><a href='/artist/".$this->artist_id."'>".$this->artist. "</a></div>";
		$out = strlen($this->title) > 24 ? substr($this->title,0,24)."..." : $this->title;
		$string .= "<div class='title'><a href='/release/".$this->release_id."'>".$out."</a></div>";
		$string .= "<div><div class='type'>".$this->type."</div><div class='date'>".$this->date."</div></div>";  
		$string .= "</div>";
		$string .= "<div class='status_marker'>";
		if ($this->status == 0) {
			$string .= "<div class='listen_marker unread' release_id=".$this->release_id." title='Unlistened'></div>";
		} else {
			$string .= "<div class='listen_marker read' release_id=".$this->release_id." title='Listened'></div>";
		}
		$string .= "</div>";
		$string .= "</div></div>";
		return $string;
	}
	
}
	
?>