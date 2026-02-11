 <?php
header('Content-Type: application/json; charset=UTF-8');
include_once('../includes/dbopen.php');
include_once('../includes/common.php');

$ClassID = isset($_REQUEST["ClassID"]) ? $_REQUEST["ClassID"] : "";
$MemberType = isset($_REQUEST["MemberType"]) ? $_REQUEST["MemberType"] : "";

if ($MemberType=="1"){


	$Sql = "
			select 
				A.*,
				date_format(A.StartDateTime, '%Y-%m-%d') as StartDate,
				B.ClassOrderTimeTypeID 
			from Classes A 
				inner join ClassOrders B on A.ClassOrderID=B.ClassOrderID
			where ClassID=$ClassID 
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$TeacherID=$Row["TeacherID"];
	$StartDateTimeStamp=$Row["StartDateTimeStamp"];
	$EndDateTimeStamp=$Row["EndDateTimeStamp"];
	$StartDateTime=$Row["StartDateTime"];
	$StartDate=$Row["StartDate"];
	$ClassOrderTimeTypeID=$Row["ClassOrderTimeTypeID"];

	$ClassRunMinute = $ClassOrderTimeTypeID*10;


	$Sql = "
			select 
				A.*
			from ClassTeacherEnters A 
			where A.TeacherID=:TeacherID 
				and A.ClassDate=:ClassDate 
				and A.ClassStartDateTime=:ClassStartDateTime 
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':TeacherID', $TeacherID);
	$Stmt->bindParam(':ClassDate', $StartDate);
	$Stmt->bindParam(':ClassStartDateTime', $StartDateTime);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$ClassStartDateTime=$Row["ClassStartDateTime"];
	$ClassEnterDateTime=$Row["ClassEnterDateTime"];

	if (!$ClassStartDateTime){

		$Sql = " insert into ClassTeacherEnters ( ";
			$Sql .= " TeacherID, ";
			$Sql .= " ClassDate, ";
			$Sql .= " ClassStartDateTime, ";
			$Sql .= " ClassRunMinute, ";
			$Sql .= " ClassEnterDateTime ";
		$Sql .= " ) values ( ";
			$Sql .= " :TeacherID, ";
			$Sql .= " :ClassDate, ";
			$Sql .= " :ClassStartDateTime, ";
			$Sql .= " :ClassRunMinute, ";
			$Sql .= " now() ";
		$Sql .= " ) ";
		//echo $Sql."<br>";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':TeacherID', $TeacherID);
		$Stmt->bindParam(':ClassDate', $StartDate);
		$Stmt->bindParam(':ClassStartDateTime', $StartDateTime);
		$Stmt->bindParam(':ClassRunMinute', $ClassRunMinute);
		$Stmt->execute();
		$Stmt = null;

	}else{

		if ($ClassEnterDateTime==""){

			$Sql = "
					update ClassTeacherEnters 
						set ClassEnterDateTime=now() 
					where TeacherID=$TeacherID 
						and ClassDate='$StartDate' 
						and ClassStartDateTime='$StartDateTime' 
			";
			$Stmt = $DbConn->prepare($Sql);
			//$Stmt->bindParam(':TeacherID', $TeacherID);
			//$Stmt->bindParam(':ClassDate', $StartDate);
			//$Stmt->bindParam(':ClassStartDateTime', $StartDateTime);
			$Stmt->execute();
			$Stmt->setFetchMode(PDO::FETCH_ASSOC);
			$Stmt = null;

		}

	}




	$Sql = "
			select 
				* 
			from Classes A 
			where A.TeacherID=$TeacherID 
				and A.StartDateTimeStamp=$StartDateTimeStamp 
				and A.EndDateTimeStamp=$EndDateTimeStamp 
				and A.ClassAttendState<=3 
	";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	
	$ClassIDs = "|";
	$ClassOrderIDs = "|";
	while($Row = $Stmt->fetch()) {
		$ClassID = $Row["ClassID"];
		$ClassOrderID = $Row["ClassOrderID"];
		$ClassIDs = $ClassIDs . $ClassID . "|";
		$ClassOrderIDs = $ClassOrderIDs . $ClassOrderID . "|";
	}
	$Stmt = null;


	$ArrClassID = explode("|", $ClassIDs);
	$ArrClassOrderID = explode("|", $ClassOrderIDs);

	for ($ii=1;$ii<=count($ArrClassID)-2;$ii++){

		$ClassID = $ArrClassID[$ii];
		$ClassOrderID = $ArrClassOrderID[$ii];

		$Sql = "
				select 
						A.*
				from Classes A 
				where A.ClassID=:ClassID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassID', $ClassID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;

		$TeacherInDateTime = $Row["TeacherInDateTime"];

		if ($TeacherInDateTime==""){
			$Sql = " update Classes set TeacherInDateTime=now() where ClassID=:ClassID";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassID', $ClassID);
			$Stmt->execute();
			$Stmt = null;
		}

	}
}


$ArrValue["CheckResult"] = "";

$QueryResult = my_json_encode($ArrValue);
echo $QueryResult; 

function my_json_encode($arr){
	//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
	array_walk_recursive($arr, function (&$item, $key) { if (is_string($item)) $item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8'); });
	return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
}

include_once('../includes/dbclose.php');
?>