<?php
require_once 'connection.php';
if(file_exists("./config/database.php") && file_exists("./config/client_secret_native.json"))
{
    $retuen['message'] = "Database & GA already config.";
    $retuen['isSuccess'] = false;
    echo json_encode($retuen);
    exit();
}
if(!empty($_POST) && !empty($_FILES))
{
    $retuen = [];
    $con = new mysqli($_POST['dbhost'],$_POST['dbuser'],$_POST['dbpassword']);
    if ($con->connect_errno) {
        $retuen['message'] = "Connect failed: ". $con->connect_error;
        $retuen['isSuccess'] = false;
        echo json_encode($retuen);
        exit();
    }
    else
    {
        $my_file = './config/';
        if(chmod($my_file, 0777))
        {
            $my_file .= 'database.php';
            if($handle = fopen($my_file, 'w')) 
            {
                $data = '<?php define("DBHOST","'.$_POST['dbhost'].'");';
                $data .= ' define("DBUSER","'.$_POST['dbuser'].'");';
                $data .= ' define("DBPASS","'.$_POST['dbpassword'].'");';
                $data .= ' define("DBNAME","'.$_POST['dbname'].'"); ?>'; 
				fwrite($handle, $data);

				$con->query("CREATE DATABASE IF NOT EXISTS `".$_POST['dbname']."`;");
				$con->query("CREATE TABLE `".$_POST['dbname']."`.`admin` (
							  `username` varchar(50) PRIMARY KEY,
							  `password` varchar(50) NOT NULL
							)");
				$con->query("INSERT INTO `".$_POST['dbname']."`.`admin` VALUES('".$_POST['userName']."','".$_POST['password']."')");
				
				$con->query("CREATE TABLE `".$_POST['dbname']."`.`site` (
							  `id` int(11) PRIMARY KEY AUTO_INCREMENT,
							  `name` varchar(50) NOT NULL,
							  `project_id` varchar(50) NOT NULL,
							  `accessToken` varchar(1000) NOT NULL,
							  `refreshToken` varchar(500) NOT NULL,
							  `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
							  `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
							)");
				
				
				if($_FILES['client_secret']['error'] == 0)
				{
					if($_FILES['client_secret']['type']=="application/json")
					{
						move_uploaded_file($_FILES['client_secret']['tmp_name'],__dir__.'/config/client_secret_native.json');
						$ga = new GA();
						if(empty($jsonError = $ga->checkJSONFile()))
						{
							$retuen['isSuccess'] = true;
							echo json_encode($retuen);
							exit();
						}
						else
						{
							$retuen['isSuccess'] = false;
							$retuen['message'] = $jsonError;
							echo json_encode($retuen);
							unlink(__dir__.'/config/client_secret_native.json');
							exit();
						}
					}
					else
					{
						$retuen['message'] = 'File type is invalid. JSON file is only allow.';
						$retuen['isSuccess'] = false;
						echo json_encode($retuen);
						exit();
					}
				}	
				else
				{
					$retuen['message'] = 'Error in file upload.';
					$retuen['isSuccess'] = false;
					echo json_encode($retuen);
					exit();
				}
			}
			else
			{
				$retuen['message'] = 'Cannot open file: '.$my_file;
				$retuen['isSuccess'] = false;
				echo json_encode($retuen);
				exit();
			}
		} 
		else
		{
			$retuen['message'] = 'Cannot change file permisstion.';
			$retuen['isSuccess'] = false;
			echo json_encode($retuen);
			exit();
		}
	}
}
 ?>
