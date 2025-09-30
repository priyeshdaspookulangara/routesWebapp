<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: playlists.php");
    exit;
}

require_once '../../src/db.php';
$link = get_db_connection();
$playlist_id = (int)$_GET['id'];

// Fetch playlist details
$sql_playlist = "SELECT * FROM playlists WHERE id = $playlist_id";
$result_playlist = mysqli_query($link, $sql_playlist);
$playlist = mysqli_fetch_assoc($result_playlist);

if (!$playlist) {
    echo "Playlist not found.";
    exit;
}

// Fetch items currently in the playlist, ordered by sort_order
$sql_items = "SELECT pi.id, m.title, m.artist, pi.type
              FROM playlist_items pi
              JOIN mp3_files m ON pi.mp3_id = m.id
              WHERE pi.playlist_id = $playlist_id
              ORDER BY pi.sort_order ASC";
$result_items = mysqli_query($link, $sql_items);
$playlist_items = mysqli_fetch_all($result_items, MYSQLI_ASSOC);

// Fetch available approved MP3s that are NOT in this playlist
$sql_available = "SELECT id, title, artist
                  FROM mp3_files
                  WHERE status = 'Approved' AND id NOT IN (
                      SELECT mp3_id FROM playlist_items WHERE playlist_id = $playlist_id
                  )";
$result_available = mysqli_query($link, $sql_available);
$available_mp3s = mysqli_fetch_all($result_available, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Playlist - <?php echo htmlspecialchars($playlist['name']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        #playlist-items-list { list-style-type: none; margin: 0; padding: 0; }
        #playlist-items-list li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 50px; }
        #playlist-items-list li span { position: absolute; margin-left: -1.3em; }
        .sortable-placeholder { border: 1px dashed #ccc; background: #f8f9fa; height: 50px; }
    </style>
</head>
<body>

<div class="container-fluid mt-5">
    <h3>Editing Playlist: <?php echo htmlspecialchars($playlist['name']); ?></h3>
    <a href="playlists.php" class="btn btn-secondary mb-3">Back to Playlists</a>

    <div class="row">
        <!-- Current Playlist Items -->
        <div class="col-md-6">
            <h4>Playlist Items</h4>
            <div class="card">
                <div class="card-body">
                    <ul id="playlist-items-list" class="sortable">
                        <?php if (empty($playlist_items)): ?>
                            <p>This playlist is empty. Add items from the library on the right.</p>
                        <?php else: ?>
                            <?php foreach ($playlist_items as $item): ?>
                                <li class="ui-state-default" data-item-id="<?php echo $item['id']; ?>">
                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong> (<?php echo htmlspecialchars($item['artist']); ?>)
                                    <span class="badge badge-<?php echo $item['type'] == 'song' ? 'primary' : 'info'; ?>"><?php echo ucfirst($item['type']); ?></span>
                                    <button class="btn btn-danger btn-sm float-right remove-item-btn" data-item-id="<?php echo $item['id']; ?>">&times;</button>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Available MP3s Library -->
        <div class="col-md-6">
            <h4>Available MP3s Library</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Artist</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($available_mp3s as $mp3): ?>
                        <tr id="mp3-<?php echo $mp3['id']; ?>">
                            <td><?php echo htmlspecialchars($mp3['title']); ?></td>
                            <td><?php echo htmlspecialchars($mp3['artist']); ?></td>
                            <td>
                                <button class="btn btn-success btn-sm add-item-btn" data-mp3-id="<?php echo $mp3['id']; ?>" data-type="song">Add as Song</button>
                                <button class="btn btn-warning btn-sm add-item-btn" data-mp3-id="<?php echo $mp3['id']; ?>" data-type="ad">Add as Ad</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    const playlistId = <?php echo $playlist_id; ?>;

    // Make the playlist items sortable
    $(".sortable").sortable({
        placeholder: "sortable-placeholder",
        update: function(event, ui) {
            var item_ids = $(this).sortable('toArray', {attribute: 'data-item-id'});
            $.ajax({
                url: '../../src/reorder_playlist.php',
                type: 'POST',
                data: { playlist_id: playlistId, item_ids: item_ids },
                dataType: 'json',
                success: function(response) {
                    if (!response.success) {
                        alert('Error reordering playlist: ' + response.message);
                    }
                }
            });
        }
    }).disableSelection();

    // Add item to playlist
    $('.add-item-btn').on('click', function() {
        var mp3Id = $(this).data('mp3-id');
        var type = $(this).data('type');

        $.ajax({
            url: '../../src/add_to_playlist.php',
            type: 'POST',
            data: { playlist_id: playlistId, mp3_id: mp3Id, type: type },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            }
        });
    });

    // Remove item from playlist
    $('#playlist-items-list').on('click', '.remove-item-btn', function() {
        if (confirm('Are you sure you want to remove this item from the playlist?')) {
            var itemId = $(this).data('item-id');
            $.ajax({
                url: '../../src/remove_from_playlist.php',
                type: 'POST',
                data: { item_id: itemId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    });
});
</script>

</body>
</html>