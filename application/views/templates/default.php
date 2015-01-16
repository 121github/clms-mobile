<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $title; ?></title>
        <link rel="stylesheet"  href="<?php echo base_url(); ?>assets/css/themes/default/jquery.mobile-1.3.2.min.css">
        <link rel="stylesheet"  href="<?php echo base_url(); ?>assets/css/themes/default/jquery.mobile.datebox.min.css">
        <link rel="stylesheet"  href="<?php echo base_url(); ?>assets/css/default.css">
        <link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/logo.ico">
        
        <script src="<?php echo base_url(); ?>assets/js/lib/jquery.js"></script>
        <script src="https://code.jquery.com/ui/1.10.3/jquery-ui.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/main.js"></script>
        <!-- Set the baseUrl in the JavaScript helper -->
        <script type="text/javascript"> helper.baseUrl = '<?php echo base_url(); ?>' + 'index.php/'; </script>
        <!-- Only load these files when they are needed ??!! DONT FORGET ME -->
        <script src="<?php echo base_url(); ?>assets/js/leads.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/diary.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/planner.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/location.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/appointment.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/regions.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/map.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/lib/jquery.mobile-1.3.2.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/lib/jqm-datebox.core.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/lib/jqm-datebox.mode.calbox.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/lib/jqm-datebox.mode.datebox.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/lib/unserialize.jquery.latest.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/lib/jquery.mobile.datebox.i18n.en-GB.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/lib/jquery.tablesorter.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
        
        <link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/img/apple-touch-icon.png" />
    </head>
    <body>
        <div id="<?php echo $pageId; ?>" class="page <?php if (isset($pageClass)) echo $pageClass ?>" data-role="page">

            <div data-role="panel" class="navmenu-panel" data-position="left" data-display="reveal" data-theme="b">
                <ul data-role="listview" class="list" data-theme="d" data-divider-theme="d">
                    <?php if($_SESSION['login']<>"reports"){ ?>
                    <li data-role="list-divider">Hi <?php echo $_SESSION['user']; ?></li>
                    <li><a href="<?php echo base_url(); ?>index.php/leads/create" class="hreflink">Create New Prospect</a></li>
                    <li><a href="<?php echo base_url(); ?>index.php/leads/search" class="hreflink">Search Leads</a></li>
                    <li><a href="<?php echo base_url(); ?>index.php/leads/acturis_pending" class="hreflink">Acturis Pending</a></li>
                    <li><a href="<?php echo base_url(); ?>index.php/leads/view" class="hreflink">Task List</a></li>
                    <li><a href="<?php echo base_url(); ?>index.php/regions/view" class="hreflink">View Regions</a></li>
                    <li><a href="<?php echo base_url(); ?>index.php/diary/month" class="hreflink">Diary Manager</a></li>
                    <li><a href="<?php echo base_url(); ?>index.php/planner/prospects" class="hreflink">Journey Planner</a></li>
                    <li><a href="<?php echo base_url(); ?>index.php/planner/appointments" class="hreflink">Appointments</a></li>
                     <li><a href="<?php echo base_url(); ?>index.php/feedback/view" class="hreflink">CAE Feedback</a></li>
                    <!--<li><a href="<?php echo base_url(); ?>index.php/reports/activity" class="hreflink">Activity Report</a></li>-->

                    <!--<li><a href="<?php echo base_url(); ?>index.php/reports/user_tracking" class="hreflink">User Tracking Report</a></li>-->
                   
                    <?php } ?>
                    <li><a href="<?php echo base_url(); ?>index.php/reports/management_information" class="hreflink">MI Report</a></li>
                    <li><a href="<?php echo base_url(); ?>index.php/income" class="hreflink">Income Report</a></li>
                                        <li><a href="<?php echo base_url(); ?>index.php/reports/renewals" class="hreflink">Renewal Report</a></li>
                                                                                 <li><a href="<?php echo base_url(); ?>index.php/reports/appointments" class="hreflink">Appointments Report</a></li>                                       <li><a href="<?php echo base_url(); ?>index.php/bonus/report" class="hreflink">Bonus Report</a></li>
                                        <li><a href="<?php echo base_url(); ?>index.php/storage" class="hreflink">Local Storage</a></li>
                                        <li><a href="<?php echo base_url(); ?>index.php/exports" class="hreflink">Exports</a></li>
                    <?php if($_SESSION['login']<>"reports"){ ?>
                    <li><a href="<?php echo base_url(); ?>index.php/planner/data_enrichment_form" data-ajax="false">Data Enrichment Form</a></li>
                    <?php } ?>
                    <li><a href="<?php echo base_url(); ?>index.php/user/account" >My Account</a></li>
                    <li><a href="<?php echo base_url(); ?>index.php/user/logout" >Logout</a></li>
                </ul>
            </div>

            <div data-role="header" class="header" data-theme="b" data-position="fixed" data-tap-toggle="false">
                <h1><?php echo $title; ?></h1>
                
                <a href="#" id="navmenu-btn" class="navmenu-btn <?php if ($pageId == 'login') echo 'menu-hide'; ?>">
                    &#9776;
                </a>
                
                <!--<img class="swtnlogo" src="<?php echo base_url(); ?>assets/img/swtnlogo.png"/>-->
            </div> <!-- /header -->

            <div data-role="content" class="content">
                <?php echo $body; ?>
            </div> <!-- /content -->

    </body>
</html>
