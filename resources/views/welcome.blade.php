<!DOCTYPE html>
<html lang="en" 
    class="layout-wide customizer-hide"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Regional Project Tracking System</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="../assets/vendor/fonts/iconify-icons.css" />
    <link rel="stylesheet" href="../assets/vendor/fonts/fontawesome.css" />

    <link rel="stylesheet" href="../assets/vendor/css/core.css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    <link rel="stylesheet" href="../assets/css/style.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- endbuild -->

    <!-- Page CSS -->
     <link rel="stylesheet" href="../assets/vendor/css/pages/page-icons.css" />

     <!-- Page -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />

</head>
<body>
    <!-- Nav Bar -->
    <nav class="navbar navbar-expand-lg bg-primary navbar-dark fixed-top">
        <div class="container">
            <a href="{{ url('/') }}" class="navbar-brand">
                <img class="img-fluid mx-2" src="../assets/img/illustrations/DepDev_white_50x50.png" alt="">
                <span>DepDev 5 Regional Project Tracking System</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navmenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navmenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="{{ url('/') }}" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="https://dro5.depdev.gov.ph" class="nav-link" target="_blank">DEPDev 5 Website</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">About RDIP</a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link">CIPG Submission</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('login') }}" class="nav-link">Login</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a href="{{ route('register') }}" class="nav-link">Register</a>
                    </li> --}}
                </ul>
            </div>
        </div>    
    </nav>

    <!-- Showcase -->
    <section class="bg-light-subtle text-light p-5 p-lg-0 pt-lg-0 text-center">
        <div class="container">
            <div class="d-sm-flex align-items-center justify-content-between">
                <div>
                    <h1>Enroll <span class="text-warning">Proposed Projects</span> </h1>
                    <p class="lead">
                       The Regional Project Tracking System (RPTS) is a system database containing the 
                       priority programs, activities, and projects (PAPs) of regional line agencies, 
                       government owned and controlled corporations, and state universities and colleges in the 
                       Bicol region that are included in the Regional Development Investment Program (RDIP) 2023-2028. 
                       It is being developed to facilitate the tracking and updating of PAPs and can generate reports 
                       as well as investment programming-related documents such as the RDIP, status of RDC-endorsed 
                       projects, and list of projects per province/district/city/municipality, among others. 
                    </p>
                    <a href="{{ route('home')}}">
                        <button class="btn btn-primary btn-lg">Start The Enrollment</button>
                    </a>
                </div> 
                <img class="img-fluid w-25" src="../assets/img/illustrations/construction_equipment2.png" alt="" />
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    {{-- <section class="bg-primary p-5">
        <div class="container">
            <div class="d-md-flex justify-content-between align-items-center">
                <h3 class="mb-3 mb-md-0 text-white">Sign Up For Our Newsletter</h3>

                <div class="input-group news-input">
                    <input type="text" class="form-control bg-white" placeholder="Enter email">
                        <button class="btn btn-dark btn-lg" type="button">Button</button>
                </div>
            </div> 
        </div>
    </section> --}}

    <!-- Boxes -->
    {{-- <section class="p-5">
        <div class="container">
            <div class="row text-center gy-4">
                <div class="col-md">
                    <div class="card bg-dark text-light">
                        <div class="card-body text-center">
                            <div class="h1 mb-3">
                                <i class="icon bx bx-laptop"></i>
                            </div>
                            <h3 class="card-title mb-3 text-white">
                                Virtual
                            </h3>
                            <p class="card-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloribus repellat consectetur inventore dicta sunt dolores?
                            </p>
                            <a href="#" class="btn btn-primary">Read More</a>
                           
                        </div>
                    </div>
                </div>
                <div class="col-md"> 
                    <div class="card bg-secondary text-light">
                        <div class="card-body text-center">
                            <div class="h1 mb-3">
                                 <i class="icon bx bx-rfid"></i>
                            </div>
                            <h3 class="card-title mb-3 text-white">
                                Hybrid
                            </h3>
                            <p class="card-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloribus repellat consectetur inventore dicta sunt dolores?
                            </p>
                            <a href="#" class="btn btn-dark">Read More</a>
                           
                        </div>
                    </div>
                </div>
                <div class="col-md">
                     <div class="card bg-dark text-light">
                        <div class="card-body text-center">
                            <div class="h1 mb-3">
                                <i class="icon bx bx-group"></i>
                            </div>
                            <h3 class="card-title mb-3 text-white">
                                In Person
                            </h3>
                            <p class="card-body">
                                Lorem ipsum dolor sit amet consectetur adipisicing elit. Doloribus repellat consectetur inventore dicta sunt dolores?
                            </p>
                            <a href="#" class="btn btn-primary">Read More</a>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Learn Sections -->
    {{-- <section id="learn" class="p-5">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-md">
                    <img src="../assets/img/illustrations/fundamentals.svg" alt="" class="img-fluid w-75">
                </div>

                <div class="col-md p-5">
                    <h2>Learn the Fundamentals</h2>
                    <p class="lead">
                        Lorem, ipsum dolor sit amet consectetur adipisicing elit. Tempore voluptate quae odio assumenda fugiat ea.
                    </p>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolore voluptatum qui facere aliquam quaerat ut assumenda dignissimos distinctio totam at ex, repellat quidem enim repudiandae aperiam. Voluptas vitae vel cupiditate!</p>
                    <a href="#" class="btn btn-light mt-3"> <i class="icon-mg bx bx-chevron-right"></i> Read More</a>
                </div>

            </div>
        </div>
    </section> --}}

     {{-- <section id="learn" class="p-5 mt-3 bg-dark text-light">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <div class="col-md p-5">
                    <h2>Learn React</h2>
                    <p class="lead">
                        Lorem, ipsum dolor sit amet consectetur adipisicing elit. Tempore voluptate quae odio assumenda fugiat ea.
                    </p>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Dolore voluptatum qui facere aliquam quaerat ut assumenda dignissimos distinctio totam at ex, repellat quidem enim repudiandae aperiam. Voluptas vitae vel cupiditate!</p>
                    <a href="#" class="btn btn-light mt-3"> <i class="icon-mg bx bx-chevron-right"></i> Read More</a>
                </div>

                  <div class="col-md">
                    <img src="../assets/img/illustrations/react.svg" alt="" class="img-fluid w-75">
                </div>

            </div>
        </div>
    </section> --}}

    <!-- Questions Accordion -->
    {{-- <section class="p-5" id="questions">
        <div class="container">
            <h2 class="text-center mb-4">Frequently Asked Questions</h2>

            <div class="accordion accordion-flush" id="questions">
                <!-- Item 1 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                    Where exactly are you located?
                </button>
                </h2>
                <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#questions">
                <div class="accordion-body">Lorem ipsum dolor, sit amet consectetur adipisicing elit. Reiciendis vero corrupti iusto magnam hic obcaecati ipsa, ducimus nulla laudantium nam culpa ad temporibus laborum, ratione velit. Magni nobis neque quo maxime accusamus aspernatur eos debitis nesciunt. Veniam totam, debitis exercitationem odio, aliquam quis aperiam obcaecati molestias sapiente architecto cupiditate minima.</div>
                </div>
            </div>
            <!-- Item 2 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingTwo">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                    How much does it cost to attend?
                </button>
                </h2>
                <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#questions">
                <div class="accordion-body"><i class="fa-brands fa-twitter"></i>Lorem ipsum dolor sit amet consectetur adipisicing elit. Pariatur eius a fugiat nobis doloribus. Totam vel quam adipisci reiciendis similique voluptatibus, dolorum architecto harum natus! Sapiente pariatur eligendi quasi commodi, consequatur laudantium perferendis, officiis ipsam ex ipsa deleniti harum adipisci consequuntur assumenda a sunt aliquid, natus excepturi earum? Adipisci, labore.</div>
                </div>
            </div>
            <!-- Item 3 -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="flush-headingThree">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                    What do I need to Know?
                </button>
                </h2>
                <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#questions">
                <div class="accordion-body">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Sit deleniti ut, labore culpa ab a, eligendi vero quidem delectus nisi officiis illo est inventore vitae totam nemo? Mollitia, corporis nemo? Voluptatibus repellendus ipsum inventore, harum ut cumque neque sequi debitis quidem fugit temporibus mollitia beatae excepturi suscipit, atque, maxime assumenda!</div>
                </div>
            </div>
            </div>
        </div>
    </section> --}}

    {{-- <section class="p-5 bg-primary">
        <div class="container">
            <h2 class="text-center text-white">Our Instructors</h2>
            <p class="lead text-center text-white mb-5">
                Our instructors all have 5+ years working as a web developer
                in the industry.
            </p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="https://randomuser.me/api/portraits/men/11.jpg" class="rounded-circle mb-3" alt="">
                            <h3 class="card-title mb-3">John Doe</h3>
                            <p class="card-text">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error voluptate blanditiis, veniam vitae velit aut!</p>
                           <i class="icon-base bx bx-printer"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="https://randomuser.me/api/portraits/women/11.jpg" class="rounded-circle mb-3" alt="">
                            <h3 class="card-title mb-3">Jane Doe</h3>
                            <p class="card-text">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error voluptate blanditiis, veniam vitae velit aut!</p>
                           <i class="icon-base bx bx-printer"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="https://randomuser.me/api/portraits/men/12.jpg" class="rounded-circle mb-3" alt="">
                            <h3 class="card-title mb-3">Steve Smith</h3>
                            <p class="card-text">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error voluptate blanditiis, veniam vitae velit aut!</p>
                           <i class="icon-base bx bx-printer"></i>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card bg-light">
                        <div class="card-body text-center">
                            <img src="https://randomuser.me/api/portraits/women/13.jpg" class="rounded-circle mb-3" alt="">
                            <h3 class="card-title mb-3">Sara Shealdon</h3>
                            <p class="card-text">Lorem ipsum, dolor sit amet consectetur adipisicing elit. Error voluptate blanditiis, veniam vitae velit aut!</p>
                           <i class="icon-base bx bx-printer"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <!-- Contact Us -->
    <section class="p-5">
        <div class="container">
            {{-- <div class="row g-4">
                <div class="col-md">
                    <h2 class="text-center mb-4">Contact Info</h2>
                    <ul class="list-group list-group-flush lead">
                        <li class="list-group-item">
                            <span class="fw-bold">Main Location:</span> Arimbay, Legazpi City, 4500
                        </li>
                    </ul>
                </div>
            </div> --}}
        </div>
    </section>

    <!-- Footer -->
    <footer class="p-5 bg-dark text-center text-white position-relative">
        <div class="container">
            <p class="lead">Copyright &copy; 2025 DepDev 5 Regional Project Tracking System</p>
           
        </div>
    </footer>



    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

    <script src="../assets/js/config.js"></script>

    <script src="../assets/vendor/libs/jquery/jquery.js"></script>

    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>

    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script> --}}

    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->

    <!-- Place this tag before closing body tag for github widget button. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>