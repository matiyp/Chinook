<?php

require_once "error.php";
require_once "functions.php";

$artistId = 1;

try {
    $dbcon = createDbConnection();
    $sql = "SELECT artists.Name, albums.Title, tracks.Name
            FROM artists
            LEFT JOIN albums ON artists.ArtistId = albums.ArtistId
            LEFT JOIN tracks ON albums.AlbumId = tracks.AlbumId
            WHERE artists.ArtistId = :ArtistId
            ORDER BY albums.Title, tracks.Name";

    $stmt = $dbcon->prepare($sql);
    $stmt->bindParam(':ArtistId', $artistId);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the results as nested arrays
    $results = [];
    foreach ($rows as $row) {
        $album = $row['Title'];
        $track = $row['Name'];

        // Add the album to the results
        if (!isset($results[$album])) {
            $results[$album] = [
                'title' => $album,
                'tracks' => []
            ];
        }

        // Add the track to the album
        if ($track) {
            array_push($results[$album]['tracks'], $track);
        }
    }

    // Output 
    $output = [
        'artist_name' => $rows[0]['Name'],
        'albums' => array_values($results)
    ];

    echo "<pre>" . json_encode($output, JSON_PRETTY_PRINT) . "</pre>";
} catch (PDOException $pdoex) {
    returnError($pdoex);
}
