                        <div class="draft_wrap">
							<div class="draft_top">
								<h3 class="draft_title">휴가 계획서</h3>
								<table class="draft_approval" style="margin-left:60px">
									<col width="">
									<colgroup span="4" width="22.5%"></colgroup>
									
									<tr style="height:60px;">
										<th rowspan="2">결<br><br>재</th>
										<td>
											<?
											$Feedback = array();
											$MemberName = array();
											$DocumentPermited = false;    // 품의서를 승인한 사람이 있는지 체크해서 있으면 true를 넣어준다.
											$StrDocumentReportMemberState1 = "-";
											if ($DocumentReportState==1) {
												
												$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=1";
												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												$Row3 = $Stmt3->fetch();
												$Stmt3 = null;

												$MemberName[0] = $Row3["MemberName"];
												$Feedback[0] = $Row3["Feedback"];
												$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
												$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
												if ($DocumentReportMemberState==0){
													$StrDocumentReportMemberState1 = "-";
												}else if ($DocumentReportMemberState==1){
													$DocumentPermited = true;
													$StrDocumentReportMemberState1 = $DocumentReportMemberModiDateTime . "<br>승인";
												}else if ($DocumentReportMemberState==2){
													$StrDocumentReportMemberState1 = $DocumentReportMemberModiDateTime . "<br>반려";
												}
												echo ("<input type='hidden' id='DocumentReportMemberID1' name='DocumentReportMemberID1' value='".$Row3["MemberID"]."'>");
												echo ($MemberName[0]); 
											
											} else {
											?>
												<select id="DocumentReportMemberID1" name="DocumentReportMemberID1">
													<option value="0">선택</option>
													<?
														if ($DocumentReportState==2){
															$Sql3 = "SELECT A.MemberID from DocumentReportMembers A where A.DocumentReportID=$DocumentReportID and DocumentReportMemberOrder=1 ";
															$Stmt3 = $DbConn->prepare($Sql3);
															$Stmt3->execute();
															$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
															$Row3 = $Stmt3->fetch();
															$Stmt3 = null;
															$DocumentReportMemberID = $Row3["MemberID"];
														}else{
															$DocumentReportMemberID = "";
														}

														$Sql3 = "SELECT A.* from Members A
																	WHERE A.MemberLevelID<=4
																	AND A.MemberID != :MemberID 
																	AND A.MemberID IN (SELECT MemberID FROM Hr_OrganLevelTaskMembers
																						WHERE Hr_OrganLevelID = :Hr_OrganLevelID)
																	ORDER BY A.MemberName ASC
																";
														$Stmt3 = $DbConn->prepare($Sql3);
														$Stmt3->bindParam(':MemberID', $My_MemberID);
														$Stmt3->bindParam(':Hr_OrganLevelID', $My_OrganLevelID);
														$Stmt3->execute();
														$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
														while($Row3 = $Stmt3->fetch()) {
														
													?>
														<option value="<?=$Row3["MemberID"]?>" <?if ($DocumentReportMemberID==$Row3["MemberID"]){?>selected<?}?>><?=$Row3["MemberName"]?></option>
													<?
														}
														$Stmt3 = null;
			
													?>
												</select>
											<?
											 }
											?>
										</td>
										<td>
											<?
											

											$StrDocumentReportMemberState2 = "-";
											if ($DocumentReportState==1) {
												$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=2";
												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												$Row3 = $Stmt3->fetch();
												$Stmt3 = null;

												$MemberName[1] = $Row3["MemberName"];
												$Feedback[1] = $Row3["Feedback"];
												$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
												$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
												if ($DocumentReportMemberState==0){
													$StrDocumentReportMemberState2 = "-";
												}else if ($DocumentReportMemberState==1){
													$DocumentPermited = true;
													$StrDocumentReportMemberState2 = $DocumentReportMemberModiDateTime . "<br>승인";
												}else if ($DocumentReportMemberState==2){
													$StrDocumentReportMemberState2 = $DocumentReportMemberModiDateTime . "<br>반려";
												}
												echo ("<input type='hidden' id='DocumentReportMemberID2' name='DocumentReportMemberID2' value='".$Row3["MemberID"]."'>");
												echo ($MemberName[1]); 
											}else{
											?>
												<select id="DocumentReportMemberID2" name="DocumentReportMemberID2">
													<option value="0">선택</option>
													<?
														
														if ($DocumentReportState==2){
															$Sql3 = "SELECT A.MemberID from DocumentReportMembers A where A.DocumentReportID=$DocumentReportID and DocumentReportMemberOrder=2 ";
															$Stmt3 = $DbConn->prepare($Sql3);
															$Stmt3->execute();
															$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
															$Row3 = $Stmt3->fetch();
															$Stmt3 = null;
															$DocumentReportMemberID = $Row3["MemberID"];
														}else{
															$DocumentReportMemberID = "";
														}
														
														$Sql3 = "SELECT A.* from Members A
																	WHERE A.MemberLevelID<=4
																	AND A.MemberID != :MemberID 
																	AND A.MemberID IN (SELECT MemberID FROM Hr_OrganLevelTaskMembers
																						WHERE Hr_OrganLevelID = :Hr_OrganLevelID)
																	ORDER BY A.MemberName ASC
																";
														$Stmt3 = $DbConn->prepare($Sql3);
														$Stmt3->bindParam(':MemberID', $My_MemberID);
														$Stmt3->bindParam(':Hr_OrganLevelID', $My_OrganLevelID);
														$Stmt3->execute();
														$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

														while($Row3 = $Stmt3->fetch()) {
														
													?>
														<option value="<?=$Row3["MemberID"]?>" <?if ($DocumentReportMemberID==$Row3["MemberID"]){?>selected<?}?>><?=$Row3["MemberName"]?></option>
													<?
														}
														$Stmt3 = null;
			
													?>
												</select>
											<?
											}
											?>
										</td>
										<td>
											<?
											$StrDocumentReportMemberState3 = "-";
											if ($DocumentReportState==1) {
												$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=3";
												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												$Row3 = $Stmt3->fetch();
												$Stmt3 = null;

												$MemberName[2] = $Row3["MemberName"];
												$Feedback[2] = $Row3["Feedback"];
												$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
												$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
												if ($DocumentReportMemberState==0){
													$StrDocumentReportMemberState3 = "-";
												}else if ($DocumentReportMemberState==1){
													$DocumentPermited = true;
													$StrDocumentReportMemberState3 = $DocumentReportMemberModiDateTime . "<br>승인";
												}else if ($DocumentReportMemberState==2){
													$StrDocumentReportMemberState3 = $DocumentReportMemberModiDateTime . "<br>반려";
												}
												echo ("<input type='hidden' id='DocumentReportMemberID2' name='DocumentReportMemberID2' value='".$Row3["MemberID"]."'>");
												echo ($MemberName[2]); 
											}else{
											?>
												<select id="DocumentReportMemberID3" name="DocumentReportMemberID3">
													<option value="0">선택</option>
													<?
														
														if ($DocumentReportState==2){
															$Sql3 = "SELECT A.MemberID from DocumentReportMembers A where A.DocumentReportID=$DocumentReportID and DocumentReportMemberOrder=3";
															$Stmt3 = $DbConn->prepare($Sql3);
															$Stmt3->execute();
															$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
															$Row3 = $Stmt3->fetch();
															$Stmt3 = null;
															$DocumentReportMemberID = $Row3["MemberID"];
														}else{
															$DocumentReportMemberID = "";
														}
														
														$Sql3 = "SELECT A.* from Members A
																	WHERE A.MemberLevelID<=4
																	AND A.MemberID != :MemberID 
																	AND A.MemberID IN (SELECT MemberID FROM Hr_OrganLevelTaskMembers
																						WHERE Hr_OrganLevelID = :Hr_OrganLevelID)
																	ORDER BY A.MemberName ASC
																";
														$Stmt3 = $DbConn->prepare($Sql3);
														$Stmt3->bindParam(':MemberID', $My_MemberID);
														$Stmt3->bindParam(':Hr_OrganLevelID', $My_OrganLevelID);
														$Stmt3->execute();
														$Stmt3->setFetchMode(PDO::FETCH_ASSOC);

														while($Row3 = $Stmt3->fetch()) {
														
													?>
														<option value="<?=$Row3["MemberID"]?>" <?if ($DocumentReportMemberID==$Row3["MemberID"]){?>selected<?}?>><?=$Row3["MemberName"]?></option>
													<?
														}
														$Stmt3 = null;
			
													?>
												</select>
											<?
											}
											?>
										</td>
										<td>
											<?
											$StrDocumentReportMemberState4 = "-";
											if ($DocumentReportState==1) {
												$Sql3 = "SELECT A.*, B.MemberName from DocumentReportMembers A inner join Members B on A.MemberID=B.MemberID where A.DocumentReportID=$DocumentReportID and A.DocumentReportMemberOrder=4";
												$Stmt3 = $DbConn->prepare($Sql3);
												$Stmt3->execute();
												$Stmt3->setFetchMode(PDO::FETCH_ASSOC);
												$Row3 = $Stmt3->fetch();
												$Stmt3 = null;

												$MemberName[3] = $Row3["MemberName"];
												$Feedback[3] = $Row3["Feedback"];
												$DocumentReportMemberState = $Row3["DocumentReportMemberState"];
												$DocumentReportMemberModiDateTime = substr($Row3["DocumentReportMemberModiDateTime"],0,10);
												if ($DocumentReportMemberState==0){
													$StrDocumentReportMemberState4 = "-";
												}else if ($DocumentReportMemberState==1){
													$DocumentPermited = true;
													$StrDocumentReportMemberState4 = $DocumentReportMemberModiDateTime . "<br>승인";
												}else if ($DocumentReportMemberState==2){
													$StrDocumentReportMemberState4 = $DocumentReportMemberModiDateTime . "<br>반려";
												}
												echo ("<input type='hidden' id='DocumentReportMemberID4' name='DocumentReportMemberID4' value='22050'>");
												echo ($MemberName[3]); 
											}else{
											?>
												<select id="DocumentReportMemberID4" name="DocumentReportMemberID4">
													<option value="22050">정우영</option>
												</select>
											<?
											}
											?>
										</td>
									</tr>
									<tr>
										<td><?=$StrDocumentReportMemberState1?></td>
										<td><?=$StrDocumentReportMemberState2?></td>
										<td><?=$StrDocumentReportMemberState3?></td>
										<td><?=$StrDocumentReportMemberState4?></td>
									</tr>
								</table>
								<?php 
									for ($i=1; $i<=4; $i++) {
										if (strstr(${"StrDocumentReportMemberState".$i},'반려')){
											$j = $i - 1;
								?>			
								<style>
								.box {
									width: 250px;
									min-height: 50px;
									border: 1px solid gray;
									display: block;
									background-color:bisque;
									/*box-shadow: 5px 5px 20px;*/
									margin: auto;
									margin-bottom: 10px;
									transition: all 0.5s;
									transition-delay: 0.4s;
									padding: 10px;
									}

									.box:hover {
									width: 255px;
									min-height: 55px;
									}
								</style>
								<div>
										<div class="box">
											<h6 style="text-align:center;color:darkslategrey"><?=$MemberName[$j]?> 님의 반려 사유</h6>
											<?=$Feedback[$j]?>
										</div>
								</div>		
								<?			
										}
									}
								?>
							</div>
							<table class="draft_table_1">
								<col width="13%">
								<col width="22%">
								<col width="">
								<col width="22%">
								<col width="13%">
								<col width="22%">
								<tr>
									<th class="draft_cell_green">제출일</th>
									<td><?=$StrDocumentReportRegDateTime?></td>
									<td>인</td>
									<th>증빙자료</th>
									<td  colspan=2 align=center>
                                        <? if (!$printMode) {?>
										<div id="multiple">
											<div type="button" class="btn btn-success fileup-btn" style="display:block;margin-left:auto;margin-right:auto;margin-bottom:5px">
												올릴 파일 선택
												<? 
													if (!$DocumentPermited) {
												?>
												<input type="file" id="upload-2" multiple accept=".jpg, .jpeg, .png, .gif, .doc, .docx, .xls, .xlsx, .hwp, .pdf, .psd, .txt, .ppt, .zip">
												<?
													}
												?>
											</div>
											<div id="upload-2-queue" class="queue" style="display:inline-block"></div>
										</div>
                                        <? } else { 
                                            if ($FileName!=""){
												$fileRealNameArr = explode(',',$FileRealName);
												$fileNameArr = explode(',',$FileName);
												for($i=0; $i<count($fileRealNameArr); $i++){
											?>
												<a href="../uploads/document_files/<?=$fileNameArr[$i]?>" download><?=$fileRealNameArr[$i]?></a><br>
											<?		
												}


											} else {?>
											-
											<?}
                                          } ?>
										<input type="hidden" id="FileRealName" name="FileRealName" value="<?=$FileRealName?>" class="draft_input">
										<input type="hidden" id="FileName" name="FileName" value="<?=$FileName?>" class="draft_input">
									</td>
								</tr>
								<tr>
									<th class="draft_cell_green">소속</th>
									<td colspan="5"><?=$Document_OrganName?></td>
								</tr>
								<tr>
									<th class="draft_cell_green">이름</th>
									<td colspan="5"><?=$Document_MemberName?></td>
								</tr>

								<tr>
									<th class="draft_cell_green">휴가 제목</th>
									<td colspan="5"><input type="input" id="DocumentReportName" name="DocumentReportName" value="<?=$DocumentReportName?>" class="draft_input"  <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
								<tr>
									<th class="draft_cell_yellow">사유</th>
									<td colspan="5" style="text-align:left;"><textarea id="DocumentReportContent" name="DocumentReportContent" class="draft_textarea"  <?=$DocumentPermited?"readonly":""?>><?=$DocumentReportContent?></textarea></td>
								</tr>
							</table>
							<?
							// 문서를 수정하는 경우 현재 등록되어 있는 휴가의 세부 내용을 가지고 온다.
							$Sql2 = "SELECT *
										from SpentHoliday 
										where DocumentReportID = $DocumentReportID";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							$Row2 = $Stmt2->fetch();

							$SpentDays = isset($Row2["SpentDays"]) ? $Row2["SpentDays"] : 0;
							$StartDate = isset($Row2["StartDate"]) ? $Row2["StartDate"] : "";
							$EndDate = isset($Row2["EndDate"]) ? $Row2["EndDate"] : "";
							
							// 문서 수정중일 경우 사용한 휴가수가 중복으로 계산되는 걸 방지
							$SpentHoliday -= $SpentDays;

							?>

							<table class="draft_table_3">
								<col width="25%">
								<col width="">
								<tr>
									<th rowspan="2">휴가 사용 일수<br>(의무 방학일수 제외)</th>
									<td align="center">총 휴가일수(일)</td>
									<td align="center">사용한 휴가 일수(일)</td>
									<td align="center">금번 휴가일수 (일)</td>
									<td align="center">잔여 휴가일수 (일)</td>
								</tr>
								<tr>
									<td><input type="input" id="MaxHoliday" name="MaxHoliday" value="<?=$MaxHoliday?>" class="draft_input"  readonly></td>
									<td><input type="input" id="SpentHoliday" name="SpentHoliday" value="<?=$SpentHoliday?>" class="draft_input"  readonly></td>
									<td><input type="input" id="Holiday" name="Holiday" value="<?=$SpentDays?>" class="draft_input"  <?=$DocumentPermited?"readonly":""?>></td>
									<td><input type="input" id="RemainHoliday" name="RemainHoliday" value="" class="draft_input"  readonly></td>
								</tr>

								<tr>
									<th>휴가 기간</th>
									<td colspan="4"><input type="input" id="StartDate" name="StartDate" value="<?=$StartDate?>" class="draft_input" style="width:40%;display:inline" <?=$DocumentPermited?"readonly":"data-uk-datepicker=\"{format:'YYYY-MM-DD', weekstart:0}\" "?>> 
									    ~ <input type="input" id="EndDate" name="EndDate" value="<?=$EndDate?>" class="draft_input" style="width:40%;display:inline"  <?=$DocumentPermited?"readonly":"data-uk-datepicker=\"{format:'YYYY-MM-DD', weekstart:0}\" "?>></td>
								</tr>
								<tr>
									<th>본인 외 비상연락망</th>
									<td colspan="4"><input type="input" id="PayMemo" name="PayMemo" value="<?=$PayMemo?>" class="draft_input"  <?=$DocumentPermited?"readonly":""?>></td>
								</tr>
							</table>
							<div class="draft_bottom">
								위의 내용과 같이 휴가계획서를 제출합니다.<br>
								<?=$StrDocumentReportRegDateTime2?>
								<div class="draft_sign_wrap">작 성 자 : <?=$Document_MemberName?> <span class="draft_sign">(인)</span></div>
							</div>
						</div>