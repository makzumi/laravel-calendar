<?php
namespace Makzumi\Calendar;

class Calendar {

	private $day_lbls = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
	private $month_lbls = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
	private $days_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	private $week_days = array();
	private $day;
	private $month;
	private $year;
	private $events = FALSE;
	private $start_hour = 8;
	private $end_hour = 20;

	private $nav = TRUE;

	private $view = 'month';

	private $html = "";

	//WRAPS AND CSS
	private $tableClass = 'table table-calendar';
	private $headClass = '';

	private $prevIco = '<';
	private $nextIco = '>';
	private $prevClass = 'cal_prev';
	private $nextClass = 'cal_next';
	private $basePath = "/";

	private $timeClass = 'ctime';
	private $dayWrap = array('<div class="cal_day">', '</div>');
	private $dateWrap = array('<div class="date">', '</div>');
	private $labelsClass = 'cal_labels';
	private $eventWrap = array('<p>', '</p>');

	public function __construct() {
		$this->day = date('d');
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

	public function setView($view) {
		$this->view = $view;
		return $this;
	}
	
	public function setTimeClass($class) {
		$this->timeClass = $class;
		return $this;
	}

	public function setStartEndHours($s, $e) {
		$this->start_hour = $s;
		$this->end_hour = $e;
		return $this;
	}

	public function setDate($date = FALSE) {

		$date = explode('-', $date);
		$day = @$date[2] ? : date('d');
		$month = @$date[1] ? : date('m');
		
		$year = @$date[0] ? : date('Y');

		$this->day = @$day;
		$this->month = @$month;
		$this->year = @$year;
		return $this;
		
	}

	public function generate() {
		$this->buildHeader();
		switch ($this->view) {
			case 'day' :
				$this->buildBodyDay();
				break;
			case 'week' :
				$this->buildBodyWeek();
				break;
			default :
				$this->buildBody();
				break;
		}
		return $this->html;
	}

	public function setBasePath($path) {
		$this->basePath = $path;
		return $this;		
	}

	public function setDayLabels($array) {
		if (count($array) != 7) return;
		$this->day_lbls = $array;
		return $this;		
	}

	public function setMonthLabels($array) {
		if (count($array) != 12) return;
		$this->month_lbls = $array;
		return $this;		
	}

	public function setEvents($events) {
		if (!is_array($events)) return;
		$this->events = $events;
		return $this;		
	}

	public function setEventsWrap($wrap) {
		$this->eventWrap = $wrap;
		return $this;		
	}

	public function setDayWrap($wrap) {
		$this->dayWrap = $wrap;
		return $this;
	}

	public function setNextIcon($html) {
		$this->nextIco = $html;
		return $this;
	}

	public function setPrevIcon($html) {
		$this->prevIco = $html;
		return $this;
	}

	public function setDateWrap($wrap) {
		$this->dateWrap = $wrap;
		return $this;
	}

	public function setTableClass($class) {
		$this->tableClass = $class;
		return $this;
	}

	public function setHeadClass($class) {
		$this->headClass = $class;
		return $this;
	}

	public function setNextClass($class) {
		$this->nextClass = $class;
		return $this;
	}

	public function setPrevClass($class) {
		$this->prevClass = $class;
		return $this;
	}

	public function setLabelsClass($class) {
		$this->labelsClass = $class;
		return $this;
	}

	private function buildHeader() {
		$month_name = $this->month_lbls[$this->month - 1] . ' ' . $this->year;
		$h = "<table class='" . $this->tableClass . "'>";
		$h .= "<tr class='" . $this->headClass . "'>";
		$cs = 5;
		if ($this->view == 'week' || $this->view == 'day')
			$h .= "<th>&nbsp;</th>";
		if ($this->view == 'day')
			$cs = 1;

		if ($this->nav) {
			$h .= "<th>";
			$h .= "<a class='" . $this->prevClass . "' href='" . $this->prevLink() . "'>" . $this->prevIco . "</a>";
			$h .= "</th>";
			$h .= "<th colspan='$cs'>";
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

		$h .= "<tr class='" . $this->labelsClass . "'>";

		if ($this->view == 'week' || $this->view == 'day') {
			$cnt = 7;
			if ($this->view == 'day')
				$cnt = 3;
			$i = date('N', strtotime($this->year . '-' . $this->month . '-' . $this->day));
			if ($i == 7)
				$i = 0;
			$h .= "<td>&nbsp;</td>";
			$h .= "<td colspan='$cnt'>";
			$h .= $this->day_lbls[$i];
			$h .= "</td>";
		} else {
			for ($i = 0; $i <= 6; $i++) {
				$h .= "<td>";
				$h .= $this->day_lbls[$i];
				$h .= "</td>";
			}
		}

		$h .= "</tr>";

		if ($this->view == 'week' || $this->view == 'day')
			$h .= self::getWeekDays();

		$this->html .= $h;
	}

	private function getWeekDays() {
		$time = date('Y-m-d', strtotime($this->year . '-' . $this->month . '-' . $this->day));
		if ($this->view == 'week') {
			$sunday = strtotime('last sunday', strtotime($time . ' +1day'));
			$day = date('j', $sunday);
			$startingDay = date('N', $sunday);
			$cnt = 6;
		}
		if ($this->view == 'day') {
			$day = $this->day;
			$cnt = 0;
		}

		$this->week_days = array();
		$h = "<tr>";
		$h .= "<td>&nbsp;</td>";
		for ($j = 0; $j <= $cnt; $j++) {
			$cs = $cnt == 0 ? 3 : 1;
			$h .= "<td colspan='$cs'>";
			$h .= $day;
			$this->week_days[] = $day;
			$day++;
			$h .= "</td>";
		}

		$h .= "</tr>";
		return $h;
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

	private function buildBodyDay() {

		$events = $this->events;
		$h = "";
		for ($i = $this->start_hour; $i < $this->end_hour; $i++) {
			for ($t = 0; $t < 2; $t++) {
				$h .= "<tr>";
				$min = $t == 0 ? ":00" : ":30";
				$h .= "<td class='$this->timeClass'>" . date('g:ia', strtotime($i . $min)) . "</td>";
				for ($k = 0; $k < 1; $k++) {
					$h .= "<td colspan='3'>";
					$h .= $this->dateWrap[0];
					$wd = $this->week_days[$k];
					$time_r = $this->year . '-' . $this->month . '-' . $wd . ' ' . $i . ':00:00';
					$min = $t == 0 ? '' : '+30 minute';
					$time_1 = strtotime($time_r . $min);
					$time_2 = strtotime(date('Y-m-d H:i:s', $time_1) . '+30 minute');

					$hasEvent = FALSE;
					foreach ($events as $key=>$event) {
						//EVENT TIME AND DATE
						$time_e = strtotime($key);
						if ($time_e >= $time_1 && $time_e < $time_2) {
							$hasEvent = TRUE;
							$h .= $this->buildEvents(FALSE, $event);
						}
					}
					$h .= !$hasEvent ? '&nbsp;' : '';
					$h .= $this->dateWrap[1];
					$h .= "</td>";
				}
				$h .= "<tr>";
			}
		}

		$this->html .= $h;
	}

	private function buildBodyWeek() {

		$events = $this->events;
		$h = "";
		for ($i = $this->start_hour; $i < $this->end_hour; $i++) {
			for ($t = 0; $t < 2; $t++) {
				$h .= "<tr>";
				$min = $t == 0 ? ":00" : ":30";
				$h .= "<td class='$this->timeClass'>" . date('g:ia', strtotime($i . $min)) . "</td>";
				for ($k = 0; $k < count($this->week_days); $k++) {
					$h .= "<td>";
					$h .= $this->dateWrap[0];
					$wd = $this->week_days[$k];
					$time_r = $this->year . '-' . $this->month . '-' . $wd . ' ' . $i . ':00:00';
					$min = $t == 0 ? '' : '+30 minute';
					$time_1 = strtotime($time_r . $min);
					$time_2 = strtotime(date('Y-m-d H:i:s', $time_1) . '+30 minute');

					$hasEvent = FALSE;
					foreach ($events as $key=>$event) {
						//EVENT TIME AND DATE
						$time_e = strtotime($key);
						if ($time_e >= $time_1 && $time_e < $time_2) {
							$hasEvent = TRUE;
							$h .= $this->buildEvents(FALSE, $event);
						}
					}
					$h .= !$hasEvent ? '&nbsp;' : '';
					$h .= $this->dateWrap[1];
					$h .= "</td>";
				}
				$h .= "<tr>";
			}
		}

		$this->html .= $h;
	}

	private function buildEvents($date, $event = FALSE) {
		if (!$this->events)
			return "";
		$events = $this->events;
		$h = "";
		//IF DAY CALC MINS
		$date = date('Y-m-d', strtotime($date));
		if ($event) {
			return $this->processEvent($event);
		}
		foreach ($events as $key=>$event) {
			$edate = date('Y-m-d', strtotime($key));
			if (is_array($event)) {
				if ($date == $edate) {
					$h .= $this->processEvent($event);
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

	private function processEvent($event) {
		$h = "";
		foreach ($event as $e) {
			$h .= $this->eventWrap[0];
			$h .= $e;
			$h .= $this->eventWrap[1];
		}
		return $h;
	}

	private function prevLink() {
		$y = $this->year;
		$d = intval($this->day);
		$d = $d < 10 ? '0' . $d : $d;
		$m = intval($this->month);
		$m = $m < 10 ? '0' . $m : $m;

		$time = $y . '-' . $m . '-' . $d;

		if ($this->view == "week") {
			$time = strtotime('last sunday', strtotime($time . ' +1 day'));
			$time = date('Y-m-d', $time);
			$time = date('Y-m-d', strtotime($time . ' -1 week'));
		} else if ($this->view == "day") {
			$time = date('Y-m-d', strtotime($time . '-1day'));
		} else {
			$time = date('Y-m', strtotime($y . '-' . $m . '-01 -1month'));
		}
		$url = $this->basePath . '?cdate=' . $time;
		return $url . $this->getOldGET();
	}

	private function nextLink() {
		$y = $this->year;
		$d = intval($this->day);
		$d = $d < 10 ? '0' . $d : $d;
		$m = intval($this->month);
		$m = $m < 10 ? '0' . $m : $m;

		$time = $y . '-' . $m . '-' . $d;

		if ($this->view == "week") {
			$time = strtotime('next sunday', strtotime($time . ' -1 day'));
			$time = date('Y-m-d', $time);
			$time = date('Y-m-d', strtotime($time . '+1week'));
		} else if ($this->view == "day") {
			$time = date('Y-m-d', strtotime($time . '+1day'));
		} else {
			$time = date('Y-m', strtotime($y . '-' . $m . '-01 +1month'));
		}

		$url = $this->basePath . '?cdate=' . $time;
		return $url . $this->getOldGET();
	}

	private function getDayDate($day) {
		$day = intval($day);
		$y = $this->year;
		$m = intval($this->month);
		$m = $m < 10 ? '0' . $m : $m;
		$d = intval($day);
		$d = $d < 10 ? '0' . $d : $d;
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
