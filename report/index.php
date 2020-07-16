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

        <?php
        $data_info = parse_ini_file("data_info.ini");
        ?>

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
        <section class="extraction-section bg-light" id="extractions">
            <div class="container">
                <div class="spacer h100"></div>
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
                                <kbd>Note:</kbd> the following databases have been taken into account.
                            </p>
                            <ul class="text-left">
                                <li class="text-black-50">CPT Ticino (<a href="https://cpt.lldweb.ch" target="_blank">link</a>)</li>
                                <li class="text-black-50">CPT Ticino old platform (<a href="http://lld.iuffp-svizzera1.ch" target="_blank">link</a>)</li>
                                <li class="text-black-50">CFP Genève (<a href="http://fr.lld.iuffp-svizzera1.ch" target="_blank">link</a>)</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="spacer0"></div>

            </div>

        </section>


        <section class="cleaning-section bg-light" id="cleaning">
            <div class="container">

                <div class="row justify-content-md-center mb-4 mb-lg-5">
                    <div class="col-xl-10 col-lg-10 ">
                        <div class="spacer h30"></div>
                        <div class="text-left text-lg-left">
                            <h1>Data Cleaning</h1>
                            <hr/>
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="text-black-50">Users</h4>
                                    <p class="text-black-50">
                                        The following criteria have been applied before the analysis to clean users:
                                    </p>
                                    <ul>
                                        <li>
                                            Students with 0 activities (<?= $data_info['no_activities_students'] ?> removed).
                                        </li>
                                        <li>
                                            Users with less than 5 login in total (<?= $data_info['low_logins'] ?> removed).
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="text-black-50">Activities</h4>
                                    <p class="text-black-50">
                                        The following criteria have been applied before the analysis to clean activities:
                                    </p>
                                    <ul>
                                        <li>
                                            Activities with total length = 0 (<?= $data_info['activities_0_length'] ?> removed)
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="spacer h10"></div>

            </div>
        </section>


        <section class="exploration-section bg-light" id="explorations">
            <div class="container">

                <div class="row justify-content-md-center mb-4 mb-lg-5">
                    <div class="col-xl-10 col-lg-10 ">
                        <div class="spacer h30"></div>
                        <div class="text-center text-lg-left">
                            <h1>Data Exploration</h1>
                            <p class="">
                                The extracted data concern the school years from <strong><?= $data_info['year_from'] ?></strong> to <strong><?= $data_info['year_to'] ?></strong>
                            </p>
                            <hr class="mb-4"/>
                            <div class="row">
                                <div class="col-md-6">
                                    <h4 class="text-left text-black-50">Users</h4>
                                    <p class="text-justify text-black-50">
                                        In total there are <code><strong><?= number_format($data_info['n_users']) ?></strong> users</code> : <br/>
                                    </p>
                                    <ul class="text-left">
                                        <li>
                                            The type is divided in <code><?= number_format($data_info['n_students']) ?></code> students, <code><?= number_format($data_info['n_supervisors']) ?></code> supervisors, <code><?= number_format($data_info['n_teachers']) ?></code> teachers and <?= number_format($data_info['n_others_u']) ?> other.
                                        </li>
                                        <li>
                                            The gender is divided in <code><?= number_format($data_info['n_males']) ?></code> males and <code><?= number_format($data_info['n_females']) ?></code> females and <?= number_format($data_info['n_unknown']) ?> unknown.
                                        </li>
                                        <li>
                                            <code><?= number_format($data_info['users_from_ti']) ?></code> from canton <u>TI</u> and <code><?= number_format($data_info['users_from_ge']) ?></code> from canton <u>GE</u>.
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="text-black-50 text-left">Activities</h4>
                                    <p class="text-justify text-black-50">
                                        In total there are <code><strong><?= number_format($data_info['n_activities']) ?></strong> activities</code> : <br/>
                                    </p>
                                    <ul class="text-left">
                                        <li>
                                            <code><?= number_format($data_info['n_recipes']) ?></code> recipes and <code><?= number_format($data_info['n_experiences']) ?></code> experiences.
                                        </li>
                                        <li>
                                            <code><?= number_format($data_info['activities_from_ti']) ?></code> from canton <u>TI</u> and <code><?= number_format($data_info['activities_from_ge']) ?></code> from canton <u>GE</u>.
                                        </li>
                                        <li>
                                            <?= number_format($data_info['activities_with_feedback_requests']) ?>
                                            (<code><?= number_format($data_info['activities_with_feedback_requests']/$data_info['n_activities']*100) ?>%</code> )
                                            with feedback request of which
                                            <code><?= number_format($data_info['activities_with_feedback_responses']/$data_info['activities_with_feedback_requests']*100) ?>%</code>
                                            received a responses.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <hr class="mb-4"/>
                                <h4 class="text-left">Activities</h4>
                                <p>asd</p>
                            </div>

                            <div class="col-md-12">
                                <?php include("results/activities_per_vintage.html") ?>
                            </div>
                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <?php include("results/activities_per_vintage_school_year.html") ?>
                            </div>

                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <nav>
                                    <div class="nav nav-tabs" role="tablist">
                                        <a class="nav-item nav-link active" data-toggle="tab" href="#nav-act-user-year" role="tab">
                                            Year
                                        </a>
                                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-act-user-vintage" role="tab">
                                            Vintage
                                        </a>
                                    </div>
                                </nav>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="nav-act-user-year" role="tabpanel">
                                        <?php include("results/activities_per_user_per_year.html") ?>
                                    </div>
                                    <div class="tab-pane fade active not-real-active" id="nav-act-user-vintage" role="tabpanel">
                                        <?php include("results/activities_per_user_per_vintage.html") ?>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <nav>
                                    <div class="nav nav-tabs" role="tablist">
                                        <a class="nav-item nav-link active" data-toggle="tab" href="#nav-act-month" role="tab">
                                            Overall
                                        </a>
                                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-act-month-ti" role="tab">
                                            Details TICINO
                                        </a>
                                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-act-month-ge" role="tab">
                                            Details GENÈVE
                                        </a>
                                    </div>
                                </nav>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="nav-act-month" role="tabpanel">
                                        <?php include("results/activities_per_month.html") ?>
                                    </div>
                                    <div class="tab-pane fade active not-real-active" id="nav-act-month-ti" role="tabpanel">
                                        <?php include("results/activities_per_month_per_vintage_TI.html") ?>
                                    </div>
                                    <div class="tab-pane fade active not-real-active" id="nav-act-month-ge" role="tabpanel">
                                        <?php include("results/activities_per_month_per_vintage_GE.html") ?>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <nav>
                                    <div class="nav nav-tabs" role="tablist">
                                        <a class="nav-item nav-link active" data-toggle="tab" href="#nav-act-lenght" role="tab">
                                            Distribution
                                        </a>
                                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-act-lenght-creation" role="tab">
                                            Creation school year
                                        </a>
                                        <a class="nav-item nav-link" data-toggle="tab" href="#nav-act-lenght-vintage" role="tab">
                                            Vintage
                                        </a>
                                    </div>
                                </nav>
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="nav-act-lenght" role="tabpanel">
                                        <?php include("results/ativities_total_length_distr.html") ?>
                                    </div>
                                    <div class="tab-pane fade active not-real-active" id="nav-act-lenght-creation" role="tabpanel">
                                        <?php include("results/activities_total_length_creation_year.html") ?>
                                    </div>
                                    <div class="tab-pane fade active not-real-active" id="nav-act-lenght-vintage" role="tabpanel">
                                        <?php include("results/activities_total_length_start_year.html") ?>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <hr class="mb-4"/>
                                <h4 class="text-left">Feedbacks</h4>
                                <p>asd</p>
                            </div>

                            <div class="col-md-12">
                                <?php include("results/feedbacks_requests_and_responses_per_year_per_canton.html") ?>
                            </div>
                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <?php include("results/feedbacks_requests_and_responses_per_month_per_school_year.html") ?>
                            </div>

                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <?php include("results/requests_per_users_distr.html") ?>
                            </div>

                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <?php include("results/feedbacks_per_response_year_per_supervisor.html") ?>
                            </div>
                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <?php include("results/feedbacks_per_request_year_per_student.html") ?>
                            </div>

                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <?php include("results/feedbacks_ratio_per_each_supervisor.html") ?>
                            </div>
                            <div class="col-md-12">
                                <div class="spacer h50"></div>
                                <?php include("results/feedbacks_ratio_per_activity_school_year_per_month.html") ?>
                            </div>





                        </div>
                    </div>
                </div>


                <!-- Featured Project Row-->
                <div class="row align-items-center no-gutters mb-4 mb-lg-5">
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

            function sleep(ms) {
                return new Promise(resolve => setTimeout(resolve, ms));
            }

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

            $(".nav-link").click(async function(){
                return true;
                $(".tab-pane .plotly-graph-div").each(async function(){
                    await sleep(100);
                    Plotly.Plots.resize(this.id);
                    var update = {
                        width: "926",
                        height: " "
                    };
                    // Plotly.relayout(this.id,update)
                });
            });

            $(".not-real-active").each(function(){
                $(this).removeClass("active");
            });


        </script>
    </body>
</html>
