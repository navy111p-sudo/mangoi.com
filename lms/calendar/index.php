<?php
  error_reporting( E_ALL );
  ini_set( "display_errors", 1 );
?>
<?php
    
    
	include('includes/loader.php');
	$_SESSION['token'] = time();
    
?>

<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>스케줄</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="스케줄">
    <meta name="author" content="Paulo Regina">

    <!-- styles -->
    <link href="lib/bootstrap/bootstrap.css" rel="stylesheet">
    <link href="lib/fullcalendar/fullcalendar.css" rel="stylesheet">
    <link href="lib/spectrum/spectrum.css" rel="stylesheet">
    <link href="lib/flatpickr/flatpickr.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">
  </head>
<body>

    <!---------------------------------------------- CALENDAR MODALs ---------------------------------------------->

    <!-- Calendar Modal -->
    <div class="modal fade" id="calendarModal" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
          	<h4 class="modal-title" id="details-body-title"></h4>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">

            <div class="loadingDiv"></div>

            <!-- QuickSave/Edit FORM -->
          	<form id="modal-form-body">
            	<p>
            		<label>제목: </label>
                	<input class="form-control" name="title" value="" type="text">
                </p>
                <p>
                	<label>설명: </label>
                	<textarea class="form-control" name="description"></textarea>
                </p>
                <p>
                    <label>분류: </label>
                    <select name="categorie" class="form-control">
                        <?php
							foreach($calendar->getCategories() as $categorie)
							{
								$_SESSION['filter'] = str_replace('&amp;', '&', $_SESSION['filter']);
								echo '<option value="'.$categorie.'">'.$categorie.'</option>';
							}
                        ?>
                    </select>
                </p>
                <p>
                	 <label>일정 색상:</label>
                	 <input type="text" class="form-control input-sm" value="#587ca3" name="color" id="colorp">
                </p>
                <div class="pull-left mr-10">
                	<p id="repeat-type-select">
                	<label>반복:</label>
                	<select id="repeat_select" name="repeat_method" class="form-control">
                        <option value="no" selected>없음</option>
                        <option value="every_day">매일</option>
                        <option value="every_week">매주</option>
                        <option value="every_month">매월</option>
						<option value="every_year">매년</option>
                	</select>
                    </p>
                </div>
                <div class="pull-left">
                	<p id="repeat-type-selected">
                	<label>시간:</label>
                	<select id="repeat_times" name="repeat_times" class="form-control">
                    	<option value="1" selected>1</option>
						<?php
                            for($i = 2; $i <= 30; $i++) {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                        ?>
                	</select>
                    </p>
                </div>
                <div class="clearfix"></div>
                <p id="event-type-select">
                    <label>종류: </label>
                    <select id="event-type" name="all-day" class="form-control">
                        <option value="true">24시간 일정 (종일)</option>
                        <option value="false">사용자 정의</option>
                    </select>
                </p>
                <div id="event-type-selected">
                	<div class="pull-left mr-10">
                    	<p>
                    	<label>시작일:</label>
                    	<input type="text" name="start_date" class="form-control input-sm flatpickr" placeholder="" id="startDate">
                        </p>
                    </div>
                    <div class="pull-left">
                    	<p>
                   		<label>시작시간:</label>
                    	<input type="text" class="form-control input-sm flatpickr" name="start_time" placeholder="" id="startTime">
                        </p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pull-left mr-10">
                    	<p>
                    	<label>종료일:</label>
                    	<input type="text" class="form-control input-sm flatpickr" name="end_date" placeholder="" id="endDate">
                        </p>
                    </div>
                    <div class="pull-left">
                    	<p>
                    	<label>종료시간:</label>
                    	<input type="text" class="form-control input-sm flatpickr" name="end_time" placeholder="" id="endTime">
                        </p>
                    </div>
                </div>
                <div class="clearfix"></div>
				<div class="custom-fields">
				<?php
					$form->generate();
				?>
				</div>
            </form>

            <!-- Modal Details -->
            <div id="details-body">
                <div id="details-body-content"></div>
            </div>

          </div>
<?php            
            if ($_SESSION['adminLevel']==0 || $_SESSION['adminLevel']==1){    //마스터    
?>  

          <div class="modal-footer">
            <button type="button" id="export-event" class="btn btn-warning">내보내기</button>
            <button type="button" id="delete-event" class="btn btn-danger">삭제</button>
            <button type="button" id="edit-event" class="btn btn-info">편집</button>
            <button type="button" id="add-event" class="btn btn-primary">추가</button>
            <button type="button" id="save-changes" class="btn btn-primary">저장</button>
          </div>
<?php            
            }      
?>
        </div>
      </div>
    </div>

    <!-- Modal Delete Prompt -->
    <div id="cal_prompt" class="modal fade">
    	<div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body"></div>
<?php            
            if ($_SESSION['adminLevel']==0 || $_SESSION['adminLevel']==1){    //마스터    
?>  

        <div class="modal-footer">
        	<a href="#" class="btn btn-danger" data-option="remove-this">삭제</a>
            <a href="#" class="btn btn-danger" data-option="remove-repetitives">전체 삭제</a>
        	<a href="#" class="btn btn-default" data-bs-dismiss="modal">닫기</a>
        </div>
<?php }?>        
        </div>
        </div>
    </div>

    <!-- Modal Edit Prompt -->
    <div id="cal_edit_prompt_save" class="modal fade">
    	<div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
        	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body-custom"></div>
        <div class="modal-footer">
        	<a href="#" class="btn btn-info" data-option="save-this">저장</a>
            <a href="#" class="btn btn-info" data-option="save-repetitives">전체 저장</a>
        	<a href="#" class="btn btn-default" data-bs-dismiss="modal">닫기</a>
        </div>
        </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div id="cal_import" class="modal fade">
    	<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Import Event</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body-import" style="white-space: normal;">
					<p class="help-block">Copy & Paste the event code from your .ics file, open it using a text editor.</p>
					<textarea class="form-control" rows="10" id="import_content" style="margin-bottom: 10px;"></textarea>
					<input type="button" class="pull-right btn btn-info" onClick="calendar.calendarImport()" value="Import" />
				</div>
			</div>
        </div>
    </div>

    <input type="hidden" name="cal_token" id="cal_token" value="<?php echo $_SESSION['token']; ?>" />

    <!---------------------------------------------- THEME ---------------------------------------------->

	<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light">
		<div class="container">
			<a class="navbar-brand" href="index.php">스케줄</a>
			<!-- search (required if you want to have search) -->
			<form id="search" class="d-flex">
				<input class="form-control me-2" type="text">
				<button class="btn btn-outline-success" type="button" style="width:150px">검색</button>
			</form>
		</div>
	</nav><!-- .navbar -->

	<div class="container" style="margin-top: 80px;">

      <!--
      <a href="export.php" class="btn btn-warning float-end">내보내기</a>
      <a href="#cal_import" class="btn btn-info float-end me-2" data-bs-toggle="modal" data-bs-target="#cal_import">가져오기</a>
      -->

      <div class="clearfix"></div>

      <?php
        if($calendar->getCategories() !== false) { ?>
      <div id="cat-holder">
      <form id="filter-category">
          <select class="form-control input-sm" name="filter" style="width: auto;">
          	<?php

            if ($_SESSION['adminLevel']==0 || $_SESSION['adminLevel']==1){    //마스터    

				$selected = (isset($_SESSION['filter']) && $_SESSION['filter'] == 'all-fields' ? 'selected' : '');
				echo '<option '.$selected.' value="all-fields">All</option>';
            }    
				foreach($calendar->getCategories() as $categorie)
				{
					$selectedLoop = (isset($_SESSION['filter']) && $_SESSION['filter'] == $categorie ? 'selected' : '');
					echo '<option '.$selectedLoop.' value="'.$categorie.'">'.$categorie.'</option>';
				}
			?>
          </select>
      </form>
      </div>
      <?php 
         
      } 
      ?>
	  
      <div class="box">
        <div class="header"></div>
        <div class="content">
            <div id="calendar"></div>
			<div id="loading" class="spinner">
			  <div class="bounce1"></div>
			  <div class="bounce2"></div>
			  <div class="bounce3"></div>
			</div>
        </div>
    </div>

    </div> <!-- /container -->

	<script src="lib/moment.js"></script>
    <script src="lib/jquery.js"></script>
    <script src="lib/bootstrap/bootstrap.js"></script>
    <script src="lib/fullcalendar/fullcalendar.js"></script>
    <script src="lib/fullcalendar/lang-all.js"></script>
	<script src="lib/spectrum/spectrum.js"></script>
	<script src="lib/flatpickr/flatpickr.js"></script>
	<script src="js/custom.js"></script>
	<script src="js/jquery.calendar.js?ver=4"></script>
	<script src="js/g.map.js"></script>  
    <script src="//maps.googleapis.com/maps/api/js" defer></script>

    <!-- call calendar plugin -->
    <script type="text/javascript">
		$().FullCalendarExt({
			calendarSelector: '#calendar',
<?php            
            if ($_SESSION['adminLevel']==0 || $_SESSION['adminLevel']==1){    //마스터    
?>  

<?php } else {?>
            editable: false,
            quickSave: false,
            eventStartEditable: false,
            eventResizableFromStart: false,
            eventDurationEditable: false,
            eventResourceEditable: false,
            enableDrop: false,
            enableResize: false,

<?php            
}              
?>

			lang: 'ko',
			tooltip: false,
			fc_extend: {
				nowIndicator: true 
			}
		});
	</script>

</body>
</html>