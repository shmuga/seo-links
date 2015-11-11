<?php
include 'Domain.class.php';
$url = "http://eprints.uny.ac.id/3204/";
$domain = Domain::from_url($url);
echo $domain->get_reg_domain();  // goddamn.co.uk.