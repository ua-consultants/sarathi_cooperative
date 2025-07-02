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
        <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
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
        <link href="css/styles.css" rel="stylesheet">
        <link rel="icon" href="img/logo-favi-icon.png">
  <style>
    .heading_text {
    font-size: 52px;
    font-weight: bold;
    color: #2800bb !important;
    font-family: 'mck-icons';
}
.header-carousel .owl-nav {
    position: absolute;
    top: 50%;
    width: 100%;
    transform: translateY(-50%);
    display: flex;
    justify-content: space-between;
    padding: 0 20px;
}

.header-carousel .owl-nav button {
    background: rgba(255,255,255,0.3) !important;
    color: white !important;
    border: none !important;
    border-radius: 50% !important;
    width: 50px;
    height: 50px;
    font-size: 18px;
}

.header-carousel .owl-nav button:hover {
    background: rgba(255,255,255,0.5) !important;
}

.header-carousel .owl-dots {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
}

/* Image overlay text positioning */
/*.image-text-overlay {*/
/*    position: absolute;*/
/*    bottom: 20px;*/
/*    left: 20px;*/
/*    z-index: 10;*/
/*}*/
  </style>
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Sarathi Cooperative",
      "alternateName": [
        "Sarathi Research Consulting and Management Services",
        "Sarathi Research Services",
        "Sarathi Consulting Services",
        "Sarathi Marketing Services",
        "Sarathi Services",
        "Sarathi Research and Marketing Services",
        "Sarathi Research and Consulting Services",
        "Sarathi Consulting and Marketing Services",
        "Research and Consulting Services",
        "Marketing Services",
        "Marketing and Consulting Services",
        "Sarathi Consultants",
        "Sarathi Consultancy"
      ],
      "url": "https://sarathicooperative.org"
    }
</script>

    </head>

    <body>

       <?php   
       include("header.php");
       $conn = new mysqli('localhost', 'u828878874_sarathi_new', '#Sarathi@2025', 'u828878874_sarathi_db');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
       ?>
        <!-- Carousel Start -->
        <div class="header-carousel owl-carousel">
            <div class="header-carousel-item">
                <div class="header-carousel-item-img-1">
                    <img src="discarded/static/media/birla-anayu.981fdfd41bf7d3f5ffe4.jpg" class="img-fluid w-100" style="height:100vh;" alt="Image">
                </div>
                <div class="carousel-caption">
                    <div class="carousel-caption-inner text-start p-3">
                        <!-- <h1 class="display-1 text-capitalize text-white mb-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.3s" style="animation-delay: 1.3s;">The most prestigious Investments company in worldwide.</h1> -->
                        <p class="mb-5 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.5s;justify-content:center;color:white">A legacy is built not just by preserving the past, but by renewing it with relevance for the future.</p>
                        <p class="mb-3 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.2s;text-align: center;color:white"> "Kumar Mangalam Birla"  </p>
                        <!-- <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;" href="#">Apply Now</a>
                        <a class="btn btn-dark rounded-pill py-3 px-5 mb-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;" href="#">Read More</a> -->
                    </div>
                </div>
            </div>
            <div class="header-carousel-item mx-auto">
                <div class="header-carousel-item-img-2">
                    <img src="discarded/static/media/mahakumbh-2025.ad29a63073cfe9f0956f.jpg" class="img-fluid w-100" style="height:100vh;" alt="Image">
                </div>
                <div class="carousel-caption">
                    <div class="carousel-caption-inner text-center p-2">
                        <!-- <h1 class="display-1 text-capitalize text-white mb-4">The most prestigious Investments company in worldwide.</h1> -->
                        <p class="mb-5 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.5s;justify-content:center;text-align: center;color:white">You may never know what results come of your actions. But if you do nothing, there will be no result.</p>
                        <p class="mb-3 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.2s;text-align: center;color:white"> "Mahatma Gandhi"  </p>
                        <!-- <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4" href="#">Apply Now</a>
                        <a class="btn btn-dark rounded-pill py-3 px-5 mb-4" href="#">Read More</a> -->
                    </div>
                </div>
            </div>
            <div class="header-carousel-item">
                <div class="header-carousel-item-img-3">
                    <img src="discarded/static/media/C295.fecb8b081324f63e2e43.jpg" class="img-fluid w-100" style="height:100vh;"  alt="Image">
                </div>
                <div class="carousel-caption">
                    <div class="carousel-caption-inner text-end p-2">
                        <!-- <h1 class="display-1 text-capitalize text-white mb-4">The most prestigious Investments company in worldwide.</h1> -->
                        <p class="mb-5 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.5s;justify-content:left;text-align: left;color:white">If you want to walk fast, walk alone. But if you want to walk far, walk together.</p>
                        <p class="mb-3 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.2s;text-align: center;color:white"> "Ratan Tata"  </p>
                        <!-- <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4" href="#">Apply Now</a>
                        <a class="btn btn-dark rounded-pill py-3 px-5 mb-4" href="#">Read More</a> -->
                    </div>
                </div>
            </div>
            <!--   -->
            <div class="header-carousel-item">
                <div class="header-carousel-item-img-1">
                    <img src="discarded/static/media/agriculture.f3ff4a5e0fa6de695cb1.jpg" class="img-fluid w-100" style="height:100vh;"  alt="Image">
                </div>
                <div class="carousel-caption">
                    <div class="carousel-caption-inner text-start p-2">
                        <!-- <h1 class="display-1 text-capitalize text-white mb-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.3s" style="animation-delay: 1.3s;">The most prestigious Investments company in worldwide.</h1> -->
                        <p class="mb-5 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.5s;justify-content:justify;text-align:center;color:white">Excellence is a continuous process and not an accident.
  </p>
                        <p class="mb-3 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.2s;text-align: center;color:white"> "Dr APJ Abdul Kalam"  </p>
                        <!-- <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;" href="#">Apply Now</a>
                        <a class="btn btn-dark rounded-pill py-3 px-5 mb-4 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.7s;" href="#">Read More</a> -->
                    </div>
                </div>
            </div>
            <div class="header-carousel-item mx-auto">
                <div class="header-carousel-item-img-2">
                    <img src="discarded/static/media/atal-setu.e09ba17e7c2a835554e8.jpg" class="img-fluid w-100" style="height:100vh;"  alt="Image">
                </div>
                <div class="carousel-caption">
                    <div class="carousel-caption-inner text-center p-2">
                        <!-- <h1 class="display-1 text-capitalize text-white mb-4">The most prestigious Investments company in worldwide.</h1> -->
                        <p class="mb-5 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.5s;justify-content:center;text-align:center ;color:white"> Spirituality teaches you to look beyond the numbers. It shows you the value of impact, not just profit.</p>
                             <p class="mb-3 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.2s;text-align: center;color:white">"Azim Premji"  </p>
                        <!-- <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4" href="#">Apply Now</a>
                        <a class="btn btn-dark rounded-pill py-3 px-5 mb-4" href="#">Read More</a> -->
                    </div>
                </div>
            </div>
            <div class="header-carousel-item">
                <div class="header-carousel-item-img-3">
                    <img src="discarded/static/media/chadrayaan.0b8d690fe8fb4ff1ba8b.jpg" class="img-fluid w-100"style="height:100vh;"   alt="Image">
                </div>
                <div class="carousel-caption">
                    <div class="carousel-caption-inner text-end p-2">
                        <!-- <h1 class="display-1 text-capitalize text-white mb-4">The most prestigious Investments company in worldwide.</h1> -->
                        <p class="mb-5 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.5s;justify-content:center;text-align: center;color:white">Success is not final, failure is not fatal: it is the courage to continue that counts</p>
                        <p class="mb-3 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.2s;text-align: center;color:white">  "Azim Premji"  </p>
                        <!-- <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4" href="#">Apply Now</a>
                        <a class="btn btn-dark rounded-pill py-3 px-5 mb-4" href="#">Read More</a> -->
                    </div>
                </div>
            </div>
            <div class="header-carousel-item">
                <div class="header-carousel-item-img-3">
                    <img src="discarded/static/media/officeemployee.67bd281824228ad6725a.jpg" class="img-fluid w-100"style="height:100vh;"   alt="Image">
                </div>
                <div class="carousel-caption">
                    <div class="carousel-caption-inner text-end p-2">
                        <!-- <h1 class="display-1 text-capitalize text-white mb-4">The most prestigious Investments company in worldwide.</h1> -->
                        <p class="mb-5 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.5s;justify-content:center;text-align: center;color:white">Learning is a constant process, and growth comes from how open you are to it.</p>
                        <p class="mb-3 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.2s;text-align: center;color:white"> "Shiv Nadar"  </p>
                        <!-- <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4" href="#">Apply Now</a>
                        <a class="btn btn-dark rounded-pill py-3 px-5 mb-4" href="#">Read More</a> -->
                    </div>
                </div>
            </div>
            <div class="header-carousel-item">
                <div class="header-carousel-item-img-3">
                    <img src="discarded/static/media/unakoti.d74e9962c9c23435a3cb.jpg" class="img-fluid w-100"style="height:100vh;"   alt="Image">
                </div>
                <div class="carousel-caption">
                    <div class="carousel-caption-inner text-end p-2">
                        <!-- <h1 class="display-1 text-capitalize text-white mb-4">The most prestigious Investments company in worldwide.</h1> -->
                        <p class="mb-5 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.5s;justify-content:center;text-align: center;color:white">Heritage is not about buildings and names alone; it’s about values that endure across generations.</p>
                        <p class="mb-3 fs-2 fadeInUp animate__animated" data-animation="fadeInUp" data-delay="1.5s" style="animation-delay: 1.2s;text-align: center;color:white"> "Ratan Tata"  </p>
                        <!-- <a class="btn btn-primary rounded-pill py-3 px-5 mb-4 me-4" href="#">Apply Now</a>
                        <a class="btn btn-dark rounded-pill py-3 px-5 mb-4" href="#">Read More</a> -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Carousel End -->


        <!-- About Start -->
<div class="container-fluid about bg-light py-2">
            <div class="container ">
                <div class="row g-5 align-items-center m-0">
                <div class="col-lg-6 col-xl-5 wow fadeInLeft" data-wow-delay="0.1s">
                        <div class="about-img position-relative" style="margin-left: 8px;">
                            <!--<img src="img/about-3.png" class="img-fluid w-100 rounded-top bg-white" alt="Image">-->
                            <img src="img/geetasarathi2.png" class="img-fluid w-100 rounded-bottom" alt="Image" style="height:320px;max-width:450px;margin-top: 35px;">
                            
                            <!-- Text overlay on the image -->
                            <div class="image-text-overlay">
                                <h2 style="color:brown; font-family:cursive; font-style:italic;">
                                    “ सारथी ” <span style="color:black; font-style:italic; font-family:cursive;"> of Viksit Bharat</span>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-xl-7 wow fadeInRight m-0" data-wow-delay="0.3s">
                        <h2 class="heading_text">About Us</h2>
                        <h4 class="mb-4">The most Profitable Investments company in worldwide.</h4>
                        <p class="text ps-4 mb-4">Sarathi is building a future-focused organization by enhancing agility, connectivity, and investing in technology and talent to provide trusted advice and innovative solutions.
                        </p>
                        <div class="row g-4 justify-content-between mb-5">
                            <div class="col-lg-6 col-xl-5">
                                <p class="text-dark"><i class="fas fa-check-circle text-primary me-1"></i> Strategy & Consulting</p>
                                <p class="text-dark mb-0"><i class="fas fa-check-circle text-primary me-1"></i> Business Process</p>
                            </div>
                            <div class="col-lg-6 col-xl-7">
                                <p class="text-dark"><i class="fas fa-check-circle text-primary me-1"></i> Marketing Rules</p>
                                <p class="text-dark mb-0"><i class="fas fa-check-circle text-primary me-1"></i> Partnerships</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <center class="impact">   
        <div class="row g-4 text-center align-items-center justify-content-center container" style="margin-top:15px !important; margin-left: 105px;">
           <h2 class="heading_text">Our Impact</h2>
            <div class="col-sm-4 column-divider">
                <div class="rounded p-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="counter-value fs-3 fw-bold text-primary" style="font-size:12px;" data-toggle="counter-up">21</span>
                        <h4 class="text-primary fs-1 mb-0" style="font-weight: 300; font-size: 12px;">+</h4>
                    </div>
                    <div class="w-100 d-flex align-items-center justify-content-center">
                        <p class="text-dark mb-0">Categories Expert</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 column-divider">
                <div class="rounded p-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="counter-value fs-3 fw-bold text-dark" data-toggle="counter-up">12</span>
                        <h4 class="text-dark fs-3 mb-0" style="font-weight: 300; font-size: 12px;">k+</h4>
                    </div>
                    <div class="w-100 d-flex align-items-center justify-content-center">
                        <p class="text-secondary mb-0">Collaborative Experienced</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="rounded p-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <span class="counter-value fs-3 fw-bold text-primary" data-toggle="counter-up">106</span>
                        <h4 class="text-primary fs-1 mb-0" style="font-weight: 300; font-size: 12px;">+</h4>
                    </div>
                    <div class="w-100 d-flex align-items-center justify-content-center">
                        <p class="text-dark mb-0">Sarathi Experts</p>
                    </div>
                </div>
            </div>
        </div>
        </center>
        </div>
</div>
        <!-- About End -->
        <!-- Announcements Start -->
<div class="container-fluid announcements py-2">
    <div class="container py-5">
        <div class="text-center mx-auto pb-3 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px; margin-top: -25px">
            <h2 class="heading_text">Sarathi News</h2>
            <p class="mb-0">Stay updated with our latest news, events, and important information</p>
        </div>
        
        <div class="row g-5">
            <!-- Announcements Grid Display (Left Side) -->
            <div class="col-lg-8 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="announcements-grid-container bg-light rounded p-4" style="min-height: 630px;">
                    
                    <!-- Grid Header -->
                    <div class="announcements-header d-flex justify-content-between align-items-center mb-4">
                        <!--<div class="grid-info">-->
                        <!--    <h5 class="mb-0 text-primary">Recent Announcements</h5>-->
                        <!--    <small class="text-muted">Click on any card to view details</small>-->
                        <!--</div>-->
                        <div class="announcements-count">
                            <span class="badge bg-primary" id="announcementsCount"></span>
                            <span class="text-muted"></span>
                        </div>
                    </div>

                    <!-- Announcements Grid -->
                    <div class="announcements-grid" id="announcementsGrid">
                        <!-- Dynamic content will be loaded here -->
                    </div>
                </div>
            </div>
            
            <!-- Calendar and Subscribe (Right Side) -->
            <div class="col-lg-4 wow fadeInRight" data-wow-delay="0.3s">
                <!-- Calendar -->
                <div class="calendar-container bg-white rounded shadow-sm p-4 mb-4">
                    <div id="announcement-calendar"></div>
                </div>
                
                <!-- Subscribe Section -->
                <div class="subscribe-container bg-white rounded shadow-sm p-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-bell text-primary fs-2 mb-2"></i>
                        <h5 style="color: #2800bb;">Stay Updated</h5>
                        <p class="text-muted mb-0">Subscribe to receive email notifications about new announcements</p>
                    </div>
                    
                    <form id="subscribeForm" class="mt-3">
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" id="subscriberEmail" placeholder="Enter your email" required>
                            <button class="btn btn-primary" type="submit" id="subscribeBtn">
                                <i class="fas fa-paper-plane me-1"></i>Subscribe
                            </button>
                        </div>
                    </form>
                    
                    <div id="subscribeMessage" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcement Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #2800bb;">
                <h5 class="modal-title text-white" id="announcementModalLabel">Announcement Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Calendar Date Details Modal -->
<div class="modal fade" id="calendarEventModal" tabindex="-1" aria-labelledby="calendarEventModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #2800bb, #4a20d6); padding: 0.9rem;">
                <h5 class="modal-title text-white fw-bold" id="calendarEventModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Events Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="calendarModalContent">
                <!-- Content will be dynamically loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Interest Registration Modal -->
<div class="modal fade" id="interestModal" tabindex="-1" aria-labelledby="interestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <h5 class="modal-title text-white fw-bold" id="interestModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Register Your Interest
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-handshake text-success fs-1 mb-3"></i>
                    <h4 class="text-success mb-2">Join Our Next Meeting!</h4>
                    <p class="text-muted">Please provide your contact details to receive seat confirmation for the upcoming meeting.</p>
                </div>
                
                <form id="interestForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="interestEmail" class="form-label fw-semibold">
                                <i class="fas fa-envelope text-primary me-2"></i>Email Address *
                            </label>
                            <input type="email" class="form-control form-control-lg" id="interestEmail" required 
                                   placeholder="your.email@example.com">
                        </div>
                        <div class="col-md-6">
                            <label for="interestPhone" class="form-label fw-semibold">
                                <i class="fas fa-phone text-primary me-2"></i>Phone Number *
                            </label>
                            <input type="tel" class="form-control form-control-lg" id="interestPhone" required 
                                   placeholder="+91 9876543210">
                        </div>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <button type="submit" class="btn btn-success btn-lg px-5" id="registerInterestBtn">
                            <i class="fas fa-paper-plane me-2"></i>Register Interest
                        </button>
                    </div>
                </form>
                
                <div id="interestMessage" class="mt-3" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Custom CSS -->
<style>
.announcements-grid-container {
    border: 1px solid #e9ecef;
}

.announcements-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    min-height: 480px;
}

.announcement-card {
    background: white;
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid transparent;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 180px;
    position: relative;
    overflow: hidden;
}

.announcement-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #2800bb, #4a20d6);
}

.announcement-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(40, 0, 187, 0.15);
    border-color: #2800bb;
}

.announcement-card:hover .card-title {
    color: #2800bb;
}
.announcements-count {
    display: none;
}

.card-title {
    color: #333;
    font-weight: 600;
    font-size: 1rem;
    line-height: 1.4;
    margin-bottom: 15px;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
    flex-grow: 1;
}

.card-date {
    display: flex;
    align-items: center;
    color: #666;
    font-size: 0.9rem;
    margin-top: auto;
    padding-top: 10px;
    border-top: 1px solid #f0f0f0;
}

.card-date i {
    margin-right: 8px;
    color: #2800bb;
    font-size: 0.85rem;
}

.no-announcements {
    grid-column: 1 / -1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 400px;
    text-align: center;
    color: #999;
}

.no-announcements i {
    font-size: 2rem;
    margin-bottom: 10px;
    opacity: 0.5;
}

.announcements-header h5 {
    color: #2800bb !important;
}

/* Calendar styles */
.calendar-container {
    border: 1px solid #e9ecef;
}

.subscribe-container {
    margin-top: 45px;
    border: 1px solid #e9ecef;
    background-image: url('img/logo.png');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    position: relative;
}

.subscribe-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: inherit;
}

.subscribe-container > * {
    position: relative;
    z-index: 1;
}

.calendar-day {
    padding-top: calc(100% * 1 / 7); /* Square cells based on available width */
    position: relative;
    font-size: 1.1rem;
    color: #555;
    cursor: default;
}

.calendar-day:hover {
    background-color: #f8f9fa;
}

.calendar-day.has-event {
    background-color: pink;
    color: white;
    font-weight: bold;
}

.calendar-day.has-event:hover {
    background-color: #1e0088;
}

.calendar-day.has-fixed-event {
    background-color: #28a745;
    color: white;
    font-weight: bold;
}

.calendar-day.has-fixed-event:hover {
    background-color: #218838;
}

.calendar-day.has-multiple-events {
    background: linear-gradient(45deg, #2800bb 50%, #28a745 50%);
    color: white;
    font-weight: bold;
}

.calendar-day.has-multiple-events:hover {
    background: linear-gradient(45deg, #1e0088 50%, #218838 50%);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.calendar-nav-btn {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #2800bb;
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.calendar-nav-btn:hover {
    background-color: #f8f9fa;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    text-align: center;
}

.calendar-weekday {
    font-weight: bold;
    color: #666;
    padding: 8px 0;
    font-size: 0.9rem;
}

.event-item {
    background: #f8f9fa;
    border-left: 4px solid #2800bb;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 0 8px 8px 0;
}

.event-item.fixed-event {
    border-left-color: #28a745;
}

.event-item h6 {
    color: #2800bb;
    margin-bottom: 8px;
}

.event-item.fixed-event h6 {
    color: #28a745;
}

.event-badge {
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 12px;
    margin-right: 8px;
}

.announcement-badge {
    background-color: #2800bb;
    color: white;
}

.fixed-event-badge {
    background-color: #28a745;
    color: white;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .announcements-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 991px) {
    .announcements-grid-container {
        min-height: 500px;
        margin-bottom: 30px;
    }
    
    .announcements-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}

@media (max-width: 768px) {
    .announcements-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .announcement-card {
        padding: 10px;
        min-height: 150px;
    }
    
    .card-title {
        font-size: 1rem;
    }
}

@media (max-width: 576px) {
    .announcement-card {
        padding: 10px;
    }
    
    .card-title {
        font-size: 0.95rem;
    }
}
.event-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    border: none;
}

.date-section {
    background: linear-gradient(135deg, #2800bb, #4a20d6);
    color: white;
    padding: 1rem;
    text-align: center;
    position: relative;
}

.date-section::after {
    content: '';
    position: absolute;
    right: -10px;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-left: 15px solid #4a20d6;
    border-top: 15px solid transparent;
    border-bottom: 15px solid transparent;
}

.date-month {
    font-size: 1.2rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
    opacity: 0.9;
}

.date-day {
    font-size: 1.9rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.date-year {
    font-size: 1rem;
    font-weight: 500;
    opacity: 0.8;
}

.content-section {
    padding: 0.7rem;
    flex: 1;
}

.event-title {
    color: #2800bb;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.event-content {
    color: #555;
    line-height: 1.;
    margin-bottom: 1.5rem;
}

.meeting-details {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.1rem;
    margin: 1.1rem 0;
    border-left: 4px solid #28a745;
}

.meeting-detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.8rem;
}

.meeting-detail-item:last-child {
    margin-bottom: 0;
}

.meeting-detail-item i {
    width: 20px;
    color: #28a745;
    margin-right: 0.8rem;
}

.cta-button {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    padding: 0.8rem 1.2rem;
    border-radius: 50px;
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.cta-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    color: white;
}

.fixed-event-card {
    border-left: 5px solid #28a745;
}

.announcement-card {
    border-left: 5px solid #2800bb;
}

@media (max-width: 768px) {
    .date-section::after {
        display: none;
    }
    
    .date-day {
        font-size: 2.5rem;
    }
    
    .content-section {
        padding: 1.5rem;
    }
    
    .event-title {
        font-size: 1.3rem;
    }
}
</style>

<!-- JavaScript -->

<script>
// Fetch announcements from database (using your existing PHP structure)
let announcements = [
    <?php
    // Fetch announcements from database
    $sql = "SELECT id, title, content, media_type, media_url, created_at, end_date FROM announcements WHERE status = 'active' ORDER BY created_at DESC LIMIT 6";
    $result = $conn->query($sql);
    
    $announcements_array = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $announcements_array[] = $row;
        }
    }
    
    // Convert to JavaScript array
    foreach ($announcements_array as $index => $announcement) {
        echo json_encode($announcement);
        if ($index < count($announcements_array) - 1) {
            echo ',';
        }
    }
    ?>
];

// Initialize announcements grid
function initializeAnnouncementsGrid() {
    const gridContainer = document.getElementById('announcementsGrid');
    const countElement = document.getElementById('announcementsCount');
    
    countElement.textContent = announcements.length;
    
    if (announcements.length === 0) {
        gridContainer.innerHTML = `
            <div class="no-announcements">
                <i class="fas fa-info-circle"></i>
                <h5>No announcements available</h5>
                <p>Check back later for updates!</p>
            </div>
        `;
        return;
    }
    
    createAnnouncementCards();
}

function createAnnouncementCards() {
    const gridContainer = document.getElementById('announcementsGrid');
    gridContainer.innerHTML = '';
    
    announcements.forEach((announcement, index) => {
        const card = document.createElement('div');
        card.className = 'announcement-card';
        card.setAttribute('data-id', announcement.id);
        card.setAttribute('data-bs-toggle', 'modal');
        card.setAttribute('data-bs-target', '#announcementModal');
        
        const endDate = new Date(announcement.end_date);
        const formattedDate = endDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        });
        
        card.innerHTML = `
            <div class="card-title">${announcement.title}</div>
            <div class="card-date">
                <i class="fas fa-calendar-alt"></i>
                ${formattedDate}
            </div>
        `;
        
        // Add click event listener
        card.addEventListener('click', function() {
            showAnnouncementModal(announcement.id);
        });
        
        gridContainer.appendChild(card);
    });
}

// Show announcement modal with full details
function showAnnouncementModal(announcementId) {
    fetch('get_announcement.php?id=' + announcementId)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const announcement = data.announcement;
            let modalContent = `
                <div class="announcement-detail">
                    <div class="mb-3">
                        <span class="badge bg-primary">${new Date(announcement.created_at).toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        })}</span>
                    </div>
            `;
            
            if (announcement.media_type === 'image' && announcement.media_url) {
                modalContent += `
                    <div class="mb-4">
                        <img src="${announcement.media_url}" class="img-fluid rounded w-100" style="max-height: 380px; object-fit: cover;" alt="Announcement Image">
                    </div>
                `;
            }
            
            modalContent += `
                    <h4 class="mb-1.5" style="color: #2800bb;">${announcement.title}</h4>
                    <div class="announcement-full-content">
                        <p style="line-height: 1.6; white-space: pre-wrap;">${announcement.content}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('modalContent').innerHTML = modalContent;
            document.getElementById('announcementModalLabel').textContent = announcement.title;
        }
    })
    .catch(error => {
        document.getElementById('modalContent').innerHTML = '<div class="alert alert-danger">Error loading announcement details.</div>';
    });
}

// Calendar functionality with enhanced features
let currentDate = new Date();
let announcementDates = [];

// Fetch announcement dates from database (using your existing PHP structure)
<?php
$date_sql = "SELECT DISTINCT end_date as announcement_date FROM announcements WHERE status = 'active'";
$date_result = $conn->query($date_sql);
$dates = [];
if ($date_result->num_rows > 0) {
    while($date_row = $date_result->fetch_assoc()) {
        $dates[] = $date_row['announcement_date'];
    }
}
echo "announcementDates = " . json_encode($dates) . ";";
?>

// Function to get the nth weekday of a month
function getNthWeekdayOfMonth(year, month, weekday, n) {
    const firstDay = new Date(year, month, 1);
    const firstWeekday = firstDay.getDay();
    let daysToAdd = (weekday - firstWeekday + 7) % 7;
    daysToAdd += (n - 1) * 7;
    return new Date(year, month, 1 + daysToAdd);
}

// Function to get the last weekday of a month
function getLastWeekdayOfMonth(year, month, weekday) {
    const lastDay = new Date(year, month + 1, 0);
    const lastWeekday = lastDay.getDay();
    let daysToSubtract = (lastWeekday - weekday + 7) % 7;
    return new Date(year, month + 1, 0 - daysToSubtract);
}

// Function to check if it's the last month of a quarter
function isLastMonthOfQuarter(month) {
    return month === 2 || month === 5 || month === 8 || month === 11; // March, June, September, December
}

// Function to get fixed events for a specific date
function getFixedEventsForDate(date) {
    const events = [];
    const year = date.getFullYear();
    const month = date.getMonth(); // 0-based (0 = January, ..., 5 = June)
    const day = date.getDate();
    const dayOfWeek = date.getDay();

    // Special case: June 2025
    if (year === 2025 && month === 5) { // month is 0-based, so 5 = June
        if (day === 22 && (dayOfWeek === 6 || dayOfWeek === 0)) {
            // Add BOD Meeting
            events.push({
                title: 'Board of Directors Meeting',
                type: 'BOD Meeting',
                description: 'Monthly Board of Directors meeting for cooperative governance and decision making.'
            });

            // Add Online Webinar
            events.push({
                title: 'Monthly Online Webinar',
                type: 'Webinar',
                description: 'Members are encouraged to engage and Participate in Sarathi Cooperative Progress as accomplished in past quarter, as well as discuss-plan the activities of next quarter.'
            });
        }
    } else {
        // Regular logic for other months/years

        // 2nd Saturday of every month - BOD Meet
        const secondSaturday = getNthWeekdayOfMonth(year, month, 6, 2);
        if (day === secondSaturday.getDate() && dayOfWeek === 6) {
            events.push({
                title: 'Board of Directors Meeting',
                type: 'BOD Meeting',
                description: 'Monthly Board of Directors meeting for cooperative governance and decision making.'
            });
        }

        // Last Saturday of every month - Online Webinar
        const lastSaturday = getLastWeekdayOfMonth(year, month, 6);
        if (day === lastSaturday.getDate() && dayOfWeek === 6) {
            events.push({
                title: 'Monthly Online Webinar',
                type: 'Webinar',
                description: 'Members are encouraged to engage and Participate in Sarathi Cooperative Progress as accomplished in past quarter, as well as discuss-plan the activities of next quarter.'
            });
        }
    }

    // Quarterly General Body Meeting (last month of each quarter)
    if (!isLastMonthOfQuarter(month) || (year === 2025 && month === 5)) {
        // Skip general body meet in June 2025 if needed, or apply normally
    }

    if (isLastMonthOfQuarter(month)) {
        const secondSunday = getNthWeekdayOfMonth(year, month, 0, 2);
        if (day === secondSunday.getDate() && dayOfWeek === 0) {
            events.push({
                title: 'Quarterly General Body Meeting',
                type: 'General Body Meeting',
                description: 'Quarterly General Body meeting for all members to discuss cooperative affairs and progress.'
            });
        }
    }

    return events;
}
// Function to get announcements for a specific date
function getAnnouncementsForDate(dateString) {
    return announcements.filter(announcement => {
        const endDate = new Date(announcement.end_date);
        const checkDate = new Date(dateString);
        return endDate.toDateString() === checkDate.toDateString();
    });
}

function renderCalendar() {
    const calendar = document.getElementById('announcement-calendar');
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    const monthNames = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"];
    
    let calendarHTML = `
        <div class="calendar-header">
            <button class="calendar-nav-btn" onclick="changeMonth(-1)">‹</button>
            <h6 class="mb-0">${monthNames[month]} ${year}</h6>
            <button class="calendar-nav-btn" onclick="changeMonth(1)">›</button>
        </div>
        <div class="calendar-grid">
            <div class="calendar-weekday">Sun</div>
            <div class="calendar-weekday">Mon</div>
            <div class="calendar-weekday">Tue</div>
            <div class="calendar-weekday">Wed</div>
            <div class="calendar-weekday">Thu</div>
            <div class="calendar-weekday">Fri</div>
            <div class="calendar-weekday">Sat</div>
    `;
    
    for (let i = 0; i < 35; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        
        const isCurrentMonth = date.getMonth() === month;
        const dateString = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

        // Check for announcements - fix the date comparison
        const hasAnnouncements = announcementDates.includes(dateString);
        
        // Check for fixed events
        const fixedEvents = getFixedEventsForDate(date);
        const hasFixedEvents = fixedEvents.length > 0;
        
        let classes = 'calendar-day';
        let title = '';
        
        if (!isCurrentMonth) {
            classes += ' text-muted';
        }
        
        if (hasAnnouncements && hasFixedEvents) {
            classes += ' has-multiple-events';
            title = 'Has announcements and scheduled events';
        } else if (hasAnnouncements) {
            classes += ' has-event';
            title = 'Has announcements';
        } else if (hasFixedEvents) {
            classes += ' has-fixed-event';
            title = fixedEvents.map(e => e.title).join(', ');
        }
        
        calendarHTML += `<div class="${classes}" 
                               title="${title}" 
                               onclick="showDateEvents('${dateString}')"
                               style="cursor: ${(hasAnnouncements || hasFixedEvents) ? 'pointer' : 'default'}">
                           ${date.getDate()}
                         </div>`;
    }
    
    calendarHTML += '</div>';
    calendar.innerHTML = calendarHTML;
}

// Enhanced showDateEvents function
function showDateEvents(dateString) {
    const date = new Date(dateString);
    const announcements = getAnnouncementsForDate(dateString);
    const fixedEvents = getFixedEventsForDate(date);
    
    if (announcements.length === 0 && fixedEvents.length === 0) {
        return; // No events to show
    }
    
    const modalTitle = document.getElementById('calendarEventModalLabel');
    const modalContent = document.getElementById('calendarModalContent');
    
    modalTitle.innerHTML = `<i class="fas fa-calendar-alt me-2"></i>Events on ${date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })}`;
    
    let contentHTML = '<div class="p-4">';
    
    // Show fixed events first
    fixedEvents.forEach(event => {
        const month = date.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
        const day = date.getDate();
        const year = date.getFullYear();
        
        // Check if it's June for venue information
        const isJune = date.getMonth() === 5; // June is month 5 (0-indexed)
        const venue = isJune ? 'Eros' : 'TBA';
        const time = '8:00 AM - 11:00 AM';
        
        contentHTML += `
            <div class="event-card fixed-event-card">
                <div class="row g-0">
                    <div class="col-md-3">
                        <div class="date-section">
                            <div class="date-month">${month}</div>
                            <div class="date-day">${day}</div>
                            <div class="date-year">${year}</div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="content-section">
                            <h3 class="event-title">${event.title}</h3>
                            <p class="event-content">${event.description}</p>
                            
                            <div class="meeting-details">
                                <div class="meeting-detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span><strong>Time:</strong> ${time}</span>
                                </div>
                                <div class="meeting-detail-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><strong>Venue:</strong> ${venue}</span>
                                </div>
                                <div class="meeting-detail-item">
                                    <i class="fas fa-users"></i>
                                    <span><strong>Type:</strong> ${event.type}</span>
                                </div>
                            </div>
                            
                            <div class="text-center mt-3">
                                <button class="btn cta-button" onclick="showInterestModal('${event.title}', '${dateString}')">
                                    <i class="fas fa-hand-point-up me-2"></i>Interested?
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    // Show announcements
    announcements.forEach(announcement => {
        const month = date.toLocaleDateString('en-US', { month: 'short' }).toUpperCase();
        const day = date.getDate();
        const year = date.getFullYear();
        
        const mediaHTML = announcement.media_type === 'image' && announcement.media_url 
            ? `<div class="mb-3">
                 <img src="${announcement.media_url}" class="img-fluid rounded" style="max-height: 350px; width: 100%; object-fit: cover;" alt="Announcement Image">
               </div>` 
            : '';
            
        contentHTML += `
            <div class="event-card announcement-card">
                <div class="row g-0">
                    <div class="col-md-3">
                        <div class="date-section" style="background: linear-gradient(135deg, #2800bb, #4a20d6);">
                            <div class="date-month">${month}</div>
                            <div class="date-day">${day}</div>
                            <div class="date-year">${year}</div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="content-section">
                            <h3 class="event-title">${announcement.title}</h3>
                            ${mediaHTML}
                            <p class="event-content">${announcement.content}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    contentHTML += '</div>';
    modalContent.innerHTML = contentHTML;
    
    // Show the modal
    const modalElement = document.getElementById('calendarEventModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

// Function to show interest registration modal
function showInterestModal(eventTitle, eventDate) {
    document.getElementById('interestModalLabel').innerHTML = `
        <i class="fas fa-user-plus me-2"></i>Register for ${eventTitle}
    `;
    
    // Hide the calendar modal first
    const calendarModal = bootstrap.Modal.getInstance(document.getElementById('calendarEventModal'));
    if (calendarModal) {
        calendarModal.hide();
    }
    
    // Show interest modal after a short delay
    setTimeout(() => {
        const interestModal = new bootstrap.Modal(document.getElementById('interestModal'));
        interestModal.show();
        
        // Store event details for form submission
        document.getElementById('interestForm').setAttribute('data-event-title', eventTitle);
        document.getElementById('interestForm').setAttribute('data-event-date', eventDate);
    }, 300);
}

// Handle interest form submission
document.getElementById('interestForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('interestEmail').value;
    const phone = document.getElementById('interestPhone').value;
    const eventTitle = this.getAttribute('data-event-title');
    const eventDate = this.getAttribute('data-event-date');
    const submitBtn = document.getElementById('registerInterestBtn');
    const messageDiv = document.getElementById('interestMessage');
    
    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Registering...';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('event_title', eventTitle);
    formData.append('event_date', eventDate);
    
    // Send request to server
    fetch('register_interest.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        messageDiv.style.display = 'block';
        if (data.success) {
            messageDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    ${data.message || 'Registration successful! You will receive a confirmation email shortly.'}
                </div>
            `;
            // Clear form
            document.getElementById('interestEmail').value = '';
            document.getElementById('interestPhone').value = '';
            
            // Close modal after 3 seconds
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('interestModal'));
                modal.hide();
            }, 3000);
        } else {
            messageDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ${data.message || 'Registration failed. Please try again.'}
                </div>
            `;
        }
    })
    .catch(error => {
        messageDiv.style.display = 'block';
        messageDiv.innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                An error occurred. Please try again later.
            </div>
        `;
    })
    .finally(() => {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Register Interest';
        
        // Hide message after 5 seconds
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    });
});
// Initialize everything on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeAnnouncementsGrid();
    renderCalendar();
});
</script>   

<!-- Blog Start -->
        <?php
// Fetch 3 latest created blogs with category information
$blog_query = "SELECT b.*, bc.name as category_name 
               FROM blogs b 
               LEFT JOIN blog_categories bc ON b.category_id = bc.id 
               WHERE b.status = 'published' 
               ORDER BY b.created_at DESC 
               LIMIT 3";

$blog_result = mysqli_query($conn, $blog_query);
?>

<center> 
    <h2 class="heading_text">Our Blogs</h2>
    <div class="container-fluid blog pb-1">
        <div class="container pb-1">
            <div class="row g-4 justify-content-center">
                <?php 
                if (mysqli_num_rows($blog_result) > 0) {
                    $delay = 0.1;
                    while($blog = mysqli_fetch_assoc($blog_result)) {
                        // Format the created date
                        $created_date = date('M d, Y', strtotime($blog['created_at']));
                        
                        // Handle featured image - use default if not available
                        $featured_image = !empty($blog['featured_image']) ? $blog['featured_image'] : 'img/default-blog.jpg';
                        
                        // Truncate title if too long
                        $title = strlen($blog['title']) > 50 ? substr($blog['title'], 0, 47) . '...' : $blog['title'];
                        
                        // Use excerpt or truncate content
                        $excerpt = !empty($blog['excerpt']) ? $blog['excerpt'] : 
                                  (strlen(strip_tags($blog['content'])) > 100 ? 
                                   substr(strip_tags($blog['content']), 0, 97) . '...' : 
                                   strip_tags($blog['content']));
                ?>
                <div class="col-md-6 col-lg-6 col-xl-4 wow fadeInUp" data-wow-delay="<?php echo $delay; ?>s">
                    <div class="blog-item bg-light rounded p-4" style="background-image: url(img/bg.png);">
                        <div class="mb-4">
                            <h4 class="text-primary mb-2"><?php echo htmlspecialchars($blog['category_name'] ?? 'General'); ?></h4>
                            <div class="d-flex justify-content-between">
                                <p class="mb-0">
                                    <span class="text-dark fw-bold">By</span> <?php echo htmlspecialchars($blog['author_name'] ?? 'Admin'); ?>
                                </p>
                                <p class="mb-0 text-muted small"><?php echo $created_date; ?></p>
                            </div>
                        </div>
                        <div class="project-img">
                            <img src="<?php echo htmlspecialchars($featured_image); ?>" 
                                 class="img-fluid w-100 rounded" 
                                 style="height:200px; object-fit: cover;" 
                                 alt="<?php echo htmlspecialchars($blog['title']); ?>"
                                 onerror="this.src='img/default-blog.jpg'">
                            <div class="blog-plus-icon">
                                <a href="<?php echo htmlspecialchars($featured_image); ?>" 
                                   data-lightbox="blog-<?php echo $blog['id']; ?>" 
                                   class="btn btn-primary btn-md-square rounded-pill">
                                    <i class="fas fa-plus fa-1x"></i>
                                </a>
                            </div>
                        </div>
                        <div class="my-4">
                            <a href="blog-detail.php?id=<?php echo $blog['id']; ?>" 
                               class="h4 text-decoration-none" 
                               title="<?php echo htmlspecialchars($blog['title']); ?>">
                                <?php echo htmlspecialchars($title); ?>
                            </a>
                            <?php if (!empty($excerpt)): ?>
                            <p class="text-muted mt-2 small"><?php echo htmlspecialchars($excerpt); ?></p>
                            <?php endif; ?>
                        </div>
                        <a class="btn btn-primary rounded-pill py-2 px-4" 
                           href="blog-detail.php?id=<?php echo $blog['id']; ?>">
                            Read More...
                        </a>
                    </div>
                </div>
                <?php 
                        $delay += 0.2;
                    }
                } else {
                    // No blogs found - show message or default content
                ?>
                <div class="col-12 text-center">
                    <div class="alert alert-info">
                        <h5>Coming Soon!</h5>
                        <p>We're working on bringing you insightful blogs. Stay tuned!</p>
                    </div>
                </div>
                <?php } ?>
            </div>
            
            <!-- More Blogs Button -->
            <?php if (mysqli_num_rows($blog_result) > 0): ?>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <a href="blogs.php" class="btn btn-outline-primary btn-lg rounded-pill px-4 py-2">
                        <i class="fas fa-blog me-2"></i>More Blogs
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</center>

<?php
// Free the result set
mysqli_free_result($blog_result);
?>
        <!-- Blog End -->


        <!-- Team Start -->
        <!--<div class="container-fluid team pb-5">-->
        <!--    <div class="container pb-5">-->
        <!--        <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">-->
        <!--            <h4 class="text-primary">Our Team</h4>-->
        <!--            <h1 class="display-4">Our Investa Company Dedicated Team Member</h1>-->
        <!--        </div>-->
        <!--        <div class="row g-4 justify-content-center">-->
        <!--            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">-->
        <!--                <div class="team-item rounded">-->
        <!--                    <div class="team-img">-->
        <!--                        <img src="img/team-1.jpg" class="img-fluid w-100 rounded-top" alt="Image">-->
        <!--                        <div class="team-icon">-->
        <!--                            <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fas fa-share-alt"></i></a>-->
        <!--                            <div class="team-icon-share">-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fab fa-facebook-f"></i></a>-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fab fa-twitter"></i></a>-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-0" href=""><i class="fab fa-instagram"></i></a>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="team-content bg-dark text-center rounded-bottom p-4">-->
        <!--                        <div class="team-content-inner rounded-bottom">-->
        <!--                            <h4 class="text-white">Mark D. Brock</h4>-->
        <!--                            <p class="text-muted mb-0">CEO & Founder</p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.3s">-->
        <!--                <div class="team-item rounded">-->
        <!--                    <div class="team-img">-->
        <!--                        <img src="img/team-2.jpg" class="img-fluid w-100 rounded-top" alt="Image">-->
        <!--                        <div class="team-icon">-->
        <!--                            <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fas fa-share-alt"></i></a>-->
        <!--                            <div class="team-icon-share">-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fab fa-facebook-f"></i></a>-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fab fa-twitter"></i></a>-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-0" href=""><i class="fab fa-instagram"></i></a>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="team-content bg-dark text-center rounded-bottom p-4">-->
        <!--                        <div class="team-content-inner rounded-bottom">-->
        <!--                            <h4 class="text-white">Mark D. Brock</h4>-->
        <!--                            <p class="text-muted mb-0">CEO & Founder</p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.5s">-->
        <!--                <div class="team-item rounded">-->
        <!--                    <div class="team-img">-->
        <!--                        <img src="img/team-3.jpg" class="img-fluid w-100 rounded-top" alt="Image">-->
        <!--                        <div class="team-icon">-->
        <!--                            <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fas fa-share-alt"></i></a>-->
        <!--                            <div class="team-icon-share">-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fab fa-facebook-f"></i></a>-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fab fa-twitter"></i></a>-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-0" href=""><i class="fab fa-instagram"></i></a>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="team-content bg-dark text-center rounded-bottom p-4">-->
        <!--                        <div class="team-content-inner rounded-bottom">-->
        <!--                            <h4 class="text-white">Mark D. Brock</h4>-->
        <!--                            <p class="text-muted mb-0">CEO & Founder</p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--            <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 wow fadeInUp" data-wow-delay="0.7s">-->
        <!--                <div class="team-item rounded">-->
        <!--                    <div class="team-img">-->
        <!--                        <img src="img/team-4.jpg" class="img-fluid w-100 rounded-top" alt="Image">-->
        <!--                        <div class="team-icon">-->
        <!--                            <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fas fa-share-alt"></i></a>-->
        <!--                            <div class="team-icon-share">-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fab fa-facebook-f"></i></a>-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-3" href=""><i class="fab fa-twitter"></i></a>-->
        <!--                                <a class="btn btn-primary btn-sm-square text-white rounded-circle mb-0" href=""><i class="fab fa-instagram"></i></a>-->
        <!--                            </div>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                    <div class="team-content bg-dark text-center rounded-bottom p-4">-->
        <!--                        <div class="team-content-inner rounded-bottom">-->
        <!--                            <h4 class="text-white">Mark D. Brock</h4>-->
        <!--                            <p class="text-muted mb-0">CEO & Founder</p>-->
        <!--                        </div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        <!-- Team End -->


        <!-- Testimonial Start -->
        <center><h2 class="heading_text"style="padding-top: 40px;
    padding-bottom: 35px;">Our Feedbacks </h2>
            <div class="container" style="background: linear-gradient(to top, rgba(141, 16, 16, 0) 0%, rgba(156, 20, 24, 0.1) 100%);">
                <div class="row g-4 align-items-center">
               
                    <!-- <div class="col-xl-4 wow fadeInLeft" data-wow-delay="0.1s"> -->
                        <!-- <div class="h-100 rounded" style="margin-bottom:30px;">
                            
                            
                            <p class="mb-4">Highly knowledgeable team offering personalized advice in various areas like business, finance, and career development. Highly recommending the membership and services to others who are looking for expert guidance.</p>
                            <a class="btn btn-primary rounded-pill text-white py-3 px-5" href="#">Read All Reviews <i class="fas fa-arrow-right ms-2"></i></a>
                        </div> -->
                    <!-- </div> -->
                    <div class="col-xl-12">
                        <div class="testimonial-carousel owl-carousel wow fadeInUp" data-wow-delay="0.1s">
                            <div class="testimonial-item bg-white rounded p-4 wow fadeInUp" data-wow-delay="0.3s">
                                <div class="d-flex">
                                    <div><i class="fas fa-quote-left fa-3x text-dark me-3"></i></div>
                                    <p class="mt-4">I recently became a member of Sarathi Cooperative, and I am thoroughly impressed by the level of service and professionalism they provide.
                                    From the moment I signed up, it was clear that Sarathi is dedicated to supporting its members every step of the way. 
                                    </p>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <div class="my-auto text-end">
                                        <h5>Rajesh Yadav</h5>
                                        <p class="mb-0">Bussiness Man</p>
                                    </div>
                                    <div class="bg-white rounded-circle ms-3">
                                        <img src="img/newimg/india2.jpg" class="rounded-circle p-2" style="width: 80px; height: 80px; border: 1px solid; border-color: var(--bs-primary);" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-item bg-white rounded p-4 wow fadeInUp" data-wow-delay="0.5s">
                                <div class="d-flex">
                                    <div><i class="fas fa-quote-left fa-3x text-dark me-3"></i></div>
                                    <p class="mt-4">Sarathi Cooperative take the time to listen and offer personalized advice that’s aligned with my goals, whether it’s business guidance, financial advice, or career development. I feel more confident in making informed decisions thanks to their expertise.
                                    </p>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <div class="my-auto text-end">
                                        <h5> Shiva Soni</h5>
                                        <p class="mb-0">Farmer</p>
                                    </div>
                                    <div class="bg-white rounded-circle ms-3">
                                        <img src="img/newimg/india3.jpg" class="rounded-circle p-2" style="width: 80px; height: 80px; border: 1px solid; border-color: var(--bs-primary);" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="testimonial-item bg-white rounded p-4 wow fadeInUp" data-wow-delay="0.7s">
                                <div class="d-flex">
                                    <div><i class="fas fa-quote-left fa-3x text-dark me-3"></i></div>
                                    <p class="mt-4">The combination of top-notch consulting services and the supportive, growth-focused community that Sarathi provides is a game-changer. If you are looking for guidance, growth opportunities, and a community that truly cares, I highly recommend becoming a member of Sarathi Cooperative.
                                    </p>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <div class="my-auto text-end">
                                        <h5>Sonali Rane</h5>
                                        <p class="mb-0">Teacher</p>
                                    </div>
                                    <div class="bg-white rounded-circle ms-3">
                                        <img src="img/newimg/indian1.jpg" class="rounded-circle p-2" style="width: 80px; height: 80px; border: 1px solid; border-color: var(--bs-primary);" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        
        <!-- Testimonial End -->


<!-- FAQ Start -->
<?php
// Fetch FAQs from database
$sql = "SELECT id, question, answer FROM faqs WHERE status = 'active' ORDER BY id ASC";
$result = $conn->query($sql);
$faqs = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $faqs[] = $row;
    }
}
?>

<center>
    <h2 class="heading_text">FAQs</h2>
</center>
<div class="container py-1">
    <div class="row g-5 align-items-center">
        <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
            <div class="accordion bg-light rounded p-4" id="accordionExample">
                <?php if (!empty($faqs)): ?>
                    <?php foreach ($faqs as $index => $faq): ?>
                        <?php 
                        $collapseId = "collapse" . ucfirst(numberToWord($index + 1));
                        $headingId = "heading" . ucfirst(numberToWord($index + 1));
                        $isFirst = ($index === 0);
                        $isLast = ($index === count($faqs) - 1);
                        ?>
                        <!-- FAQ <?php echo $index + 1; ?> -->
                        <div class="accordion-item border-0 <?php echo $isLast ? 'mb-0' : 'mb-4'; ?>">
                            <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                <button class="accordion-button <?php echo $isFirst ? '' : 'collapsed'; ?> text-dark fs-5 fw-bold" 
                                        type="button" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#<?php echo $collapseId; ?>" 
                                        aria-expanded="<?php echo $isFirst ? 'true' : 'false'; ?>" 
                                        aria-controls="<?php echo $collapseId; ?>">
                                    <?php echo htmlspecialchars($faq['question']); ?>
                                </button>
                            </h2>
                            <div id="<?php echo $collapseId; ?>" 
                                 class="accordion-collapse collapse <?php echo $isFirst ? 'show' : ''; ?>" 
                                 aria-labelledby="<?php echo $headingId; ?>" 
                                 data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- No FAQs Available -->
                    <div class="accordion-item border-0 mb-0">
                        <div class="accordion-body text-center py-5">
                            <p class="text-muted">No FAQs available at the moment. Please check back later.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Images Column -->
        <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
            <div class="faq-img RotateMoveRight rounded">
                <img src="img/newimg/oldman.jpg" class="img-fluid rounded w-100" style="height:280px; margin-top:20px;" alt="Image"><br>
                <img src="img/conference.jpg" class="img-fluid rounded w-100" style="height:280px; margin-top:20px;" alt="Image">
            </div>
        </div>
    </div>
</div>

<?php
// Helper function to convert numbers to words for ID generation
function numberToWord($num) {
    $words = [
        1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen', 20 => 'Twenty'
    ];
    
    if ($num <= 20) {
        return $words[$num];
    } else {
        return 'Item' . $num; // Fallback for numbers > 20
    }
}
?>
<!-- FAQ End -->


        <!-- Footer Start -->
      <?php   
       include("footer.php");
       ?>
       <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<script>
$(document).ready(function(){
    // Header Carousel
    if($('.header-carousel').length) {
        $('.header-carousel').owlCarousel({
            items: 1,
            loop: true,
            autoplay: true,
            autoplayTimeout: 5000,
            nav: true,
            dots: true,
            animateOut: 'fadeOut',
            animateIn: 'fadeIn',
            navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>']
        });
    }
    
    // Project Carousel
    if($('.project-carousel').length) {
        $('.project-carousel').owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            responsive: {
                0: { items: 1 },
                768: { items: 2 },
                992: { items: 3 }
            }
        });
    }
    
    // Testimonial Carousel
    if($('.testimonial-carousel').length) {
        $('.testimonial-carousel').owlCarousel({
            loop: true,
            margin: 30,
            nav: false,
            dots: true,
            responsive: {
                0: { items: 1 },
                768: { items: 1 },
                992: { items: 1 }
            }
        });
    }
    
    // Counter Animation
    $('.counter-value').each(function() {
        $(this).prop('Counter', 0).animate({
            Counter: $(this).text()
        }, {
            duration: 2000,
            easing: 'swing',
            step: function(now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
    
    // Initialize WOW animations
    if(typeof WOW !== 'undefined') {
        new WOW().init();
    }
});
</script>
    </body>

</html>