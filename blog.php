<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <title>Sarathi Cooperative</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
        <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Template Stylesheet -->
        <link href="css/style.css" rel="stylesheet">
        <style>
       .image_slide {
            position: relative;
            max-width: 55%;
            height: 400px;
            background-image: url('img/geetasarathi2.png');
            background-size: cover;
            background-position: top center;
            background-repeat: no-repeat;
           
            margin-left: 25%;
            margin-top: 0px;
        }
        .overlay-text {
            position: absolute;
            top: 50%;
            left: 45%;
            transform: translate(-50%, -50%);
            color: black;
            text-align: center;
            font-size: 2em;
            z-index: 1;
            margin-right: 20px;
            white-space: nowrap;
        }

        .coming-soon-text {
            position: absolute;
            top: 60%;
            left: 45%;
            transform: translateX(-50%);
            color: black;
            font-size: 30px;
            z-index: 1;
            margin-top: 30px;
            font-family: cursive;
            font-style: italic;
        }
       

        </style>
    </head>

    <body>

        <?php  
        include("header.php");
        ?>

<section>
            <div class="image_slide">
                <h2 class="overlay-text"><span style="color:rgba(158,8,68,255)">“सारथी”</span> of Viksit Bharat</h2>
                <p class="coming-soon-text">Sarathi Blogs will be displayed here</p>
            </div>
</section>
       <br> <?php  
       include("footer.php");
       ?>
       <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    </body>

</html>