<?php

// Pages Covered: Inner Pages

get_header();
$context = Timber::context();
$context['post'] = new Timber\Post();
?><p>inner page</p><?php
Timber::render('page.twig', $context);
get_footer();