<?php
require_once 'connection.php';
require_once 'admin_security.php';

function getDurationFromText($duration)
{
	$arrDate = [];
	$arrField = [];	
	$duration = strtolower($duration);
	if($duration == "week")
	{
		$arrDate['dimension'] = "ga:dayOfWeekName";
		$arrDate['EndDate'] = date('Y-m-d');
		$startDate = $arrDate['EndDate'];
		for($i=1;$i<=7;$i++)
		{
			$startDate = date('Y-m-d',strtotime('-1 day', strtotime($startDate)));
			$arrField[date('l',strtotime($startDate))] = date('l',strtotime($startDate));
		}
		$arrDate['StartDate'] = $startDate;
		$arrDate['arrField'] = $arrField;
	}
	if($duration == "month")
	{
		$arrDate['dimension'] = "ga:date";
		$arrDate['EndDate'] = date('Y-m-d');
		$startDate = $arrDate['EndDate'];
		for($i=1;$i<=30;$i++)
		{
			$startDate = date('Y-m-d',strtotime('-1 day', strtotime($startDate)));
			$arrField[date('Ymd',strtotime($startDate))] = date('d-m-Y',strtotime($startDate));
		}
		$arrDate['StartDate'] = $startDate;
		$arrDate['arrField'] = $arrField;
	}
	if($duration == "year")
	{
		$arrDate['dimension'] = "ga:yearMonth";
		$arrDate['EndDate'] = date('Y-m-d',strtotime('+1 month',time()));
		$startDate = $arrDate['EndDate'];
		for($i=1;$i<=12;$i++)
		{
			$startDate = date('Y-m-d',strtotime('-1 month', strtotime($startDate)));
			$arrField[date('Ym',strtotime($startDate))] = date('F',strtotime($startDate));
		}
		$arrDate['StartDate'] = $startDate;
		$arrDate['arrField'] = $arrField;
	}
	return $arrDate;
}

function printResults($reports) {
	$arrReport = [];
		  for ( $reportIndex = 0; $reportIndex < count( $reports ); $reportIndex++ ) {
			  $arrReport[$reportIndex] = [];
			$report = $reports[ $reportIndex ];
			$header = $report->getColumnHeader();
			$dimensionHeaders = $header->getDimensions();
			$metricHeaders = $header->getMetricHeader()->getMetricHeaderEntries();
			$rows = $report->getData()->getRows();

			for ( $rowIndex = 0; $rowIndex < count($rows); $rowIndex++) {
				$arrReport[$reportIndex][$rowIndex]=[];
				$row = $rows[ $rowIndex ];
				$dimensions = $row->getDimensions();
				$metrics = $row->getMetrics();
				  
				$arrReport[$reportIndex][$rowIndex]['dimensionHeaders'] = [];
				for ($i = 0; $i < count($dimensionHeaders) && $i < count($dimensions); $i++) {
					$arrReport[$reportIndex][$rowIndex]['dimensionHeaders'][$dimensionHeaders[$i]] = $dimensions[$i];
				}
				$arrReport[$reportIndex][$rowIndex]['metrics'] = [];
				for ($j = 0; $j < count($metrics); $j++) {
					$values = $metrics[$j]->getValues();
					for ($k = 0; $k < count($values); $k++) {
					  $entry = $metricHeaders[$k];
					  $arrReport[$reportIndex][$rowIndex]['metrics'][$entry->getName()] = $values[$k];
					}
				}
			}
		  }
		  return  $arrReport;
		}

$db = new DB();
$ga = new GA();
$arrResult = [];
$site = $db->getAccessToken(['name'=>$_POST['site']]);
if($site['isSuccess'])
{
	$site = $site['objSite'];
	
    if($_POST['chartType'] == "live_user")
    {
        $arrResult = $ga->getLiveUser($site);
    }
    elseif($_POST['chartType'] == "session_by_device")
    {
        $arrMetrics = ['ga:sessions'=>'Sessions'];
        
        $arrParam = getDurationFromText('month');
		
        $arrConfig = [];
        $arrConfig['dimensions'] = ['ga:deviceCategory'];
        $arrConfig['StartDate'] = $arrParam['StartDate'];
		$arrConfig['EndDate'] = $arrParam['EndDate'];
		$analyticsData = $ga->getReport($site,$arrMetrics,$arrConfig);

        if($analyticsData['is_success'])
		{
            $arrProccessData = printResults($analyticsData['data']);
            $arrData = [];
            // get total
            $total = 0;
			foreach($arrProccessData[0] as $record)
			{
                $total += $record['metrics']['Sessions'];
            }
           
            foreach($arrProccessData[0] as $record)
			{
                $per = ($record['metrics']['Sessions']*100)/$total;
                $arrData[] = [ucfirst($record['dimensionHeaders']['ga:deviceCategory']),$per];
            }
            $arrResult['is_success'] = true;
			$arrResult['data'] = $arrData;
		}
		else
		{
			$arrResult['is_success'] = false;
			$arrResult['message'] = $analyticsData['error']->error->message;
		}
    }
    elseif($_POST['chartType'] == "active_pages")
    {
        $arrMetrics = ['ga:users'=>'Users','ga:percentNewSessions'=>'% New Sessions'];
        $arrParam = getDurationFromText('month');
        $arrConfig = [];
        $arrConfig['dimensions'] = ['ga:pagePath'];
        $arrConfig['StartDate'] = $arrParam['StartDate'];
		$arrConfig['EndDate'] = $arrParam['EndDate'];
        $arrConfig['sort'] = ['field'=>'ga:users','order'=>'DESCENDING'];
        $arrConfig['page_size'] = 10;
		$analyticsData = $ga->getReport($site,$arrMetrics,$arrConfig);

        if($analyticsData['is_success'])
		{
            $arrProccessData = printResults($analyticsData['data']);
            $arrData = [];
			foreach($arrProccessData[0] as $record)
			{
                $arrData[] = [
                    'PageUrl'=>$record['dimensionHeaders']['ga:pagePath'],
                    'Users' =>number_format($record['metrics']['Users']),
                    'NewSessions' =>number_format($record['metrics']['% New Sessions'],2)
                ];
            }
            $arrResult['is_success'] = true;
			$arrResult['data'] = $arrData;
		}
		else
		{
			$arrResult['is_success'] = false;
			$arrResult['message'] = $analyticsData['error']->error->message;
		}
    }
	elseif($_POST['chartType'] == "chart_user_type")
	{
		$arrMetrics = ['ga:users'=>'Users'];
		
		$arrParam = getDurationFromText($_POST['chart_duration']);
		
		$arrConfig = [];
		$arrConfig['StartDate'] = $arrParam['StartDate'];
		$arrConfig['EndDate'] = $arrParam['EndDate'];
		$arrConfig['dimensions'] = ['ga:userType',$arrParam['dimension']];
		$analyticsData = $ga->getReport($site,$arrMetrics,$arrConfig);
		//echo "<pre>";
		//print_r($analyticsData);
		//die;
		if($analyticsData['is_success'])
		{
			$arrProccessData = printResults($analyticsData['data']);
			
			$arrColor['New Visitor']['backgroundColor'] = "rgba(88, 103, 195,0.4)";
			$arrColor['New Visitor']['borderColor'] = "rgba(88, 103, 195,0.7)";
			$arrColor['New Visitor']['borderWidth'] = 0.6;
			
			$arrColor['Returning Visitor']['backgroundColor'] = "rgba(28, 134, 191,0.4)";
			$arrColor['Returning Visitor']['borderColor'] = "rgba(28, 134, 191,0.7)";
			$arrColor['Returning Visitor']['borderWidth'] = 0.6;
			
			$arrKeys = [];
			foreach($arrParam['arrField'] as $key => $value)
			{
				$arrKeys[$key] = 0;
			}
			
			$arrData = [];
			foreach($arrProccessData[0] as $record)
			{
				if(!array_key_exists($record['dimensionHeaders']['ga:userType'],$arrData))
				{
					$arrData[$record['dimensionHeaders']['ga:userType']] = [];
					$arrData[$record['dimensionHeaders']['ga:userType']]['data'] = $arrKeys;
				}
				$arrData[$record['dimensionHeaders']['ga:userType']]['data'][$record['dimensionHeaders'][$arrParam['dimension']]] = intval($record['metrics']['Users']);
			}
			$datasets = [];
			$arrTotal = [];
			foreach($arrData as $key => $data)
			{
				$tmpArray = [];
				$tmpArray['label'] = $key;
				$tmpArray['data'] = array_reverse(array_values($data['data']));
				$tmpArray = array_merge($tmpArray,$arrColor[$key]);
				$datasets[] = $tmpArray;
				$arrTotal[$key] = number_format(array_sum($data['data']));
			}
			
			$arrReturn = [];
			$arrReturn['labels'] = array_reverse(array_values($arrParam['arrField']));
			$arrReturn['datasets'] = $datasets;
		
			$arrResult['is_success'] = true;
			$arrResult['data'] = $arrReturn;
			$arrResult['total'] = $arrTotal;
		}
		else
		{
			$arrResult['is_success'] = false;
			$arrResult['message'] = $analyticsData['error']->error->message;
		}
	}
    elseif($_POST['chartType'] == "chart_custom")
	{
        $metrics = ucfirst(str_replace("ga:","",$_POST['metrics']));
        $dimension = ucfirst(str_replace("ga:","",$_POST['dimension']));
		$arrMetrics = [$_POST['metrics']=>$_POST['metrics']];
		
       
		$arrParam = getDurationFromText($_POST['chart_duration']);
		
        $tmpDate = explode("-",$_POST['chart_duration']);
         
		$arrConfig = [];
		$arrConfig['StartDate'] = date('Y-m-d',strtotime($tmpDate[0]));
		$arrConfig['EndDate'] = date('Y-m-d',strtotime($tmpDate[1]));
		$arrConfig['dimensions'] = [$_POST['dimension']];
        $arrConfig['sort'] = ['field'=>$_POST['metrics'],'order'=>'DESCENDING'];
        $arrConfig['page_size'] = 13;
        
		$analyticsData = $ga->getReport($site,$arrMetrics,$arrConfig);
		
		if($analyticsData['is_success'])
		{
			$arrProccessData = printResults($analyticsData['data']);
			$arrData = [];
			foreach($arrProccessData[0] as $record)
			{
				$arrData[$record['dimensionHeaders'][$_POST['dimension']]] = intval($record['metrics'][$_POST['metrics']]);
			}
            
			$arrReturn = [];
			$arrReturn['labels'] = array_keys($arrData);
			$arrReturn['datasets'][] = [
                'label'=> $dimension,
                'data'=>array_values($arrData),
                'backgroundColor'=>"rgba(88, 103, 195,0.4)"
            ];
            $arrResult['data'] = $arrReturn;  
			$arrResult['is_success'] = true;
		}
		else
		{
			$arrResult['is_success'] = false;
			$arrResult['message'] = $analyticsData['error']->error->message;
		}
	}
	elseif($_POST['chartType'] == "usersChart")
	{
		$arrMetrics = ['ga:users'=>'Users'];
		
		$arrParam = getDurationFromText($_POST['chart_duration']);
		
		$arrConfig = [];
		$arrConfig['StartDate'] = $arrParam['StartDate'];
		$arrConfig['EndDate'] = $arrParam['EndDate'];
		$arrConfig['dimensions'] = [$arrParam['dimension']];
		$analyticsData = $ga->getReport($site,$arrMetrics,$arrConfig);
		
		if($analyticsData['is_success'])
		{
			$arrProccessData = printResults($analyticsData['data']);
			
			$arrKeys = [];
			foreach($arrParam['arrField'] as $key => $value)
			{
				$arrKeys[$key] = 0;
			}
			
			$arrData = [];
			$arrData['Users']['data'] = $arrKeys;
			foreach($arrProccessData[0] as $record)
			{
				$arrData['Users']['data'][$record['dimensionHeaders'][$arrParam['dimension']]] = intval($record['metrics']['Users']);
			}
			
			$datasets = [];
			$arrTotal = [];
			foreach($arrData as $key => $data)
			{
				$tmpArray = [];
				$tmpArray['label'] = $key;
				$tmpArray['data'] = array_reverse(array_values($data['data']));
				$datasets[] = $tmpArray;
				$arrTotal[$key] = number_format(array_sum($data['data']));
			}
			
			$arrReturn = [];
			$arrReturn['labels'] = array_reverse(array_values($arrParam['arrField']));
			$arrReturn['datasets'] = $datasets;
		
			$arrResult['is_success'] = true;
			$arrResult['data'] = $arrReturn;
			$arrResult['total'] = $arrTotal;
		}
		else
		{
			$arrResult['is_success'] = false;
			$arrResult['message'] = $analyticsData['error']->error->message;
		}
		
	}
	elseif($_POST['chartType'] == "bounceRateChart")
	{
		$arrMetrics = ['ga:bounceRate'=>'Bounce Rate'];
		
		$arrParam = getDurationFromText($_POST['chart_duration']);
		
		$arrConfig = [];
		$arrConfig['StartDate'] = $arrParam['StartDate'];
		$arrConfig['EndDate'] = $arrParam['EndDate'];
		$arrConfig['dimensions'] = [$arrParam['dimension']];
		$analyticsData = $ga->getReport($site,$arrMetrics,$arrConfig);
		
		if($analyticsData['is_success'])
		{
			$arrProccessData = printResults($analyticsData['data']);
			
			$arrKeys = [];
			foreach($arrParam['arrField'] as $key => $value)
			{
				$arrKeys[$key] = 0;
			}
			
			$arrData = [];
			$arrData['Bounce Rate']['data'] = $arrKeys;
			foreach($arrProccessData[0] as $record)
			{
				$arrData['Bounce Rate']['data'][$record['dimensionHeaders'][$arrParam['dimension']]] = intval($record['metrics']['Bounce Rate']);
			}
			
			$datasets = [];
			$arrTotal = [];
			foreach($arrData as $key => $data)
			{
				$tmpArray = [];
				$tmpArray['label'] = $key;
				$tmpArray['data'] = array_reverse(array_values($data['data']));
				$datasets[] = $tmpArray;
				$arrTotal[$key] = array_sum($data['data'])/count($data['data'])."%";
			}
			
			$arrReturn = [];
			$arrReturn['labels'] = array_reverse(array_values($arrParam['arrField']));
			$arrReturn['datasets'] = $datasets;
		
			$arrResult['is_success'] = true;
			$arrResult['data'] = $arrReturn;
			$arrResult['total'] = $arrTotal;
		}
		else
		{
			$arrResult['is_success'] = false;
			$arrResult['message'] = $analyticsData['error']->error->message;
		}
		
	}
	elseif($_POST['chartType'] == "sessionDuration")
	{
		$arrMetrics = ['ga:sessionDuration'=>'Session Duration'];
		
		$arrParam = getDurationFromText($_POST['chart_duration']);
		
		$arrConfig = [];
		$arrConfig['StartDate'] = $arrParam['StartDate'];
		$arrConfig['EndDate'] = $arrParam['EndDate'];
		$arrConfig['dimensions'] = [$arrParam['dimension']];
		$analyticsData = $ga->getReport($site,$arrMetrics,$arrConfig);
		
		if($analyticsData['is_success'])
		{
			$arrProccessData = printResults($analyticsData['data']);
			//echo "<pre>";
			//print_r($arrProccessData);
			//die;
			$arrKeys = [];
			foreach($arrParam['arrField'] as $key => $value)
			{
				$arrKeys[$key] = 0;
			}
			
			$arrData = [];
			$arrData['Session Duration']['data'] = $arrKeys;
			foreach($arrProccessData[0] as $record)
			{
				$arrData['Session Duration']['data'][$record['dimensionHeaders'][$arrParam['dimension']]] = floatval($record['metrics']['Session Duration']);
			}
			
			$datasets = [];
			$arrTotal = [];
			foreach($arrData as $key => $data)
			{
				$tmpArray = [];
				$tmpArray['label'] = $key;
				$tmpArray['data'] = array_reverse(array_values($data['data']));
				$datasets[] = $tmpArray;
				$arrTotal[$key] = array_sum($data['data']);
			}
			
			$arrReturn = [];
			$arrReturn['labels'] = array_reverse(array_values($arrParam['arrField']));
			$arrReturn['datasets'] = $datasets;
		
			$arrResult['is_success'] = true;
			$arrResult['data'] = $arrReturn;
			$arrResult['total'] = $arrTotal;
		}
		else
		{
			$arrResult['is_success'] = false;
			$arrResult['message'] = $analyticsData['error']->error->message;
		}
		
	}
    elseif($_POST['chartType'] == "worldmap")
	{
		$arrMetrics = ['ga:newUsers'=>'Users'];
		
		$arrConfig = [];
		$arrConfig['dimensions'] = ['ga:latitude','ga:longitude','ga:country'];
        $arrConfig['filter'] = [
            ['dimension'=>'ga:latitude','operator'=>'REGEXP','value'=>'^[^0]']
        ];
        $arrConfig['sort'] = ['field'=>'ga:newUsers','order'=>'DESCENDING'];
        $arrConfig['page_size'] = 100;
        
		$analyticsData = $ga->getReport($site,$arrMetrics,$arrConfig);
 
		if($analyticsData['is_success'])
		{
			$arrProccessData = printResults($analyticsData['data']);
			$arrData = [];
			foreach($arrProccessData[0] as $record)
			{
                $location = $record['dimensionHeaders'];
				$arrData[] = ['latLng'=>[floatval($location['ga:latitude']),floatval($location['ga:longitude'])],'name'=>$location['ga:country']."(".$record['metrics']['Users'].")"];
			}
			$arrResult['is_success'] = true;
			$arrResult['data'] = $arrData;
		}
		else
		{
			$arrResult['is_success'] = false;
			$arrResult['message'] = $analyticsData['error']->error->message;
		}
		
	}
}
else
{
	$arrResult = $site;
}
echo json_encode($arrResult);
die;
?>
