# Numu Tracker PHP v2 Backend

This is the v2 backend for Numu Tracker, written in PHP. v1 was very sloppy and this was meant to be a cleaner rebuild with a more optimized database. There are two parts to the backend. One part is dedicated to pulling information into the local database from the Musicbrainz API, and the second part is a REST-like (lazy) API to support the Numu Tracker iOS application.

Built November 2016.

## To Install

1. Import database.sql into your MySQL database.
2. Install composer dependencies (just Pusher)
3. Rename example.database.php to database.php and insert your MySQL details.
4. Open download_lastfm_artists.php, scan_for_album_art.php, and scan_for_artist_art.php and put in your own LastFM API key.
5. Opn push_notifications.php and insert Pusher account info if you want to process notifications.


From there the API won't work until you set up a user, import some artists through the API, and then start running backend.php periodically to create and import artist information from MusicBrainz.

The JSON API accepts a variety of commands and uses basic auth.
