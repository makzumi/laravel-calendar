laravel-calendar
================

Flexible Calendar for Laravel 4, usage:

Add this to "require" in composer.json:

		"makzumi/calendar": "dev-master"

After that, in app.php:

		'providers' => array(
				...,
				'Makzumi\Calendar\CalendarServiceProvider',
				),

Usage, from your controller:

		$events = array(
			"2014-04-09" => array(
				"Event 1",
				"Event 2",
			),
			"2014-04-12" => array(
				"Event A",
			),
			"2014-05-14" => array(
				"Event 1",
			),
		);

		$cal = Calendar::make();
		/* OPTIONAL METHODS */
		$cal->setDate(Input::get('cdate'));
		$cal->setBasePath('/tests/cal');
		$cal->showNav(true);
		$cal->setEventsWrap(array('<p>', '</p>'));
		$cal->setDayWrap(array('<div>','</div>'));
		$cal->setNextIcon('>>');	
		$cal->setPrevIcon('<<');
		$cal->setDateWrap(array('<div>','</div>'));
		$cal->setTableClass('table');
		$cal->setHeadClass('table-header');
		$cal->setNextClass('btn');
		$cal->setPrevClass('btn');		
		$cal->setEvents($events);
		/* END OPTIONAL METHODS */

		echo $cal->generate();
		
		
		
