<!DOCTYPE html>
<html lang="en">
<head>
<?php require($_SERVER['DOCUMENT_ROOT'] . "/head.php"); ?>
</head>
<body>
    <video id="webcam" autoplay></video>
    <script>
        // Function to start the webcam
        async function startWebcam() {
            const video = document.getElementById('webcam');
            try {
                // Request access to the webcam
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                // Set the source of the video element to the webcam stream
                video.srcObject = stream;
            } catch (error) {
                console.error('Error accessing the webcam:', error);
            }
        }

        // Start the webcam when the page loads
        window.onload = startWebcam;
    </script>
</body>
</html>
