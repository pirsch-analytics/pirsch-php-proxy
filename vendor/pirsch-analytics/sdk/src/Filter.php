<?php
namespace Pirsch;

const SCALE_DAY = 'day';
const SCALE_WEEK = 'week';
const SCALE_MONTH = 'month';
const SCALE_YEAR = 'year';

const CUSTOM_METRIC_TYPE_INTEGER = 'integer';
const CUSTOM_METRIC_TYPE_FLOAT = 'float';

class Filter {
	public $id;
	public $from;
	public $to;
	public $tz;
	public $start;
	public $scale;
	public $path;
	public $pattern;
	public $entry_path;
	public $exit_path;
	public $event;
	public $event_meta_key;
	// TODO $meta_...
	public $language;
	public $country;
	public $region;
	public $city;
	public $referrer;
	public $referrer_name;
	public $os;
	public $browser;
	public $platform;
	public $screen_class;
	public $utm_source;
	public $utm_medium;
	public $utm_campaign;
	public $utm_content;
	public $utm_term;
	// TODO $tag_...
	public $custom_metric_key;
	public $custom_metric_type;
	public $include_avg_time_on_page;
	public $offset;
	public $limit;
	public $sort;
	public $direction;
	public $search;
}
