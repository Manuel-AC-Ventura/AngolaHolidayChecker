<?php
require_once('./vendor/autoload.php');
require_once('./service.php');
use Dotenv\Dotenv;

class AngolaHolidayChecker extends Service{
    private $apiKey;
    private $apiUrl = "https://www.googleapis.com/calendar/v3/calendars/pt.ao%23holiday%40group.v.calendar.google.com/events?key=";

    public function __construct(){
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

		$this->apiKey = $_ENV['GOOGLE_API_KEY'] ?? null;
    }

    private function isWeekend($date) {
		$dayOfWeek = date('N', strtotime($date));
		return $dayOfWeek >= 6;
	}

    private function isHoliday($date, $data) {
		foreach ($data['items'] as $item) {
			if (isset($item['start']['date'])) {
				$holidayDate = substr($item['start']['date'], 0, 10);
				if ($date == $holidayDate) {
					return true;
				}
			}
		}
		return false;
	}

    public function isHolidayOrWeekend($date) {
		if ($this->apiKey === null) {
			return "API_KEY não encontrada no arquivo .env";
		}
	
		$url = $this->apiUrl . $this->apiKey;
		
		$data = $this->fetchDataFromAPI($url);
	
		if (is_string($data)) {
			return $data;
		}
	
		if ($this->isHoliday($date, $data) || $this->isWeekend($date)) {
			echo"true";
		}else{
            echo"false";
        }
	}
	
	public function checker(){
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$date = file_get_contents('php://input');
			$this->isHolidayOrWeekend($date);
		}
	}
}

$holiday = new AngolaHolidayChecker();
$holiday->checker();