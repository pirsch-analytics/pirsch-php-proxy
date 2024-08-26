<?php
namespace Pirsch;

require __DIR__.'/../vendor/autoload.php';

class Client {
	const DEFAULT_BASE_URL = 'https://api.pirsch.io';

	const AUTHENTICATION_ENDPOINT = '/api/v1/token';
	const HIT_ENDPOINT = '/api/v1/hit';
	const EVENT_ENDPOINT = '/api/v1/event';
	const SESSION_ENDPOINT = '/api/v1/session';
	const DOMAIN_ENDPOINT = '/api/v1/domain';
	const SESSION_DURATION_ENDPOINT = '/api/v1/statistics/duration/session';
	const TIME_ON_PAGE_ENDPOINT = '/api/v1/statistics/duration/page';
	const UTM_SOURCE_ENDPOINT = '/api/v1/statistics/utm/source';
	const UTM_MEDIUM_ENDPOINT = '/api/v1/statistics/utm/medium';
	const UTM_CAMPAIGN_ENDPOINT = '/api/v1/statistics/utm/campaign';
	const UTM_CONTENT_ENDPOINT = '/api/v1/statistics/utm/content';
	const UTM_TERM_ENDPOINT = '/api/v1/statistics/utm/term';
	const TOTAL_VISITORS_ENDPOINT = '/api/v1/statistics/total';
	const VISITORS_ENDPOINT = '/api/v1/statistics/visitor';
	const PAGES_ENDPOINT = '/api/v1/statistics/page';
	const ENTRY_PAGES_ENDPOINT = '/api/v1/statistics/page/entry';
	const EXIT_PAGES_ENDPOINT = '/api/v1/statistics/page/exit';
	const CONVERSION_GOALS_ENDPOINT = '/api/v1/statistics/goals';
	const EVENTS_ENDPOINT = '/api/v1/statistics/events';
	const EVENT_METADATA_ENDPOINT = '/api/v1/statistics/event/meta';
	const LIST_EVENTS_ENDPOINT = '/api/v1/statistics/event/list';
	const GROWTH_RATE_ENDPOINT = '/api/v1/statistics/growth';
	const ACTIVE_VISITORS_ENDPOINT = '/api/v1/statistics/active';
	const TIME_OF_DAY_ENDPOINT = '/api/v1/statistics/hours';
	const LANGUAGE_ENDPOINT = '/api/v1/statistics/language';
	const REFERRER_ENDPOINT = '/api/v1/statistics/referrer';
	const OS_ENDPOINT = '/api/v1/statistics/os';
	const OS_VERSION_ENDPOINT = '/api/v1/statistics/os/version';
	const BROWSER_ENDPOINT = '/api/v1/statistics/browser';
	const BROWSER_VERSION_ENDPOINT = '/api/v1/statistics/browser/version';
	const COUNTRY_ENDPOINT = '/api/v1/statistics/country';
	const REGION_ENDPOINT = '/api/v1/statistics/region';
	const CITY_ENDPOINT = '/api/v1/statistics/city';
	const PLATFORM_ENDPOINT = '/api/v1/statistics/platform';
	const SCREEN_ENDPOINT = '/api/v1/statistics/screen';
	const TAG_KEYS_ENDPOINT = "/api/v1/statistics/tags";
	const TAG_DETAILS_ENDPOINT = "/api/v1/statistics/tag/details";
	const KEYWORDS_ENDPOINT = '/api/v1/statistics/keywords';

	const REFERRER_QUERY_PARAMS = array(
		'ref',
		'referer',
		'referrer',
		'source',
		'utm_source'
	);

	private $clientID;
	private $clientSecret;
	private $client;

	function __construct($clientID, $clientSecret, $timeout = 5.0, $baseURL = self::DEFAULT_BASE_URL) {
		$this->clientID = $clientID;
		$this->clientSecret = $clientSecret;
		$this->client = new \GuzzleHttp\Client([
			'base_uri' => $baseURL,
			'timeout' => floatval($timeout)
		]);
	}

	function hit($retry = true) {
		try {
			$response = $this->client->post(self::HIT_ENDPOINT, [
				'headers' => $this->getRequestHeader(),
				'json' => [
					'url' => $this->getRequestURL(),
					'ip' => $this->getHeader('REMOTE_ADDR'),
					'user_agent' => $this->getHeader('HTTP_USER_AGENT'),
					'accept_language' => $this->getHeader('HTTP_ACCEPT_LANGUAGE'),
					'sec_ch_ua' => $this->getHeader('HTTP_SEC_CH_UA'),
					'sec_ch_ua_mobile' => $this->getHeader('HTTP_SEC_CH_UA_MOBILE'),
					'sec_ch_ua_platform' => $this->getHeader('HTTP_SEC_CH_UA_PLATFORM'),
					'sec_ch_ua_platform_version' => $this->getHeader('HTTP_SEC_CH_UA_PLATFORM_VERSION'),
					'sec_ch_width' => $this->getHeader('HTTP_SEC_CH_WIDTH'),
					'sec_ch_viewport_width' => $this->getHeader('HTTP_SEC_CH_VIEWPORT_WIDTH'),
					'referrer' => $this->getReferrer()
				]
			]);
			return json_decode($response->getBody());
		} catch(\GuzzleHttp\Exception\RequestException $e) {
			if ($e->getResponse()->getStatusCode() == 401 && $retry) {
				$this->refreshToken();
				return $this->hit(false);
			} else {
				throw new \Exception('Error sending page view: '.$e->getResponse()->getBody());
			}
		}

		return null;
	}

	function pageview(HitOptions $data, $retry = true) {
		try {
			if (is_null($data)) {
				$data = new HitOptions;
			}

			$data->url = $this->isEmpty($data->url) ? $this->getRequestURL() : $data->url;
			$data->ip = $this->isEmpty($data->ip) ? $this->getHeader('REMOTE_ADDR') : $data->ip;
			$data->user_agent = $this->isEmpty($data->user_agent) ? $this->getHeader('HTTP_USER_AGENT') : $data->user_agent;
			$data->accept_language = $this->isEmpty($data->accept_language) ? $this->getHeader('HTTP_ACCEPT_LANGUAGE') : $data->accept_language;
			$data->sec_ch_ua = $this->isEmpty($data->sec_ch_ua) ? $this->getHeader('HTTP_SEC_CH_UA') : $data->sec_ch_ua;
			$data->sec_ch_ua_mobile = $this->isEmpty($data->sec_ch_ua_mobile) ? $this->getHeader('HTTP_SEC_CH_UA_MOBILE') : $data->sec_ch_ua_mobile;
			$data->sec_ch_ua_platform = $this->isEmpty($data->sec_ch_ua_platform) ? $this->getHeader('HTTP_SEC_CH_UA_PLATFORM') : $data->sec_ch_ua_platform;
			$data->sec_ch_ua_platform_version = $this->isEmpty($data->sec_ch_ua_platform_version) ? $this->getHeader('HTTP_SEC_CH_UA_PLATFORM_VERSION') : $data->sec_ch_ua_platform_version;
			$data->sec_ch_width = $this->isEmpty($data->sec_ch_width) ? $this->getHeader('HTTP_SEC_CH_WIDTH') : $data->sec_ch_width;
			$data->sec_ch_viewport_width = $this->isEmpty($data->sec_ch_viewport_width) ? $this->getHeader('HTTP_SEC_CH_VIEWPORT_WIDTH') : $data->sec_ch_viewport_width;
			$data->title = $this->isEmpty($data->title) ? '' : $data->title;
			$data->referrer = $this->isEmpty($data->referrer) ? $this->getReferrer() : $data->referrer;
			$data->screen_width = $this->isEmpty($data->screen_width) ? 0 : $data->screen_width;
			$data->screen_height = $this->isEmpty($data->screen_height) ? 0 : $data->screen_height;
			$response = $this->client->post(self::HIT_ENDPOINT, [
				'headers' => $this->getRequestHeader(),
				'json' => [
					'url' => $data->url,
					'ip' => $data->ip,
					'user_agent' => $data->user_agent,
					'accept_language' => $data->accept_language,
					'sec_ch_ua' => $data->sec_ch_ua,
					'sec_ch_ua_mobile' => $data->sec_ch_ua_mobile,
					'sec_ch_ua_platform' => $data->sec_ch_ua_platform,
					'sec_ch_ua_platform_version' => $data->sec_ch_ua_platform_version,
					'sec_ch_width' => $data->sec_ch_width,
					'sec_ch_viewport_width' => $data->sec_ch_viewport_width,
					'title' => $data->title,
					'referrer' => $data->referrer,
					'screen_width' => intval($data->screen_width),
					'screen_height' => intval($data->screen_height),
					'tags' => $data->tags
				]
			]);
			return json_decode($response->getBody());
		} catch(\GuzzleHttp\Exception\RequestException $e) {
			if ($e->getResponse()->getStatusCode() == 401 && $retry) {
				$this->refreshToken();
				return $this->pageview($data, false);
			} else if ($e->getResponse()->getStatusCode() != 200) {
				throw new \Exception('Error sending page view: '.$e->getResponse()->getBody());
			}
		}

		return null;
	}

	function event($name, $duration = 0, $meta = NULL, HitOptions $data = NULL, $retry = true) {
		try {
			if (is_null($data)) {
				$data = new HitOptions;
			}

			$data->url = $this->isEmpty($data->url) ? $this->getRequestURL() : $data->url;
			$data->ip = $this->isEmpty($data->ip) ? $this->getHeader('REMOTE_ADDR') : $data->ip;
			$data->user_agent = $this->isEmpty($data->user_agent) ? $this->getHeader('HTTP_USER_AGENT') : $data->user_agent;
			$data->accept_language = $this->isEmpty($data->accept_language) ? $this->getHeader('HTTP_ACCEPT_LANGUAGE') : $data->accept_language;
			$data->sec_ch_ua = $this->isEmpty($data->sec_ch_ua) ? $this->getHeader('HTTP_SEC_CH_UA') : $data->sec_ch_ua;
			$data->sec_ch_ua_mobile = $this->isEmpty($data->sec_ch_ua_mobile) ? $this->getHeader('HTTP_SEC_CH_UA_MOBILE') : $data->sec_ch_ua_mobile;
			$data->sec_ch_ua_platform = $this->isEmpty($data->sec_ch_ua_platform) ? $this->getHeader('HTTP_SEC_CH_UA_PLATFORM') : $data->sec_ch_ua_platform;
			$data->sec_ch_ua_platform_version = $this->isEmpty($data->sec_ch_ua_platform_version) ? $this->getHeader('HTTP_SEC_CH_UA_PLATFORM_VERSION') : $data->sec_ch_ua_platform_version;
			$data->sec_ch_width = $this->isEmpty($data->sec_ch_width) ? $this->getHeader('HTTP_SEC_CH_WIDTH') : $data->sec_ch_width;
			$data->sec_ch_viewport_width = $this->isEmpty($data->sec_ch_viewport_width) ? $this->getHeader('HTTP_SEC_CH_VIEWPORT_WIDTH') : $data->sec_ch_viewport_width;
			$data->title = $this->isEmpty($data->title) ? '' : $data->title;
			$data->referrer = $this->isEmpty($data->referrer) ? $this->getReferrer() : $data->referrer;
			$data->screen_width = $this->isEmpty($data->screen_width) ? 0 : $data->screen_width;
			$data->screen_height = $this->isEmpty($data->screen_height) ? 0 : $data->screen_height;
			$response = $this->client->post(self::EVENT_ENDPOINT, [
				'headers' => $this->getRequestHeader(),
				'json' => [
					'event_name' => $name,
					'event_duration' => $duration,
					'event_meta' => $meta,
					'url' => $data->url,
					'ip' => $data->ip,
					'user_agent' => $data->user_agent,
					'accept_language' => $data->accept_language,
					'sec_ch_ua' => $data->sec_ch_ua,
					'sec_ch_ua_mobile' => $data->sec_ch_ua_mobile,
					'sec_ch_ua_platform' => $data->sec_ch_ua_platform,
					'sec_ch_ua_platform_version' => $data->sec_ch_ua_platform_version,
					'sec_ch_width' => $data->sec_ch_width,
					'sec_ch_viewport_width' => $data->sec_ch_viewport_width,
					'title' => $data->title,
					'referrer' => $data->referrer,
					'screen_width' => intval($data->screen_width),
					'screen_height' => intval($data->screen_height),
					'tags' => $data->tags
				]
			]);
			return json_decode($response->getBody());
		} catch(\GuzzleHttp\Exception\RequestException $e) {
			if ($e->getResponse()->getStatusCode() == 401 && $retry) {
				$this->refreshToken();
				return $this->event($name, $duration, $meta, $data, false);
			} else if ($e->getResponse()->getStatusCode() != 200) {
				throw new \Exception('Error sending event: '.$e->getResponse()->getBody());
			}
		}

		return null;
	}

	function session($retry = true) {
		try {
			$response = $this->client->post(self::SESSION_ENDPOINT, [
				'headers' => $this->getRequestHeader(),
				'json' => [
					'ip' => $this->getHeader('REMOTE_ADDR'),
					'user_agent' => $this->getHeader('HTTP_USER_AGENT'),
					'sec_ch_ua' => $this->getHeader('HTTP_SEC_CH_UA'),
					'sec_ch_ua_mobile' => $this->getHeader('HTTP_SEC_CH_UA_MOBILE'),
					'sec_ch_ua_platform' => $this->getHeader('HTTP_SEC_CH_UA_PLATFORM'),
					'sec_ch_ua_platform_version' => $this->getHeader('HTTP_SEC_CH_UA_PLATFORM_VERSION'),
					'sec_ch_width' => $this->getHeader('HTTP_SEC_CH_WIDTH'),
					'sec_ch_viewport_width' => $this->getHeader('HTTP_SEC_CH_VIEWPORT_WIDTH'),
				]
			]);
			return json_decode($response->getBody());
		} catch(\GuzzleHttp\Exception\RequestException $e) {
			if ($e->getResponse()->getStatusCode() == 401 && $retry) {
				$this->refreshToken();
				return $this->session(false);
			} else if ($e->getResponse()->getStatusCode() != 200) {
				throw new \Exception('Error extending session: '.$e->getResponse()->getBody());
			}
		}

		return null;
	}

	function domain($retry = true) {
		try {
			if ($this->getAccessToken() === '' && $retry) {
				$this->refreshToken();
			}

			$response = $this->client->get(self::DOMAIN_ENDPOINT, [
				'headers' => $this->getRequestHeader()
			]);
			$domains = json_decode($response->getBody());

			if (count($domains) !== 1) {
				throw new \Exception('Error reading domain from result');
			}

			return $domains[0];
		} catch(\GuzzleHttp\Exception\RequestException $e) {
			if ($e->getResponse()->getStatusCode() == 401 && $retry) {
				$this->refreshToken();
				return $this->domain(false);
			} else if ($e->getResponse()->getStatusCode() != 200) {
				throw new \Exception('Error getting domain: '.$e->getResponse()->getBody());
			}
		}

		return null;
	}

	function sessionDuration(Filter $filter) {
		return $this->performGet(self::SESSION_DURATION_ENDPOINT, $filter);
	}

	function timeOnPage(Filter $filter) {
		return $this->performGet(self::TIME_ON_PAGE_ENDPOINT, $filter);
	}

	function utmSource(Filter $filter) {
		return $this->performGet(self::UTM_SOURCE_ENDPOINT, $filter);
	}

	function utmMedium(Filter $filter) {
		return $this->performGet(self::UTM_MEDIUM_ENDPOINT, $filter);
	}

	function utmCampaign(Filter $filter) {
		return $this->performGet(self::UTM_CAMPAIGN_ENDPOINT, $filter);
	}

	function utmContent(Filter $filter) {
		return $this->performGet(self::UTM_CONTENT_ENDPOINT, $filter);
	}

	function utmTerm(Filter $filter) {
		return $this->performGet(self::UTM_TERM_ENDPOINT, $filter);
	}

	function totalVisitors(Filter $filter) {
		return $this->performGet(self::TOTAL_VISITORS_ENDPOINT, $filter);
	}

	function visitors(Filter $filter) {
		return $this->performGet(self::VISITORS_ENDPOINT, $filter);
	}

	function pages(Filter $filter) {
		return $this->performGet(self::PAGES_ENDPOINT, $filter);
	}

	function entryPages(Filter $filter) {
		return $this->performGet(self::ENTRY_PAGES_ENDPOINT, $filter);
	}

	function exitPages(Filter $filter) {
		return $this->performGet(self::EXIT_PAGES_ENDPOINT, $filter);
	}

	function conversionGoals(Filter $filter) {
		return $this->performGet(self::CONVERSION_GOALS_ENDPOINT, $filter);
	}

	function events(Filter $filter) {
		return $this->performGet(self::EVENTS_ENDPOINT, $filter);
	}

	function eventMetadata(Filter $filter) {
		return $this->performGet(self::EVENT_METADATA_ENDPOINT, $filter);
	}

	function listEvents(Filter $filter) {
		return $this->performGet(self::LIST_EVENTS_ENDPOINT, $filter);
	}

	function growth(Filter $filter) {
		return $this->performGet(self::GROWTH_RATE_ENDPOINT, $filter);
	}

	function activeVisitors(Filter $filter) {
		return $this->performGet(self::ACTIVE_VISITORS_ENDPOINT, $filter);
	}

	function timeOfDay(Filter $filter) {
		return $this->performGet(self::TIME_OF_DAY_ENDPOINT, $filter);
	}

	function languages(Filter $filter) {
		return $this->performGet(self::LANGUAGE_ENDPOINT, $filter);
	}

	function referrer(Filter $filter) {
		return $this->performGet(self::REFERRER_ENDPOINT, $filter);
	}

	function os(Filter $filter) {
		return $this->performGet(self::OS_ENDPOINT, $filter);
	}

	function osVersions(Filter $filter) {
		return $this->performGet(self::OS_VERSION_ENDPOINT, $filter);
	}

	function browser(Filter $filter) {
		return $this->performGet(self::BROWSER_ENDPOINT, $filter);
	}

	function browserVersions(Filter $filter) {
		return $this->performGet(self::BROWSER_VERSION_ENDPOINT, $filter);
	}

	function country(Filter $filter) {
		return $this->performGet(self::COUNTRY_ENDPOINT, $filter);
	}

	function region(Filter $filter) {
		return $this->performGet(self::REGION_ENDPOINT, $filter);
	}

	function city(Filter $filter) {
		return $this->performGet(self::CITY_ENDPOINT, $filter);
	}

	function platform(Filter $filter) {
		return $this->performGet(self::PLATFORM_ENDPOINT, $filter);
	}

	function screen(Filter $filter) {
		return $this->performGet(self::SCREEN_ENDPOINT, $filter);
	}

	function tagKeys(Filter $filter) {
		return $this->performGet(self::TAG_KEYS_ENDPOINT, $filter);
	}

	function tags(Filter $filter) {
		return $this->performGet(self::TAG_DETAILS_ENDPOINT, $filter);
	}

	function keywords(Filter $filter) {
		return $this->performGet(self::KEYWORDS_ENDPOINT, $filter);
	}

	private function performGet($url, Filter $filter, $retry = true) {
		try {
			if ($this->getAccessToken() === '' && $retry) {
				$this->refreshToken();
			}

			$query = http_build_query($filter);
			$response = $this->client->get($url.'?'.$query, [
				'headers' => $this->getRequestHeader()
			]);
			return json_decode($response->getBody());
		} catch(\GuzzleHttp\Exception\RequestException $e) {
			if ($e->getResponse()->getStatusCode() == 401 && $retry) {
				$this->refreshToken();
				return $this->performGet($url, $filter, false);
			} else if ($e->getResponse()->getStatusCode() != 200) {
				throw new \Exception('Error getting result for '.$url.': '.$e->getResponse()->getBody());
			}
		}
	}

	private function refreshToken() {
		try {
			if (empty($this->clientID)) {
				throw new \Exception('Single access tokens cannot be refreshed');
			}

			$response = $this->client->post(self::AUTHENTICATION_ENDPOINT, [
				'headers' => [
					'Content-Type' => 'application/x-www-form-urlencoded'
				],
				'json' => [
					'grant_type' => 'client_credentials',
					'client_id' => $this->clientID,
					'client_secret' => $this->clientSecret
				]
			]);

			if ($response->getStatusCode() != 200) {
				throw new \Exception('Error refreshing token '.$response->getStatusCode().': '.$response->getBody());
			}

			$resp = json_decode($response->getBody());
			$_SESSION['pirsch_access_token'] = $resp->access_token;
		} catch(\GuzzleHttp\Exception\RequestException $e) {
			if ($e->getResponse()->getStatusCode() != 200) {
				throw new \Exception('Error refreshing token '.$e->getResponse()->getStatusCode().': '.$e->getResponse()->getBody());
			}
		}
	}

	private function getRequestHeader() {
		return [
			'Authorization' => 'Bearer '.$this->getAccessToken(),
			'Content-Type' => 'application/json'
		];
	}

	private function getAccessToken() {
		if (empty($this->clientID)) {
			return $this->clientSecret;
		} else if (isset($_SESSION['pirsch_access_token'])) {
			return $_SESSION['pirsch_access_token'];
		}

		return '';
	}

	private function isUnauthorized($header) {
		if (is_null($header)) {
			return false;
		}

		return strpos($header, '401') !== false;
	}

	private function getRequestURL() {
		return 'http'.(isset($_SERVER['HTTPS']) ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}

	private function getReferrer() {
		$referrer = $this->getHeader('HTTP_REFERER');

		if (empty($referrer)) {
			foreach(self::REFERRER_QUERY_PARAMS as $key) {
				$referrer = $this->getQueryParam($key);

				if ($referrer != '') {
					return $referrer;
				}
			}
		}

		return $referrer;
	}

	private function getHeader($name) {
		if (isset($_SERVER[$name])) {
			return $_SERVER[$name];
		}

		return '';
	}

	private function getQueryParam($name) {
		if (isset($_GET[$name])) {
			return $_GET[$name];
		}

		return '';
	}

	private function isEmpty($str) {
		if (is_null($str)) {
			return true;
		}

		return empty(trim($str, ' \t\n'));
	}
}
