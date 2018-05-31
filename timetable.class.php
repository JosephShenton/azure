<?php 

	/**
	* Timetable JSON Generation Class
	*/
	class Timetable
	{
		function getTimetableJSON($username, $password) {
			$ch = curl_init();

			curl_setopt_array($ch, self::setOptions($username, $password));

			$results = curl_exec($ch);

			if(!$results) {
			  die('Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch));
			} else {
			  	return self::createJSON($results);
			}

			curl_close($ch);
		}

		function setOptions($user, $pass) {
			$options = array(
				CURLOPT_URL => 'https://web2.wyong-h.schools.nsw.edu.au/portal/login/login?redir=%2Fportal%2Ftimetable%2Fexport',
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTPHEADER => self::setHeaders(self::getCookie()),
				CURLOPT_POST => 1,
				CURLOPT_POSTFIELDS => self::buildQuery($user, $pass)
			);

			return $options;
		}

		function setHeaders($PortalSID) {
			return [
			  "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
			  "Origin: https://web2.wyong-h.schools.nsw.edu.au",
			  "Accept-Encoding: gzip, deflate, br",
			  "Referer: https://web2.wyong-h.schools.nsw.edu.au/portal/login",
			  "Content-Type: application/x-www-form-urlencoded",
			  "Cookie: __utmc=177990691; __utmz=177990691.1517469442.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not provided); __utmc=242220160; __utmz=242220160.1517469442.1.1.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not provided); _vwo_uuid_v2=D5A64C0A3AD3F3728A6635A5BEA8BFDCE|43ebb1ee3229d469206d2df153de9bd1; __utma=177990691.604505399.1517469442.1517469442.1517718303.2; __utmt=1; __utma=242220160.936032147.1517469442.1517469442.1517718304.2; __utmt_t2=1; MoodleSessionSnr=1b22mp6a8olbhl98eeq8c1n0r3; __utmb=177990691.2.10.1517718303; __utmb=242220160.2.10.1517718304; SentralSID=djh267t5s73pfus6umrv5rd347; PortalSID=$PortalSID",
			  "Upgrade-Insecure-Requests: 1",
			  "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Safari/537.36",
			  "Accept-Language: en-GB,en-US;q=0.9,en;q=0.8",
			 ];
		}

		function buildQuery($username, $password) {
			$body = [
				"username" => "$username",
				"password" => "$password",
				"action" => "login",
			];
			return http_build_query($body);
		}

		function getCookie() {
			$ch = curl_init('https://web2.wyong-h.schools.nsw.edu.au/portal/login/login?redir=%2Fportal%2Ftimetable%2Fexport');

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 1);

			$result = curl_exec($ch);

			preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);        // get cookie

			$cookies = array();

			foreach($matches[1] as $item) {
				
				parse_str($item, $cookie);
				
				$cookies = array_merge($cookies, $cookie);
			}

			return $cookies['PortalSID'];
		}

		function createJSON($resp) {
			$txt = $resp;

			$events = explode('|', self::prep_vcard($txt));

			$result = [];
			foreach ($events as $key => $event) {
			    parse_str(trim($event), $t);
			    $result[] = array_map('trim', $t);
			}

			$result = array_filter($result);

			// echo "<pre>".print_r($result, JSON_PRETTY_PRINT)."</pre>";

			array_walk($result, function(&$value, $key) {
			    foreach (['period_start', 'fetch_time', 'period_end'] as $date_key) {
			        if (isset($value[$date_key])) {
			            $value[$date_key] = date('d/m/Y g:i A', strtotime($value[$date_key]));
			        }
			    }

			    // foreach (['class'] as $class_key) {
			    // 	if (isset($value[$class_key])) {
			    // 		$value[$class_key] = str_replace(": ", "", strtok($value[$class_key], ': '));
			    // 	}
			    // }

			    foreach (['teacher'] as $teacher_key) {
			    	if (isset($value[$teacher_key])) {
			    		$value[$teacher_key] = ucwords(strtolower($value[$teacher_key]));
			    	}
			    }
			});

			header('Content-Type: application/json');

			return json_encode($result, JSON_PRETTY_PRINT);
		}

		function prep_vcard($str) {
		    return str_replace([
		        '\n',
		        'BEGIN:VCALENDAR',
		        'VERSION:2.0',
		        'PRODID:-//My Timetable//',
		        'BEGIN:VEVENT',
		        'END:VEVENT',
		        'DTSTART;VALUE=DATE-TIME:',
		        'DTSTAMP;VALUE=DATE-TIME:',
		        'DTEND;VALUE=DATE-TIME:',
		        'UID:',
		        'DESCRIPTION:',
		        'Teacher:',
		        'Period:',
		        'SUMMARY:',
		        'LOCATION:Room: ',
		        ' Yr',
		    ], [
		        '',
		        '',
		        '',
		        '',
		        '',
		        '|',
		        '&period start=',
		        '&fetch time=',
		        '&period end=',
		        '&UID=',
		        '',
		        '&teacher=',
		        '&period=',
		        '&class=',
		        '&room=',
		        '&year=',
		    ], str_replace("&", "and", $str));
		}

		function readableDate($timestring) {
			return date('d/m/Y h:i:s', strtotime($timestring));
		}


		function GetBetween($content,$start,$end){
		    $r = explode($start, $content);
		    if (isset($r[1])){
		        $r = explode($end, $r[1]);
		        return $r[0];
		    }
		    return '';
		}
	}

?>
