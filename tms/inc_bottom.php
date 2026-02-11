
			</div>
		</div>
		
	</div>
</div>

<div class="footer">
	<address>
		<span>서울시 서초구 반포대로4길 58, 지하1층(서초동, 케이탑리츠 서초빌딩)</span>
		<span>메이플런치카페 ㅣ사업자등록번호 : 165-47-00317 ㅣ대표 : 서은옥</span>
	</address>
	Copyright © 메이플런치카페 All rights reserved.
</div>


<script>
//float : class="allownumericwithdecimal"
$(".allownumericwithdecimal").on("keypress keyup blur",function (event) {
    //this.value = this.value.replace(/[^0-9\.]/g,'');
    $(this).val($(this).val().replace(/[^0-9\.]/g,''));
    if ((event.which != 46 || $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});

//int : class="allownumericwithoutdecimal"
$(".allownumericwithoutdecimal").on("keypress keyup blur",function (event) {    
    $(this).val($(this).val().replace(/[^\d].+/, ""));
    if ((event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});
</script>