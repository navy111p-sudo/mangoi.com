<!-- main header -->
<header id="header_main">
	<div class="header_main_content">
		<nav class="uk-navbar">
							
			<!-- main sidebar switch -->
			<a href="#" id="sidebar_main_toggle" class="sSwitch sSwitch_left">
				<span class="sSwitchIcon"></span>
			</a>
			
			<!-- secondary sidebar switch -->
			<a href="#" id="sidebar_secondary_toggle" class="sSwitch sSwitch_right sidebar_secondary_check">
				<span class="sSwitchIcon"></span>
			</a>
			
			<div id="menu_top_dropdown" class="uk-float-left uk-hidden-small" >
				<div class="uk-button-dropdown" data-uk-dropdown="{mode:'click'}">
					<a href="#" class="top_menu_toggle"><i class="material-icons md-24">beach_access</i></a>
					<div class="uk-dropdown uk-dropdown-width-4">
						<div class="uk-grid uk-dropdown-grid">
					<!--Weather Forecast courtesy of www.tititudorancea.com-->
					<style>
					.WFOT1 {border:2px solid #E1E1E1; background-color:#F1F1F1; padding:10px}
					.WFH1 {font:bold 14px Arial, sans-serif; margin-bottom:6px; width:730px;}
					TABLE.WFOT TD {vertical-align:top}
					.FCOVTMP {font:14px Arial, sans-serif; line-height:16px; padding-bottom:4px}
					.FCOVEXP {font:12px Arial, sans-serif; line-height:14px; text-align:center}
					.WFI {background-color:#3399FF;padding:0}
					.WTL {color:blue;font-weight:bold}
					.WTH {color:red;float:right;font-weight:bold}
					.WFLK {font-size:11px;color:#900;text-decoration:none}
					.WFDAY {font-size:12px;text-align:center;font-weight:bold}

                    #header_main .uk-navbar .uk-navbar-nav>li>a {
                        line-height: 40px;
                    }
                    .navbar-search{
                        height: 30px;
                        line-height:45px;
                    }
                    .navbar-search #menuSearchBar{
                        width: 100%;
                        height: 100%;
                    }

                    @media only screen and (max-width: 767px) {
                        #header_main {
                            padding: 0 15px !important;
                        }  
                        #header_main .uk-navbar .uk-navbar-nav > li > a {
                            padding: 2px 5px 0 !important;
                        }
                        #header_main .user_actions .user_action_icon > .uk-badge {
                            min-width: 10px !important;
                        }
                        .navbar-search{
                            padding-right: 10px;
                        }
                    }

                    /* 갤럭시 폴드일 때 */
                    @media only screen and (max-width: 358px) {
                        #header_main .uk-navbar .uk-navbar-nav > li > a {
                            padding: 2px 3px 0 !important;
                        }
                        .navbar-search{
                            width: 100px;
                        }
                    }
					</style>
					<div style="position:relative;background-color:#FFFFFF">
					<div id="wf_div"></div>
					<script type="text/javascript" src="https://tools.tititudorancea.com/weather_forecast.js?place=cagayan&amp;s=1&amp;days=7&amp;utf8=no&amp;columns=7&amp;temp=c"></script>
					<div style="font:10px Arial, sans-serif;color:#000000" align="right"><a href="https://www.tititudorancea.com/z/weather.htm">Weather forecast</a> 
					provided by <a href="https://www.tititudorancea.com/">tititudorancea.com</a></div>
					
                </div>
					<!--end of Weather Forecast-->
						</div>
					</div>
				</div>
			</div>

						
			<div class="uk-navbar-flip">
				<ul class="uk-navbar-nav user_actions">
					
					<li class="navbar-search">
                        <input id="menuSearchBar" type="text" placeholder="메뉴 및 강사명 검색" onkeyup="javascript:SearchMenuAndTeacher()" />
                        <div id="menuSearchResultList" style="background-color:#ffffff"/>
                    </li>
					<li style="display:<?if ($_ADMIN_LEVEL_ID_>13){?>none<?}?>;"><a href="#" id="main_search_btn" class="user_action_icon"><i class="material-icons md-24 md-light">people</i></a></li>
					
					<li><a href="#" id="full_screen_toggle" class="user_action_icon uk-visible-large"><i class="material-icons md-24 md-light">fullscreen</i></a></li>

					<li><a href="javascript:OpenFavoriteForm(<?=$_LINK_ADMIN_ID_?>);" class="user_action_icon uk-visible-large"><i class="material-icons md-24 md-light">stars</i></a></li>
					<?
					$http_host = $_SERVER['HTTP_HOST'];
					$request_uri = $_SERVER['REQUEST_URI'];
					$url = 'http://' . $http_host . $request_uri;
					?>

<!--                    DataCredit 수정 - 2024-03-23-->
                    <script>
                        var currentUserId = '<?php echo $_COOKIE["LoginMemberID"]; ?>';

                        // alert currentUserId:
                        // alert(currentUserId);
                    </script>


					<script>
					function OpenFavoriteForm(MemberID){
						FavoriteUrl = "<?=str_replace("&","^^",$request_uri)?>";
						openurl = "favorite_form.php?MemberID="+MemberID+"&FavoriteUrl="+FavoriteUrl;
						$.colorbox({	
							href:openurl
							,width:"95%" 
							,height:"95%"
							,maxWidth: "850"
							,maxHeight: "500"
							,title:""
							,iframe:true 
							,scrolling:true
							,onClosed:function(){location.reload(true);}
							//,onComplete:function(){alert(1);}
						}); 
					}

                    function OpenClassOrderBulk(){
	                    openurl = "class_order_bulk_form.php";
	                    $.colorbox({	
		                href:openurl
		                ,width:"95%" 
		                ,height:"95%"
		                ,maxWidth: "750"
		                ,maxHeight: "650"
		                ,title:""
		                ,iframe:true 
		                ,scrolling:true
		                //,onClosed:function(){location.reload(true);}
		                //,onComplete:function(){alert(1);}
	                });
}
                    function OpenClassSchedule(){
	                    openurl = "class_schedule.php";
	                    //cordova_iab.InAppOpenBrowser(openurl);
	                    window.open(openurl, "class_schedule", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
                    }

                    function OpenClassScheduleByTeacher(){
	                    openurl = "class_schedule_by_teacher.php";
	                    window.open(openurl, "class_schedule_by_teacher", "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1080,height=800");
                    }

                    function SearchMenuAndTeacher(){
                        let menuSearchBar = document.getElementById('menuSearchBar');
                        let menuSearchResultList = document.getElementById('menuSearchResultList');

                        // 필터링 함수 정의
                        // 수강신청 관리 메뉴를 특정 사용자에게만 보여주기 위한 함수
                        // 보여주지 않을 메뉴 : 수강신청관리, 스케줄링대상, 스케줄링완료, 수업종료대상, 수업종료완료, 장기홀드목록, 단체수강신청
                        const hideClassManagementForUser = (menu) => {
                            // if (currentUserId === 'snickerkoki' && menu.title.includes('수강신청관리')) {
                            if (currentUserId === 'snickerkoki' && menu.title.includes('수강신청관리') ||
                                menu.title.includes('스케줄링대상') || menu.title.includes('스케줄링완료') ||
                                menu.title.includes('수업종료대상') || menu.title.includes('수업종료완료') ||
                                menu.title.includes('장기홀드목록') || menu.title.includes('단체수강신청')) {
                                return false;
                            }
                            return true;
                        };

                        const searchFunc = (objId) => {
                            let searchId = menuSearchBar.value;
                            return objId.indexOf(searchId.toLowerCase()) !== -1;
                        };

                        }
                        const menuList = [
                            //가맹점 관리
                            {
                                title: '대리점',
                                url:'center_list.php'
                            },
                            {
                                title: '지사',
                                url:'branch_list.php'
                            },
                            {
                                title: '대표지사',
                                url:'branch_group_list.php'
                            },
                            {
                                title: '영업본부',
                                url: 'manager_list.php'
                            },
                            {
                                title: '본사관리',
                                url: 'company_list.php'
                            },
                            {
                                title: '독립사이트',
                                url: 'online_site_list.php'
                            },
                            {
                                title: '프랜차이즈',
                                url:'franchise_list.php'
                            },
                            {
                                title: '충전금내역',
                                url:'center_saved_money_list.php'
                            },
                            {
                                title: '포인트내역',
                                url:'member_point_list.php'
                            },
                            {
                                title: '본사대리점_B2B결제',
                                url:'b2b_payment_list.php'
                            },
                            //교육센터
                            {
                                title: '강사관리',
                                url:'teacher_list.php'
                            },                            {
                                title: '강사소개영상',
                                url:'teacher_video_list.php'
                            },
                            {
                                title: '강사 리뷰',
                                url:'teacher_review_list.php'
                            },
                            {
                                title: '강사그룹',
                                url:'teacher_group_list.php'
                            },
                            {
                                title: '교육센터',
                                url:'edu_center_list.php'
                            },
                            {
                                title: '출신지역관리',
                                url:'teacher_pay_type_item_list.php'
                            },
                            //학생관리                          
                            {
                                title: '학생목록',
                                url:'student_list.php'
                            },
                            {
                                title: '종료일자확인',
                                url:'student_crisis_list.php'
                            },
                            {
                                title: '학생상세목록',
                                url:'student_detail_list.php'
                            },
                            {
                                title: '수강연장',
                                url:'class_order_renew_center_form.php'
                            },
                            {
                                title: '수업현황',
                                url:'account_center_class_status.php'
                            },
                            {
                                title: '월별평가서',
                                url:'monthy_report_list.php'
                            },
                            {
                                title: '출결현황',
                                url:'attend_status_list.php'
                            },
                            {
                                title: '연속결석',
                                url:'absent_status_list.php'
                            },
                            {
                                title: '상담내역',
                                url:'counsel_list.php'
                            },                            {
                                title: '탈락률',
                                url:'leaving_out.php'
                            },
                            //레벨테스트
                            {
                                title: '스케줄링대상',
                                url:'leveltest_apply_list.php?type=11'
                            },
                            {
                                title: '스케줄링완료',
                                url:'leveltest_apply_list.php?type=21'
                            },                            {
                                title: '테스트완료',
                                url:'leveltest_apply_list.php?type=51'
                            },
                            {
                                title: '결석(미응시)',
                                url:'leveltest_apply_list.php?type=61'
                            },
                            //수강신청관리
                            {
                                title: '스케줄링대상',
                                url:'class_order_list.php?type=11'
                            },
                            {
                                title: '스케줄링완료',
                                url:'class_order_list.php?type=21'
                            },
                            {
                                title: '수업종료대상',
                                url:'class_order_list.php?type=31'
                            },
                            {
                                title: '수업종료완료',
                                url:'class_order_list.php?type=41'
                            },
                            {
                                title: '장기홀드목록',
                                url:'class_order_list.php?type=99'
                            },
                            {
                                title: '단체수강신청',
                                url:'javascript:OpenClassOrderBulk()'
                            },
                            //교재구매관리
                            {
                                title: '결제완료',
                                url:'product_order_list.php?type=11'
                            },
                            {
                                title: '발송완료',
                                url:'product_order_list.php?type=21'
                            },
                            {
                                title: '취소완료',
                                url:'product_order_list.php?type=31'
                            },
                            //수업관리
                            {
                                title: '알림메시지',
                                url:'teacher_message_list.php'
                            },
                            {
                                title: '오늘 수업',
                                url:'class_list.php?type=1'
                            },
                            {
                                title: 'Class Attendance',
                                url:'teacher_enter_excel_form.php'
                            },
                            {
                                title: '수업현황',
                                url:'teacher_class_count.php'
                            },
                            {
                                title: '연기/취소/변경',
                                url:'class_list.php?type=9'
                            },
                            {
                                title: '전체스케쥴(Date)',
                                url:'javascript:OpenClassSchedule()'
                            },
                            {
                                title: '전체스케쥴(Teacher)',
                                url:'javascript:OpenClassScheduleByTeacher()'
                            },
                            {
                                title: '신규',
                                url:'class_qna_list.php?type=1'
                            },
                            {
                                title: '진행중',
                                url:'class_qna_list.php?type=2'
                            },
                            {
                                title: '위임',
                                url:'class_qna_list.php?type=3'
                            },
                            {
                                title: '완료-강사 미확인',
                                url:'class_qna_list.php?type=4'
                            },
                            {
                                title: '완료-강사 확인',
                                url:'class_qna_list.php?type=5'
                            },
                            //커뮤니티
                            {
                                title: '공지사항',
                                url:'board_list.php?BoardCode=notice'
                            },
                            {
                                title: '질문답변',
                                url:'board_list.php?BoardCode=qna'
                            },
                            {
                                title: '1:1문의',
                                url:'direct_qna_member_list.php'
                            },
                            {
                                title: '건의사항',
                                url:'direct_qna_member_list.php'
                            },
                            {
                                title: '이벤트',
                                url:'event_list.php'
                            },
                            {
                                title: 'FAQ',
                                url:'faq_list.php'
                            },
                            {
                                title: '수강후기',
                                url:'mypage_review_list.php'
                            },
                            {
                                title: '원격지원목록',
                                url:'remote_support_list.php'
                            },
                            //자료실
                            {
                                title: '학습자료실',
                                url:'board_list.php?BoardCode=reference'
                            },
                            {
                                title: '지사자료실',
                                url:'board_list.php?BoardCode=branch'
                            },
                            {
                                title: '대리점자료실',
                                url:'board_list.php?BoardCode=center'
                            },
                            {
                                title: '기타자료실',
                                url:'board_list.php?BoardCode=etc'
                            },
                            {
                                title: '자료교환',
                                url:'teacher_data_list.php'
                            },
                            //교재콘텐츠관리
                            {
                                title: '교재관리',
                                url:'book_list.php'
                            },
                            {
                                title: '교재 그룹 관리',
                                url:'book_gourp_list.php'
                            },
                            {
                                title: '판매 교재 관리',
                                url:'product_list.php'
                            },
                            {
                                title: '판매 교재 그룹 관리',
                                url:'product_list.php'
                            },
                            {
                                title: '판매 교재 그룹 관리',
                                url:'product_seller_list.php'
                            },
                            //정산통계관리
                            {
                                title: '지사별정산',
                                url:'account_branch.php'
                            },
                            {
                                title: '대리점별정산',
                                url:'account_center.php'
                            },
                            {
                                title: 'SLP 정산(상세)',
                                url:'account_center_slp.php'
                            },
                            {
                                title: 'SLP 정산',
                                url:'account_center_slpmangoi.php'
                            },
                            {
                                title: 'SLP 정산(본사로얄티)',
                                url:'account_center_slpmangoi_2.php'
                            },
                            {
                                title: 'SLP 수업현황',
                                url:'account_center_slpmangoi_class_status.php'
                            },
                            {
                                title: '강사별정산',
                                url:'account_teacher.php'
                            },
                            {
                                title: '본사매출통계',
                                url:'account_total.php'
                            },
                            {
                                title: '학생수업통계',
                                url:'account_study_total.php'
                            },
                            {
                                title: '지사수업통계',
                                url:'account_branch_study.php'
                            },
                            {
                                title: '교사수업통계',
                                url:'account_teacher_study.php'
                            },
                            {
                                title: '통계그래프',
                                url:'account_graph_total.php'
                            },
                            {
                                title: '학생수 데이터',
                                url:'number_of_student_branch.php'
                            },
                            {
                                title: '커미션 데이터',
                                url:'commision_branch.php'
                            },
                            //그룹웨어/마이룸
                            {
                                title: '휴가 및 병가원',
                                url:'my_document_holiday_list.php'
                            },
                            {
                                title: '기안 및 지출서',
                                url:'my_document_draft_list.php'
                            },
                            {
                                title: '확인할 문서',
                                url:'my_document_comfirm_list.php'
                            },
                            {
                                title: '관리 메시지',
                                url:'master_message_list.php'
                            },
                            {
                                title: '즐겨 찾기',
                                url:'favorite_list.php'
                            },
                            //성과평가 시스템
                            {
                                title: '조직관리',
                                url:'hr_organ_level_list.php'
                            },
                            {
                                title: '직무관리',
                                url:'hr_organ_task_list.php'
                            },
                            {
                                title: '업적평가 조직도',
                                url:'hr_evaluation_organ_table.php'
                            },
                            {
                                title: '역량평가 조직도',
                                url:'hr_evaluation_competency_table.php'
                            },
                            {
                                title: '직무별 역량관리',
                                url:'hr_competency_indicator_task.php'
                            },
                            {
                                title: '역량평가 문항관리',
                                url:'hr_competency_indicator_list.php'
                            },
                            {
                                title: 'KPI 문항관리',
                                url:'hr_kpi_indicator_list.php'
                            },
                            {
                                title: '평가등록',
                                url:'hr_evaluation_list.php'
                            },
                            {
                                title: '목표설정현황',
                                url:'hr_staffall_target_list.php'
                            },
                            {
                                title: '업적평가현황',
                                url:'hr_staffall_evaluation_list.php'
                            },
                            {
                                title: '역량평가현황',
                                url:'hr_staffall_evaluation_competency_list.php'
                            },
                            {
                                title: '성과평가마감',
                                url:'hr_evaluation_finishing.php'
                            },
                            {
                                title: '결과관리',
                                url:'hr_staffall_indicator_list.php'
                            },
                            {
                                title: 'DB다운로드',
                                url:'hr_staffall_dbdownload.php'
                            },
                            {
                                title: '목표설정',
                                url:'hr_staff_target_list.php'
                            },
                            {
                                title: '업적평가실시',
                                url:'hr_staff_evaluation_list.php'
                            },
                            {
                                title: '역량평가실시',
                                url:'hr_staff_indicator_list.php'
                            },
                            {
                                title: '부서원평가결과',
                                url:'hr_staffteam_indicator_list.php'
                            },
                            {
                                title: '인사평가자료실',
                                url:'board_list.php?BoardCode=hrfile'
                            },
                            //회계/급여 관리
                            {
                                title: '회계관리',
                                url:'account_book.php'
                            },
                            {
                                title: '급여 기본정보 관리',
                                url:'pay_info.php'
                            },
                            {
                                title: '급여관리',
                                url:'pay.php'
                            },
                            {
                                title: '4대보험요율 관리',
                                url:'pay_insurance_rate_list.php'
                            },
                            {
                                title: '카드비용 날짜관리',
                                url:'card_money_date_form.php'
                            },
                            {
                                title: '과세항목설정',
                                url:'pay_tax_info_form.php'
                            },
                            {
                                title: '공제항목설정',
                                url:'pay_deduction_info_form.php'
                            },
                            {
                                title: '급여결재하기',
                                url:'pay_confirm_form.php'
                            },
                            {
                                title: '초과근무수당작성',
                                url:'overtimepay_form.php'
                            },
                            {
                                title: '초과근무수당결재',
                                url:'overtimepay_confirm.php'
                            },
                            {
                                title: '급여열람권한',
                                url:'pay_auth.php'
                            },
                            {
                                title: '급여열람',
                                url:'pay_view.php'
                            },
                            {
                                title: '본인급여열람',
                                url:'account_book.php'
                            },
                            {
                                title: '회계관리',
                                url:'pay_self_view.php'
                            },
                            //운영관리
                            {
                                title: '내정보관리',
                                url:'member_form.php'
                            },
                            {
                                title: '내정보관리',
                                url:'staff_list.php'
                            },
                            {
                                title: '직원관리',
                                url:'staff_list.php'
                            },
                            {
                                title: '직원휴가관리',
                                url:'staff_holiday_list.php'
                            },
                            {
                                title: '부서관리',
                                url:'departments_list.php'
                            },
                            {
                                title: '환율관리',
                                url:'currency_form.php?CountryCode=PH'
                            },
                            {
                                title: '결재라인관리',
                                url:'approval_line_list.php'
                            },
                            {
                                title: '보고서양식',
                                url:'document_list.php'
                            },
                            {
                                title: '메시지 내역',
                                url:'send_message_log_list.php'
                            },
                            {
                                title: '베스트 강사',
                                url:'teacher_best_list.php'
                            },
                            {
                                title: '쿠폰 관리',
                                url:'coupon_list.php'
                            },
                            {
                                title: '지사미수관리',
                                url:'branch_account_list.php'
                            },
                            {
                                title: 'B2C 결제관리',
                                url:'b2c_payment_list.php'
                            },
                            {
                                title: 'B2B 결제관리',
                                url:'b2b_payment_list.php'
                            },
                            {
                                title: '교재결제관리',
                                url:'product_payment_list.php'
                            },
                            {
                                title: '대리점 수강연장 현황',
                                url:'center_class_renew_status.php'
                            },
                            {
                                title: '강사출근현황',
                                url:'teacher_attend_excel_form.php'
                            },
                            {
                                title: '수업출석현황',
                                url:'teacher_enter_excel_form.php'
                            },
                            {
                                title: '강사별수업종료현황',
                                url:'student_secession_by_teacher.php'
                            },
                            {
                                title: '포인트 항목관리',
                                url:'point_type_list.php'
                            },
                            {
                                title: '팝업 관리',
                                url:'popup_list.php'
                            },
                            //스케줄 관리
                            {
                                title: '스케줄 관리',
                                url:'calendar_view.php'
                            },

                            //강사명
                            {
                                group: 'teacher',
                                title: 'Mariane',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=24'
                            },
                            {
                                group: 'teacher',
                                title: 'Rica',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=26'
                            },
                            {
                                group: 'teacher',
                                title: 'Gretchelle',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=29'
                            },
                            {
                                group: 'teacher',
                                title: 'Maj',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=34'
                            },
                            {
                                group: 'teacher',
                                title: 'Farrah',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=35'
                            },
                            {
                                group: 'teacher',
                                title: 'Janice',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=37'
                            },
                            {
                                group: 'teacher',
                                title: 'Faye',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=38'
                            },
                            {
                                group: 'teacher',
                                title: 'Reiza (ML)',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=44'
                            },
                            {
                                group: 'teacher',
                                title: 'Donna',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=45'
                            },
                            {
                                group: 'teacher',
                                title: 'Maimai',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=52'
                            },
                            {
                                group: 'teacher',
                                title: 'JP',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=60'
                            },
                            {
                                group: 'teacher',
                                title: 'Chaine',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=64'
                            },
                            {
                                group: 'teacher',
                                title: 'Junessa',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=68'
                            },
                            {
                                group: 'teacher',
                                title: 'Junry',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=75'
                            },
                            {
                                group: 'teacher',
                                title: 'Ann',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=115'
                            },
                            {
                                group: 'teacher',
                                title: 'Jane',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=131'
                            },
                            {
                                group: 'teacher',
                                title: 'Prince',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=134'
                            },
                            {
                                group: 'teacher',
                                title: 'Anin',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=146'
                            },
                            {
                                group: 'teacher',
                                title: 'Ash',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=153'
                            },
                            {
                                group: 'teacher',
                                title: 'Daisy',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=154'
                            },
                            {
                                group: 'teacher',
                                title: 'R',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=155'
                            },
                            {
                                group: 'teacher',
                                title: 'Apple',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=156'
                            },
                            {
                                group: 'teacher',
                                title: '중국어 장선생님',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=157'
                            },
                            {
                                group: 'teacher',
                                title: 'Melca',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=162'
                            },
                            {
                                group: 'teacher',
                                title: 'Kliv',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=163'
                            },
                            {
                                group: 'teacher',
                                title: 'Sol',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=164'
                            },
                            {
                                group: 'teacher',
                                title: 'Ryan',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=165'
                            },
                            {
                                group: 'teacher',
                                title: 'Jenny',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=166'
                            },
                            {
                                title: 'Mo',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=167'
                            },
                            {
                                group: 'teacher',
                                title: '장지웅2',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=168'
                            },
                            {
                                group: 'teacher',
                                title: 'Beth',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=171'
                            },
                            {
                                group: 'teacher',
                                title: 'Shan',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=172'
                            },
                            {
                                group: 'teacher',
                                title: 'Novy',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=173'
                            },
                            {
                                group: 'teacher',
                                title: 'Kes',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=174'
                            },
                            {
                                group: 'teacher',
                                title: 'test teacher',
                                url: 'teacher_form.php?ListParam=1=1^^PageListNum=30^^SearchState=1^^CurrentPage=2&TeacherID=175'
                            },
                        ];

                        if (menuSearchBar.value) {
                            // 필터링 조건 적용
                            const filteredMenu = menuList.filter((menu) => hideClassManagementForUser(menu) && searchFunc(menu.title.toLowerCase()));
                            if (filteredMenu) {
                                filteredMenu.forEach((menu) => showFilteredMenu(menu));
                            }

                        const searchFunc = (objId) => {
                            let searchId = menuSearchBar.value;
                            return objId.indexOf(searchId.toLowerCase()) !== -1;
                        }

                        const showFilteredMenu = (menu) => {
                            menuSearchResultList.style.display = "block";
                            const menuSearchResult = document.createElement("li");
                            menuSearchResult.innerHTML = `
                            <a href="${menu.url}">
                            <li style="padding-left: 5px; border-bottom: 1px solid #e0e0e0">
                                ${menu.group === 'teacher' ? '강사> ' : '메뉴> '}${menu.title}
                            </li>
                            </a>`;
                            menuSearchResultList.append(menuSearchResult);
                        };

                        menuSearchResultList.innerHTML = "";
                        menuSearchResultList.style.display = "none";
                        // input 값이 있다면,
                        if (menuSearchBar.value) {
                            const filteredMenu = menuList.filter((menu) => searchFunc(menu.title.toLowerCase()));
                            if (filteredMenu) {
                                filteredMenu.forEach((menu) => showFilteredMenu(menu));
                            }
                        }
                    } 
					</script>
					

					<?
					if ($_LINK_ADMIN_LEVEL_ID_<=4){

						$Sql = "select 
										count(*) TotalRowCount 
								from MasterMessages A 
								where 
									A.MasterMessageID not in (select MasterMessageID from MasterMessageReads where MemberID=".$_LINK_ADMIN_ID_.") 
									and
									A.MasterMessageAlarmType=1
									and
									datediff(A.MasterMessageRegDateTime, now())=0
								";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);
						$Row = $Stmt->fetch();
						$Stmt = null;

						$TotalRowCount = $Row["TotalRowCount"];

						
						$Sql = "
								select 
									A.*
								from MasterMessages A
								where 
									A.MasterMessageID not in (select MasterMessageID from MasterMessageReads where MemberID=".$_LINK_ADMIN_ID_.")
									and
									A.MasterMessageAlarmType=1
									and
									datediff(A.MasterMessageRegDateTime, now())=0
								order by A.MasterMessageRegDateTime desc";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);

					?>

					<li data-uk-dropdown="{mode:'click',pos:'bottom-right'}"  style="display:;">
						<a href="#" class="user_action_icon"><i class="material-icons md-24 md-light">&#xE7F4;</i><span class="uk-badge"><?=$TotalRowCount?></span></a>
						<div class="uk-dropdown uk-dropdown-xlarge">
							<div class="md-card-content">
								<ul class="uk-tab uk-tab-grid" data-uk-tab="{connect:'#header_alerts',animation:'slide-horizontal'}">
									<li class="uk-width-1-1 uk-active"><a href="#" class="js-uk-prevent uk-text-small">Messages (<?=$TotalRowCount?>)</a></li>
									<!--<li class="uk-width-1-2"><a href="#" class="js-uk-prevent uk-text-small">Alerts (4)</a></li>-->
								</ul>
								<ul id="header_alerts" class="uk-switcher uk-margin">
									<li>
										<ul class="md-list md-list-addon">

										<?php
										$ListCount = 1;
										while($Row = $Stmt->fetch()) {
											$MasterMessageID = $Row["MasterMessageID"];

											$MasterMessageType = $Row["MasterMessageType"];
											$MasterMessageText = $Row["MasterMessageText"];
											$MasterMessageRegDateTime = $Row["MasterMessageRegDateTime"];
								
										?>
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-warning">&#xE8B2;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading"><a><?=$MasterMessageRegDateTime?></a></span>
													<span class="uk-text-small uk-text-muted">
														<?=$MasterMessageText?> 
													</span>
													<span class="uk-text-small uk-text-muted" style="text-align:right">
														<a href="javascript:SetMasterMessageRead(<?=$MasterMessageID?>, <?=$_LINK_ADMIN_ID_?>);"><?=$확인완료[$LangID]?></a>
													</span>
												</div>
											</li>
										<?
											$ListCount++;
										}
										$Stmt = null;
										?>
										</ul>
										<div class="uk-text-center uk-margin-top uk-margin-small-bottom">
											<a href="master_message_list.php" class="md-btn md-btn-flat md-btn-flat-primary js-uk-prevent">Show All</a>
										</div>

										<script>
										function SetMasterMessageRead(MasterMessageID, MemberID){
												url = "ajax_set_master_message_read.php";

												//location.href = url + "?MasterMessageID="+MasterMessageID+"&MemberID="+MemberID;
												$.ajax(url, {
													data: {
														MasterMessageID: MasterMessageID,
														MemberID: MemberID
													},
													success: function (data) {
														json_data = data;
														MasterMessageReadDateTime = json_data.MasterMessageReadDateTime;
														if ($("#DivMasterMessageReadDateTime_"+MasterMessageID).length>0){
															document.getElementById("DivMasterMessageReadDateTime_"+MasterMessageID).innerHTML = MasterMessageReadDateTime;
															document.getElementById("DivMasterMessageReadBtn_"+MasterMessageID).innerHTML = "-";
														}else{

														}

													},
													error: function () {

													}
												});
										}
										</script>
									</li>
									<!--
									<li>
										<ul class="md-list md-list-addon">
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-warning">&#xE8B2;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading">Consequatur excepturi esse.</span>
													<span class="uk-text-small uk-text-muted uk-text-truncate">Beatae accusantium eligendi non.</span>
												</div>
											</li>
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-success">&#xE88F;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading">Officiis et qui.</span>
													<span class="uk-text-small uk-text-muted uk-text-truncate">Et dolores soluta ullam voluptatibus cupiditate.</span>
												</div>
											</li>
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-danger">&#xE001;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading">Doloribus odit vitae.</span>
													<span class="uk-text-small uk-text-muted uk-text-truncate">Placeat dolorem nam sunt voluptatem cum similique assumenda.</span>
												</div>
											</li>
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-primary">&#xE8FD;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading">Et ea.</span>
													<span class="uk-text-small uk-text-muted uk-text-truncate">Voluptas voluptatem occaecati nobis et rerum illum.</span>
												</div>
											</li>
										</ul>
									</li>
									-->
								</ul>
							</div>
						</div>
					</li>

					<?
					}else if ($_LINK_ADMIN_LEVEL_ID_==15){

						$Sql = "select 
										count(*) TotalRowCount 
								from TeacherMessages A 
								where 
									A.TeacherMessageID not in (select TeacherMessageID from TeacherMessageReads where MemberID=".$_LINK_ADMIN_ID_.") 
									and A.MemberID=".$_LINK_ADMIN_ID_." 
									and datediff(A.TeacherMessageRegDateTime, now())=0 
								";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);
						$Row = $Stmt->fetch();
						$Stmt = null;

						$TotalRowCount = $Row["TotalRowCount"];

						
						$Sql = "
								select 
									A.*
								from TeacherMessages A
								where 
									A.TeacherMessageID not in (select TeacherMessageID from TeacherMessageReads where MemberID=".$_LINK_ADMIN_ID_.") 
									and A.MemberID=".$_LINK_ADMIN_ID_." 
									and datediff(A.TeacherMessageRegDateTime, now())=0 
								order by A.TeacherMessageRegDateTime desc";
						$Stmt = $DbConn->prepare($Sql);
						$Stmt->execute();
						$Stmt->setFetchMode(PDO::FETCH_ASSOC);
					?>

					<li data-uk-dropdown="{mode:'click',pos:'bottom-right'}"  style="display:;">
						<a href="#" class="user_action_icon"><i class="material-icons md-24 md-light">&#xE7F4;</i><span class="uk-badge"><?=$TotalRowCount?></span></a>
						<div class="uk-dropdown uk-dropdown-xlarge">
							<div class="md-card-content">
								<ul class="uk-tab uk-tab-grid" data-uk-tab="{connect:'#header_alerts',animation:'slide-horizontal'}">
									<li class="uk-width-1-1 uk-active"><a href="#" class="js-uk-prevent uk-text-small">Messages (<?=$TotalRowCount?>)</a></li>
									<!--<li class="uk-width-1-2"><a href="#" class="js-uk-prevent uk-text-small">Alerts (4)</a></li>-->
								</ul>
								<ul id="header_alerts" class="uk-switcher uk-margin">
									<li>
										<ul class="md-list md-list-addon">

										<?php
										$ListCount = 1;
										while($Row = $Stmt->fetch()) {
											$TeacherMessageID = $Row["TeacherMessageID"];

											$TeacherMessageType = $Row["TeacherMessageType"];
											$TeacherMessageText = $Row["TeacherMessageText"];
											$TeacherMessageRegDateTime = $Row["TeacherMessageRegDateTime"];
								
										?>
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-warning">&#xE8B2;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading"><a><?=$TeacherMessageRegDateTime?></a></span>
													<span class="uk-text-small uk-text-muted">
														<?=$TeacherMessageText?> 
													</span>
													<span class="uk-text-small uk-text-muted" style="text-align:right">
														<a href="javascript:SetTeacherMessageRead(<?=$TeacherMessageID?>, <?=$_LINK_ADMIN_ID_?>);"><?=$확인완료[$LangID]?></a>
													</span>
												</div>
											</li>
										<?
											$ListCount++;
										}
										$Stmt = null;
										?>
										</ul>
										<div class="uk-text-center uk-margin-top uk-margin-small-bottom">
											<a href="teacher_message_list.php" class="md-btn md-btn-flat md-btn-flat-primary js-uk-prevent">Show All</a>
										</div>

										<script>
										function SetTeacherMessageRead(TeacherMessageID, MemberID){
												url = "ajax_set_teacher_message_read.php";

												//location.href = url + "?TeacherMessageID="+TeacherMessageID+"&MemberID="+MemberID;
												$.ajax(url, {
													data: {
														TeacherMessageID: TeacherMessageID,
														MemberID: MemberID
													},
													success: function (data) {
														json_data = data;
														TeacherMessageReadDateTime = json_data.TeacherMessageReadDateTime;
														if ($("#DivTeacherMessageReadDateTime_"+TeacherMessageID).length>0){
															document.getElementById("DivTeacherMessageReadDateTime_"+TeacherMessageID).innerHTML = TeacherMessageReadDateTime;
															document.getElementById("DivTeacherMessageReadBtn_"+TeacherMessageID).innerHTML = "-";
														}else{

														}

													},
													error: function () {

													}
												});
										}
										</script>
									</li>
									<!--
									<li>
										<ul class="md-list md-list-addon">
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-warning">&#xE8B2;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading">Consequatur excepturi esse.</span>
													<span class="uk-text-small uk-text-muted uk-text-truncate">Beatae accusantium eligendi non.</span>
												</div>
											</li>
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-success">&#xE88F;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading">Officiis et qui.</span>
													<span class="uk-text-small uk-text-muted uk-text-truncate">Et dolores soluta ullam voluptatibus cupiditate.</span>
												</div>
											</li>
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-danger">&#xE001;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading">Doloribus odit vitae.</span>
													<span class="uk-text-small uk-text-muted uk-text-truncate">Placeat dolorem nam sunt voluptatem cum similique assumenda.</span>
												</div>
											</li>
											<li>
												<div class="md-list-addon-element">
													<i class="md-list-addon-icon material-icons uk-text-primary">&#xE8FD;</i>
												</div>
												<div class="md-list-content">
													<span class="md-list-heading">Et ea.</span>
													<span class="uk-text-small uk-text-muted uk-text-truncate">Voluptas voluptatem occaecati nobis et rerum illum.</span>
												</div>
											</li>
										</ul>
									</li>
									-->
								</ul>
							</div>
						</div>
					</li>

					<?
					}
					?>
					<li data-uk-dropdown="{mode:'click',pos:'bottom-right'}">
						<a href="#" class="user_action_image"><img class="md-user-image" src="images/logo_profile.png" alt=""/> <?=$_LINK_ADMIN_NAME_?></a>
						<div class="uk-dropdown uk-dropdown-small">
							<ul class="uk-nav js-uk-prevent">
								<li><a href="logout.php">Logout</a></li>
							</ul>
						</div>
					</li>
				</ul>
			</div>
		</nav>
	</div> 
	<div class="header_main_search_form">
		<i class="md-icon header_main_search_close material-icons">&#xE5CD;</i>
		<form name="MemberChangeForm" id="MemberChangeForm" method="post">
			<input type="text" name="DummyInput" id="DummyInput" style="display:none;">
			<input type="text" name="ChangeMemberID" id="ChangeMemberID" class="header_main_search_input" style="color:#ffffff;" onkeyup="MemberChangeFormEn()" placeholder="<?=$전환할_아이디를_입력하세요[$LangID]?>"/>
			<a class="header_main_search_btn uk-button-link" href="javascript:MemberChangeFormSubmit();"><i class="md-icon material-icons">people</i></a>
			<script>
			function MemberChangeFormEn(){
				if (event.keyCode == 13){
					MemberChangeFormSubmit();
				}else{
					return false;
				}
			}
			function MemberChangeFormSubmit(){

				var ChangeMemberID = $.trim($('#ChangeMemberID').val());

				if (ChangeMemberID == "") {
					UIkit.modal.alert("<?=$전환할_아이디를_입력하세요[$LangID]?>");
				} else {
					url = "ajax_set_change_member.php";

					//location.href = url + "?NewID="+NewID;
					$.ajax(url, {
						data: {
							ChangeMemberID: ChangeMemberID
						},
						success: function (data) {
							json_data = data;
							UIkit.modal.alert(data.ErrMsg);
							
							if (data.ErrNum==0){
							
								setTimeout(function(){
									location.reload();
								}, 1000);

							}
						},
						error: function () {
							//UIkit.modal.alert("Error while contacting server, please try again");
						}
					});

				}

			}
			</script>
			
			<!--
			<script type="text/autocomplete">
				<ul class="uk-nav uk-nav-autocomplete uk-autocomplete-results">
					{{~items}}
					<li data-value="{{ $item.value }}">
						<a href="{{ $item.url }}" class="needsclick">
							{{ $item.value }}<br>
							<span class="uk-text-muted uk-text-small">{{{ $item.text }}}</span>
						</a>
					</li>
					{{/items}}
					<li data-value="autocomplete-value">
						<a class="needsclick">
							Autocomplete Text<br>
							<span class="uk-text-muted uk-text-small">Helper text</span>
						</a>
					</li>
				</ul>
			</script>
			-->
		</form>
	</div>
</header>
<!-- main header end -->


