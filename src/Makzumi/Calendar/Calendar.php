<?php
namespace Makzumi\Calendar;

class Calendar {

	private $day_lbls = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	private $month_lbls = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	private $days_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	private $month;
	private $year;
	private $events = FALSE;

	private $nav = TRUE;

	private $html = "";

	//WRAPS AND CSS
	private $tableClass = 'table table-calendar';
	private $headClass = '';

	private $prevIco = '<';
	private $nextIco = '>';
	private $prevClass = 'cal_prev';
	private $nextClass = 'cal_next';
	private $basePath = "/";

	private $dayWrap = array('<div class="cal_day">', '</div>');
	private $dateWrap = array('<div class="date">', '</div>');
	private $eventWrap = array('<p>', '</p>');

	public function __construct() {

		$this->month = date('n');
		$this->year = date('Y');
	}

	public function make() {
		return new static();
	}

	public function showNav($show) {
		$this->nav = $show;
		return $this;
	}

	public function setDate($date = FALSE) {
		if (!$date)
			return;
		$date = explode('-', $date);
		$month = $date[1];
		$year = $date[0];
		$month = intval($month);
		$this->month = $month;
		$this->year = $year;
	}

	public function generate() {
		$this->buildHeader();
		$this->buildBody();
		return $this->html;
	}

	public function setBasePath($path) {
		$this->basePath = $path;
	}

	public function setDayLabels($array) {
		if (count($array) != 7)
			return;
		$this->day_lbls = $array;
	}

	public function setMonthLabels($array) {
		if (count($array) != 12)
			return;
		$this->month_lbls = $array;
	}

	public function setEvents($events) {
		if (!is_array($events))
			return FALSE;
		$this->events = $events;
	}

	public function setEventsWrap($wrap) {
		$this->eventWrap = $wrap;
	}

	public function setDayWrap($wrap) {
		$this->dayWrap = $wrap;
	}

	public function setNextIcon($html) {
		$this->nextIco = $html;
	}

	public function setPrevIcon($html) {
		$this->prevIco = $html;
	}

	public function setDateWrap($wrap) {
		$this->dateWrap = $wrap;
	}

	public function setTableClass($class) {
		$this->tableClass = $class;
	}

	public function setHeadClass($class) {
		$this->headClass = $class;
	}

	public function setNextClass($class) {
		$this->nextClass = $class;
	}

	public function setPrevClass($class) {
		$this->prevClass = $class;
	}

	private function buildHeader() {
		$month_name = $this->month_lbls[$this->month - 1] . ' ' . $this->year;
		$h = "<table class='" . $this->tableClass . "'>";
		$h .= "<tr class='" . $this->headClass . "'>";
		if ($this->nav) {
			$h .= "<th>";
			$h .= "<a class='" . $this->prevClass . "' href='" . $this->prevLink() . "'>" . $this->prevIco . "</a>";
			$h .= "</th>";
			$h .= "<th colspan='5'>";
			$h .= $month_name;
			$h .= "</th>";
			$h .= "<th>";
			$h .= "<a class='" . $this->nextClass . "' href='" . $this->nextLink() . "'>" . $this->nextIco . "</a>";
			$h .= "</th>";
		} else {
			$h .= "<th colspan='7'>";
			$h .= $month_name;
			$h .= "</th>";
		}
		$h .= "</tr>";
		$h .= "<tr>";

		for ($i = 0; $i <= 6; $i++) {
			$h .= "<td>";
			$h .= $this->day_lbls[$i];
			$h .= "</td>";
		}
		$h .= "</tr>";

		$this->html .= $h;
	}

	private function buildBody() {
		$day = 1;
		$startingDay = date('N', strtotime('first day of this month'));
		$monthLength = $this->days_month[$this->month - 1];
		$h = "<tr>";
		for ($i = 0; $i < 9; $i++) {
			for ($j = 0; $j <= 6; $j++) {
				$h .= "<td>";
				$h .= $this->dateWrap[0];
				if ($day <= $monthLength && ($i > 0 || $j >= $startingDay)) {
					$h .= $this->dayWrap[0];
					$h .= $day;
					$h .= $this->dayWrap[1];
					$curr_date = $this->getDayDate($day);
					$h .= $this->buildEvents($curr_date);
					$day++;
				} else {
					$h .= "&nbsp;";
				}
				$h .= $this->dateWrap[1];
				$h .= "</td>";
			}
			// stop making rows if we've run out of days
			if ($day > $monthLength) {
				break;
			} else {
				$h .= "</tr>";
				$h .= "<tr>";
			}
		}
		$h .= "</tr>";
		$h .= "</table>";
		$this->html .= $h;

	}

	private function buildEvents($date) {
		if (!$this->events)
			return "";
		$events = $this->events;
		$h = "";
		foreach ($events as $key=>$event) {

			if (is_array($event)) {
				if ($date == $key) {
					foreach ($event as $e) {
						$h .= $this->eventWrap[0];
						$h .= $e;
						$h .= $this->eventWrap[1];
					}
				}
			} else {
				if ($date == $key) {
					$h .= $this->eventWrap[0];
					$h .= $event;
					$h .= $this->eventWrap[1];
				}
			}
		}
		return $h;
	}

	private function prevLink() {
		$y = $this->year;
		$m = $this->month < 10 ? '0' . $this->month : $this->month;
		$time = strtotime($y . '-' . $m . '-01 -1month');
		$url = $this->basePath . '?cdate=' . date('Y-m', $time);
		return $url . $this->getOldGET();
	}

	private function nextLink() {
		$y = $this->year;
		$m = $this->month < 10 ? '0' . $this->month : $this->month;
		$time = strtotime($y . '-' . $m . '-01 +1month');
		$url = $this->basePath . '?cdate=' . date('Y-m', $time);
		return $url . $this->getOldGET();
	}

	private function getDayDate($day) {
		$day = intval($day);
		$y = $this->year;
		$m = $this->month < 10 ? '0' . $this->month : $this->month;
		$d = $day < 10 ? '0' . $day : $day;
		$date = $y . '-' . $m . '-' . $d;
		return $date;
	}

	private function getOldGET() {
		$get = $_GET;
		$vars = '';
		foreach ($get as $key=>$value)
			if ($key != 'cdate')
				$vars .= '&' . $key . '=' . $value;
		return $vars;
	}

}
