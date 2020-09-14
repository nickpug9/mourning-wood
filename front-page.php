<?php

// Pages Covered: Home Page

get_header();
$context = Timber::context();
$context['post'] = new Timber\Post();
Timber::render('front-page.twig', $context);
get_footer();
