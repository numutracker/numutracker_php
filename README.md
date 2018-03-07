# Numu Tracker PHP v2 Backend

This is the v2 backend for Numu Tracker, written in PHP. v1 was very sloppy and this was meant to be a cleaner rebuild with a more optimized database. There are two parts to the backend. One part is dedicated to pulling information into the local database from the Musicbrainz API, and the second part is a REST-like (lazy) API to support the Numu Tracker iOS application.

Built November 2016.

It is meant to support the iOS app, available here: https://itunes.apple.com/us/app/numu-new-music-tracker/id1158641228

New version built in Django with more improvements is in development, follow along at https://github.com/amiantos/numutracker_django

## To Install

1. Import database.sql into your MySQL database.
2. Install composer dependencies (just Pusher)
3. Rename example.database.php to database.php and insert your MySQL details.
4. Open download_lastfm_artists.php, scan_for_album_art.php, and scan_for_artist_art.php and put in your own LastFM API key.
5. Opn push_notifications.php and insert Pusher account info if you want to process notifications.


From there the API won't work until you set up a user, import some artists through the API, and then start running backend.php periodically to create and import artist information from MusicBrainz.

The JSON API accepts a variety of commands, uses basic auth, and is accessible via json.php. Here's some examples of queries it can return.

**Recent Releases**  
https://www.numutracker.com/v2/json.php?page=1&rel_mode=all&limit=20&offset=0

**Upcoming Releases**  
https://www.numutracker.com/v2/json.php?page=1&rel_mode=allupcoming&limit=20&offset=0

**User's Unlistened Releases**  
https://www.numutracker.com/v2/json.php?user=test@test.com&rel_mode=unlistened&page=1&limit=20&offset=0

**User's Upcoming Releases**  
https://www.numutracker.com/v2/json.php?user=test@test.com&rel_mode=upcoming&page=1&limit=20&offset=0

