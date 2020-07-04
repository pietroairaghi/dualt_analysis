<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>DualT DB analysis</title>
        <link rel="icon" type="image/x-icon" href="assets/img/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.13.0/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Varela+Round" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
        <script type="text/javascript">window.PlotlyConfig = {MathJaxConfig: 'local'};</script>
        <script src="assets/js/plotly.min.js"></script>
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand js-scroll-trigger" href="#page-top">CHILI EPFL</a>
                <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="fas fa-bars"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#about">About</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#projects">Projects</a></li>
                        <li class="nav-item"><a class="nav-link js-scroll-trigger" href="#signup">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- Masthead-->
        <header class="masthead">
            <div class="container d-flex h-100 align-items-center">
                <div class="mx-auto text-center">
                    <h1 class="mx-auto my-0">eDAP</h1>
                    <h2 class="text-white-50 mx-auto mt-2 mb-5">Database extraction, data cleaning and exploration for CHILI @ EPFL</h2>
                    <a class="btn btn-primary js-scroll-trigger" href="#projects">Report</a>
                </div>
            </div>
        </header>
        <!-- Projects-->
        <section class="projects-section bg-light" id="projects">
            <div class="container">

                <div class="row justify-content-md-center mb-4 mb-lg-5">
                    <div class="col-xl-10 col-lg-10 ">
                        <div class="spacer h30"></div>
                        <div class="text-center text-lg-left">
                            <h1>MySQL Extraction</h1>
                            <hr/>
                            <p class="text-justify text-black-50">
                                Database extraction can be found at
                                <a href="notebooks/mysql_extractions.html" target="_blank">this jupyter notebook</a>
                                where you can download all the queries used to obtain the raw data.
                                In addition below a simplified database schema is represented.
                            </p>
                            <p>
                                <img src="data/model_schema.png" width="100%">
                            </p>
                            <p class="text-justify text-black-50">
                                At the center of this scheme are the <code>users</code>.
                                Each user has their own information and is linked to <span class="font-italic">classes</span> and <span class="font-italic">companies</span>.
                                Each user can have multiple recipes or experiences (<code>activities</code>).
                                Each activity consists of its own information and various <u>steps</u> or <u>images</u> (files).
                                The activity can be <u>classified</u> or used to create a <code>reflection</code>.
                                The reflection is personal to the user who created the activity,
                                but his or her supervisor can give it an extra reflection, and this is called <code>feedback</code>.
                                Users can be of various types including <span class="font-italic">students</span>, <span class="font-italic">supervisors</span> (bosses), <span class="font-italic">teachers</span>.
                            </p>
                            <h4 class="text-black-50 mt-5">Preprocess</h4>
                            <p class="text-justify text-black-50">
                                A preprocess-phase is needed.
                                In <a href="notebooks/users_preprocess.html" target="_blank">this notebook</a>
                                you can see how users were cleaned from duplicates, identified by gender, one-hotted the user type and filled some <span class="font-italic">NaN</span> values. Tha same with <a href="/notebooks/activities_preprocess.html" target="_blank">the activities</a>.
                            </p>
                            <p class="text-justify text-black-50">
                                Moreover, based on the information we have, some users have also been associated with their school grades, those during the year and those of the final exams.
                            </p>
                            <p class="text-justify text-black-50 mt-5">
                                <kbd>Note:</kbd> the following databases are taken into account.
                            </p>
                            <ul>
                                <li class="text-black-50">CPT Ticino (<a href="https://cpt.lldweb.ch" target="_blank">link</a>)</li>
                                <li class="text-black-50">CPT Ticino old platform (<a href="http://lld.iuffp-svizzera1.ch" target="_blank">link</a>)</li>
                                <li class="text-black-50">CFP Genève (<a href="http://fr.lld.iuffp-svizzera1.ch" target="_blank">link</a>)</li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>

        </section>


        <section class="exploration-section bg-light" id="explorations">
            <div class="container">

                <div class="row justify-content-md-center mb-4 mb-lg-5">
                    <div class="col-xl-10 col-lg-10 ">
                        <div class="spacer h30"></div>
                        <div class="text-center text-lg-left">
                            <h1>Data Exploration</h1>
                            <hr/>
                            <h4 class="text-black-50">Activities</h4>
                            <p class="text-justify text-black-50">
                                Database extraction can be found at
                                <a href="notebooks/mysql_extractions.html" target="_blank">this jupyter notebook</a>
                                where you can download all the queries used to obtain the raw data.
                                In addition below a simplified database schema is represented.
                            </p>
                            <p class="text-justify text-black-50">
                                At the center of this scheme are the <code>users</code>.
                                Each user has their own information and is linked to <span class="font-italic">classes</span> and <span class="font-italic">companies</span>.
                                Each user can have multiple recipes or experiences (<code>activities</code>).
                                Each activity consists of its own information and various <u>steps</u> or <u>images</u> (files).
                                The activity can be <u>classified</u> or used to create a <code>reflection</code>.
                                The reflection is personal to the user who created the activity,
                                but his or her supervisor can give it an extra reflection, and this is called <code>feedback</code>.
                                Users can be of various types including <span class="font-italic">students</span>, <span class="font-italic">supervisors</span> (bosses), <span class="font-italic">teachers</span>.
                            </p>
                            <p class="text-justify text-black-50">
                                A preprocess-phase is needed.
                                In <a href="notebooks/users_preprocess.html" target="_blank">this notebook</a>
                                you can see how users were cleaned from duplicates, identified by gender, one-hotted the user type and filled some <span class="font-italic">NaN</span> values. Tha same with <a href="/notebooks/activities_preprocess.html" target="_blank">the activities</a>.
                            </p>
                            <p class="text-justify text-black-50">
                                Moreover, based on the information we have, some users have also been associated with their school grades, those during the year and those of the final exams.
                            </p>
                            <p class="text-justify text-black-50 mt-5">
                                <kbd>Note:</kbd> the following databases are taken into account.
                            </p>
                            <ul>
                                <li class="text-black-50">CPT Ticino (<a href="https://cpt.lldweb.ch" target="_blank">link</a>)</li>
                                <li class="text-black-50">CPT Ticino old platform (<a href="http://lld.iuffp-svizzera1.ch" target="_blank">link</a>)</li>
                                <li class="text-black-50">CFP Genève (<a href="http://fr.lld.iuffp-svizzera1.ch" target="_blank">link</a>)</li>
                            </ul>
                        </div>
                    </div>
                </div>


                <!-- Featured Project Row-->
                <div class="row align-items-center no-gutters mb-4 mb-lg-5">
                    <div class="col-xl-6 col-lg-6 col-sm-12">
                        <?php include("results/users_type_gender_TI.html") ?>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-sm-12">
                        <?php include("results/users_type_gender_GE.html") ?>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-sm-12">
                        <?php include("results/activities_type_TI.html") ?>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-sm-12">
                        <?php include("results/activities_type_GE.html") ?>
                    </div>

                    <div class="col-xl-6 col-lg-6 col-sm-12">
                        <?php include("results/users_type_gender.html") ?>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-sm-12">
                        <?php include("results/activities_type.html") ?>
                    </div>

                    <div class="col-xl-8 col-lg-7">
                        <ul class="nav nav-tabs mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                <?php include("results/users_type_gender.html") ?>
                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                <?php include("results/activities_type.html") ?>
                            </div>
                            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">...</div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-5">
                        <div class="featured-text text-center text-lg-left">
                            <h4>Shoreline</h4>
                            <p class="text-black-50 mb-0">Grayscale is <a class="asd" href="javascript:void(0);">open</a> source and MIT licensed. This means you can use it for any project - even commercial projects! Download it, customize it, and publish your website!</p>
                        </div>
                    </div>
                </div>
                <!-- Project One Row-->
                <div class="row justify-content-center no-gutters mb-5 mb-lg-0">
                    <div class="col-lg-6"><img class="img-fluid" src="assets/img/demo-image-01.jpg" alt="" /></div>
                    <div class="col-lg-6">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-left">
                                    <h4 class="text-white">Misty</h4>
                                    <p class="mb-0 text-white-50">An example of where you can put an image of a project, or anything else, along with a description.</p>
                                    <hr class="d-none d-lg-block mb-0 ml-0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Project Two Row-->
                <div class="row justify-content-center no-gutters">
                    <div class="col-lg-6"><img class="img-fluid" src="assets/img/demo-image-02.jpg" alt="" /></div>
                    <div class="col-lg-6 order-lg-first">
                        <div class="bg-black text-center h-100 project">
                            <div class="d-flex h-100">
                                <div class="project-text w-100 my-auto text-center text-lg-right">
                                    <h4 class="text-white">Mountains</h4>
                                    <p class="mb-0 text-white-50">Another example of a project with its respective description. These sections work well responsively as well, try this theme on a small screen!</p>
                                    <hr class="d-none d-lg-block mb-0 mr-0" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Signup-->
        <section class="signup-section" id="signup">
            <div class="container">
                <div class="row">
                    <div class="col-md-10 col-lg-8 mx-auto text-center">
                        <i class="far fa-paper-plane fa-2x mb-2 text-white"></i>
                        <h2 class="text-white mb-5">Subscribe to receive updates!</h2>
                        <form class="form-inline d-flex">
                            <input class="form-control flex-fill mr-0 mr-sm-2 mb-3 mb-sm-0" id="inputEmail" type="email" placeholder="Enter email address..." />
                            <button class="btn btn-primary mx-auto" type="submit">Subscribe</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- Contact-->
        <section class="contact-section bg-black">
            <div class="container">
                <div class="row">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-map-marked-alt text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Address</h4>
                                <hr class="my-4" />
                                <div class="small text-black-50">4923 Market Street, Orlando FL</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-envelope text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Email</h4>
                                <hr class="my-4" />
                                <div class="small text-black-50"><a href="#!">hello@yourdomain.com</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="card py-4 h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-mobile-alt text-primary mb-2"></i>
                                <h4 class="text-uppercase m-0">Phone</h4>
                                <hr class="my-4" />
                                <div class="small text-black-50">+1 (555) 902-8832</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="social d-flex justify-content-center">
                    <a class="mx-2" href="#!"><i class="fab fa-twitter"></i></a>
                    <a class="mx-2" href="#!"><i class="fab fa-facebook-f"></i></a>
                    <a class="mx-2" href="#!"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </section>
        <!-- Footer-->
        <footer class="footer bg-black small text-center text-white-50"><div class="container">Copyright © Your Website 2020</div></footer>
        <!-- Bootstrap core JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
        <!-- Third party plugin JS-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
        <!-- Core theme JS-->
        <script src="js/scripts.js"></script>
        <script>

            var plotly_config = {"responsive": true,
                modeBarButtonsToRemove: ["zoom2d", "pan2d", "select2d", "lasso2d", "zoomIn2d", "zoomOut2d", "autoScale2d", "resetScale2d", "hoverClosestCartesian", "hoverCompareCartesian", "zoom3d", "pan3d", "resetCameraDefault3d", "resetCameraLastSave3d", "hoverClosest3d", "orbitRotation", "tableRotation", "zoomInGeo", "zoomOutGeo", "resetGeo", "hoverClosestGeo", "sendDataToCloud", "hoverClosestGl2d", "hoverClosestPie", "toggleHover", "resetViews", "toggleSpikelines", "resetViewMapbox"],
                modeBarButtonsToAdd: [{
                name: 'Download as SVG',
                icon: Plotly.Icons.camera,
                click: function(gd) {
                    Plotly.downloadImage(gd, {format: 'svg'})
                }
            }],
                displaylogo: false
            }


        </script>
    </body>
</html>
