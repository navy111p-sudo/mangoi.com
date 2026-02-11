<?
include_once('../../includes/dbopen.php');
include_once('../../includes/common.php');

$ClassOrderEndDateLogFileName = $_SERVER["PHP_SELF"];

	$requestData = file_get_contents('php://input'); 
	$JsonData = json_decode($requestData, true);
	$object = json_decode($JsonData);
	$pay_method = $object->pay_method; 
	$ordr_idxx = $object->ordr_idxx; 
	$good_name = $object->good_name; 
	$good_mny = $object->good_mny; 
	$buyr_name = $object->buyr_name; 
	$buyr_mail = $object->buyr_mail; 
	$buyr_tel1 = $object->buyr_tel1; 
	$buyr_tel2 = $object->buyr_tel2; 
	$bt_batch_key = $object->bt_batch_key; 
	$bt_group_id = $object->bt_group_id; 
	$quotaopt = $object->quotaopt; 
	$req_tx = $object->req_tx; 
	$card_pay_method = $object->card_pay_method; 
	$currency = $object->currency; 


    /* ============================================================================== */
    /* =   PAGE : 지불 요청 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2013   KCP Inc.   All Rights Reserved.                    = */
    /* ============================================================================== */
?>
<?
    /* ============================================================================== */
    /* = 라이브러리 및 사이트 정보 include                                          = */
    /* = -------------------------------------------------------------------------- = */
	$trace_no = "";

    require "./pp_cli_hub_lib.php";
    include "../cfg/site_conf_inc.php";
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   01. 지불 요청 정보 설정                                                  = */
    /* = -------------------------------------------------------------------------- = */
    //$pay_method = isset($_POST["pay_method"]) ? $_POST["pay_method"] : "";  // 결제 방법
    //$ordr_idxx  = isset($_POST["ordr_idxx"]) ? $_POST["ordr_idxx"] : "";  // 주문 번호
    //$good_name  = isset($_POST["good_name"]) ? $_POST["good_name"] : "";  // 상품 정보
    //$good_mny   = isset($_POST["good_mny"]) ? $_POST["good_mny"] : "";  // 결제 금액
    //$buyr_name  = isset($_POST["buyr_name"]) ? $_POST["buyr_name"] : "";  // 주문자 이름
    //$buyr_mail  = isset($_POST["buyr_mail"]) ? $_POST["buyr_mail"] : "";  // 주문자 E-Mail
    //$buyr_tel1  = isset($_POST["buyr_tel1"]) ? $_POST["buyr_tel1"] : "";  // 주문자 전화번호
    //$buyr_tel2  = isset($_POST["buyr_tel2"]) ? $_POST["buyr_tel2"] : "";  // 주문자 휴대폰번호
    //$req_tx     = isset($_POST["req_tx"]) ? $_POST["req_tx"] : "";  // 요청 종류
    //$currency   = isset($_POST["currency"]) ? $_POST["currency"] : "";  // 화폐단위 (WON/USD)
    /* = -------------------------------------------------------------------------- = */
    $mod_type      = isset($_POST["mod_type"]) ? $_POST["mod_type"] : "";                         // 변경TYPE(승인취소시 필요)
    $mod_desc      = isset($_POST["mod_desc"]) ? $_POST["mod_desc"] : "";                         // 변경사유


    $amount        = "";                                               // 총 금액
    $panc_mod_mny  = "";                                               // 부분취소 요청금액
    $panc_rem_mny  = "";                                               // 부분취소 가능금액
    /* = -------------------------------------------------------------------------- = */
    $tran_cd       = "";                                               // 트랜잭션 코드
    $bSucc         = "";                                               // DB 작업 성공 여부
    /* = -------------------------------------------------------------------------- = */
    $res_cd        = "";                                               // 결과코드
    $res_msg       = "";                                               // 결과메시지
    $tno           = "";                                               // 거래번호
    /* = -------------------------------------------------------------------------- = */
    //$card_pay_method = isset($_POST["card_pay_method"]) ? $_POST["card_pay_method"] : "";                    // 카드 결제 방법
    $card_cd         = "";                                             // 카드 코드
    $card_no         = "";                                             // 카드 번호
    $card_name       = "";                                             // 카드명
    $app_time        = "";                                             // 승인시간
    $app_no          = "";                                             // 승인번호
    $noinf           = "";                                             // 무이자여부
    $quota           = "";                                             // 할부개월
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   02. 인스턴스 생성 및 초기화                                              = */
    /* = -------------------------------------------------------------------------- = */

    $c_PayPlus  = new C_PAYPLUS_CLI;
    $c_PayPlus->mf_clear();
    
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   03. 처리 요청 정보 설정, 실행                                            = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. 승인 요청                                                          = */
    /* = -------------------------------------------------------------------------- = */
    // 업체 환경 정보
    $cust_ip = getenv( "REMOTE_ADDR" ); // 요청 IP (옵션값)

    if ( $req_tx == "pay" )
    {
    $tran_cd = "00100000";

    $common_data_set = "";

    $common_data_set .= $c_PayPlus->mf_set_data_us( "amount",   $good_mny    );
    $common_data_set .= $c_PayPlus->mf_set_data_us( "currency", $currency    );
    $common_data_set .= $c_PayPlus->mf_set_data_us( "cust_ip",  $cust_ip );
    $common_data_set .= $c_PayPlus->mf_set_data_us( "escw_mod", "N"      );

    $c_PayPlus->mf_add_payx_data( "common", $common_data_set );

    // 주문 정보
    $c_PayPlus->mf_set_ordr_data( "ordr_idxx", $ordr_idxx );
    $c_PayPlus->mf_set_ordr_data( "good_name", $good_name );
    $c_PayPlus->mf_set_ordr_data( "good_mny",  $good_mny  );
    $c_PayPlus->mf_set_ordr_data( "buyr_name", $buyr_name );
    $c_PayPlus->mf_set_ordr_data( "buyr_tel1", $buyr_tel1 );
    $c_PayPlus->mf_set_ordr_data( "buyr_tel2", $buyr_tel2 );
    $c_PayPlus->mf_set_ordr_data( "buyr_mail", $buyr_mail );

        if ( $pay_method == "CARD" )
        {
            $card_data_set = "";

            $card_data_set .= $c_PayPlus->mf_set_data_us( "card_mny", $good_mny );        // 결제 금액

                //$quotaopt      = isset($_POST["quotaopt"]) ? $_POST["quotaopt"] : "";
				//$bt_group_id      = isset($_POST["bt_group_id"]) ? $_POST["bt_group_id"] : "";
				//$bt_batch_key      = isset($_POST["bt_batch_key"]) ? $_POST["bt_batch_key"] : "";
				
				if ( $card_pay_method == "Batch" )
                {
                    $card_data_set .= $c_PayPlus->mf_set_data_us( "card_tx_type",   "11511000" );
                    $card_data_set .= $c_PayPlus->mf_set_data_us( "quota",          $quotaopt );
                    $card_data_set .= $c_PayPlus->mf_set_data_us( "bt_group_id",    $bt_group_id );
                    $card_data_set .= $c_PayPlus->mf_set_data_us( "bt_batch_key",   $bt_batch_key );
                }
            $c_PayPlus->mf_add_payx_data( "card", $card_data_set );
        }
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-2. 취소/매입 요청                                                     = */
    /* = -------------------------------------------------------------------------- = */
        else if ( $req_tx == "mod" )
        {

            $tran_cd = "00200000";
			
			$tno      = isset($_POST["tno"]) ? $_POST["tno"] : "";
			$mod_desc      = isset($_POST["mod_desc"]) ? $_POST["mod_desc"] : "";
			$mod_mny      = isset($_POST["mod_mny"]) ? $_POST["mod_mny"] : "";
			$rem_mny      = isset($_POST["rem_mny"]) ? $_POST["rem_mny"] : "";


            $c_PayPlus->mf_set_modx_data( "tno",      $tno      );      // KCP 원거래 거래번호
            $c_PayPlus->mf_set_modx_data( "mod_type", $mod_type            );      // 원거래 변경 요청 종류
            $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip             );      // 변경 요청자 IP
            $c_PayPlus->mf_set_modx_data( "mod_desc", $mod_desc );      // 변경 사유

            if ( $mod_type == "STPC" ) // 부분취소의 경우
            {
                $c_PayPlus->mf_set_modx_data( "mod_mny", $mod_mny ); // 취소요청금액
                $c_PayPlus->mf_set_modx_data( "rem_mny", $rem_mny ); // 취소가능잔액
            }
        }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   03-3. 실행                                                               = */
    /* ------------------------------------------------------------------------------ */
        if ( $tran_cd != "" )
        {
            $c_PayPlus->mf_do_tx( $trace_no, $g_conf_home_dir, $g_conf_site_cd, "", $tran_cd, "",
                                  $g_conf_gw_url, $g_conf_gw_port, "payplus_cli_slib", $ordr_idxx,
                                  $cust_ip, "3" , 0, 0); // 응답 전문 처리

            $res_cd  = $c_PayPlus->m_res_cd;  // 결과 코드
            $res_msg = $c_PayPlus->m_res_msg; // 결과 메시지
        }
        else
        {
            $c_PayPlus->m_res_cd  = "9562";
            $c_PayPlus->m_res_msg = "연동 오류|Payplus Plugin이 설치되지 않았거나 tran_cd값이 설정되지 않았습니다.";
        }

    /* ============================================================================== */


    /* ============================================================================== */
    /* =   04. 승인 결과 처리                                                       = */
    /* = -------------------------------------------------------------------------- = */
        if ( $req_tx == "pay" )
        {
            if ( $res_cd == "0000" )
            {
                $tno   = $c_PayPlus->mf_get_res_data( "tno"       ); // KCP 거래 고유 번호

    /* = -------------------------------------------------------------------------- = */
    /* =   04-1. 신용카드 승인 결과 처리                                            = */
    /* = -------------------------------------------------------------------------- = */
                if ( $pay_method == "CARD" )
                {
                    $card_cd   = $c_PayPlus->mf_get_res_data( "card_cd"   ); // 카드사 코드
                    $card_no   = $c_PayPlus->mf_get_res_data( "card_no"   ); // 카드 번호
                    $card_name = $c_PayPlus->mf_get_res_data( "card_name" ); // 카드 종류
                    $app_time  = $c_PayPlus->mf_get_res_data( "app_time"  ); // 승인 시간
                    $app_no    = $c_PayPlus->mf_get_res_data( "app_no"    ); // 승인 번호
                    $noinf     = $c_PayPlus->mf_get_res_data( "noinf"     ); // 무이자 여부 ( 'Y' : 무이자 )
                    $quota     = $c_PayPlus->mf_get_res_data( "quota"     ); // 할부 개월 수
                }

    /* = -------------------------------------------------------------------------- = */
    /* =   04-2. 승인 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.         = */
    /* = -------------------------------------------------------------------------- = */
    /* =         승인 결과를 DB 작업 하는 과정에서 정상적으로 승인된 건에 대해      = */
    /* =         DB 작업을 실패하여 DB update 가 완료되지 않은 경우, 자동으로       = */
    /* =         승인 취소 요청을 하는 프로세스가 구성되어 있습니다.                = */
    /* =         DB 작업이 실패 한 경우, bSucc 라는 변수(String)의 값을 "false"     = */
    /* =         로 세팅해 주시기 바랍니다. (DB 작업 성공의 경우에는 "false" 이외의 = */
    /* =         값을 세팅하시면 됩니다.)                                           = */
    /* = -------------------------------------------------------------------------- = */

	$PayTradenum = $ordr_idxx;
	$amount = $good_mny;

	//' 결제결과 코드
	$PayResultCd = $res_cd;

	//' 결제결과 메시지
	$PayResultMsg = $res_msg;

	//' 구매자명
	$PayCustName = $buyr_name;

	//' 상품명
	$PayGoods = $good_name;

	//' 결제금액
	$PayMny = $amount;


	$use_pay_method = "100000000000";
	$ClassOrderPayUseCashPaymentType = 1;

	$ClassOrderPayPgFeeRatio = $OnlineSitePgCardFeeRatio;
	$ClassOrderPayPgFeePrice = 0;

	//없는 변수 초기화
	$bSucces_cd = "";
	$res_msg_bsucc = "";
	$pnt_issue = "";
	$bank_name = "";
	$bank_code = "";
	$bankname = "";
	$depositor = "";
	$account = "";
	$va_date = "";
	$cash_yn = "";
	$cash_authno = "";
	$cash_tr_code = "";
	$cash_id_info = "";
	$cash_no = "";

	$pay_homeurl = "";
	$PayMethod = "";

	$PayReTrno = "";

	$PayReNum = "";
	$PayReTime = "";
	$PayCardCD = "";
	$PayCard = "";
	$PayDivMon = "";
	$PayBankCD = "";
	$PayBank = "";
	$PayCashYN = "";
	$PayReqURL = "";
	//없는 변수 초기화


	$Sql = "select * from ClassOrderPays where ClassOrderPayNumber=:ClassOrderPayNumber";
	$Stmt = $DbConn->prepare($Sql);
	$Stmt->bindParam(':ClassOrderPayNumber', $PayTradenum);
	$Stmt->execute();
	$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	$Row = $Stmt->fetch();
	$Stmt = null;
	$ClassOrderPayBatchResultID = $Row["ClassOrderPayBatchResultID"];


	if ($PayResultCd == "0000"){
		$ClassOrderPayBatchResultState = 1;
	}else{
		$ClassOrderPayBatchResultState = 1;
	}

	
	
	$res_msg = iconv('EUC-KR', 'UTF-8', $res_msg);
	$card_name = iconv('EUC-KR', 'UTF-8', $card_name);
	$PayResultMsg = iconv('EUC-KR', 'UTF-8', $PayResultMsg);


	//$Sql = "insert into TestTable (TestTableText, TestTableLog) values (now(), :TestTableLog)";
	//$Stmt = $DbConn->prepare($Sql);
	//$Stmt->bindParam(':TestTableLog', $res_msg);
	//$Stmt->execute();
	//$Stmt->setFetchMode(PDO::FETCH_ASSOC);
	//$Stmt = null;

	$Sql = " update ClassOrderPayBatchResults set ";
		
		$Sql .= " req_tx = :req_tx, ";
		$Sql .= " pay_method = :pay_method, ";
		$Sql .= " bSucc = :bSucc, ";
		$Sql .= " res_cd = :res_cd, ";
		$Sql .= " res_msg_bsucc = :res_msg_bsucc, ";
		$Sql .= " res_msg = :res_msg, ";
		$Sql .= " amount = :amount, ";
		$Sql .= " panc_mod_mny = :panc_mod_mny, ";
		$Sql .= " panc_rem_mny = :panc_rem_mny, ";
		$Sql .= " mod_type = :mod_type, ";
		$Sql .= " ordr_idxx = :ordr_idxx, ";
		$Sql .= " tno = :tno, ";
		$Sql .= " good_mny = :good_mny, ";
		$Sql .= " good_name = :good_name, ";
		$Sql .= " buyr_name = :buyr_name, ";
		$Sql .= " buyr_tel1 = :buyr_tel1, ";
		$Sql .= " buyr_tel2 = :buyr_tel2, ";
		$Sql .= " buyr_mail = :buyr_mail, ";
		$Sql .= " card_cd = :card_cd, ";
		$Sql .= " card_no = :card_no, ";
		$Sql .= " card_name = :card_name, ";
		$Sql .= " app_time = :app_time, ";
		$Sql .= " app_no = :app_no, ";
		$Sql .= " quota = :quota, ";
		$Sql .= " noinf = :noinf, ";

		$Sql .= " ClassOrderPayBatchResultState = :ClassOrderPayBatchResultState, ";
		$Sql .= " ClassOrderPayBatchResultModiDateTime = now() ";
	$Sql .= " where ClassOrderPayBatchResultID = :ClassOrderPayBatchResultID";

	$Stmt = $DbConn->prepare($Sql);

	$Stmt->bindParam(':req_tx', $req_tx);
	$Stmt->bindParam(':pay_method', $pay_method);
	$Stmt->bindParam(':bSucc', $bSucc);
	$Stmt->bindParam(':res_cd', $res_cd);
	$Stmt->bindParam(':res_msg_bsucc', $res_msg_bsucc);
	$Stmt->bindParam(':res_msg', $res_msg);
	$Stmt->bindParam(':amount', $amount);
	$Stmt->bindParam(':panc_mod_mny', $panc_mod_mny);
	$Stmt->bindParam(':panc_rem_mny', $panc_rem_mny);
	$Stmt->bindParam(':mod_type', $mod_type);
	$Stmt->bindParam(':ordr_idxx', $ordr_idxx);
	$Stmt->bindParam(':tno', $tno);
	$Stmt->bindParam(':good_mny', $good_mny);
	$Stmt->bindParam(':good_name', $good_name);
	$Stmt->bindParam(':buyr_name', $buyr_name);
	$Stmt->bindParam(':buyr_tel1', $buyr_tel1);
	$Stmt->bindParam(':buyr_tel2', $buyr_tel2);
	$Stmt->bindParam(':buyr_mail', $buyr_mail);
	$Stmt->bindParam(':card_cd', $card_cd);
	$Stmt->bindParam(':card_no', $card_no);
	$Stmt->bindParam(':card_name', $card_name);
	$Stmt->bindParam(':app_time', $app_time);
	$Stmt->bindParam(':app_no', $app_no);
	$Stmt->bindParam(':quota', $quota);
	$Stmt->bindParam(':noinf', $noinf);

	$Stmt->bindParam(':ClassOrderPayBatchResultState', $ClassOrderPayBatchResultState);
	$Stmt->bindParam(':ClassOrderPayBatchResultID', $ClassOrderPayBatchResultID);
	$Stmt->execute();
	$Stmt = null;

	


	$Sql = " update ClassOrderPays set ";
		
		
		if ($PayResultCd == "0000"){
			
			$ChClassOrderState = 1;//ClassOrderState 변경해준다.

			$Sql .= " ClassOrderPayProgress = 21, ";
			$Sql .= " ClassOrderPayPaymentDateTime = now(), ";
			$Sql .= " ClassOrderPayUseCashPaymentType = $ClassOrderPayUseCashPaymentType, ";

			$Sql .= " ClassOrderPayPgFeeRatio = :ClassOrderPayPgFeeRatio, ";
			$Sql .= " ClassOrderPayPgFeePrice = :ClassOrderPayPgFeePrice, ";
		
		}
		

		$Sql .= " LastUpdateUrl = 'pp_cli_hub.php', ";

		//kcp ==============
		$Sql .= " site_cd = :site_cd, ";
		$Sql .= " req_tx = :req_tx, ";
		$Sql .= " use_pay_method = :use_pay_method, ";
		$Sql .= " bSucc = :bSucc, ";
		$Sql .= " bSucces_cd = :bSucces_cd, ";
		$Sql .= " res_cd = :res_cd, ";
		$Sql .= " res_msg = :res_msg, ";
		$Sql .= " res_msg_bsucc = :res_msg_bsucc, ";
		$Sql .= " amount = :amount, ";
		$Sql .= " ordr_idxx = :ordr_idxx, ";
		$Sql .= " tno = :tno, ";
		$Sql .= " good_name = :good_name, ";
		$Sql .= " buyr_name = :buyr_name, ";
		$Sql .= " buyr_tel1 = :buyr_tel1, ";
		$Sql .= " buyr_tel2 = :buyr_tel2, ";
		$Sql .= " buyr_mail = :buyr_mail, ";
		$Sql .= " pnt_issue = :pnt_issue, ";
		$Sql .= " app_time = :app_time, ";
		$Sql .= " card_cd = :card_cd, ";
		$Sql .= " card_name = :card_name, ";
		$Sql .= " noinf = :noinf, ";
		$Sql .= " quota = :quota, ";
		$Sql .= " app_no = :app_no, ";
		$Sql .= " bank_name = :bank_name, ";
		$Sql .= " bank_code = :bank_code, ";
		$Sql .= " bankname = :bankname, ";
		$Sql .= " depositor = :depositor, ";
		$Sql .= " account = :account, ";
		$Sql .= " va_date = :va_date, ";
		$Sql .= " cash_yn = :cash_yn, ";
		$Sql .= " cash_authno = :cash_authno, ";
		$Sql .= " cash_tr_code = :cash_tr_code, ";
		$Sql .= " cash_id_info = :cash_id_info, ";
		$Sql .= " cash_no = :cash_no, ";
		$Sql .= " pay_homeurl = :pay_homeurl, ";
		$Sql .= " PayMethod = :PayMethod, ";
		$Sql .= " PayCustName = :PayCustName, ";
		$Sql .= " PayGoods = :PayGoods, ";
		$Sql .= " PayMny = :PayMny, ";
		$Sql .= " OrderNumPay = :OrderNumPay, ";
		$Sql .= " PayResultCd = :PayResultCd, ";
		$Sql .= " PayResultMsg = :PayResultMsg, ";
		$Sql .= " PayReTrno = :PayReTrno, ";
		$Sql .= " PayReNum = :PayReNum, ";
		$Sql .= " PayReTime = :PayReTime, ";
		$Sql .= " PayCardCD = :PayCardCD, ";
		$Sql .= " PayCard = :PayCard, ";
		$Sql .= " PayDivMon = :PayDivMon, ";
		$Sql .= " PayBankCD = :PayBankCD, ";
		$Sql .= " PayBank = :PayBank, ";
		$Sql .= " PayCashYN = :PayCashYN, ";
		$Sql .= " PayReqURL = :PayReqURL, ";	
		//kcp ==============
		
		$Sql .= " ClassOrderPayDateTime = now(), ";
		$Sql .= " ClassOrderPayModiDateTime = now() ";
	$Sql .= " where ClassOrderPayNumber = :OrderNumPay ";

	$Stmt = $DbConn->prepare($Sql);

	if ($PayResultCd == "0000"){
		$Stmt->bindParam(':ClassOrderPayPgFeeRatio', $ClassOrderPayPgFeeRatio);
		$Stmt->bindParam(':ClassOrderPayPgFeePrice', $ClassOrderPayPgFeePrice);
	}


	//kcp ==============
	$Stmt->bindParam(':site_cd', $g_conf_site_cd);
	$Stmt->bindParam(':req_tx', $req_tx);
	$Stmt->bindParam(':use_pay_method', $use_pay_method);
	$Stmt->bindParam(':bSucc', $bSucc);
	$Stmt->bindParam(':bSucces_cd', $bSucces_cd);
	$Stmt->bindParam(':res_cd', $res_cd);
	$Stmt->bindParam(':res_msg', $res_msg);
	$Stmt->bindParam(':res_msg_bsucc', $res_msg_bsucc);
	$Stmt->bindParam(':amount', $amount);
	$Stmt->bindParam(':ordr_idxx', $ordr_idxx);
	$Stmt->bindParam(':tno', $tno);
	$Stmt->bindParam(':good_name', $good_name);
	$Stmt->bindParam(':buyr_name', $buyr_name);
	$Stmt->bindParam(':buyr_tel1', $buyr_tel1);
	$Stmt->bindParam(':buyr_tel2', $buyr_tel2);
	$Stmt->bindParam(':buyr_mail', $buyr_mail);
	$Stmt->bindParam(':pnt_issue', $pnt_issue);
	$Stmt->bindParam(':app_time', $app_time);
	$Stmt->bindParam(':card_cd', $card_cd);
	$Stmt->bindParam(':card_name', $card_name);
	$Stmt->bindParam(':noinf', $noinf);
	$Stmt->bindParam(':quota', $quota);
	$Stmt->bindParam(':app_no', $app_no);
	$Stmt->bindParam(':bank_name', $bank_name);
	$Stmt->bindParam(':bank_code', $bank_code);
	$Stmt->bindParam(':bankname', $bankname);
	$Stmt->bindParam(':depositor', $depositor);
	$Stmt->bindParam(':account', $account);
	$Stmt->bindParam(':va_date', $va_date);
	$Stmt->bindParam(':cash_yn', $cash_yn);
	$Stmt->bindParam(':cash_authno', $cash_authno);
	$Stmt->bindParam(':cash_tr_code', $cash_tr_code);
	$Stmt->bindParam(':cash_id_info', $cash_id_info);
	$Stmt->bindParam(':cash_no', $cash_no);
	$Stmt->bindParam(':pay_homeurl', $pay_homeurl);
	$Stmt->bindParam(':PayMethod', $PayMethod);
	$Stmt->bindParam(':PayCustName', $PayCustName);
	$Stmt->bindParam(':PayGoods', $PayGoods);
	$Stmt->bindParam(':PayMny', $PayMny);
	$Stmt->bindParam(':OrderNumPay', $PayTradenum);
	$Stmt->bindParam(':PayResultCd', $PayResultCd);
	$Stmt->bindParam(':PayResultMsg', $PayResultMsg);
	$Stmt->bindParam(':PayReTrno', $PayReTrno);
	$Stmt->bindParam(':PayReNum', $PayReNum);
	$Stmt->bindParam(':PayReTime', $PayReTime);
	$Stmt->bindParam(':PayCardCD', $PayCardCD);
	$Stmt->bindParam(':PayCard', $PayCard);
	$Stmt->bindParam(':PayDivMon', $PayDivMon);
	$Stmt->bindParam(':PayBankCD', $PayBankCD);
	$Stmt->bindParam(':PayBank', $PayBank);
	$Stmt->bindParam(':PayCashYN', $PayCashYN);
	$Stmt->bindParam(':PayReqURL', $PayReqURL);
	//kcp ==============
	$Stmt->execute();
	$Stmt = null;


	if ($PayResultCd == "0000"){

		$Sql = "select * from ClassOrderPays where ClassOrderPayNumber=:ClassOrderPayNumber";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPayNumber', $PayTradenum);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$ClassOrderPayID = $Row["ClassOrderPayID"];
		$ClassOrderPayUseCashPrice = $Row["ClassOrderPayUseCashPrice"];
		$ClassOrderPayPaymentMemberID = $Row["ClassOrderPayPaymentMemberID"];

		if ($ChClassOrderState==1){
			$Sql = "update ClassOrders set ClassOrderState=1 where ClassOrderID in ( select ClassOrderID from ClassOrderPayDetails where ClassOrderPayID=:ClassOrderPayID )";
			$Stmt = $DbConn->prepare($Sql);
			$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
			$Stmt->execute();
			$Stmt = null;
		}

		$Sql = "update ClassOrders set ClassOrderPayID=:ClassOrderPayID where ClassOrderID in ( select ClassOrderID from ClassOrderPayDetails where ClassOrderPayID=:ClassOrderPayID )";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
		$Stmt->execute();
		$Stmt = null;


		
		//======================= 학습 종료일 설정 ======================================================
		$Sql = "select 
					A.ClassOrderID,
					B.ClassOrderPayTotalWeekCount,
					C.MemberID, 
					C.ClassOrderStartDate,
					C.ClassOrderEndDate,
					
					D.MemberPayType,
					E.CenterPayType,

					datediff(C.ClassOrderEndDate, now()) as DiffClassOrderEndDate

				from ClassOrderPayDetails A 
					inner join ClassOrderPayMonthNumbers B on A.ClassOrderPayMonthNumberID=B.ClassOrderPayMonthNumberID 
					inner join ClassOrders C on A.ClassOrderID=C.ClassOrderID 
					inner join Members D on C.MemberID=D.MemberID 
					inner join Centers E on D.CenterID=E.CenterID 
				where A.ClassOrderPayID=:ClassOrderPayID
				order by A.ClassOrderPayDetailID asc 
				";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':ClassOrderPayID', $ClassOrderPayID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);

		while($Row = $Stmt->fetch()) {

			$ClassOrderID = $Row["ClassOrderID"];
			$ClassOrderPayTotalWeekCount = $Row["ClassOrderPayTotalWeekCount"];
			$MemberID = $Row["MemberID"];
			$ClassOrderStartDate = $Row["ClassOrderStartDate"];
			$ClassOrderEndDate = $Row["ClassOrderEndDate"];
			$MemberPayType = $Row["MemberPayType"];
			$CenterPayType = $Row["CenterPayType"];
			$DiffClassOrderEndDate = $Row["DiffClassOrderEndDate"];


			if ($DiffClassOrderEndDate>=0){//종료일 이전 결제====================================================================================

				$ClassOrderPayTotalDayCount = $ClassOrderPayTotalWeekCount * 7;//종료일 기준일때는 1을 빼주지 않음
				$NewClassOrderEndDate = date("Y-m-d", strtotime(substr($ClassOrderEndDate,0,10). " + ".$ClassOrderPayTotalDayCount." days"));
				$ClassOrderPayStartDate = date("Y-m-d", strtotime(substr($ClassOrderEndDate,0,10). " + 1 days"));//이변결제의 수업 시작날짜
			
			}else{//종료일 이후 결제====================================================================================
				
				
				$ExistStudyWeek[0] = 0;
				$ExistStudyWeek[1] = 0;
				$ExistStudyWeek[2] = 0;
				$ExistStudyWeek[3] = 0;
				$ExistStudyWeek[4] = 0;
				$ExistStudyWeek[5] = 0;
				$ExistStudyWeek[6] = 0;

				$Sql2 = " 
						select 
							A.*
						from ClassOrderSlots A 
						where A.ClassOrderID=$ClassOrderID and A.ClassOrderSlotType=1 and A.ClassOrderSlotMaster=1 and A.ClassOrderSlotEndDate is null 
						order by A.StudyTimeWeek asc
				";
				$Stmt2 = $DbConn->prepare($Sql2);
				$Stmt2->execute();
				$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
				while($Row2 = $Stmt2->fetch()) {
					
					$StudyTimeWeek = $Row2["StudyTimeWeek"];
					$ExistStudyWeek[$StudyTimeWeek] = 1;

				}
				$Stmt2 = null;


				$ClassOrderStartDateWeek = date("w", strtotime(date("Y-m-d")));//오늘의 요일
				$SetLastDate = 0;

				$ListNum = 0;
				while($SetLastDate==0){
					
					if ($ClassOrderStartDateWeek==7){//일요일이면 0으로 초기화
						$ClassOrderStartDateWeek = 0;
					}
					
					if ($ExistStudyWeek[$ClassOrderStartDateWeek]==1){
						$NewClassOrderStartDate = date("Y-m-d", strtotime(date("Y-m-d"). " + ".$ListNum." days"));
						$SetLastDate=1;
					}
					
					$ListNum++;
					$ClassOrderStartDateWeek++;
				}

				$ClassOrderPayTotalDayCount = ( $ClassOrderPayTotalWeekCount * 7 ) - 1;//시작일 기준일때는 1을 빼줌
			
				$NewClassOrderEndDate = date("Y-m-d", strtotime(substr($NewClassOrderStartDate,0,10). " + ".$ClassOrderPayTotalDayCount." days"));	
				
				$ClassOrderPayStartDate = $NewClassOrderStartDate;//이변결제의 수업 시작날짜
				
			}


			$Sql2 = "update ClassOrders set LastClassOrderEndDate=ClassOrderEndDate, LastClassOrderEndDateByPay=ClassOrderEndDate, ClassOrderEndDate=:ClassOrderEndDate, ClassOrderModiDateTime=now() where ClassOrderID=$ClassOrderID";
			$Stmt2 = $DbConn->prepare($Sql2);
			$Stmt2->bindParam(':ClassOrderEndDate', $NewClassOrderEndDate);
			$Stmt2->execute();
			$Stmt2 = null;

			//종료일 로그 남기기 =======================================
			$ClassOrderEndDateLogFileQueryNum = 1;
			$Sql_EndDateLog = " insert into ClassOrderEndDateLogs ( ";
				$Sql_EndDateLog .= " ClassOrderID, ";
				$Sql_EndDateLog .= " ClassOrderEndDate, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogFileName, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogFileQueryNum, ";
				$Sql_EndDateLog .= " ClassOrderEndDateLogRegDateTime ";
			$Sql_EndDateLog .= " ) values ( ";
				$Sql_EndDateLog .= " :ClassOrderID, ";
				$Sql_EndDateLog .= " :ClassOrderEndDate, ";
				$Sql_EndDateLog .= " :ClassOrderEndDateLogFileName, ";
				$Sql_EndDateLog .= " :ClassOrderEndDateLogFileQueryNum, ";
				$Sql_EndDateLog .= " now() ";
			$Sql_EndDateLog .= " ) ";
			$Stmt_EndDateLog = $DbConn->prepare($Sql_EndDateLog);
			$Stmt_EndDateLog->bindParam(':ClassOrderID', $ClassOrderID);
			$Stmt_EndDateLog->bindParam(':ClassOrderEndDate', $NewClassOrderEndDate);
			$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileName', $ClassOrderEndDateLogFileName);
			$Stmt_EndDateLog->bindParam(':ClassOrderEndDateLogFileQueryNum', $ClassOrderEndDateLogFileQueryNum);
			$Stmt_EndDateLog->execute();
			$Stmt_EndDateLog = null;
			//종료일 로그 남기기 =======================================
			

		}
		$Stmt = null;

		//var_dump($ClassOrderPayStartDate);
		//var_dump($PayTradenum);
		$Sql2 = "update ClassOrderPays set ClassOrderPayStartDate=:ClassOrderPayStartDate where ClassOrderPayNumber=:ClassOrderPayNumber";
		$Stmt2 = $DbConn->prepare($Sql2);
		$Stmt2->bindParam(':ClassOrderPayStartDate', $ClassOrderPayStartDate);
		$Stmt2->bindParam(':ClassOrderPayNumber', $PayTradenum);
		$Stmt2->execute();
		$Stmt2 = null;	
		//======================= 학습 종료일 설정 ======================================================


		//================= 포인트 ======================
		$OnlineSitePaymentPoint = round($ClassOrderPayUseCashPrice * ($OnlineSitePaymentPointRatio / 100));

		/*
		$Sql = "select A.MemberPointID from MemberPoints A where A.MemberPointTypeID=3 and A.MemberID=:MemberID and A.MemberPointState=1 and A.RootOrderID=:RootOrderID";
		$Stmt = $DbConn->prepare($Sql);
		$Stmt->bindParam(':MemberID', $PointMemberID);
		$Stmt->bindParam(':RootOrderID', $ClassOrderPayID);
		$Stmt->execute();
		$Stmt->setFetchMode(PDO::FETCH_ASSOC);
		$Row = $Stmt->fetch();
		$Stmt = null;
		$MemberPointID = $Row["MemberPointID"];

		if (!$MemberPointID){
			InsertPointWithRootOrderID(3, 0, $PointMemberID, "수강신청(웹)", "수강신청(웹)" ,$OnlineSitePaymentPoint, $ClassOrderPayID);
		}
		*/

		//================= 포인트 ======================


	}





	$bSucc = "";             // DB 작업 실패일 경우 "false" 로 세팅

    /* = -------------------------------------------------------------------------- = */
    /* =   04-3. DB 작업 실패일 경우 자동 승인 취소                                 = */
    /* = -------------------------------------------------------------------------- = */
            if ( $req_tx == "pay" )
            {
                if( $res_cd == "0000" )
                {
                    if ( $bSucc == "false" )
                    {
                        $c_PayPlus->mf_clear();

                        $tran_cd = "00200000";

                        $c_PayPlus->mf_set_modx_data( "tno",      $tno                         );  // KCP 원거래 거래번호
                        $c_PayPlus->mf_set_modx_data( "mod_type", "STSC"                       );  // 원거래 변경 요청 종류
                        $c_PayPlus->mf_set_modx_data( "mod_ip",   $cust_ip                     );  // 변경 요청자 IP (옵션값)
                        $c_PayPlus->mf_set_modx_data( "mod_desc", "결과 처리 오류 - 자동 취소" );  // 변경 사유

                        $c_PayPlus->mf_do_tx( $tno,  $g_conf_home_dir, $g_conf_site_cd,
                                              "",  $tran_cd,    "",
                                              $g_conf_gw_url,  $g_conf_gw_port,  "payplus_cli_slib",
                                              $ordr_idxx, $cust_ip, "3" ,
                                              0, 0);

                        $res_cd  = $c_PayPlus->m_res_cd;
                        $res_msg = $c_PayPlus->m_res_msg;
                    }
                }
            } // End of [res_cd = "0000"]
            }
        }

    /* ============================================================================== */
    /* =   05. 취소/매입 결과 처리                                                  = */
    /* = -------------------------------------------------------------------------- = */
        else if ( $req_tx == "mod" )
        {
            if ( $res_cd == "0000" )
            {
                if ( $mod_type == "STPC" )
                {
                $amount       = $c_PayPlus->mf_get_res_data( "amount"       ); // 총 금액
                $panc_mod_mny = $c_PayPlus->mf_get_res_data( "panc_mod_mny" ); // 부분취소 요청금액
                $panc_rem_mny = $c_PayPlus->mf_get_res_data( "panc_rem_mny" ); // 부분취소 가능금액
                }
            }
        }
    /* ============================================================================== */

    /* ============================================================================== */
    /* =   06. 폼 구성 및 결과페이지 호출                                           = */
    /* ============================================================================== */


include_once('../../includes/dbclose.php');

?>

