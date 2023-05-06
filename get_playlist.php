<?php
require 'error.php';
require 'functions.php';

//Choosing the artist
$playlist_id = 1;

try {
    //connecting to db
    $dbcon = createDbConnection();
    $sql = "SELECT Name, Composer
            FROM tracks
            INNER JOIN playlist_track ON tracks.TrackId = playlist_track.TrackId
            WHERE playlist_track.PlaylistId = :PlaylistId";
    $stmt = $dbcon->prepare($sql);
    $stmt->bindParam(':PlaylistId', $playlist_id);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the results
    $results = array();
    foreach ($rows as $row) {
        $name = $row['Name'];
        $composer = $row['Composer'];

        // Add the track to the results
        $track = array(
            'name' => $name,
            'composer' => $composer
        );
        array_push($results, $track);
    }

    // Output 
    echo "<pre>" . json_encode(array(
        'tracks' => $results
    ), JSON_PRETTY_PRINT) . "</pre>";
} catch (PDOException $pdoex) {
    returnError($pdoex);
}
