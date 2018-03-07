<?php
	
class Data {
	
	private $db;
	private $mysqli;
	private $page = 1;
	private $limit = 50;
	private $offset = 0;
	private $search;
	
	public function __construct() {
		$this->db = Database::getInstance();
		$this->mysqli = $this->db->getConnection(); 
	}
	
	public function returnFilterString($current_user_id) {
		$sql_query = "SELECT * FROM v2_users WHERE user_id = '" . $this->mysqli->real_escape_string($current_user_id) . "'";
		$result = $this->mysqli->query($sql_query);
		$num_rows = $result->num_rows;
		
		if ($num_rows == 1) { 
			$row = mysqli_fetch_array($result);
			
			// Generate query string for searches...
			$query = "";
			$bump = 0;
			if ($row['album'] == 1) {
				$query .= "v2_release_group.type = 'Album'";
				$bump++;
			}
			
			if ($row['single'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Single'";
				$bump++;
			}
			
			if ($row['ep'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'EP'";
				$bump++;
			}
			
			if ($row['live'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Live'";
				$bump++;
			}
			
			if ($row['soundtrack'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Soundtrack'";
				$bump++;
			}
			
			if ($row['remix'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Remix'";
				$bump++;
			}
			
			if ($row['other'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "(v2_release_group.type != 'Album' AND v2_release_group.type != 'Single' AND v2_release_group.type != 'EP' AND v2_release_group.type != 'Live' AND v2_release_group.type != 'Soundtrack' AND v2_release_group.type != 'Remix')";
				$bump++;
			}
			
			$current_query_string = " AND (".$query.")";
			
			if ($bump == 0) { $current_query_string = ""; }
			
			return $current_query_string;
		}
	}
	
	public function returnFilterStringArtists($current_user_id) {
		$sql_query = "SELECT * FROM v2_users WHERE user_id = '" . $this->mysqli->real_escape_string($current_user_id) . "'";
		$result = $this->mysqli->query($sql_query);
		$num_rows = $result->num_rows;
		
		if ($num_rows == 1) { 
			$row = mysqli_fetch_array($result);
			
			// Generate query string for searches...
			$query = "";
			$bump = 0;
			if ($row['album'] == 1) {
				$query .= "v2_release_group.type = 'Album'";
				$bump++;
			}
			
			if ($row['single'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Single'";
				$bump++;
			}
			
			if ($row['ep'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'EP'";
				$bump++;
			}
			
			if ($row['live'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Live'";
				$bump++;
			}
			
			if ($row['soundtrack'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Soundtrack'";
				$bump++;
			}
			
			if ($row['remix'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "v2_release_group.type = 'Remix'";
				$bump++;
			}
			
			if ($row['other'] == 1) {
				if ($bump > 0) { $query .= " OR ";}
				$query .= "(v2_release_group.type != 'Album' AND v2_release_group.type != 'Single' AND v2_release_group.type != 'EP' AND v2_release_group.type != 'Live' AND v2_release_group.type != 'Soundtrack' AND v2_release_group.type != 'Remix')";
				$bump++;
			}
			
			$current_query_string = " AND (".$query.")";
			
			if ($bump == 0) { $current_query_string = ""; }
			
			return $current_query_string;
		}
	}
	
	public function returnReleasesString($current_user_id,$type,$page,$artist_id = 0) {
		
		$this->page = $this->mysqli->real_escape_string($page);
		$this->offset = (($this->page)-1) * $this->limit;
		
		if ($current_user_id > 0) {
			switch ($type) {
				case "artist":
					$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE is_deleted = 0 AND v2_release_group.artist_id = $artist_id";
					$sql .= $this->returnFilterString($current_user_id);
					$sql .= " ORDER BY date DESC,name ASC";
					break;
				case "user":
					$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date <= NOW() AND v2_release_group.artist_id IN (SELECT artist_id FROM `v2_user_artist` where user_id = '".$current_user_id."') AND is_deleted = 0 ";
					$sql .= $this->returnFilterString($current_user_id);
					$sql .= " ORDER BY date DESC,name ASC";
					break;
				case "all":
					$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date <= NOW() AND is_deleted = 0 ";
					$sql .= $this->returnFilterString($current_user_id);
					$sql .= " ORDER BY date DESC,name ASC";
					break;
				case "upcoming":
					$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date > NOW() AND v2_release_group.artist_id IN (SELECT artist_id FROM `v2_user_artist` where user_id = '".$current_user_id."') AND is_deleted = 0 ";
					$sql .= $this->returnFilterString($current_user_id);
					$sql .= " ORDER BY date ASC,name ASC";
					break;
			}
			
			$sql_query = "SELECT v2_release_group.artist_id,v2_release_group.release_id, (SELECT read_status from v2_user_listen WHERE user_id ='".$current_user_id."' and release_id = v2_release_group.release_id) as read_status, ar.name AS artist_name, title, v2_release_group.type, ar.art AS artist_art, v2_release_group.art AS album_art, date ".$sql." LIMIT {$this->limit} OFFSET {$this->offset}";
			
		} else {
			switch ($type) {
				case "all":
					$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date <= NOW() AND is_deleted = 0 ";
					$sql .= " ORDER BY date DESC,name ASC";
					break;
				case "recent":
					$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date BETWEEN CURDATE()-INTERVAL 1 WEEK AND CURDATE() AND is_deleted = 0 ";
					$sql .= "AND v2_release_group.type = 'Album'";
					$sql .= " ORDER BY date DESC,name ASC";
					break;
				case "upcoming":
					$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE date > NOW() AND is_deleted = 0 ";
					$sql .= " ORDER BY date ASC,name ASC";
					break;
				case "artist":
					$sql = "FROM `v2_release_group` LEFT JOIN `v2_artist` as ar ON v2_release_group.artist_id = ar.artist_id WHERE is_deleted = 0 AND v2_release_group.artist_id = $artist_id";
					$sql .= $this->returnFilterString($current_user_id);
					$sql .= " ORDER BY date DESC,name ASC";
					break;
			}
			$sql_query = "SELECT v2_release_group.artist_id,v2_release_group.release_id, 0 as read_status, ar.name AS artist_name, title, v2_release_group.type, ar.art AS artist_art, v2_release_group.art AS album_art, date ".$sql." LIMIT {$this->limit} OFFSET {$this->offset}";
		}
		
		
		$results_array = $this->returnReleases($sql_query);
		
		$results_string = '';
		foreach ($this->returnReleases($sql_query) as $release) { 
			$results_string .= $release->show();
		}
		
		// Get total number of results and total pages based on limit...
		$sql_query = "SELECT count(v2_release_group.release_id) as count ".$sql;
		$result = $this->mysqli->query($sql_query);
		$num_rows = $result->num_rows;
		
		while ($row = mysqli_fetch_array($result)) {
			$total_rows = $row['count'];
		}
		$total_pages = ceil($total_rows/$this->limit);

		
		$results_string .= "<div id='navigation'>";
		$results_string .=  "<div id='nav_prev'>";
		if ($this->page>1) {
		$results_string .=  "<a href='?page=".($this->page-1)."'>Previous</a>";
		} else {
			$results_string .= "&nbsp;";
		}
		$results_string .=  "</div>";
		$results_string .=  "<div id='nav_data'>";
		if ($total_pages > 0) {
		$results_string .= "Page {$this->page} of {$total_pages}";
		} else {
			$results_string .= "No Results";
		}
		$results_string .= "</div>";
		$results_string .=  "<div id='nav_next'>";
		if ($total_pages > $this->page) {
			$results_string .=  "<a href='?page=".($this->page+1)."'>Next</a>";
		} else {
			$results_string .= "&nbsp;";
		}
		$results_string .=  "</div>";
		$results_string .=  "</div>";
		
				
		$data_array = array("page"=>"$this->page","total_pages"=>"$total_pages","total_results"=>"$total_rows","results"=>$results_string);
		
		return $data_array;

		
	}
	
	public function returnReleases($query) {
		$result = $this->mysqli->query($query);
		$num_rows = $result->num_rows;
		$results_array = array();
		while ($row = mysqli_fetch_array($result)) {
		    
		    // Pick art...
		    if ($row['album_art'] == '0' || $row['album_art'] == '2') {
		        $album_art = array(
		            "thumb"=>"https://www.numutracker.com/nonly3-1024.png",
		            "full"=>"https://www.numutracker.com/nonly3-1024.png",
		            "large"=>"https://www.numutracker.com/nonly3-1024.png",
		            "xlarge"=>"https://www.numutracker.com/nonly3-1024.png"
		        );
		    } else {
		        $album_art = array(
		            "thumb"=>"https://www.numutracker.com/v2/covers/thumb/".$row['album_art'],
		            "full"=>"https://www.numutracker.com/v2/covers/".$row['album_art'],
		            "large"=>"https://www.numutracker.com/v2/covers/large/".$row['album_art'],
		            "xlarge"=>"https://www.numutracker.com/v2/covers/xlarge/".$row['album_art']
		        );
		    }
		    
		    // Artist art
		    if ($row['artist_art'] == '0' || $row['artist_art'] == '2') {
		        $artist_art = array(
		            "thumb"=>"https://www.numutracker.com/nonly3-1024.png",
		            "full"=>"https://www.numutracker.com/nonly3-1024.png",
		            "large"=>"https://www.numutracker.com/nonly3-1024.png",
		            "xlarge"=>"https://www.numutracker.com/nonly3-1024.png"
		        );
		    } else {
		        $artist_art = array(
		            "thumb"=>"https://www.numutracker.com/v2/artist/thumb/".$row['artist_art'],
		            "full"=>"https://www.numutracker.com/v2/artist/".$row['artist_art'],
		            "large"=>"https://www.numutracker.com/v2/artist/large/".$row['artist_art'],
		            "xlarge"=>"https://www.numutracker.com/v2/artist/xlarge/".$row['artist_art']
		        );
		    }
		    
		    if ($row['read_status'] == null) { $row['read_status'] = 0; }       
		    
		    
		    $release = new Release(array(
		    "artist" => $row['artist_name'],
		    "artist_id" =>$row['artist_id'],
		    "title" => $row['title'],
		    "release_id" => $row['release_id'],
		    "type" => $row['type'],
		    "date" => date("F j, Y", strtotime($row['date'])),
		    "art" =>  $album_art,
		    "artist_art" =>  $artist_art,
		    "status" => $row['read_status']
		    ));
		    
		    array_push($results_array,$release);
		    
		}
		
		return $results_array;
	}
	
	public function returnArtistsString($current_user_id,$type,$page) {
		
		$this->page = $this->mysqli->real_escape_string($page);
		$this->offset = (($this->page)-1) * $this->limit;
		
		if ($current_user_id > 0) {
			switch ($type) {
				case "user":
					$sql_query = "SELECT v2_artist.name, v2_artist.artist_id, v2_artist.sort_name, v2_artist.art,
					(SELECT MAX(v2_release_group.date) FROM v2_release_group WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $this->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as recent_date, (SELECT count(v2_release_group.release_id) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE (read_status =  '0' OR read_status IS NULL) AND v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $this->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as unread, (SELECT count(v2_release_group.release_id) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $this->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as total_releases,
					coalesce((SELECT IF(v2_user_artist.user_id IS NULL,'0','1')  FROM `v2_user_artist` WHERE v2_artist.artist_id = v2_user_artist.artist_id AND v2_user_artist.user_id = '".$current_user_id."'),0) as follow_status
					FROM  `v2_user_artist`
					LEFT JOIN  `v2_artist` ON v2_user_artist.artist_id = v2_artist.artist_id
					LEFT JOIN  `v2_users` ON v2_user_artist.user_id = v2_users.user_id WHERE v2_users.user_id = '". $current_user_id ."' ";
					break;
				case "all":
					$sql_query = "SELECT v2_artist.name, v2_artist.artist_id, v2_artist.sort_name, v2_artist.art,
					(SELECT MAX(v2_release_group.date) FROM v2_release_group WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $this->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as recent_date, (SELECT count(v2_release_group.release_id) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE (read_status =  '0' OR read_status IS NULL) AND v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $this->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as unread, (SELECT count(v2_release_group.release_id) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $this->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as total_releases,
					coalesce((SELECT IF(v2_user_artist.user_id IS NULL,'0','1')  FROM `v2_user_artist` WHERE v2_artist.artist_id = v2_user_artist.artist_id AND v2_user_artist.user_id = '".$current_user_id."'),0) as follow_status
					FROM  `v2_artist`
					";
					break;
			}
			
			$sql_query .= "ORDER BY recent_date DESC, name";
						
		} else {
			$sql_query = "SELECT v2_artist.name, v2_artist.artist_id, v2_artist.sort_name, v2_artist.art,
			(SELECT MAX(v2_release_group.date) FROM v2_release_group WHERE v2_release_group.artist_id = v2_artist.artist_id";
			$sql_query .= ") as recent_date, (SELECT count(v2_release_group.release_id) FROM `v2_release_group`
			LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
			ON v2_release_group.release_id = x.release_id WHERE (read_status =  '0' OR read_status IS NULL) AND v2_release_group.artist_id = v2_artist.artist_id";
			$sql_query .= ") as unread, (SELECT count(v2_release_group.release_id) FROM `v2_release_group`
			LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
			ON v2_release_group.release_id = x.release_id WHERE v2_release_group.artist_id = v2_artist.artist_id";
			$sql_query .= ") as total_releases,
			0 as follow_status
			FROM  `v2_artist`
			";
			$sql_query .= "ORDER BY recent_date DESC, name";
					
		}
		
		
		$results_array = $this->returnArtists($sql_query." LIMIT {$this->limit} OFFSET {$this->offset}");
		
		$results_string = '';
		$sql_query." LIMIT {$this->limit} OFFSET {$this->offset}";
		foreach ($this->returnArtists($sql_query." LIMIT {$this->limit} OFFSET {$this->offset}") as $artist) { 
			$results_string .= $artist->show();
		}
		
		// Get total number of results and total pages based on limit...
		$sql_query = "SELECT count(x.artist_id) as count FROM (".$sql_query.") as x";
		$result = $this->mysqli->query($sql_query);
		$num_rows = $result->num_rows;
		
		while ($row = mysqli_fetch_array($result)) {
			$total_rows = $row['count'];
		}
		$total_pages = ceil($total_rows/$this->limit);

		
		$results_string .= "<div id='navigation'>";
		$results_string .=  "<div id='nav_prev'>";
		if ($this->page>1) {
		$results_string .=  "<a href='?page=".($this->page-1)."'>Previous</a>";
		} else {
			$results_string .= "&nbsp;";
		}
		$results_string .=  "</div>";
		$results_string .=  "<div id='nav_data'>";
		if ($total_pages > 0) {
		$results_string .= "Page {$this->page} of {$total_pages}";
		} else {
			$results_string .= "No Results";
		}
		$results_string .= "</div>";
		$results_string .=  "<div id='nav_next'>";
		if ($total_pages > $this->page) {
			$results_string .=  "<a href='?page=".($this->page+1)."'>Next</a>";
		} else {
			$results_string .= "&nbsp;";
		}
		$results_string .=  "</div>";
		$results_string .=  "</div>";
		
				
		$data_array = array("page"=>"$this->page","total_pages"=>"$total_pages","total_results"=>"$total_rows","results"=>$results_string);
		
		return $data_array;

		
	}
	
	public function returnSearchArtistsString($current_user_id,$search,$page) {
		
		$this->page = $this->mysqli->real_escape_string($page);
		$this->offset = (($this->page)-1) * $this->limit;
		$this->search = $this->mysqli->real_escape_string($search);
		
		
		if ($current_user_id > 0) {
			$sql_query = "SELECT v2_artist.name, v2_artist.artist_id, v2_artist.sort_name, v2_artist.art,
					(SELECT MAX(v2_release_group.date) FROM v2_release_group WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $this->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as recent_date, (SELECT count(release_mbid) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE (read_status =  '0' OR read_status IS NULL) AND v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $this->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as unread, (SELECT count(release_mbid) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= $this->returnFilterStringArtists($current_user_id);
					$sql_query .= ") as total_releases,
					coalesce((SELECT IF(v2_user_artist.user_id IS NULL,'0','1')  FROM `v2_user_artist` WHERE v2_artist.artist_id = v2_user_artist.artist_id AND v2_user_artist.user_id = '".$current_user_id."'),0) as follow_status
					FROM  `v2_artist` WHERE v2_artist.name LIKE '%".$this->search."%'
					";
		
			$sql_query .= "ORDER BY recent_date DESC, name";
						
		} else {
			$sql_query = "SELECT v2_artist.name, v2_artist.artist_id, v2_artist.sort_name, v2_artist.art,
					(SELECT MAX(v2_release_group.date) FROM v2_release_group WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= ") as recent_date, (SELECT count(release_mbid) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE (read_status =  '0' OR read_status IS NULL) AND v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= ") as unread, (SELECT count(release_mbid) FROM `v2_release_group`
					LEFT JOIN  (SELECT * from v2_user_listen WHERE user_id ='".$current_user_id."') AS x
					ON v2_release_group.release_id = x.release_id WHERE v2_release_group.artist_id = v2_artist.artist_id";
					$sql_query .= ") as total_releases,
					coalesce((SELECT IF(v2_user_artist.user_id IS NULL,'0','1')  FROM `v2_user_artist` WHERE v2_artist.artist_id = v2_user_artist.artist_id AND v2_user_artist.user_id = '".$current_user_id."'),0) as follow_status
					FROM  `v2_artist` WHERE v2_artist.name LIKE '%".$this->search."%'
					";
		
			$sql_query .= "ORDER BY recent_date DESC, name";
		}
		
		
		$results_array = $this->returnArtists($sql_query." LIMIT {$this->limit} OFFSET {$this->offset}");
		
		$results_string = '';
		foreach ($this->returnArtists($sql_query." LIMIT {$this->limit} OFFSET {$this->offset}") as $artist) { 
			$results_string .= $artist->show();
		}
		
				
		$data_array = array("page"=>"$this->page","total_pages"=>"$total_pages","total_results"=>"$total_rows","results"=>$results_string);
		
		return $data_array;

		
	}

	public function returnArtists($query) {
		$result = $this->mysqli->query($query);
		$num_rows = $result->num_rows;
		$results_array = array();
		while ($row = mysqli_fetch_array($result)) {
		    
		    // Pick art...
		    if ($row['art'] == '0' || $row['art'] == '2') {
				$art = array(
					"thumb"=>"https://www.numutracker.com/nonly3-1024.png",
					"full"=>"https://www.numutracker.com/nonly3-1024.png",
					"large"=>"https://www.numutracker.com/nonly3-1024.png",
					"xlarge"=>"https://www.numutracker.com/nonly3-1024.png"
				);
			} else {
				$art = array(
					"thumb"=>"https://www.numutracker.com/v2/artist/thumb/".$row['art'],
					"full"=>"https://www.numutracker.com/v2/artist/".$row['art'],
					"large"=>"https://www.numutracker.com/v2/artist/large/".$row['art'],
					"xlarge"=>"https://www.numutracker.com/v2/artist/xlarge/".$row['art']
				);
			}
			    
		    $recent_date = date("F j, Y", strtotime($row['recent_date']));     
		    
		    
		    $release = new Artist(array(
		    "artist_id" => $row['artist_id'],
		    "recent_date" => $recent_date,
		    "artist_art" => $art,
		    "status" => $row['follow_status'],
		    "artist" => $row['name'],
			"unread"=>$row['unread'],
			"total_releases"=>$row['total_releases'],
			"follow_status"=>$row['follow_status']));
		    
		    array_push($results_array,$release);
		    
		}
		
		return $results_array;
	}
}

?>