<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Club Management System</title>
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#600000">

    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          navigator.serviceWorker.register('sw.js')
            .then(registration => console.log('SW registered'))
            .catch(err => console.log('SW registration failed: ', err));
        });
      }
    </script>
</head>
<body class="user-body">
    <div class="overlay"></div>