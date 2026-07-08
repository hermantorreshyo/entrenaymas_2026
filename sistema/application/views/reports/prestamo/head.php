<head>
    <meta charset="utf-8">
    <base href="/templates/<?php echo $empresa->template_path ?>/"/>
    <title><?php echo (isset($seo_title)) ? (html_entity_decode($seo_title,ENT_QUOTES)) : (html_entity_decode($empresa->seo_title,ENT_QUOTES)); ?></title>

    <meta name="description" content="<?php echo (isset($seo_description)) ? (html_entity_decode($seo_description,ENT_QUOTES)) : (html_entity_decode($empresa->seo_description,ENT_QUOTES)); ?>">

    <meta name="keywords" content="<?php echo (isset($seo_keywords)) ? (html_entity_decode($seo_keywords,ENT_QUOTES)) : (html_entity_decode($empresa->seo_keywords,ENT_QUOTES)); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

    <meta name="author" content="">

    <script type="text/javascript" src="/sistema/resources/js/common.js"></script>

    <script type="text/javascript" src="/sistema/resources/js/main.js"></script>

    <link href="/sistema/resources/css/common.css" rel="stylesheet" media="screen">
    
    <script type="text/javascript">
    // Constantes que se utilizan en el sistema
    const ID_EMPRESA = "<?php echo $empresa->id ?>";
    const CURRENT_URL = "<?php echo current_url(); ?>";
    </script>


    <!--[if lt IE 9]>
	<script src="js/html5shiv.js"></script>
	<![endif]-->


    <!-- CSS Files
    ================================================== -->
    <link rel="stylesheet" href="css/bootstrap.css" type="text/css">
    <link rel="stylesheet" href="css/jpreloader.css" type="text/css">
    <link rel="stylesheet" href="css/animate.css" type="text/css">
    <link rel="stylesheet" href="css/flexslider.css" type="text/css">
    <link rel="stylesheet" href="css/plugin.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.css" type="text/css">
    <link rel="stylesheet" href="css/owl.theme.css" type="text/css">
    <link rel="stylesheet" href="css/owl.transitions.css" type="text/css">
    <link rel="stylesheet" href="css/magnific-popup.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

    <!-- custom background -->
    <link rel="stylesheet" href="css/bg.css" type="text/css">

    <!-- color scheme -->
    <link rel="stylesheet" href="css/color.css" type="text/css" id="colors">


    <!-- load fonts -->
    <link rel="stylesheet" href="fonts/elegant_font/HTML_CSS/style.css" type="text/css">
    <link rel="stylesheet" href="fonts/et-line-font/style.css" type="text/css">

    <!-- revolution slider -->
    <link rel="stylesheet" href="rs-plugin/css/settings.css" type="text/css">
    <link rel="stylesheet" href="css/rev-settings.css" type="text/css">
</head>