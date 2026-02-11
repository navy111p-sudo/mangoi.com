/*
 *	jQuery FullCalendar Extendable Plugin
 *	An Ajax (PHP - Mysql - jquery) script that extends the functionalities of the fullcalendar plugin
 *  Dependencies:
 *   - jquery
 * 	 - jquery spectrum
 *   - flatpickr
 *   - fullcalendar 5
 *   - Bootstrap 5
 *  Author: Paulo Regina
 *  Website: www.pauloreg.com
 *  Fullcalendar 5.10.2
 *	Released Under Envato Regular or Extended Licenses
 */

(function($, undefined)
{
	$.fn.extend
	({
		// FullCalendar Extendable Plugin
		FullCalendarExt: function(options)
		{
			var token = 'token='+$('#cal_token').val();
			var tmz = Intl.DateTimeFormat().resolvedOptions().timeZone;

			// Default Configurations (General)
			var defaults =
			{
				calendarSelector: '#calendar',
				loadingSelector: '#loading',

				lang: 'en',

				token: '',
				
				timezone: tmz,
				
				ajaxJsonFetch: 'includes/cal_events.php?'+token+'&timezone='+tmz,
				ajaxUiUpdate: 'includes/cal_update.php?'+token+'&timezone='+tmz,
				ajaxEventQuickSave: 'includes/cal_quicksave.php?'+token+'&timezone='+tmz,
				ajaxEventEdit: 'includes/cal_edit_update.php?'+token+'&timezone='+tmz,
				ajaxEventDelete: 'includes/cal_delete.php?'+token,
				ajaxEventExport: 'includes/cal_export.php?'+token,
				ajaxRepeatCheck: 'includes/cal_check_rep_events.php?'+token,
				ajaxRetrieveDescription: 'includes/cal_description.php?'+token,
				ajaxImport: 'importer.php?'+token,
				jsonConfig: 'includes/form.json',

				modalSelector: '#calendarModal',
				modalPromptSelector: '#cal_prompt',
				modalEditPromptSelector: '#cal_edit_prompt_save',

				formAddEventSelector: 'form#add_event',
				formFilterSelector: 'form#filter-category select',
				formSearchSelector:"form#search",

				newEventText: '새 일정 추가',
				successAddEventMessage: '새 일정이 추가되었습니다.',
				successDeleteEventMessage: '일정이 삭제되었습니다.',
				successUpdateEventMessage: '일정을 수정했습니다.',
				failureAddEventMessage: '일정 추가 실패!',
				failureDeleteEventMessage: '일정 삭제 실패!',
				failureUpdateEventMessage: '일정 수정 실패!',
				generalFailureMessage: '실행 실패!',
				ajaxError: '내용 가져오기 실패!',
				emptyForm: '빈 내용이 있으면 안됩니다!',
				unableToOpenEvent: '에러.일정을 열 수 없습니다!',

				eventText: '일정: ',
				repetitiveEventActionText: '반복되는 일정인데 어떻게 하시겠습니까?',
				
				todayLabel: '오늘',
				monthLabel: '월',
				weekLabel: '주',
				dayLabel: '일',
				listLabel: '목록',
				
				direction: 'ltr', // ltr or rtl
				weekText: 'W',

				defaultColor: '#587ca3',

				weekType: 'timeGridWeek',
				dayType: 'timeGridDay', // timeGridWeek, timeGridDay, dayGridDay, dayGridWeek
				listType: 'listWeek', // listDay, listWeek, listMonth, and listYear
				titleRangeSeparator: ' \u2013 ',
				
				tooltip: true,
				editable: true,
				lazyFetching: true,
				filter: true,
				quickSave: true,
				navLinks: true,
				firstDay: 0,

				gcal: false,
				gcalUrlText: '구글에서 보기',

				version: 'modal',

				initialView: 'dayGridMonth', // dayGridMonth, dayGridWeek, timeGridDay, listWeek
				aspectRatio: 1.35, // will make day boxes bigger
				weekends: true, // show (true) the weekend or not (false)
				weekNumbers: false, // show week numbers (true) or not (false)
				weekNumberCalculation: 'iso',

				hiddenDays: [], // [0,1,2,3,4,5,6] to hide days as you wish

				themeSystem: 'standard', // standard, bootstrap5, bootstrap
				
				eventTimeFormat: {
					hour: '2-digit',
					minute: '2-digit',
					hour12: false
				},

				fixedWeekCount: true,

				allDaySlot: true, // true, false

				slotDuration: '00:30:00',
				slotMinTime: '00:00:00',
				slotMaxTime: '24:00:00',
				nextDayThreshold: '00:00:00',
				slotEventOverlap: true,
				displayEventEnd: true,
				
				enableDrop: true,
				enableResize: true,

				savedRedirect: 'index.php',
				removedRedirect: 'index.php',
				updatedRedirect: 'index.php',

				ajaxLoaderMarkup: '<div class="loadingDiv"></div>',

				otherSource: false,

				modalFormBody: $('#modal-form-body').html(),

				icons_title: false,
				fc_extend: {},
				
				polling: true,
				pollingInt: 30, // time in seconds
				
				dayMaxEventRows: true,
				moreLinkClick: 'popover', // 'popover', 'week', 'day', view name, function
				palette: [
							["#0b57a4","#8bbdeb","#000000","#2a82d7","#148aa5","#3714a4","#587ca3","#a50516"],
							["#fb3c8f","#1b4f15","#1b4f15","#686868","#3aa03a","#ff0080","#fee233","#fc1cad"],
							["#7f2b14","#000066","#2b4726","#fd7222","#fc331c","#af31f2","#fc0d1b","#2b8a6d"],
							["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"]
						]
            }
			
			var ops =  $.extend(defaults, options);

			var opt = ops;

			if(opt.gcal == true) { opt.weekType = ''; opt.dayType = ''; }
			
			var fc_calendar = {};
			
			var fcOpts = 
			{
				locale: opt.lang,
				editable: opt.editable,
				dayMaxEventRows: opt.dayMaxEventRows,
				moreLinkClick: opt.moreLinkClick,
				navLinks: opt.navLinks,

				initialView: opt.initialView,
				aspectRatio: opt.aspectRatio,
				weekends: opt.weekends,
				weekNumbers: opt.weekNumbers,
				weekNumberCalculation: opt.weekNumberCalculation,
				weekText: opt.weekText,
				direction: opt.direction,
				hiddenDays: opt.hiddenDays,
				themeSystem: opt.themeSystem,
				allDaySlot: opt.allDaySlot,
				slotDuration: opt.slotDuration,
				slotMinTime: opt.slotMinTime,
				slotMaxTime: opt.slotMaxTime,
				slotEventOverlap: opt.slotEventOverlap,
				fixedWeekCount: opt.fixedWeekCount,
				eventTimeFormat: opt.eventTimeFormat,
				headerToolbar:
				{
					start: 'prev,next today',
					center: 'title',
					end: 'dayGridMonth,'+opt.weekType+','+opt.dayType+','+opt.listType
				},
				titleRangeSeparator: opt.titleRangeSeparator,
				monthNames: opt.monthNames,
				monthNamesShort: opt.monthNamesShort,
				dayNames: opt.dayNames,
				dayNamesShort: opt.dayNamesShort,
				buttonText: {
					today: opt.todayLabel,
					month: opt.monthLabel,
					week: opt.weekLabel,
					day: opt.dayLabel,
					list: opt.listLabel
				},
				firstDay: opt.firstDay,
				lazyFetching: opt.lazyFetching,
				selectable: opt.quickSave,
				selectMirror: opt.quickSave,
				eventStartEditable: opt.enableDrop,
				eventDurationEditable: opt.enableResize,
				nextDayThreshold: opt.nextDayThreshold,
				displayEventEnd: opt.displayEventEnd,
				loading: function(bool) {
					if(bool == false)
					{
						$(opt.loadingSelector).hide();
					} else if(bool == true) {
						$(opt.loadingSelector).show();
					}
				},
				select: function(info)
				{
					let view = info.view;
					
					let selectStart = info.start;
					let selectEnd = info.end;
					
					calendar.view = view.type;
					if(opt.version == 'modal')
					{
						calendar.quickModal(selectStart, selectEnd, info.allDay);
						fullCal.unselect();
					}

				   if(view.type !== 'dayGridMonth')
				   {
					 if(moment(selectStart).format('HH:mm') !== moment(selectEnd).format('HH:mm'))
					 {
					   $('#event-type option[value="false"]').prop('selected', true);
					   $('#event-type-select').show();
					   $('#event-type-selected').show();
					 }
				   }
				},
				eventSources: [
					opt.otherSource,
					{
						url: opt.ajaxJsonFetch
					}
				],
				eventDrop:
					function(info)
					{
						eventDropResize(info);
					},
				eventResize:
					function(info)
					{
						eventDropResize(info);
					},
				eventDidMount:
					function(info)
					{
						let element = $(info.el);
						let event = info.event._def;
						let view = info.view;
						
						if(opt.tooltip)
						{
							var tooltip = new bootstrap.Tooltip(info.el, {
								title: event.title,
								placement: 'top',
								trigger: 'hover',
								container: 'body'
							});
						}
						
						if(element.attr('href'))
						{
							element.attr('data-toggle', 'modal');
							element.attr('href', 'javascript:void(0)');
							element.attr('onclick', 'calendar.openModalGcal("' + encodeURI(event.title) + '","' + event.url + '");');
						} else {
							if(opt.icons_title == true)
							{
								var currentTitle = element.find('.fc-title').text();
								var replaced = currentTitle.replace(/\[(.*?)\]/gi, '<i class="$1"></i>');
								element.find('.fc-title').html(replaced);
							}

							var d_color = event.ui.backgroundColor;
							
							var d_startDate = moment(event.extendedProps.utcStart).format('YYYY-MM-DD');
							var d_startTime = moment(event.extendedProps.utcStart).format('HH:mm');
							var d_endDate = moment(event.extendedProps.utcEnd).format('YYYY-MM-DD');
							var d_endTime = moment(event.extendedProps.utcEnd).format('HH:mm');

							var e_val = moment(event.extendedProps.utcEnd).isValid();
							if(e_val == false)
							{
								var d_endDate = d_startDate;
								var d_endTime = d_startTime;
							}
							
							element.attr('data-eventstart', event.extendedProps.utcStart);
							element.attr('data-eventend', event.extendedProps.utcEnd);
							element.attr('data-d_startdate', d_startDate);
							element.attr('data-d_starttime', d_startTime);
							element.attr('data-d_enddate', d_endDate);
							element.attr('data-d_endtime', d_endTime);

							if(opt.version == 'modal')
							{
								// Open action (modalView Mode)
								element.attr('data-toggle', 'modal');
								element.attr('href', 'javascript:void(0)');
								
								element.attr('data-id', event.publicId);
								element.attr('data-rep_id', event.extendedProps.original_id);
								element.attr('data-title', encodeURIComponent(event.title));
								element.attr('data-url',  encodeURIComponent(event.url));
								element.attr('data-color', d_color);
								
								element.attr('onclick', 'calendar.openModal(this)');
							}
						}
						Object.freeze(calendar);
					},
				eventClick: function(info) {
					$(opt.loadingSelector).show();
				}
			}
			
			var fc_extends =  $.extend(fcOpts, opt.fc_extend);

			// fullCalendar
			var calendarEl = document.querySelector(opt.calendarSelector);
			var fullCal = new FullCalendar.Calendar(calendarEl, fc_extends);
			fullCal.render();
			
			if(opt.polling)
			{
				setInterval(function() {
					fullCal.refetchEvents();
				}, opt.pollingInt*1000);
			}
			
				// Function to Open Modal
				calendar.openModal = function(elem)
				{
					fc_calendar.id = $(elem).data('id');
					fc_calendar.rep_id = $(elem).data('rep_id');
					fc_calendar.title = $(elem).data('title');
					fc_calendar.url = $(elem).data('url');
					fc_calendar.eventStart = $(elem).data('eventstart');
					fc_calendar.eventEnd = $(elem).data('eventend');
					fc_calendar.color = $(elem).data('color');
					fc_calendar.d_startDate = $(elem).data('d_startdate');
					fc_calendar.d_startTime = $(elem).data('d_starttime');
					fc_calendar.d_endDate = $(elem).data('d_enddate');
					fc_calendar.d_endTime = $(elem).data('d_endtime');
				
					if(opt.icons_title == true)
					{
						fc_calendar.title = fc_calendar.title.replace(/\[(.*?)\]/gi, '<i class="$1"></i>');
						fc_calendar.title = decodeURIComponent(fc_calendar.title);
					} else {
						fc_calendar.title = decodeURIComponent(fc_calendar.title);
					}

					$('#modal-form-body').hide();
					$('#details-body').show();

					fc_calendar.ExpS = fc_calendar.eventStart;
					fc_calendar.ExpE = fc_calendar.eventEnd;
 
					$.ajax({
						type: "POST",
						url: opt.ajaxRetrieveDescription,
						data: {id: fc_calendar.id, title: encodeURIComponent(fc_calendar.title), start: fc_calendar.d_startDate, mode: 'edit'},
						cache: false,
						beforeSend: function() { $('.loadingDiv').show(); $('.modal-footer').hide() },
						error: function() { $('.loadingDiv').hide(); console.log(opt.ajaxError) },
						success: function(json_enc)
						{
							$(opt.loadingSelector).hide();
							if(json_enc)
							{
								$('.loadingDiv').hide();
								var json = JSON.parse(json_enc);
								var dsc = json.description.replace('$null', '');
								var color = json.color.replace('$null', '');
								var cat = json.category.replace('$null', '');

								fc_calendar.description_editable = json.description_editable.replace('&amp;', '&');
								fc_calendar.description = dsc.replace('&amp;', '&');
								fc_calendar.category = cat.replace('&amp;', '&');
								fc_calendar.color = color;
								fc_calendar.data = json;
								
								var thisData = JSON.parse(JSON.stringify(json));

								$('#details-body-title').html(fc_calendar.title);
								
								customFieldsOutput(dsc, thisData);
								
								$('#export-event, #delete-event, #edit-event').show();
								$('#save-changes, #add-event').hide();

								$('.modal-footer').show();
								$(opt.modalSelector).modal('show');
							} else {
								alert(opt.unableToOpenEvent);
							}
						}
					});

					// Delete button
					$('#delete-event').off().on('click', function(e)
					{
						remove(fc_calendar.id);
						e.preventDefault();
					});

					// Export button
					$('#export-event').off().on('click', function(e)
					{
						exportIcal(fc_calendar.id, fc_calendar.title, fc_calendar.ExpS, fc_calendar.ExpE);
						e.preventDefault();
					});

					 // Edit Button
					 $('#edit-event').off().on('click', function(e) {

						document.getElementById("modal-form-body").reset();
						$('#modal-form-body').html(opt.modalFormBody);

						$('#export-event, #delete-event, #edit-event, #add-event').hide();
						$('#save-changes').show().css('width', '100%');

						$('#details-body, #event-type-select').hide();
						$('#repeat-type-select, #repeat-type-selected').hide();
						$('#event-type-selected').show();
						$('#modal-form-body').show();
						$(opt.modalSelector).modal('show');
						
						customFieldsEditFields();
						
						// save action
						$('#save-changes').off().on('click', function(e) {
							if($('input[name=title]').val().length == 0)
							{
								alert(opt.emptyForm);
							} else {
							    editFormData = new FormData();
								
								var fdValues = $('#modal-form-body').serializeArray();

								$.each(fdValues, function(key, input) {
									if($.isArray(input.value))
									{
										for (var i = 0; i < input.value.length; i++) {
											editFormData.append(input.name+'[]', input.value[i]);
										}
									} else {
										editFormData.append(input.name, input.value);
									}
								});
								
								var checkbox = $('#modal-form-body').find("input[type=checkbox]");
								$.each(checkbox, function(key, val) {
									if(!$(this).is(":checked")) {
										editFormData.append($(val).attr('name'), '');
									}
								});
								
								if($('#file')[0])
								{
									if($('#file')[0].files[0] !== undefined && $('#file')[0].files[0] !== "undefined")
									{
										editFormData.append('file', $('#file')[0].files[0]);
									}
								}
								
								let inputStartDate = flatpickr('#startDate').input.value + ' ' + flatpickr('#startTime').input.value;
								let inputEndDate = flatpickr('#endDate').input.value + ' ' + flatpickr('#endTime').input.value;
								
								editFormData.set('timezone', opt.timezone);
								
								update(fc_calendar.id, editFormData);
							}
							e.preventDefault();
						})

					 });

				} //-- End openModal
				
				// Current event data
				calendar.data = function()
				{
					var data = {
						id: fc_calendar.id,
						title: fc_calendar.title,
						url: fc_calendar.url,
						start: fc_calendar.eventStart,
						end: fc_calendar.eventEnd,
						description: fc_calendar.description,
						color: fc_calendar.color
					};
					return data;
				} // -- End calendar data

				// Google Calendar Open Modal
				calendar.openModalGcal = function(title, url) {
					$('#modal-form-body').hide();
					$('#details-body').show();
					$('#details-body-title').html(title);
					$('#details-body-content').html('<a target="_blank" href="'+url+'">'+opt.gcalUrlText+'</a>');
					$('#export-event, #delete-event, #edit-event').hide();
					$('#save-changes, #add-event').hide();
					$('.modal-footer').hide();
					$(opt.modalSelector).modal('show');
				} //-- End openModalGcal

				// Function to quickModal
				calendar.quickModal = function(start, end, allDay)
				{
					document.getElementById("modal-form-body").reset();
					$('#modal-form-body').html(opt.modalFormBody);

					var start_factor = moment(start).format('YYYY-MM-DD');
					var startTime_factor = moment(start).format('HH:mm');
					var end_factor = moment(end).format('YYYY-MM-DD');
					var endTime_factor = moment(end).format('HH:mm');

					var e_val = moment(end).isValid();
					if(e_val == false)
					{
						var end_factor = start_factor;
						var endTime_factor = startTime_factor;
					}

					$('#startDate').val(start_factor);
					$('#startTime').val(startTime_factor);
					$('#endDate').val(end_factor);
					$('#endTime').val(endTime_factor);

					$('#details-body').hide();

					$('#event-type-select').show();
					$('#event-type-selected').hide();

					$('#repeat-type-select').show();
					$('#repeat-type-selected').hide();

					$('#export-event, #delete-event, #edit-event, #save-changes').hide();
					$('#add-event').show().css('width', '100%');

					$('.modal-footer').show();
					$('#modal-form-body').show();
					
					$('#details-body-title').html(opt.newEventText);
					$(opt.modalSelector).modal('show');

					$('#event-type').on('change', function() {
						var event_type_value = $(this).val();
						if(event_type_value == 'false')
						{
							$('#event-type-select').show();
							$('#event-type-selected').show();
						} else if (event_type_value == 'true') {
							$('#event-type-select').show();
							$('#event-type-selected').hide();
						}
					})

					$('#repeat_select').on('change', function() {
						var value = $(this).val();
						if(value !== 'no')
						{
							$('#repeat-type-select').show();
							$('#repeat-type-selected').show();
						} else if (value == 'no') {
							$('#repeat-type-select').show();
							$('#repeat-type-selected').hide();
						}
					})
					
					$("#colorp").spectrum("set", opt.defaultColor);
					
					// add action
					$('#add-event').off().on('click', function(e) {
						if($('input[name=title]').val().length == 0)
						{
							alert(opt.emptyForm);
						} else {
							formData = new FormData($('#modal-form-body').get(0));
							if($('#file')[0])
							{
								formData.append('file', $('#file')[0].files[0]);
							}
							
							let inputStartDate = flatpickr('#startDate').input.value + ' ' + flatpickr('#startTime').input.value;
							let inputEndDate = flatpickr('#endDate').input.value + ' ' + flatpickr('#endTime').input.value;
							
							formData.set('timezone', opt.timezone);
							
							quickSave(formData);
						}
						e.preventDefault();
					})

				} //-- End quickModal
				
				// Function eventDropResize
				var eventDropResize = function(info)
				{
					let element = $(info.el);
					let event = info.event._def;
					let instance = info.event._instance.range;
					
					// fix fullcalendar glitch that coverts to timezone already converted time
					var er_fullStartDate = moment(instance.start).utc().format('YYYY-MM-DD HH:mm');
					var er_fullEndDate = moment(instance.end).utc().format('YYYY-MM-DD HH:mm');
					var er_startDate = moment(instance.start).utc().format('YYYY-MM-DD');
					var er_startTime = moment(instance.start).utc().format('HH:mm');
					var er_endDate = moment(instance.end).utc().format('YYYY-MM-DD');
					var er_endTime = moment(instance.end).utc().format('HH:mm');
					
					var e_val = moment(instance.end).isValid();

					if(instance.end === null || instance.end === 'null' || e_val == false)
					{
						Eend = er_startDate+' '+er_startTime;
						EaD = 'false';
					} else {
						Eend = er_endDate+' '+er_endTime;
						EaD = event.allDay;
					}

					var theEvent = 'title='+encodeURIComponent(event.title)+'&start=' + er_startDate + ' ' + er_startTime +
								   '&end=' + Eend +
								   '&id=' + event.publicId +
								   '&allDay=' + EaD +
								   '&timezone=' + opt.timezone +
								   '&original_id=' + event.extendedProps.original_id;
					
					$.post(opt.ajaxUiUpdate, theEvent, function(response) {
						fullCal.refetchEvents();
					});
				}
				
				// Function quickSave
				var quickSave = function(formData)
				{
					$.ajax({
						url: opt.ajaxEventQuickSave,
						data: formData,
						type: "POST",
						cache: false,
						processData: false,
						contentType: false,
						beforeSend: function() { $('.loadingDiv').show(); $('.modal-footer').hide() },
						error: function() { $('.loadingDiv').hide(); console.log(opt.ajaxError); },
						success: function(res)
						{
							$('.loadingDiv').hide();
							if(res == 1)
							{
								$(opt.modalSelector).modal('hide');
								fullCal.refetchEvents();
							} else {
								alert(opt.failureAddEventMessage);
								$('.modal-footer').show();
							}
						}

					});
				} //-- End quickSave

				// Function to precheck event before update
				var update = function(id, theEvent)
				{
					var construct = "id="+id;

					// First check if the event is a repetitive event
					$.ajax({
						type: "POST",
						url: opt.ajaxRepeatCheck,
						data: construct,
						cache: false,
						beforeSend: function() { $('.loadingDiv').show(); },
						error: function() { $('.loadingDiv').hide(); console.log(opt.ajaxError) },
						success: function(response) {
							$('.loadingDiv').hide();
							if(response == 'REP_FOUND')
							{
								// prompt user
								$(opt.modalSelector).modal('hide');

								$(opt.modalEditPromptSelector+" .modal-header").html('<h4>'+opt.eventText+fc_calendar.title+'</h4>');
								$(opt.modalEditPromptSelector+" .modal-body-custom").css('padding', '15px').html(opt.repetitiveEventActionText);

								$(opt.modalEditPromptSelector).modal('show');

								// Action - save this
								$('[data-option="save-this"]').unbind('click').on('click', function(e)
								{
									update_this(id, theEvent);
									$(opt.modalEditPromptSelector).modal('hide');
									$(opt.modalSelector).modal('hide');
									e.preventDefault();
								 });

								// Action - save repetitives
								$('[data-option="save-repetitives"]').unbind('click').on('click', function(e)
								{
									var construct_two = 'true';

									update_this(id, theEvent, construct_two);
									$(opt.modalEditPromptSelector).modal('hide');
									$(opt.modalSelector).modal('hide');
									e.preventDefault();
								 });

							} else {
								update_this(id, theEvent);
							}
						},
						error: function(response) {
							alert(opt.generalFailureMessage);
						}
					});
				}

				// Function to update single and repetitive events
				var update_this = function(id, theEvent, construct_two)
				{
					if(construct_two === undefined)
					{
						editFormData.append('id', id);
					} else {
						editFormData.append('id', id);
						editFormData.append('rep_id', fc_calendar.rep_id);
						editFormData.append('method', 'repetitive_event');
					}
					
					editFormData.append('otitle', fc_calendar.title);
					
					$.ajax({
						type: "POST",
						url: opt.ajaxEventEdit,
						data: theEvent,
						cache: false,
						processData: false,
						contentType: false,
						beforeSend: function() { $('.loadingDiv').show(); },
						error: function() { $('.loadingDiv').hide(); console.log(opt.ajaxError) },
						success: function(response) {
							$('.loadingDiv').hide();
							if(response == '')
							{
								$(opt.modalSelector).modal('hide');
								fullCal.refetchEvents();
							} else {
								alert(opt.failureUpdateEventMessage);
							}
						},
						error: function(response) {
							alert(opt.failureUpdateEventMessage);
						}
					});
				}

				// Function to precheck event before removal
				var remove = function(id)
				{
					// First check if the event is a repetitive event
					var construct = 'id='+id;

					$.ajax({
						type: "POST",
						url: opt.ajaxRepeatCheck,
						data: {id: id},
						cache: false,
						beforeSend: function() { $('.loadingDiv').show(); },
						error: function() { $('.loadingDiv').hide(); console.log(opt.ajaxError) },
						success: function(response)
						{
							$('.loadingDiv').hide();
							if(response == 'REP_FOUND')
							{
								// prompt user
								$(opt.modalSelector).modal('hide');

								$(opt.modalPromptSelector+" .modal-header").html('<h4>'+opt.eventText+fc_calendar.title+'</h4>');
								$(opt.modalPromptSelector+" .modal-body").html(opt.repetitiveEventActionText);

								$(opt.modalPromptSelector).modal('show');

								// Action - remove this
								$('[data-option="remove-this"]').unbind('click').on('click', function(e)
								{
									remove_this(construct);
									$(opt.modalPromptSelector).modal('hide');
									e.preventDefault();
								});

								// Action - remove repetitive
								$('[data-option="remove-repetitives"]').unbind('click').on('click', function(e)
								{
									var construct = "id="+id+'&rep_id='+fc_calendar.rep_id+'&method=repetitive_event&delete=yes';

									remove_this(construct);
									$(opt.modalPromptSelector).modal('hide');
									e.preventDefault();
								});

							} else {
								remove_this(construct);
							}
						},
						error: function(response) {
							alert(opt.generalFailureMessage);
						}
					});
				};

				// Functo to Remove Event from the database
				var remove_this = function(construct)
				{
					// just remove this
					var dataQs = construct+'&title='+encodeURIComponent(fc_calendar.title)+'&start='+fc_calendar.d_startDate
					$.ajax({
						type: "POST",
						url: opt.ajaxEventDelete,
						data: dataQs,
						cache: false,
						beforeSend: function() { $('.loadingDiv').show(); },
						error: function() { $('.loadingDiv').hide(); console.log(opt.ajaxError) },
						success: function(response)
						{
							$('.loadingDiv').hide();
							if(response == '')
							{
								$(opt.modalSelector).modal('hide');
								fullCal.refetchEvents();
							} else {
								alert(opt.failureDeleteEventMessage);
							}
						}
					});
				}

				// Function to Export Calendar
				var exportIcal = function(expID, expTitle, expStart, expEnd)
				{
					var start_factor = expStart;
					var end_factor = expEnd;

					var construct = '&method=export&id='+encodeURIComponent(expID)+'&title='+encodeURIComponent(expTitle)+'&start_date='+encodeURIComponent(start_factor)+'&end_date='+encodeURIComponent(end_factor);

					window.location = opt.ajaxEventExport+construct;

				} // -- End export Calendar

				// Import
				calendar.calendarImport = function()
				{
					txt = 'import='+encodeURIComponent($('#import_content').val());
					$.post(opt.ajaxImport, txt, function(response)
					{
						alert(response);
						fullCal.refetchEvents();
						$('#cal_import').modal('hide');
						$('#import_content').val('');
					});
				} // -- End Import Calendar

				// Remove Obj Prop
				var removeObjProp = function(obj, props)
				{
					for(var i = 0; i < props.length; i++) {
						if(obj.hasOwnProperty(props[i])) {
							delete obj[props[i]];
						}
					}
				};
				// -- End Remove of Obj Prop
				
				// Fiter
				if(opt.filter == true)
				{
					$(opt.formFilterSelector).on('change', function(e)
					{
						selected_value = $(this).val();

						construct = 'filter='+encodeURIComponent(selected_value);

						$.post('includes/loader.php', construct, function(response)
						{
							fullCal.refetchEvents();
						});

						 e.preventDefault();
					});

				// Search Form
				// keypress
				$(opt.formSearchSelector).keypress(function(e)
				{
					if(e.which == 13)
					{
						search_me();
						e.preventDefault();
					}
				});

				// submit button
				$(opt.formSearchSelector+' button').on('click', function(e)
				{
					search_me();
				});

				function search_me()
				{
					value = $(opt.formSearchSelector+' input').val();

					construct = 'search='+encodeURIComponent(value);

					$.post('includes/loader.php', construct, function(response)
					{
						fullCal.refetchEvents();
					});
				}
				
				window.onbeforeunload = function() {
					var nfd = new FormData();
					nfd.append('search', '');
					nfd.append('filter', $(opt.formFilterSelector+' option:selected').val());
					navigator.sendBeacon("includes/loader.php", nfd);
				}

			 } // filter check
             
			 
			function customFieldsOutput(dsc, thisData)
			{
				removeObjProp(thisData, ['all-day','categorie','categories','category','color','description','description_editable','end','start','id','repeat_id','repeat_type','title','url','user_id']);

				var html_pair = '';
				$.getJSON(opt.jsonConfig, {_: new Date().getHours()}).then(function($json) {
					if($('.custom-fields').children().length > 0)
					{
						if(Object.keys(thisData).length > 0)
						{
							if(!Object.keys(thisData).every(function(x) { return thisData[x] === '' || thisData[x] === null; })) { html_pair = '<hr />'; }
							for(var key in thisData)
							{
								var value = thisData[key];
								var v = [];
								if(key == 'file')
								{
									if(value !== undefined && value !== "undefined" && value !== "")
									{
										if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(value))
										{
											value = '<a target="_blank" href="'+value+'"><img src="'+value+'" class="img-responsive" /></a>';
										} else {
											value = '<a target="_blank" href="'+value+'">'+value+'</a>';
										}
									} else {
										value = '';
									}
								}
								
								var label = JSONfindKeyByValue($json, '<'+key+'>');
								
								if(value.length > 0 || $.isArray(value) && value !== null)
								{ 
									if($.isArray(value) && value !== null)
									{
										for(var i = 0; i < value.length; i++)
										{ 
											v.push(JSONgetValues($json, value[i]));
										}
										html_pair += '<h5><strong>'+label+'</strong></h5><p>'+v.join(', ')+'</p><p class="custom-field-sep" style="margin-bottom: 0; height: 2px;">&nbsp;</p>';
									} else {
										if(value)
										{
											html_pair += '<h5><strong>'+label+'</strong></h5><p>'+value+'</p><p class="custom-field-sep" style="margin-bottom: 0; height: 2px;">&nbsp;</p>';
										}
									}
								}
							}
						}
					} 

					$('#details-body-content').html(dsc + html_pair);
				}).fail(function() {
					$('#details-body-content').html(dsc);
				});
			}
			
			function customFieldsEditFields()
			{
				$('#modal-form-body :input').each(function() {
					var name = $(this).attr('name');
					var tag = $(this)[0].tagName;

					switch(tag)
					{
						case "SELECT":
							var typeData = fc_calendar.data[name];
							if(typeData !== undefined)
							{
								$('option[value="'+typeData.replace('&amp;', '&')+'"]').prop('selected', true);
							}
						break;

						case "INPUT":
							var name = name.replace(/\[.*?\]/g,"");
							var typeName = $(this).attr('type');
							var typeData = fc_calendar.data[name];

							if(typeName == 'checkbox')
							{
								if(typeData !== undefined)
								{
									var values = typeData;
									for(var i = 0; i < values.length; i++)
									{
										$('input[value="'+values[i]+'"]').prop( "checked", true );
									}
								}
							}

							if(typeName == 'radio')
							{
								if(typeData !== undefined)
								{
									$('input[value="'+typeData+'"]').prop( "checked", true );
								}
							}

							if(typeName == 'file')
							{
								if(typeData !== undefined && typeData !== "undefined")
								{
									$(this).before('<p class="file-attachment"><a target="_blank" href="'+typeData+'">'+typeData+'</a></p>');
								}
							}

							if(typeName == 'text')
							{
								$('input[name="'+name+'"]').val(fc_calendar.data[name]);
								$('input[name=title]').val(fc_calendar.data['title']);
								setTimeout(function() {
									$("#colorp").spectrum("set", fc_calendar.data['color']);
								}, 50);
								$('#startDate').val(fc_calendar.d_startDate);
								$('input#startTime').val(fc_calendar.d_startTime);
								$('#endDate').val(fc_calendar.d_endDate);
								$('input#endTime').val(fc_calendar.d_endTime);
							}
						break;

						case "TEXTAREA":
							var typeData = fc_calendar.data[name];
							$('textarea[name="'+name+'"]').val(typeData);
							$('textarea[name="description"]').val( fc_calendar.data['description_editable'] );
						break;

						default:
							$(':input[name="'+name+'"]').val(fc_calendar.data[name]);
						break;
					}
				});
			}
			
			function JSONfindKeyByValue(json, searchVal)
			{ 
				var foundKey = '';
				for (var key in json)
				{ 
					if (json.hasOwnProperty(key))
					{
						var filteredJson = json[key].fields;
						for (var finalKey in filteredJson)
						{
							if(filteredJson.hasOwnProperty(finalKey))
							{
								Object.keys(filteredJson).forEach(function(key) {
									if(filteredJson[key].includes(searchVal) || filteredJson[key].includes(searchVal.replace(">","[]>")))
									{
										foundKey = key;
									} else {
										return false;
									}
								});
							}
						}
					}
				}
				 
				return foundKey;
			}
		 
			function JSONgetValues(obj, key) 
			{
				var objects = [];
				for (var i in obj) 
				{
					if (!obj.hasOwnProperty(i)) continue;
					if (typeof obj[i] == 'object') 
					{
						objects = objects.concat(JSONgetValues(obj[i], key));
					} else if (i == key) {
						objects.push(obj[i]);
					}
				}
				return objects;
			}
		
		} // FullCalendar Ext

	}); // fn

})(jQuery);

// define object at end of plugin to fix ie bug
var calendar = {};
var editFormData, formData;