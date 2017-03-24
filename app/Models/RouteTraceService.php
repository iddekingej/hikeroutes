<?php 
namespace App\Models;

use App\Lib\TableService;
use App\Lib\GPXReader;
use App\Lib\AddressService;

class RouteTraceException extends \Exception
{
	function __construct($p_msg,$p_previous)
	{
		parent::__construct($p_msg,1,$p_previous);
	}
}

class RouteTraceService extends TableService{
	static protected $model=RouteTrace::class;
	
	public static function updateGpxFile(RouteTrace $p_routeTrace,$p_gpxData)
	{
		$l_routeFile=$p_routeTrace->routeFile()->getResults();
		$l_routeFile->gpxdata=$p_gpxData;
		$l_routeFile->save();
		$l_gpxParser=new GPXReader();
		$l_gpxList=$l_gpxParser->parse($p_gpxData);
		$l_locData=AddressService::locationStringFromGPX($l_gpxList->getStart());
		$l_location=LocationService::getLocation($l_locData->data);
		if($l_location !== null){
			$l_id_location=$l_location->id;
		} else {
			$l_id_location=null;
		}
		$l_info=$l_gpxList->getInfo();
		$p_routeTrace->id_location=$l_id_location;
		$p_routeTrace->startdate=$l_gpxList->getStart()->getDatePart();
		$p_routeTrace->minlon=$l_info->minLon;
		$p_routeTrace->maxlon=$l_info->maxLon;
		$p_routeTrace->minlat=$l_info->minLat;
		$p_routeTrace->maxlat=$l_info->maxLat;
		$p_routeTrace->save();
	}
	
	public static function addGpxFile($p_gpxData)
	{
		$l_gpxParser=new GPXReader();
		$l_gpxList=$l_gpxParser->parse($p_gpxData);
		$l_locData=AddressService::locationStringFromGPX($l_gpxList->getStart());
		$l_location=LocationService::getLocation($l_locData->data);
		if($l_location !== null){
			$l_id_location=$l_location->id;
		} else {
			$l_id_location=null;
		}
		$l_routeFile=RouteFile::create(["gpxdata"=>$p_gpxData]);
		$l_info=$l_gpxList->getInfo();
		
		$l_trace=RouteTrace::create([
			"id_routefile"=>$l_routeFile->id
		,	"id_location"=>$l_id_location
		,	"startdate"=>$l_gpxList->getStart()->getDatePart()
		,	"minlon"=>$l_info->minLon
		,	"maxlon"=>$l_info->maxLon
		,	"minlat"=>$l_info->minLat
		,	"maxlat"=>$l_info->maxLat
		,	"id_user"=>\Auth::user()->id
		]);
		return $l_trace;
	}
	
}

?>