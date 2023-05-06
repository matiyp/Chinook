<?php
// Include necessary files
require_once "error.php";
require_once "functions.php";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(array('error' => 'Method not allowed'));
    exit();
}

// Get the request data
$request_body = file_get_contents('php://input');
$data = json_decode($request_body, true);

// Check if artist, album, and tracks are defined in the request data
if (!isset($data['artist']) || !isset($data['album']) || !isset($data['tracks'])) {
    http_response_code(400);
    echo json_encode(array('error' => 'Artist info not defined'));
    exit();
}

// Extract artist, album, and tracks from the request data
$artist = $data['artist'];
$album = $data['album'];
$tracks = $data['tracks'];

try {
    // Create a database connection
    $dbcon = createDbConnection();

    // Start a transaction
    $dbcon->beginTransaction();

    // Insert the artist
    $stmt = $dbcon->prepare("INSERT INTO artists (Name) VALUES (:Name)");
    $stmt->bindParam(':Name', $artist['name']);
    $stmt->execute();
    $artistId = $dbcon->lastInsertId();

    // Insert the album
    $stmt = $dbcon->prepare("INSERT INTO albums (ArtistId, Title) VALUES (:ArtistId, :Title)");
    $stmt->bindParam(':ArtistId', $artistId);
    $stmt->bindParam(':Title', $album['title']);
    $stmt->execute();
    $albumId = $dbcon->lastInsertId();

    // Insert the tracks
    foreach ($tracks as $track) {
        $stmt = $dbcon->prepare("INSERT INTO tracks (AlbumId, Name, MediaTypeID, Milliseconds, UnitPrice) VALUES (:AlbumId, :Name, 1, 345932, 0.99)");
        $stmt->bindParam(':AlbumId', $albumId);
        $stmt->bindParam(':Name', $track['name']);
        $stmt->execute();
    }

    // Commit the transaction
    $dbcon->commit();

    // Output a success message
    http_response_code(200);
    echo json_encode(array('message' => 'Artist, album and tracks added successfully'));
} catch (PDOException $pdoex) {
    // Roll back the transaction in case of an error
    $dbcon->rollback();
    // Call the returnError function to handle the error
    returnError($pdoex);
}
