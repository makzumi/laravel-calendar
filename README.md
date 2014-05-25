laravel-calendar
================

Flexible Calendar for Laravel 4, supports Month, Week and Day Views and multiple events per date.

To change the view type dynamically, pass in a GET variable called cv (calendar view) with either 'week' or 'day'. Day and Week views are split into 30 minute interval rows. 

Install:

Add this to "require" in composer.json:

		"makzumi/calendar": "dev-master"

After that run a composer update, then in app.php:

		'providers' => array(
					...,
					'Makzumi\Calendar\CalendarServiceProvider',
				),

Usage, from your controller:

		$events = array(
			"2014-04-09 10:30:00" => array(
				"Event 1",
				"Event 2 <strong> with html</stong>",
			),
			"2014-04-12 14:12:23" => array(
				"Event 3",
			),
			"2014-05-14 08:00:00" => array(
				"Event 4",
			),
		);

		$cal = Calendar::make();
		/**** OPTIONAL METHODS ****/
		$cal->setDate(Input::get('cdate')); //Set starting date
		$cal->setBasePath('/tests/cal'); // Base path for URLs
		$cal->showNav(true); // Now or hide navigation
		$cal->setView('day'); //Or 'week' or null
		$cal->setStartEndHours(8,20); // Set the hour range for day and week view
		$cal->setTimeClass('ctime'); //Class Name for times column on day and week views
		$cal->setEventsWrap(array('<p>', '</p>')); // Set the event's content wrapper
		$cal->setDayWrap(array('<div>','</div>')); //Set the day's number wrapper
		$cal->setNextIcon('>>');	
		$cal->setPrevIcon('<<');
		$cal->setsetDayLabels(array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat')); //Label names for week days
		$cal->setsetMonthLabels(array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');); //Month names
		$cal->setDateWrap(array('<div>','</div>')); //Set cell inner content wrapper
		$cal->setTableClass('table'); //Set the table's class 
		$cal->setHeadClass('table-header'); //Set top header's class
		$cal->setNextClass('btn'); // Set next btn class 
		$cal->setPrevClass('btn'); // Set Prev btn class
		$cal->setEvents($events); // Receives the events array
		/**** END OPTIONAL METHODS ****/

		echo $cal->generate() // Return the calendar's html;
		
		
		
