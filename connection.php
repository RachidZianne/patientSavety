<?php
error_reporting(1);
require_once __DIR__ . '/vendor/autoload.php';
session_start();
class GA
{
	private $client;
	public function __construct()
	{
		$this->client = new Google_Client();
		if(file_exists(__DIR__.'/config/client_secret_native.json'))
		{
			$this->checkJSONFile();
		}
	}
	public function checkJSONFile()
	{
		try{
			$this->client->setAuthConfig(__DIR__.'/config/client_secret_native.json');	
		}
		catch(exception $e)
		{
			return $e->getMessage();
		}
	}
	public function getAuthUrl()
	{
		$this->client->addScope(Google_Service_Analytics::ANALYTICS_READONLY);
		$this->client->setAccessType('offline');
		$this->client->setRedirectUri('urn:ietf:wg:oauth:2.0:oob');
		return $this->client->createAuthUrl();
	}
	public function getAccessToken($code)
	{
		$this->client->authenticate($code);
		$return = [];
		$return['accessToken'] = $this->client->getAccessToken();
		$return['refreshToken'] = $this->client->getRefreshToken();
		return $return;
	}
	public function checkAccessToken($objSite)
	{
		$return = [];
		$this->client->setAccessToken($objSite->accessToken);
		
		$this->client->refreshToken($objSite->refreshToken);
		$accessToken = $this->client->getAccessToken(); 
		
		if ($this->client->isAccessTokenExpired()) {
			$this->client->refreshToken($objSite->refreshToken);
			$objSite->accessToken = json_encode($this->client->getAccessToken());
			$return['isUpdated'] = true;
		}
		else
		{
			$return['isUpdated'] = false;
		}
		$return['objSite'] = $objSite;
		return $return; 
	}
	function getReport($objSite,$arrMetrics,$arrConfig) {
		
	
		$this->client->setAccessToken($objSite->accessToken);
		$analytics = new Google_Service_AnalyticsReporting($this->client);
		  // Replace with your view ID, for example XXXX.
		$VIEW_ID = $objSite->project_id;

		$request = new Google_Service_AnalyticsReporting_ReportRequest();
		$request->setViewId($VIEW_ID);
		
		// Create the Metrics object.
		$setMetrics=[];
		foreach($arrMetrics as $key => $alias)
		{
			$ga_metrics = new Google_Service_AnalyticsReporting_Metric();
			$ga_metrics->setExpression($key);
			$ga_metrics->setAlias($alias);
			$setMetrics[] = $ga_metrics;
		}
		$request->setMetrics($setMetrics);
		
		//Create the Dimensions object.
		if(!empty($arrConfig['dimensions']))
		{
			$setDimensions=[];
			foreach($arrConfig['dimensions'] as $dimensions)
			{
				$objDimensions = new Google_Service_AnalyticsReporting_Dimension();
				$objDimensions->setName($dimensions);
				$setDimensions[] = $objDimensions;
			}
			$request->setDimensions($setDimensions);
		}
		if(!empty($arrConfig['filter']))
		{
			$setFilter=[];
			foreach($arrConfig['filter'] as $filter)
			{
				$dimensionFilter = new Google_Service_AnalyticsReporting_DimensionFilter();
				$dimensionFilter->setDimensionName($filter['dimension']);
				$dimensionFilter->setOperator($filter['operator']);
				$dimensionFilter->setExpressions([$filter['value']]);
				$setFilter[] = $dimensionFilter;
			}
			// Create the DimensionFilterClauses
			$dimensionFilterClause = new Google_Service_AnalyticsReporting_DimensionFilterClause();
			$dimensionFilterClause->setFilters($setFilter);
			$request->setDimensionFilterClauses(array($dimensionFilterClause));
		}
        if(!empty($arrConfig['StartDate']))
        {
		  // Create the DateRange object.
            $dateRange = new Google_Service_AnalyticsReporting_DateRange();
            $dateRange->setStartDate($arrConfig['StartDate']);
            $dateRange->setEndDate($arrConfig['EndDate']);
            $request->setDateRanges($dateRange);
        }
        
        if(!empty($arrConfig['sort']))
        {
            $orderby =new Google_Service_AnalyticsReporting_OrderBy();
            $orderby->setFieldName($arrConfig['sort']['field']);
            $orderby->setSortOrder($arrConfig['sort']['order']);
            $request->setOrderBys($orderby);
        }
        if(!empty($arrConfig['page_size']))
        {
            $request->setPageSize($arrConfig['page_size']);
        }
		$return = [];
		try{
			$body = new Google_Service_AnalyticsReporting_GetReportsRequest();
			$body->setReportRequests( array( $request) );
			$return['data'] = $analytics->reports->batchGet( $body );
			$return['is_success'] = true;
		}
		catch(exception $e)
		{
			$return['is_success'] = false;
			$return['error'] = json_decode($e->getMessage());
		}
		return $return;
	}
    
    function getLiveUser($objSite)
    {
        $this->client->setAccessToken($objSite->accessToken);
		$analytics = new Google_Service_Analytics($this->client);
        
        $optParams = ['dimensions' => 'rt:medium'];
//        $optParams = ['dimensions' => 'rt:userType'];
//        $optParams = ['dimensions' => 'rt:country'];
//        $optParams = ['dimensions' => 'rt:browser'];
//        $optParams = ['dimensions' => 'rt:operatingSystem'];
        try {
            $results = $analytics->data_realtime->get(
                'ga:'.$objSite->project_id,
                'rt:activeUsers',
                $optParams);
            // Success. 
            $return['is_success'] = true;  
            $return['liveUser'] = $results->totalsForAllResults['rt:activeUsers'];
            return $return;
        } catch (apiServiceException $e) {
            // Handle API service exceptions.
            $return['is_success'] = false;
			$return['error'] = json_decode($e->getMessage());
        }
        return $return;
    }

}
if(file_exists("./config/database.php"))
{
	require_once "./config/database.php";
	class DB
	{
		private $db;
		private $ga;
		public function __construct()
		{
			$this->db = new mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
			if ($this->db->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			} 
			$this->ga = new GA();
		}
		
		public function getAccessToken($arrData,$is_newSite=false)
		{
			$return = [];
			if($is_newSite)
			{
				if(empty($arrData['code']))
				{
					$return['isSuccess'] = false;
					$return['message'] = "Code is emply";
				}
				else
				{
					$token = $this->ga->getAccessToken($arrData['code']);
					if(empty($token['accessToken']))
					{
						$return['isSuccess'] = false;
						$return['message'] = "Code is invalid";
					}
					else
					{
						$arrInsert = [];
						$arrInsert['name'] = $arrData['name'];
						$arrInsert['project_id'] = $arrData['project_id'];
						$arrInsert['accessToken'] = json_encode($token['accessToken']);
						$arrInsert['refreshToken'] = $token['refreshToken'];
						if($this->insert('site',$arrInsert))
						{
							$return['isSuccess'] = true;
						}
						else
						{
							$return['isSuccess'] = false;
							$return['message'] = "DB Error";
						}
					}
				}
			}
			else
			{
				if(isset($arrData['id']))
				{
					$arrSite = $this->select('site',['id'=>$arrData['id']]);
				}
				elseif(isset($arrData['name']))
				{
					$arrSite = $this->select('site',['name'=>$arrData['name']]);
				}
				
				if($arrSite['total_record']==1)
				{
					$record = $arrSite['rs']->fetch_object();
					
					$token =$this->ga->checkAccessToken($record);
					
					if($token['isUpdated'])
					{
						$this->update('site',['accessToken'=>$token['objSite']->accessToken],['id'=>$record->id]);
					}
					$return['isSuccess'] = true;
					$return['objSite'] = $token['objSite'];
				}
				else
				{
					$return['isSuccess'] = false;
					$return['message'] = "Site is invalid";
				}
				
			}
			return $return;
		}
		
		public function insert($tbl,$data)
		{
			$sql = "INSERT INTO `$tbl`";
			$fields = array_keys($data);
			$values = array_values($data);
			$sql .= "(`".implode("`,`",$fields)."`)";
			$sql .= " VALUES('".implode("','",$values)."')";
			return $this->db->query($sql);
		}
        public function delete($tbl,$where)
		{
			$sql = "DELETE FROM `$tbl`";
            $arrWhere = [];
			foreach($where as $field => $val)
			{
				$arrWhere[] = " `$field` = '".mysqli_real_escape_string($this->db,$val)."' ";	
			}
			$sql .= "WHERE 1 AND".implode(" AND ",$arrWhere);	
			return $this->db->query($sql);
		}
		public function update($tbl,$data,$where)
		{
			$sql = "UPDATE `$tbl` SET ";
			
			$arrfield = [];
			foreach($data as $field => $val)
			{
				$arrfield[] = " `$field` = '".mysqli_real_escape_string($this->db,$val)."' ";	
			}
			$sql .= " ".implode(", ",$arrfield)." ";
			$arrWhere = [];
			foreach($where as $field => $val)
			{
				$arrWhere[] = " `$field` = '".mysqli_real_escape_string($this->db,$val)."' ";	
			}
			$sql .= "WHERE 1 AND".implode(" AND ",$arrWhere);		
			return $this->db->query($sql);
		}
		public function select($tbl,$where=[],$field="*")
		{
			$sql = "SELECT ";
			if(is_array($field))
			{
				$sql .= "`".explode("`,`",$field)."`";  
			}
			else
			{
				$sql .= " $field ";
			}
			$sql .= " FROM `$tbl` ";
			if(!empty($where))
			{
				$arrWhere = [];
				foreach($where as $field => $val)
				{
					$arrWhere[] = " `$field` = '".mysqli_real_escape_string($this->db,$val)."' ";	
				}
				$sql .= "WHERE 1 AND".implode(" AND ",$arrWhere);		
			}
			$rs = $this->db->query($sql);
			
			$return = [];
			if($rs->num_rows>0)
			{
				$return['total_record'] = $rs->num_rows;
				$return['rs'] = $rs;
			}
			else
			{
				$return['total_record'] = 0;
			}
			return $return;
		}
	}
}

?>
