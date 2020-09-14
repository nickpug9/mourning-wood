<?php

// Pages Covered: Blog Index, Category Archive Pages

get_header();
$context = Timber::context();
$context['posts'] = new Timber\PostQuery();
Timber::render('index.twig', $context);
get_footer();
