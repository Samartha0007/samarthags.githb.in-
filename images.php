<?php
// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
    $target_dir = "upload/";
    $username = isset($_POST["username"]) ? $_POST["username"] : "anonymous";

    // Loop through each uploaded file
    $num_files = count($_FILES["fileToUpload"]["name"]);
    for ($i = 0; $i < $num_files; $i++) {
        $target_file = $target_dir . $username . "_" . uniqid() . "_" . $i . ".jpg"; // Unique filename based on username and index

        // Process file upload for each file
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($_FILES["fileToUpload"]["name"][$i], PATHINFO_EXTENSION));

        // Check if image file is a valid image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"][$i]);
        if($check === false) {
            $uploadOk = 0;
        }

        // Check file size (max 5MB)
        if ($_FILES["fileToUpload"]["size"][$i] > 5000000) {
            $uploadOk = 0;
        }

        // Allow only certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "<p>Error by Sammu.</p>";
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"][$i], $target_file)) {
                echo "<p>Uploaded by Sammu</p>";
            } else {
                echo "<p>Error</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Images by Sammu</title>
    <style>
        /* CSS styles */
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 90%;
            margin: auto;
            padding: 20px;
            position: relative; /* Required for modal positioning */
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* Adjusted for mobile */
            grid-gap: 20px;
        }

        .image-container {
            position: relative;
            width: 100%; /* Adjusted for mobile */
            height: 300px; /* Adjusted for mobile */
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid black; /* Add black border */
            margin-bottom: 40px;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Ensure the image fills the container */
            display: block;
            transition: transform 0.3s ease; /* Apply transition for zoom effect */
        }

        .username {
            position: absolute;
            bottom: 0;
            left: 0;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 10px; /* Increased padding for better readability */
            font-size: 6px; /* Increased font size for better readability */
            border-radius: 0 0 10px 0;
        }

        /* Plus icon */
        .plus-icon {
            position: fixed;
            bottom: 70px; /* Adjust the distance from the bottom as needed */
            right: 20px;
            background-color: #007bff;
            color: #fff;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .plus-icon:hover {
            background-color: #0056b3;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            position: relative; /* Required for close button positioning */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        /* Footer text */
        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 14px; /* Adjusted font size for mobile */
        }

        /* Adjustments for mobile responsiveness */
        @media screen and (max-width: 768px) {
            .image-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); /* Adjusted column width for tablets */
            }

            .image-container {
                height: 250px; /* Adjusted image container height for tablets */
            }
        }

        @media screen and (max-width: 480px) {
            .plus-icon {
                width: 50px; /* Further adjusted size for smaller mobile screens */
                height: 50px;
                line-height: 50px;
                font-size: 28px;
            }
        }

        /* Modified CSS for full-size images on mobile */
        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100%, 1fr)); /* Adjusted for mobile */
            grid-gap: 10px;
        }

        .image-container {
            width: 100%; /* Adjusted for mobile */
            height: auto; /* Adjusted for mobile */
        }

        .image-container img {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="image-grid">
        <?php
        // Display uploaded images
        $files = glob("upload/*");
        foreach ($files as $file) {
            $username = getUsernameFromFilename($file);
            echo '<div class="image-container">';
            echo '<img src="' . $file . '" alt="Uploaded Image">';
            echo '<div class="username">Uploaded by ' . htmlspecialchars($username) . '</div>';
            echo '</div>';
        }

        function getUsernameFromFilename($filename) {
            $parts = explode("_", basename($filename));
            return reset($parts); // Return the first part of the filename
        }
        ?>
    </div>

    <button id="openModalBtn" class="plus-icon">+</button>
</div>

<!-- Modal -->
<div id="uploadModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="uploadForm" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="username" value="<?php echo isset($_COOKIE["username"]) ? $_COOKIE["username"] : "anonymous"; ?>">
            <input type="file" name="fileToUpload[]" id="fileToUpload" multiple>
            <input type="submit" value="Upload Images" name="submit">
        </form>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("uploadModal");

    // Get the button that opens the modal
    var btn = document.getElementById("openModalBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function shuffleImages() {
        var grid = document.querySelector('.image-grid');
        for (var i = grid.children.length; i >= 0; i--) {
            grid.appendChild(grid.children[Math.random() * i | 0]);
        }
    }

    // Shuffle images when the page loads or refreshes
    window.onload = function() {
        shuffleImages();
    };
</script>
<div class="footer-text">
    Designed by Samartha Gs
</div>

</body>
</html>
