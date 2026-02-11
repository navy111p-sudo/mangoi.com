<?
						foreach($departments as $key => $value){
							${"part".$key} = array();
						}	
						

						// 각 부서별로 직원들 데이터를 가지고 온다.
						foreach($departments as $key => $value){
							$Sql2 = "SELECT A.StaffID, A.StaffName, B.MemberID, B.MemberName 
										FROM Staffs A 
										LEFT JOIN Members B ON A.StaffID = B.StaffID
										WHERE A.StaffState = 1 AND A.StaffManagement = $key AND B.MemberID <> '' 
										ORDER BY B.MemberName";
							$Stmt2 = $DbConn->prepare($Sql2);
							$Stmt2->execute();
							$Stmt2->setFetchMode(PDO::FETCH_ASSOC);
							while($Row2 = $Stmt2->fetch()){
								//array_push(${"part".$i},[$Row2["MemberID"] => $Row2["MemberName"]]);
								${"part".$key}[$Row2["MemberID"]] = $Row2["MemberName"];
							}
							//asort(${"part".$i});
						}
						
						?>

						<script>
							// 부서 카테고리별로 직원들 선택할수 있는 내용 변경
							function categoryChange(e, selectName, selectedID) {

								
								
									<?
									//배열에 값 넣어주기
									foreach($departments as $key => $value){
										//배열 생성
										echo "
											var part".$key." = [];
											var part".$key."Value = [];
										";


										foreach(${"part".$key} as $key2 => $value2){
											if ($key2 != null)
											echo "part".$key.".push('".$key2."');";
											echo "part".$key."Value.push('".$value2."');\n";
										} 
									}
									?>

								
								var target = document.getElementById(selectName);


								if (e.value == 'none'){

									target.options.length = 0;

								} else {
									partArray = eval("part"+e.value);
									partArrayValue = eval("part"+e.value+"Value");
									/*
									if(e.value == "1") var partArray = part1, partArrayValue= part1Value;
									else if(e.value == "2") var partArray = part2, partArrayValue= part2Value;
									else if(e.value == "3") var partArray = part3, partArrayValue= part3Value;
									else if(e.value == "4") var partArray = part4, partArrayValue= part4Value;
									else if(e.value == "0") var partArray = part0, partArrayValue= part0Value;
									*/

									target.options.length = 0;

									for (i=0;i<partArray.length;i++) {
										var opt = document.createElement("option");
										opt.value = partArray[i];
										if (partArray[i] == selectedID) opt.selected = true;
										opt.innerHTML = partArrayValue[i];
										target.appendChild(opt);
									}

								}
								
							}
							<?
							// 저장되어 있던  결재 라인의 부서부분과 결제자의 이름을 세팅한다.
							if (!empty($DocumentReportMemberID)){
								
								for ($i=1;$i<=count($DocumentReportMemberID);$i++){
									if (key($DocumentReportMemberID[($i-1)]) != 0) {
										$key = key($DocumentReportMemberID[($i-1)]);
										echo "var category = document.getElementById('category".($i-1)."');";
										echo "category.value = ".$DocumentReportMemberID[($i-1)][$key].";";
										echo "categoryChange(category, 'DocumentReportMemberID".($i-1)."', ".$key." );";
									}
								}
							}	
							?>
						</script>