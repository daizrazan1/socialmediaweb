<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posts = json_decode(file_get_contents('data/posts.json'), true);
    $id = time();
    
    if (!empty($_POST['image_url'])) {
        $imgPath = $_POST['image_url'];
    } else {
        $imgPath = "uploads/$id.jpg";
        move_uploaded_file($_FILES['image']['tmp_name'], $imgPath);
    }
    
    array_unshift($posts, [
        "id" => $id,
        "username" => $_SESSION['username'],
        "image" => $imgPath,
        "caption" => $_POST['caption'],
        "likes" => 0,
        "is_private" => isset($_POST['is_private'])
    ]);
    file_put_contents('data/posts.json', json_encode($posts, JSON_PRETTY_PRINT));
    header("Location: profile.php");
    exit;
}
?>
<?php include 'header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-dark text-white">
                <div class="card-header">
                    <h3 class="text-center">Create New Post</h3>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Upload Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Or Image URL</label>
                            <input type="url" name="image_url" class="form-control" placeholder="https://...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Caption</label>
                            <textarea name="caption" class="form-control" rows="3" required placeholder="Write a caption..."></textarea>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_private" class="form-check-input" id="privateCheck">
                                <label class="form-check-label" for="privateCheck">Make this post private</label>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
