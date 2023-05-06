<?php
require 'functions.php';

// Set the artist ID
$artist_id = 5;

try {
    // Connect to the database
    $dbcon = createDbConnection();
    $dbcon->beginTransaction();

    // Get the artist's name
    $stmt = $dbcon->prepare('SELECT name FROM artists WHERE ArtistId = :ArtistId');
    $stmt->bindParam(':ArtistId', $artist_id);
    $stmt->execute();
    $artist_name = $stmt->fetchColumn();

    // Delete playlist tracks associated with the artist
    $stmt = $dbcon->prepare('DELETE FROM playlist_track
        WHERE TrackId IN (
            SELECT tracks.TrackId FROM tracks
            JOIN albums ON tracks.AlbumId = albums.AlbumId
            WHERE albums.ArtistId = :ArtistId
        )');
    $stmt->bindParam(':ArtistId', $artist_id);
    $stmt->execute();

    // Delete invoice items associated with the artist's tracks
    $stmt = $dbcon->prepare('DELETE FROM invoice_items
        WHERE TrackId IN (
            SELECT tracks.TrackId FROM tracks
            JOIN albums ON tracks.AlbumId = albums.AlbumId
            WHERE albums.ArtistId = :ArtistId
        )');
    $stmt->bindParam(':ArtistId', $artist_id);
    $stmt->execute();

    // Delete the artist's tracks
    $stmt = $dbcon->prepare('DELETE FROM tracks
        WHERE AlbumId IN (
            SELECT albums.AlbumId FROM albums
            WHERE albums.ArtistId = :ArtistId
        )');
    $stmt->bindParam(':ArtistId', $artist_id);
    $stmt->execute();

    // Delete the artist's albums
    $stmt = $dbcon->prepare('DELETE FROM albums WHERE ArtistId = :ArtistId');
    $stmt->bindParam(':ArtistId', $artist_id);
    $stmt->execute();

    // Delete the artist
    $stmt = $dbcon->prepare('DELETE FROM artists WHERE ArtistId = :ArtistId');
    $stmt->bindParam(':ArtistId', $artist_id);
    $stmt->execute();

    // Commit the transaction
    $dbcon->commit();

} catch (PDOException $pdoex) {
    returnError($pdoex);
}
