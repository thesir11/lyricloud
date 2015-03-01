<?php

require_once('assets/libs/genius-api/src/rapgenius.php');
require_once('app/php/cloud.php');

/*
* Checks if artist exists in database
*
* @param string $artist_name name of the artist
* @return bool true if artist exists, false otherwise
*/
function artist_exists($artist_name) {

	$html = file_get_html( 'http://genius.com/artists/' . $artist_name );

	return $html;

}


/*
* Return lyrics from given song
*
* @param Song song object
* @return string lyrics
*/
function getLyricsFromSong($song) {

	$songFetched = lyrics($song["link"]);
	return $songFetched['lyrics'];

}

/*
* Return songs array from given album
*
* @param object album
* @return array array of Song objects
*/

function getSongsFromAlbum($album) {


	$songs = array();

	foreach (tracklist($album["link"]) as $song) {
		$songs[] = new Song($song["title"], $song["artist"], getLyricsFromSong($song));
	}



	return $songs;

}

/*
* Return album array from given artist
*
* @param string $artist_name
* @return array array of albums
*/
function getAlbumsFromArtist($artist_name) {

	return album_list($artist_name);

}


/*
* Return artist object from given artist name
*
* @param string $artist_name
* @return Artist artist object containing all songs. NULL if artist is not in DB
*/
function getArtistFromAPI($artist_name) {

	if (!artist_exists($artist_name)) {
		return NULL;
	}

	$artist = new Artist();

	

	foreach (getAlbumsFromArtist($artist_name) as $album) {

		foreach(getSongsFromAlbum($album) as $song) {
			if (!$artist->hasSong($song->getTitle())) {
				//makes sure repeated songs are not inserted
				$artist->addSong($song);
			}
		}

	}

	return $artist;

}

?>