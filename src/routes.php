<?php
// Routes

$app->get('/results', function ($request, $response, $args) {

	$Runners = new Runners($this->db);
	$args['results'] = $Runners->get_results();

    return $this->renderer->render($response, 'results.phtml', $args);
});

$app->get('/prizes', function ($request, $response, $args) {

	$Runners = new Runners($this->db);
	$Photos = new Photos($this->db);
	$args['winners'] = $Runners->get_prize_winners();
	$args['marshals'] = $Photos->get_marshal_prize_contenders();

    return $this->renderer->render($response, 'prizes.phtml', $args);
});

$app->get('/mc', function ($request, $response, $args) {

	$Runners = new Runners($this->db);
	$Runners->calc();
	$args['winners'] = $Runners->get_prize_winners();

    return $this->renderer->render($response, 'mc.phtml', $args);
});


$app->any('/entry', function ($request, $response, $args) {

	$Runners = new Runners($this->db);

	$data = $request->getParsedBody();
	if ($data) {
		if ($Runners->add_finish_time($data['bib'], $data['finish_time'])) {
			$args['confirm'] = $Runners->find_by_bib($data['bib']);
		}
	}


    return $this->renderer->render($response, 'entry.phtml', $args);
});


$app->get('/photos/import', function ($request, $response, $args) {

	$Photos = new Photos($this->db);
	$Photos->import();

    return $this->renderer->render($response, 'photo_import.phtml', $args);
});

$app->any('/photos/tag', function ($request, $response, $args) {

	$Photos = new Photos($this->db);
	
	$args['type']  = 'mugshot';

	$data = $request->getParsedBody();
	if ($data) {
		$Photos->tag_photo($data['photo_id'], $data['bib'], $data['type']);
		$args['type'] = $data['type'];
	}

	$args['photo'] = $Photos->get_next_to_tag();

    return $this->renderer->render($response, 'photo_tag.phtml', $args);
});


$app->get('/slideshow[/{type}]', function ($request, $response, $args) {

	$Photos = new Photos($this->db);

	$type = null;

	if (isset($args['type']) && $args['type']) {
		$type = $args['type'];
	}

	$args['photos'] = $Photos->get_slideshow($type);

    // Render index view
    return $this->renderer->render($response, 'slideshow.phtml', $args);
});



$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});


